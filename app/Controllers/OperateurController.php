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
                'totalUsers' => $totalClient,
                'totalUtilisateurs' => $totalClient,
                'totalRegimes' => 0, // À adapter selon vos modèles
                'avgRegimePrix' => 0, // À adapter
                'caloriesAverage' => 0, // À adapter
                'totalActivites' => 0, // À adapter
                'totalTransactionsMontant' => $gainTotal,
                'totalApproved' => 0, // À adapter
                'totalPending' => 0, // À adapter
                'totalRejected' => 0, // À adapter

                // Données dynamiques envoyées au graphique Chart.js (7 jours de la semaine)
                'chartData' => [12, 19, 15, 25, 22, 30, 28]
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

        $data = [
            'gainRetrait' => $gainRetrait['frais'] ?? 0,
            'gainTransfert' => $gainTransfert['frais'] ?? 0,
            'gainTotal' => ($gainRetrait['frais'] ?? 0) + ($gainTransfert['frais'] ?? 0)
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
        $baremeModel = new \App\Models\BaremeModel();

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
        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'actif' => $this->request->getPost('actif')
        ];
        $prefixeModel->addPrefix($data);
        return redirect()->to('/operateur/prefixes');
    }

    public function editPrefix($id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixe = $prefixeModel->getPrefixById($id);

        if (!$prefixe) {
            return redirect()->to('/operateur/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'actif' => $this->request->getPost('actif')
        ];

        $prefixeModel->updatePrefix($id, $data);
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe mis à jour avec succès.');
    }

    public function deletePrefix($id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixeModel->delete($id);
        return redirect()->to('/operateur/prefixes')->with('success', 'Préfixe supprimé avec succès.');
    }

}