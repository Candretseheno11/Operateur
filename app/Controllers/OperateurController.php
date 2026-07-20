<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionsModel;
use App\Models\BaremeModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;

class OperateurController extends BaseController
{
    protected $clientModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->transactionModel = new TransactionsModel();
    }


    public function dashboard()
    {
        $gainRetrait = $this->transactionModel->getGainByRetrait();
        $gainTransfert = $this->transactionModel->getGainByTransfert();
        $totalClient = $this->clientModel->getCountClients();
        $gainTotal = $gainRetrait + $gainTransfert;
        $data = [
            'gainRetrait' => $gainRetrait['frais'] ?? 0,
            'gainTransfert' => $gainTransfert['frais'] ?? 0,
            'gainTotal' => ($gainRetrait['frais'] ?? 0) + ($gainTransfert['frais'] ?? 0),
            'totalClient' => $totalClient,
            'stats' => [
                'pieData' => [
                    $gainRetrait['frais'] ?? 0,
                    $gainTransfert['frais'] ?? 0
                ]
            ]
        ];

        return view('operateur/index', $data);
    }

    public function comptes()
    {
        $clients = $this->clientModel
            ->select('
                clients.nom,
                clients.telephone,
                comptes.solde
            ')
            ->join('comptes', 'comptes.id_client = clients.id')
            ->findAll();

        return view('operateur/comptes', [
            'clients' => $clients
        ]);
    }


    public function transactions()
    {
        $transactions = $this->transactionModel
            ->select('
                transactions.*,
                types_operations.libelle
            ')
            ->join('types_operations', 'types_operations.id = transactions.id_type_operation')
            ->orderBy('date_transaction', 'DESC')
            ->findAll();

        return view('operateur/transactions', [
            'transactions' => $transactions
        ]);
    }

    public function gains()
    {
        $gainRetrait = $this->transactionModel->getGainByRetrait();
        $gainTransfert = $this->transactionModel->getGainByTransfert();
        $gainTransfertOperateur = $this->transactionModel->getGainByTransfertForOperatorType(0);
        $gainTransfertAutresOperateurs = $this->transactionModel->getGainByTransfertForOperatorType(1);
        $gainBreakdown = $this->transactionModel->getTransferGainBreakdown();

        $data = [
            'gainRetrait' => $gainRetrait['frais'] ?? 0,
            'gainTransfert' => $gainTransfert['frais'] ?? 0,
            'gainTransfertOperateur' => $gainTransfertOperateur['frais'] ?? 0,
            'gainTransfertAutresOperateurs' => $gainTransfertAutresOperateurs['frais'] ?? 0,
            'gainTotal' => ($gainRetrait['frais'] ?? 0) + ($gainTransfert['frais'] ?? 0),
            'gainBreakdown' => $gainBreakdown,
        ];

        return view('operateur/gains', $data);
    }

    public function bareme()
    {
        $baremeModel = new BaremeModel();
        $baremes = $baremeModel->getAllBaremesWithOperations();

        return view('operateur/bareme', [
            'baremes' => $baremes
        ]);
    }


    // Traitement de la mise à jour d'un barème (Modal)


    // Suppression d'un barème

    public function updateBareme($id)
    {
        $baremeModel = new BaremeModel();

        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais')
        ];
        $bareme = $baremeModel->getBaremeById($id);
        if (!$bareme) {
            return redirect()->to('/operateur/bareme')->with('error', 'Barème introuvable.');
        } else {
            $baremeModel->updateBareme($id, $data);
            return redirect()->to('/operateur/bareme')->with('success', 'Barème mis à jour avec succès.');
        }


    }


    // Traitement de la mise à jour d'un barème (Modal)
    public function editBareme()
    {
        $baremeModel = new BaremeModel();

        $id = $this->request->getPost('id');
        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais')
        ];

        if ($baremeModel->update($id, $data)) {
            return redirect()->to('/operateur/bareme')->with('success', 'Barème mis à jour avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du barème.');
    }

    // Suppression d'un barème
    public function deleteBareme($id)
    {
        $baremeModel = new BaremeModel();

        if ($baremeModel->delete($id)) {
            return redirect()->to('/operateur/bareme')->with('success', 'Barème supprimé avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression.');
    }
    public function addBaremeForm()
    {
        $baremeModel = new BaremeModel();
        $typesOperationsModel = new TypeOperationModel();
        $typesOperations = $typesOperationsModel->select('id, libelle')->findAll();

        return view('operateur/add_bareme', [
            'typesOperations' => $typesOperations
        ]);
    }


    public function editFromBareme($id)
    {
        $baremeModel = new BaremeModel();
        $bareme = $baremeModel->getBaremeById($id);

        if (!$bareme) {
            return redirect()->to('/operateur/bareme')->with('error', 'Barème introuvable.');
        }

        $typesOperationsModel = new TypeOperationModel();
        $typesOperations = $typesOperationsModel->select('id, libelle')->findAll();

        return view('operateur/edit_bareme', [
            'bareme' => $bareme,
            'typesOperations' => $typesOperations
        ]);
    }



    public function editFormPrefix($id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixe = $prefixeModel->getPrefixById($id);

        if (!$prefixe) {
            return redirect()->to('/operateur/prefixes')->with('error', 'Préfixe introuvable.');
        }

        return view('operateur/edit_prefix', [
            'prefixe' => $prefixe
        ]);
    }


    public function addBareme()
    {
        $baremeModel = new BaremeModel();
        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais')
        ];
        if ($baremeModel->checkBaremeExists($data['id_type_operation'], $data['montant_min'], $data['montant_max'])) {
            return redirect()->back()->with('error', 'Un barème avec ces paramètres existe déjà.');
        }
        $baremeModel->addBareme($data);
        return redirect()->to('/operateur/bareme');
    }

    public function prefixes()
    {
        $prefixeModel = new PrefixeModel();
        $prefixes = $prefixeModel->findAll();

        return view('operateur/prefixes', [
            'prefixes' => $prefixes
        ]);
    }

    public function addPrefix()
    {
        $prefixeModel = new PrefixeModel();
        $estAutreOperateur = (int) $this->request->getPost('est_autre_operateur');
        $pourcentageExtra = (float) $this->request->getPost('pourcentage_extra');

        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'actif' => $this->request->getPost('actif'),
            'est_autre_operateur' => $estAutreOperateur,
            'pourcentage_extra' => $estAutreOperateur === 1 ? $pourcentageExtra : 0.0,
        ];
        $prefixeModel->addPrefix($data);
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe ajouté avec succès.');
    }

    public function addFormPrefix()
    {

        return view('operateur/add_prefixe');
    }
    public function updatePrefix($id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixe = $prefixeModel->getPrefixById($id);

        if (!$prefixe) {
            return redirect()->to('/operateur/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $estAutreOperateur = (int) ($this->request->getPost('est_autre_operateur') ?? 0);
        $pourcentageExtra = (float) ($this->request->getPost('pourcentage_extra') ?? 0);
        $actif = (int) ($this->request->getPost('actif') ?? 1);

        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'actif' => $actif,
            'est_autre_operateur' => $estAutreOperateur,
            'pourcentage_extra' => $pourcentageExtra,
        ];

        // Force to skip validation for update - keep original value if new one is same
        $prefixeModel->skipValidation(true);

        if ($prefixeModel->update($id, $data)) {
            return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe mis à jour avec succès.');
        } else {
            $errors = $prefixeModel->errors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Erreur lors de la mise à jour.';
            return redirect()->back()->withInput()->with('error', $errorMsg);
        }
    }

    public function deletePrefix($id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixeModel->delete($id);
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe supprimé avec succès.');
    }

}