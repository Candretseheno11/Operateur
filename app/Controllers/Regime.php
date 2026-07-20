<?php

namespace App\Controllers;

use App\Models\RegimeModel;

class Regime extends BaseController
{
    protected $regimeModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
    }

    // READ ALL
    // READ ALL
    public function index()
    {
        $regimes = $this->regimeModel->getAllRegimes();

        // Calcul statistiques
        $totalRegimes = count($regimes);

        $totalPrix = 0;
        $totalDuree = 0;
        $totalVariation = 0;

        foreach ($regimes as $regime) {

            $totalPrix += $regime['prix'];
            $totalDuree += $regime['duree'];
            $totalVariation += $regime['variation_poids'];

        }

        $stats = [

            'totalRegimes' => $totalRegimes,

            'avgRegimePrix' => $totalRegimes > 0
                ? round($totalPrix / $totalRegimes, 2)
                : 0,

            'avgRegimeDuree' => $totalRegimes > 0
                ? round($totalDuree / $totalRegimes, 2)
                : 0,

            'avgVariationPoids' => $totalRegimes > 0
                ? round($totalVariation / $totalRegimes, 2)
                : 0,
        ];

        $data = [
            'regimes' => $regimes,
            'stats' => $stats,
            'pager' => $this->regimeModel->pager,
        ];



        return view('Admin/Regime/index', $data);
    }
    // READ ONE
    public function show($id)
    {
        $data['regime'] = $this->regimeModel->getRegimeById($id);

        if (!$data['regime']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Régime introuvable");
        }

        return view('Admin/Regime/show', $data);
    }

    // FORM CREATE
    public function create()
    {
        return view('Admin/Regime/create');
    }

    // INSERT
    public function store()
    {
        $data = [
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
            'prix' => $this->request->getPost('prix'),
            'duree' => $this->request->getPost('duree'),
            'variation_poids' => $this->request->getPost('variation_poids'),
            'pourcentage_viande' => $this->request->getPost('pourcentage_viande'),
            'pourcentage_poisson' => $this->request->getPost('pourcentage_poisson'),
            'pourcentage_volaille' => $this->request->getPost('pourcentage_volaille')
        ];

        $this->regimeModel->createRegime($data);

        return redirect()->to('/admin/regime')->with('success', 'Régime créé avec succès.');
    }

    // FORM UPDATE
    public function edit($id)
    {
        $data['regime'] = $this->regimeModel->getRegimeById($id);

        if (!$data['regime']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Régime introuvable");
        }

        return view('Admin/Regime/edit', $data);
    }

    // UPDATE
    public function update($id)
    {
        $data = [
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
            'prix' => $this->request->getPost('prix'),
            'duree' => $this->request->getPost('duree'),
            'variation_poids' => $this->request->getPost('variation_poids'),
            'pourcentage_viande' => $this->request->getPost('pourcentage_viande'),
            'pourcentage_poisson' => $this->request->getPost('pourcentage_poisson'),
            'pourcentage_volaille' => $this->request->getPost('pourcentage_volaille')
        ];

        $this->regimeModel->updateRegime($id, $data);

        return redirect()->to('/admin/regime')->with('success', 'Régime mis à jour avec succès.');
    }

    // DELETE
    public function delete($id)
    {
        $this->regimeModel->deleteRegime($id);

        return redirect()->to('/admin/regime')->with('success', 'Régime supprimé avec succès.');

    }
}