<?php

namespace App\Controllers;

use App\Models\PrefixeModel;

class PromotionController extends BaseController
{
    private PrefixeModel $prefixeModel;

    public function __construct()
    {
        $this->prefixeModel = new PrefixeModel();
    }

    /**
     * Liste des promotions (préfixes avec pourcentage_extra > 0)
     */
    public function index()
    {
        $promotions = $this->prefixeModel
            ->where('est_autre_operateur', 1)
            ->where('pourcentage_extra >', 0)
            ->findAll();

        return view('operateur/promotions', [
            'promotions' => $promotions
        ]);
    }

    /**
     * Formulaire d'ajout de promotion
     */
    public function add()
    {
        $prefixes = $this->prefixeModel->where('actif', 1)->findAll();
        
        return view('operateur/add_promotion', [
            'prefixes' => $prefixes
        ]);
    }

    /**
     * Créer une nouvelle promotion
     */
    public function create()
    {
        $prefixeId = $this->request->getPost('prefixe_id');
        $pourcentage = (float) $this->request->getPost('pourcentage_extra');

        if ($pourcentage <= 0 || $pourcentage > 100) {
            return redirect()->back()->with('error', 'Le pourcentage doit être entre 0 et 100.');
        }

        $prefixe = $this->prefixeModel->getPrefixById($prefixeId);
        if (!$prefixe) {
            return redirect()->back()->with('error', 'Préfixe introuvable.');
        }

        $data = [
            'est_autre_operateur' => 1,
            'pourcentage_extra' => $pourcentage
        ];

        if ($this->prefixeModel->update($prefixeId, $data)) {
            return redirect()->to('/operateur/promotions')->with('success', 'Promotion créée avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la création de la promotion.');
    }

    /**
     * Formulaire de modification de promotion
     */
    public function edit($id)
    {
        $promotion = $this->prefixeModel->getPrefixById($id);
        
        if (!$promotion) {
            return redirect()->to('/operateur/promotions')->with('error', 'Promotion introuvable.');
        }

        return view('operateur/edit_promotion', [
            'promotion' => $promotion
        ]);
    }

    /**
     * Mettre à jour une promotion
     */
    public function update($id)
    {
        $pourcentage = (float) $this->request->getPost('pourcentage_extra');

        if ($pourcentage < 0 || $pourcentage > 100) {
            return redirect()->back()->with('error', 'Le pourcentage doit être entre 0 et 100.');
        }

        $data = [
            'est_autre_operateur' => $pourcentage > 0 ? 1 : 0,
            'pourcentage_extra' => $pourcentage
        ];

        if ($this->prefixeModel->update($id, $data)) {
            return redirect()->to('/operateur/promotions')->with('success', 'Promotion mise à jour avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour de la promotion.');
    }

    /**
     * Supprimer une promotion
     */
    public function delete($id)
    {
        $promotion = $this->prefixeModel->getPrefixById($id);
        
        if (!$promotion) {
            return redirect()->to('/operateur/promotions')->with('error', 'Promotion introuvable.');
        }

        $data = [
            'est_autre_operateur' => 0,
            'pourcentage_extra' => 0
        ];

        if ($this->prefixeModel->update($id, $data)) {
            return redirect()->to('/operateur/promotions')->with('success', 'Promotion supprimée avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression de la promotion.');
    }
}
