<?php

namespace App\Controllers;

use App\Models\BaremeModel;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\TransactionsModel;
use App\Models\TypeOperationModel;
use RuntimeException;

/**
 * Gère les opérations du client connecté :
 * voir le solde, dépôt automatique, retrait automatique, transfert, historique.
 *
 * Protégé par les filtres 'auth' + 'role:client' (voir Config/Filters.php et Routes.php).
 */
class ClientController extends BaseController
{
    // Libellés attendus dans la table `types_operations`.
    private const LIBELLE_DEPOT = 'Depot';
    private const LIBELLE_RETRAIT = 'Retrait';
    private const LIBELLE_TRANSFERT = 'Transfert';

    private CompteModel $compteModel;
    private TransactionsModel $transactionsModel;
    private ClientModel $clientModel;
    private TypeOperationModel $typeOperationModel;
    private BaremeModel $baremeModel;
    private PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->compteModel = new CompteModel();
        $this->transactionsModel = new TransactionsModel();
        $this->clientModel = new ClientModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeModel = new BaremeModel();
        $this->prefixeModel = new PrefixeModel();
    }

    /**
     * Page d'aperçu : solde + historique récent.
     */
    public function dashboard()
    {
        $client = session()->get('client');
        $compte = $this->compteModel->getCompteByClientId($client['id']);

        if (!$compte) {
            return redirect()->to('/login')->with('error', 'Compte introuvable.');
        }

        return view('client/dashboard', [
            'client' => $client,
            'compte' => $compte,
            'transactions' => $this->transactionsModel->getTransactionsByCompte($compte['id']),
        ]);
    }

    /**
     * Endpoint AJAX : voir le solde à jour.
     */
    public function solde()
    {
        $compte = $this->compteActuel();
        if (!$compte) {
            return $this->jsonError('Compte introuvable.', 404);
        }

        return $this->response->setJSON([
            'success' => true,
            'solde' => (float) $compte['solde'],
        ]);
    }

    /**
     * Dépôt automatique (supposé instantané, sans validation externe).
     */
    public function depot()
    {
        $compte = $this->compteActuel();
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return $this->jsonError('Montant de dépôt invalide.');
        }

        if (!$compte) {
            return $this->jsonError('Compte introuvable.');
        }

        try {
            $idTypeDepot = $this->getTypeOperationId(self::LIBELLE_DEPOT);
            $frais = $this->calculerFrais($idTypeDepot, $montant);
        } catch (RuntimeException $e) {
            return $this->jsonError($e->getMessage());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->compteModel->crediter($compte['id'], $montant);

        $this->transactionsModel->insert([
            'id_compte_source' => null,
            'id_compte_destination' => $compte['id'],
            'id_type_operation' => $idTypeDepot,
            'montant' => $montant,
            'date_transaction' => date('Y-m-d H:i:s'),
            'frais' => $frais,
            'frais_promotion' => 0,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Échec dépôt compte ' . $compte['id'] . ' : ' . json_encode($db->error()));
            return $this->jsonError('Une erreur est survenue lors du dépôt.');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Dépôt de " . number_format($montant, 2, ',', ' ') . " Ar effectué avec succès.",
            'solde' => (float) $this->compteModel->getSolde($compte['id']),
        ]);
    }

    /**
     * Retrait automatique. Frais déterminé par le barème (id_type_operation + tranche de montant).
     */
    public function retrait()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return $this->jsonError('Montant de retrait invalide.');
        }

        $compte = $this->compteActuel();
        if (!$compte) {
            return $this->jsonError('Compte introuvable.');
        }

        try {
            $idTypeRetrait = $this->getTypeOperationId(self::LIBELLE_RETRAIT);
            $frais = $this->calculerFrais($idTypeRetrait, $montant);
        } catch (RuntimeException $e) {
            return $this->jsonError($e->getMessage());
        }

        $totalDebit = $montant + $frais;

        $db = \Config\Database::connect();
        $db->transStart();

        // Verrou de la ligne pour empêcher un double retrait simultané
        $compteVerrouille = $this->compteModel->getCompteForUpdate($compte['id']);

        if (!$compteVerrouille || (float) $compteVerrouille['solde'] < $totalDebit) {
            $db->transComplete();
            return $this->jsonError('Solde insuffisant pour couvrir le retrait et les frais de ' . number_format($frais, 2, ',', ' ') . ' Ar.');
        }

        $debitOk = $this->compteModel->debiter($compte['id'], $totalDebit);

        if ($debitOk) {
            $this->transactionsModel->insert([
                'id_compte_source' => $compte['id'],
                'id_compte_destination' => null,
                'id_type_operation' => $idTypeRetrait,
                'montant' => $montant,
                'date_transaction' => date('Y-m-d H:i:s'),
                'frais' => $frais,
                'frais_promotion' => 0,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$debitOk) {
            log_message('error', 'Échec retrait compte ' . $compte['id'] . ' : ' . json_encode($db->error()));
            return $this->jsonError('Une erreur est survenue lors du retrait.');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Retrait de " . number_format($montant, 2, ',', ' ') . " Ar effectué. (Frais : " . number_format($frais, 2, ',', ' ') . " Ar)",
            'solde' => (float) $this->compteModel->getSolde($compte['id']),
        ]);
    }

    /**
     * Transfert vers un ou plusieurs clients, identifiés par leur numéro de téléphone.
     * Frais déterminé par le barème + supplément éventuel si autre opérateur.
     */
    public function transfert()
    {
        $telephonesJson = $this->request->getPost('telephones');
        $montantTotal = (float) $this->request->getPost('montant');
        $includeFees = $this->request->getPost('includeFees') === '1';

        $telephones = json_decode($telephonesJson, true);
        if (empty($telephones) || !is_array($telephones)) {
            return $this->jsonError('Veuillez ajouter au moins un destinataire.');
        }

        if ($montantTotal <= 0) {
            return $this->jsonError('Montant de transfert invalide.');
        }

        $compteSource = $this->compteActuel();
        if (!$compteSource) {
            return $this->jsonError('Compte introuvable.');
        }

        try {
            $idTypeTransfert = $this->getTypeOperationId(self::LIBELLE_TRANSFERT);
        } catch (RuntimeException $e) {
            return $this->jsonError($e->getMessage());
        }

        $nombreDestinataires = count($telephones);
        $montantParDestinataire = $montantTotal / $nombreDestinataires;

        // Récupérer le préfixe du client source
        $clientSource = session()->get('client');

        // Vérifier tous les destinataires d'abord
        $destinataires = [];
        $totalFrais = 0;
        foreach ($telephones as $tel) {
            $tel = trim($tel);
            if (!preg_match('/^0(32|33|34|37|38)\d{7}$/', $tel)) {
                return $this->jsonError("Numéro de téléphone {$tel} invalide.");
            }

            $clientDest = $this->clientModel->getClientByTelephone($tel);
            if (!$clientDest || empty($clientDest['id_compte'])) {
                return $this->jsonError("Aucun compte actif ne correspond au numéro {$tel}.");
            }
            if ($clientDest['id_compte'] === $compteSource['id']) {
                return $this->jsonError("Vous ne pouvez pas transférer vers votre propre compte ({$tel}).");
            }

            // Vérifier si le destinataire est sur un autre opérateur
            $prefixeDestData = $this->prefixeModel->getPrefixeByNumber($tel);
            $estAutreOperateur = $prefixeDestData && $prefixeDestData['est_autre_operateur'] == 1;

            try {
                $fraisBase = $this->calculerFrais($idTypeTransfert, $montantParDestinataire);
                $fraisPromotion = 0;

                // Ajouter les frais de promotion/supplément si autre opérateur
                if ($estAutreOperateur && $prefixeDestData) {
                    $fraisPromotion = ($montantParDestinataire * (float) $prefixeDestData['pourcentage_extra']) / 100;
                }

                $fraisTotalPartiel = $fraisBase + $fraisPromotion;
                $totalFrais += $fraisTotalPartiel;
            } catch (RuntimeException $e) {
                return $this->jsonError($e->getMessage());
            }

            $destinataires[] = [
                'telephone' => $tel,
                'client' => $clientDest,
                'frais' => $fraisTotalPartiel,
                'frais_promotion' => $fraisPromotion,
                'est_autre_operateur' => $estAutreOperateur
            ];
        }

        $totalDebit = $includeFees ? $montantTotal + $totalFrais : $montantTotal;

        $db = \Config\Database::connect();
        $db->transStart();

        // Collecter tous les IDs de comptes à verrouiller pour éviter un interblocage (deadlock)
        $idsComptes = [$compteSource['id']];
        foreach ($destinataires as $dest) {
            $idsComptes[] = $dest['client']['id_compte'];
        }
        $idsComptes = array_unique($idsComptes);
        sort($idsComptes);

        // Verrouiller les comptes dans l'ordre croissant des IDs
        foreach ($idsComptes as $idCompte) {
            $this->compteModel->getCompteForUpdate($idCompte);
        }

        $soldeSource = $this->compteModel->getSolde($compteSource['id']);

        if ($soldeSource === null || (float) $soldeSource < $totalDebit) {
            $db->transComplete();
            return $this->jsonError('Solde insuffisant. Requis : ' . number_format($totalDebit, 2, ',', ' ') . ' Ar.');
        }

        // Débiter le compte source
        $debitOk = $this->compteModel->debiter($compteSource['id'], $totalDebit);

        if ($debitOk) {
            // Créditer chaque destinataire et créer une transaction
            foreach ($destinataires as $dest) {
                $creditOk = $this->compteModel->crediter($dest['client']['id_compte'], $montantParDestinataire);
                if ($creditOk) {
                    $this->transactionsModel->insert([
                        'id_compte_source' => $compteSource['id'],
                        'id_compte_destination' => $dest['client']['id_compte'],
                        'id_type_operation' => $idTypeTransfert,
                        'montant' => $montantParDestinataire,
                        'date_transaction' => date('Y-m-d H:i:s'),
                        'frais' => $dest['frais'],
                        'frais_promotion' => $dest['frais_promotion'],
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$debitOk) {
            log_message('error', 'Échec transfert multiple : ' . json_encode($db->error()));
            return $this->jsonError('Une erreur est survenue lors du transfert.');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Transfert de " . number_format($montantTotal, 2, ',', ' ') . " Ar envoyé à {$nombreDestinataires} destinataire(s).",
            'solde' => (float) $this->compteModel->getSolde($compteSource['id']),
        ]);
    }

    /**
     * Historique complet (avec filtre optionnel par type d'opération).
     * ?type=depot|retrait|transfert
     */
    public function historique()
    {
        $compte = $this->compteActuel();
        if (!$compte) {
            return $this->jsonError('Compte introuvable.', 404);
        }

        $typeParam = $this->request->getGet('type');
        $mapLibelles = [
            'depot' => self::LIBELLE_DEPOT,
            'retrait' => self::LIBELLE_RETRAIT,
            'transfert' => self::LIBELLE_TRANSFERT,
        ];

        if (isset($mapLibelles[$typeParam])) {
            $idType = $this->typeOperationModel->getIdByLibelle($mapLibelles[$typeParam]);
            $transactions = $idType
                ? $this->transactionsModel->getTransactionsByCompteAndType($compte['id'], $idType)
                : [];
        } else {
            $transactions = $this->transactionsModel->getTransactionsByCompte($compte['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Récupère le compte du client actuellement connecté (via la session).
     */
    private function compteActuel(): ?array
    {
        $client = session()->get('client');
        if (!$client) {
            return null;
        }

        return $this->compteModel->getCompteByClientId($client['id']);
    }

    /**
     * Résout l'ID d'un type d'opération à partir de son libellé.
     */
    private function getTypeOperationId(string $libelle): int
    {
        $id = $this->typeOperationModel->getIdByLibelle($libelle);

        if ($id === null) {
            throw new RuntimeException("Type d'opération \"{$libelle}\" introuvable en base (table types_operations).");
        }

        return $id;
    }

    /**
     * Calcule le frais applicable via le barème (id_type_operation + tranche de montant).
     */
    private function calculerFrais(int $idTypeOperation, float $montant): float
    {
        $bareme = $this->baremeModel->getBaremeForMontant($idTypeOperation, $montant);

        if ($bareme === null) {
            throw new RuntimeException('Aucun barème configuré pour ce montant.');
        }

        return (float) $bareme['frais'];
    }

    /**
     * Retourne une réponse JSON d'erreur formatée.
     */
    private function jsonError(string $message, int $statusCode = 400)
    {
        return $this->response->setStatusCode($statusCode)->setJSON([
            'success' => false,
            'message' => $message,
        ]);
    }
}