<?php

namespace App\Controllers;

use App\Models\CodeModel;

class Operateur extends BaseController
{
    protected $codeModel;

    public function __construct()
    {
        $this->codeModel = new CodeModel();
    }

    // LISTE
    public function index()
    {
        // Données paginées pour le tableau
        $codes = $this->codeModel->paginate(10);

        // Statistiques sans pagination
        $totalCodes = $this->codeModel->countAll();

        $used = $this->codeModel
            ->where('is_used', 1)
            ->countAllResults();

        $unused = $this->codeModel
            ->where('is_used', 0)
            ->countAllResults();

        $totalMontant = $this->codeModel
            ->selectSum('montant')
            ->first()['montant'];

        $data = [
            'codes' => $codes,
            'pager' => $this->codeModel->pager,

            'stats' => [
                'totalCodes' => $totalCodes,
                'usedCodes' => $used,
                'unusedCodes' => $unused,
                'totalMontant' => $totalMontant
            ]
        ];

        return view('Admin/Code/index', $data);
    }
    // FORM CREATE
    public function create()
    {
        return view('Admin/Code/create');
    }

    // INSERT
    public function store()
    {
        $data = [
            'code' => $this->request->getPost('code'),
            'montant' => $this->request->getPost('montant'),
            'is_used' => $this->request->getPost('is_used') ?? 0
        ];

        $this->codeModel->insert($data);

        return redirect()->to('/admin/code')
            ->with('success', 'Code ajouté avec succès');
    }

    // FORM EDIT
    public function edit($id)
    {
        $code = $this->codeModel->find($id);

        if (!$code) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Code introuvable');
        }

        return view('Admin/Code/edit', ['codeData' => $code]);
    }

    // UPDATE
    public function update($id)
    {
        $data = [
            'code' => $this->request->getPost('code'),
            'montant' => $this->request->getPost('montant'),
            'is_used' => $this->request->getPost('is_used')
        ];

        $this->codeModel->update($id, $data);

        return redirect()->to('/admin/code')
            ->with('success', 'Code modifié avec succès');
    }

    // DELETE
    public function delete($id)
    {
        $db = \Config\Database::connect();

        $transaction = $db->table('transactions')
            ->where('code_id', $id)
            ->countAllResults();

        if ($transaction > 0) {
            return redirect()->to('/admin/code')
                ->with('error', 'Impossible de supprimer ce code car il est déjà utilisé dans une transaction.');
        }

        $this->codeModel->delete($id);

        return redirect()->to('/admin/code')
            ->with('success', 'Code supprimé avec succès');
    }
}