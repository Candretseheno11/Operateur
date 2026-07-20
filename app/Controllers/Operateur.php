<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionsModel;

class OperateurController extends BaseController
{
    protected $clientModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->transactionModel = new TransactionsModel();
    }


    public function index()
    {
        $gainRetrait = $this->transactionModel->getGainByRetrait();
        $gainTransfert = $this->transactionModel->getGainByTransfert();

        $data = [
            'gainRetrait' => $gainRetrait['frais'] ?? 0,
            'gainTransfert' => $gainTransfert['frais'] ?? 0,
            'gainTotal' => ($gainRetrait['frais'] ?? 0) + ($gainTransfert['frais'] ?? 0)
        ];

        return view('operateur/dashboard', $data);
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
}