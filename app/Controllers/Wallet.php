<?php

namespace App\Controllers;

use App\Models\WalletModel;
use App\Models\CodeModel;
use App\Models\TransactionModel;

class Wallet extends BaseController
{
    protected $walletModel;
    protected $codeModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->walletModel = new WalletModel();
        $this->codeModel = new CodeModel();
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        $userId = session('user.id');

        if (!$userId) {
            return redirect()->to('/login')
                ->with('error', 'Veuillez vous connecter pour accéder au portefeuille.');
        }

        $wallet = $this->walletModel->getWalletByUser($userId);

        // ✅ pagination réelle
        $transactions = $this->transactionModel->getPaginatedByUser($userId, 5);

        return view('wallet/index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'pager' => $this->transactionModel->pager,
        ]);
    }

    /* public function addMoney()
   {
       $userId = session('user.id');
       if (!$userId) {
           return redirect()->to('/login')->with('error', 'Veuillez vous connecter pour créditer votre portefeuille.');
       }

       if (!$this->validate(['code' => 'required'])) {
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
       }

       $code = $this->request->getPost('code');
       $isValid = $this->codeModel->checkCode($code);

       if (!$isValid) {
           return redirect()->back()->with('error', 'Code invalide ou déjà utilisé.');
       }

       $result = $this->walletModel->addMoneyWithCode($userId, $code);

       if ($result) {
           return redirect()->to('/wallet')->with('success', 'Montant ajouté au portefeuille avec succès.');
       }

       return redirect()->back()->with('error', 'Impossible d\'ajouter l\'argent avec ce code.');
   }
*/
    public function addMoney()
    {
        $userId = session('user.id');

        if (!$userId) {
            return redirect()->to('/login')
                ->with('error', 'Veuillez vous connecter.');
        }

        if (
            !$this->validate([
                'code' => 'required'
            ])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $code = $this->request->getPost('code');

        $codeData = $this->codeModel->validateCode($code);

        if (!$codeData) {
            return redirect()->back()
                ->with('error', 'Code invalide ou déjà utilisé.');
        }

        // Vérifie si le code est déjà en attente
        $existing = $this->transactionModel
            ->where('code_id', $codeData['id'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Ce code est déjà en attente.');
        }

        // Créer transaction en attente
        $this->transactionModel->createTransaction([
            'user_id' => $userId,
            'code_id' => $codeData['id'],
            'montant' => $codeData['montant'],
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/wallet')
            ->with('success', 'Demande envoyée à l’administrateur.');
    }
}