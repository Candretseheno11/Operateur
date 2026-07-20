<?php

namespace App\Controllers;

use App\Libraries\TransferFeeCalculator;
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
    // Si les libellés réels en base sont différents, adapte ces constantes.
    private const LIBELLE_DEPOT = 'depot';
    private const LIBELLE_RETRAIT = 'retrait';
    private const LIBELLE_TRANSFERT = 'transfert';

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
            return redirect()->to('/client/dashboard')->with('error', 'Montant de dépôt invalide.');
        }

        if (!$compte) {
            return redirect()->to('/client/dashboard')->with('error', 'Compte introuvable.');
        }

        try {
            $idTypeDepot = 1;
            $frais = $this->calculerFrais($idTypeDepot, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/dashboard')->with('error', $e->getMessage());
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
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Echec dépôt compte ' . $compte['id'] . ' : ' . json_encode($db->error()));
            return redirect()->to('/client/dashboard')->with('error', 'Une erreur est survenue lors du dépôt.');
        }

        return redirect()->to('/client/dashboard')->with('success', "Dépôt de " . number_format($montant, 2, ',', ' ') . " Ar effectué avec succès.");
    }

    /**
     * Retrait automatique. Frais déterminé par le barème (id_type_operation + tranche de montant).
     */
    public function retrait()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->to('/client/dashboard')->with('error', 'Montant de retrait invalide.');
        }

        $compte = $this->compteActuel();
        if (!$compte) {
            return redirect()->to('/client/dashboard')->with('error', 'Compte introuvable.');
        }

        try {
            $idTypeRetrait = 2;
            $frais = $this->calculerFrais($idTypeRetrait, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/dashboard')->with('error', $e->getMessage());
        }

        $totalDebit = $montant + $frais;

        $db = \Config\Database::connect();
        $db->transStart();

        // Verrou de la ligne pour empêcher un double retrait simultané
        $compteVerrouille = $this->compteModel->getCompteForUpdate($compte['id']);

        if (!$compteVerrouille || (float) $compteVerrouille['solde'] < $totalDebit) {
            $db->transComplete();
            return redirect()->to('/client/dashboard')->with('error', 'Solde insuffisant pour couvrir le retrait et les frais de ' . number_format($frais, 2, ',', ' ') . ' Ar.');

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
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$debitOk) {
            log_message('error', 'Echec retrait compte ' . $compte['id'] . ' : ' . json_encode($db->error()));
            return redirect()->to('/client/dashboard')->with('error', 'Une erreur est survenue lors du retrait.');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Retrait de " . number_format($montant, 2, ',', ' ') . " Ar effectué. (Frais : " . number_format($frais, 2, ',', ' ') . " Ar)",
            'solde' => (float) $this->compteModel->getSolde($compte['id']),
        ]);
    }

    /**
     * Transfert vers un autre client, identifié par son numéro de téléphone.
     * Frais déterminé par le barème (id_type_operation + tranche de montant).
     */
    public function transfert()
    {
        $telephoneDest = trim((string) $this->request->getPost('telephone'));
        $montant = (float) $this->request->getPost('montant');

        if (!preg_match('/^0\d{9}$/', $telephoneDest)) {
            return redirect()->to('/client/dashboard')->with('error', 'Numéro de téléphone destinataire invalide.');
        }

        if ($montant <= 0) {
            return redirect()->to('/client/dashboard')->with('error', 'Montant de transfert invalide.');
        }

        $compteSource = $this->compteActuel();
        if (!$compteSource) {
            return redirect()->to('/client/dashboard')->with('error', 'Compte introuvable.');
        }

        $prefixeDest = $this->prefixeModel->getPrefixeByNumber($telephoneDest);
        if (!$prefixeDest || (int) ($prefixeDest['actif'] ?? 0) !== 1) {
            return redirect()->to('/client/dashboard')->with('error', 'Ce préfixe d’opérateur n’est pas valide ou non supporté.');
        }

        $clientDest = $this->clientModel->getClientByTelephone($telephoneDest);
        if (!$clientDest || empty($clientDest['id_compte'])) {
            return redirect()->to('/client/dashboard')->with('error', 'Aucun compte actif ne correspond à ce numéro.');
        }

        if ($clientDest['id_compte'] === $compteSource['id']) {
            return redirect()->to('/client/dashboard')->with('error', 'Vous ne pouvez pas transférer vers votre propre compte.');
        }

        try {
            $idTypeTransfert = 3;
            $frais = $this->calculerFraisTransfert($idTypeTransfert, $montant, $prefixeDest);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/dashboard')->with('error', $e->getMessage());
        }

        $totalDebit = $montant + $frais;

        $db = \Config\Database::connect();
        $db->transStart();

        // Verrouiller les deux comptes dans un ordre constant (id croissant)
        // pour éviter les interblocages si deux transferts croisés ont lieu en même temps.
        $idsOrdonnes = [$compteSource['id'], $clientDest['id_compte']];
        sort($idsOrdonnes);
        foreach ($idsOrdonnes as $idCompte) {
            $this->compteModel->getCompteForUpdate($idCompte);
        }

        $soldeSource = $this->compteModel->getSolde($compteSource['id']);

        if ($soldeSource === null || (float) $soldeSource < $totalDebit) {
            $db->transComplete();
            return redirect()->to('/client/dashboard')->with('error', 'Solde insuffisant. Requis : ' . number_format($totalDebit, 2, ',', ' ') . ' Ar (frais inclus).');
        }

        $debitOk = $this->compteModel->debiter($compteSource['id'], $totalDebit);
        $creditOk = $debitOk && $this->compteModel->crediter($clientDest['id_compte'], $montant);

        if ($debitOk && $creditOk) {
            $this->transactionsModel->insert([
                'id_compte_source' => $compteSource['id'],
                'id_compte_destination' => $clientDest['id_compte'],
                'id_type_operation' => $idTypeTransfert,
                'montant' => $montant,
                'date_transaction' => date('Y-m-d H:i:s'),
                'frais' => $frais,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$debitOk || !$creditOk) {
            log_message('error', 'Echec transfert ' . $compteSource['id'] . ' -> ' . $clientDest['id_compte'] . ' : ' . json_encode($db->error()));
            return $this->jsonError('Une erreur est survenue lors du transfert.');
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Transfert de " . number_format($montant, 2, ',', ' ') . " Ar envoyé à {$telephoneDest}.",
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
     * Lève une exception claire si le libellé n'existe pas en base
     * (mauvaise config, plutôt que d'insérer une transaction avec un ID invalide).
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
     * Lève une exception si aucun palier ne couvre ce montant.
     */
    private function calculerFrais(int $idTypeOperation, float $montant): float
    {
        $bareme = $this->baremeModel->getBaremeForMontant($idTypeOperation, $montant);

        if ($bareme === null) {
            throw new RuntimeException('Aucun barème configuré pour ce montant.');
        }

        return (float) $bareme['frais'];
    }

    private function calculerFraisTransfert(int $idTypeOperation, float $montant, ?array $prefixeDestination): float
    {
        $fraisBase = $this->calculerFrais($idTypeOperation, $montant);

        return TransferFeeCalculator::calculateFee($fraisBase, $prefixeDestination);
    }

    private function jsonError(string $message, int $statusCode = 400)
    {
        return $this->response->setStatusCode($statusCode)->setJSON([
            'success' => false,
            'message' => $message,
        ]);
    }
}