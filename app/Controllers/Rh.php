<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use App\Models\CongeModel;
use App\Models\SoldeModel;

class Rh extends BaseController
{
    private function checkAuth()
    {
        if (!session()->has('user')) {
            redirect()->to('/login')->send();
            exit;
        }
    }

    /**
     * DASHBOARD RH
     */
    public function index()
    {
        $this->checkAuth();

        $congeModel = new CongeModel();
        $employeModel = new EmployeModel();

        $demandesEnAttente = $congeModel
            ->select('conges.*, types_conge.nom as type_nom, employes.nom, employes.prenom')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('employes', 'employes.id = conges.employe_id')
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $demandesEnAttente = is_array($demandesEnAttente) ? $demandesEnAttente : [];

        $nbEnAttente = count($demandesEnAttente);

        $nbEmployes = $employeModel->where('role', 'employee')->countAllResults();

        $congesApprouvesMois = $congeModel
            ->where('statut', 'approuve')
            ->where("strftime('%m', date_debut)", date('m'))
            ->where("strftime('%Y', date_debut)", date('Y'))
            ->countAllResults();

        return view('RH/index', [
            'demandesEnAttente' => $demandesEnAttente,
            'demandes' => $demandesEnAttente,

            'nbDemandesEnAttente' => $nbEnAttente,
            'nbEnAttente' => $nbEnAttente,
            'nbApprouve' => 0,
            'nbRefuse' => 0,

            'nbEmployes' => $nbEmployes,
            'congesApprouvesMois' => $congesApprouvesMois,
        ]);
    }

    /**
     * LISTE DEMANDES
     */
    public function demandes()
    {
        $this->checkAuth();

        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $demandes = $congeModel
            ->select('conges.*, types_conge.nom as type_nom, employes.nom, employes.prenom')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('employes', 'employes.id = conges.employe_id')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $demandes = is_array($demandes) ? $demandes : [];

        foreach ($demandes as &$d) {
            $solde = $soldeModel
                ->where('employe_id', $d['employe_id'])
                ->where('type_conge_id', $d['type_conge_id'])
                ->where('annee', date('Y'))
                ->first();

            $d['solde_actuel'] = $solde['solde'] ?? 0;
        }

        $nbEnAttente = 0;
        $nbApprouve = 0;
        $nbRefuse = 0;

        foreach ($demandes as $d) {
            if ($d['statut'] === 'en_attente')
                $nbEnAttente++;
            if ($d['statut'] === 'approuve')
                $nbApprouve++;
            if ($d['statut'] === 'refuse')
                $nbRefuse++;
        }

        return view('RH/demandes', [
            'demandes' => $demandes,
            'nbEnAttente' => $nbEnAttente,
            'nbApprouve' => $nbApprouve,
            'nbRefuse' => $nbRefuse,
        ]);
    }

    /**
     * APPROUVER (AJAX JSON)
     */
    public function approuver($id)
    {
        $this->checkAuth();

        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $demande = $congeModel->find($id);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Demande introuvable'
            ]);
        }

        if ($demande['statut'] !== 'en_attente') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Déjà traité'
            ]);
        }

        $solde = $soldeModel
            ->where('employe_id', $demande['employe_id'])
            ->where('type_conge_id', $demande['type_conge_id'])
            ->where('annee', date('Y'))
            ->first();

        if (!$solde || $solde['solde'] < $demande['nb_jours']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solde insuffisant'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $congeModel->update($id, [
            'statut' => 'approuve',
            'commentaire' => 'Approuvé RH'
        ]);

        $soldeModel->update($solde['id'], [
            'solde' => $solde['solde'] - $demande['nb_jours']
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => $db->transStatus()
        ]);
    }

    /**
     * REFUSER (AJAX JSON)
     */
    public function refuser($id)
    {
        $this->checkAuth();

        $congeModel = new CongeModel();

        $demande = $congeModel->find($id);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Introuvable'
            ]);
        }

        $motif = $this->request->getPost('motif_refus') ?? 'Refus RH';

        $congeModel->update($id, [
            'statut' => 'refuse',
            'commentaire' => $motif
        ]);

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    /**
     * HISTORIQUE
     */
    public function historique()
    {
        $this->checkAuth();

        $congeModel = new CongeModel();

        $historique = $congeModel
            ->select('
            conges.*,
            types_conge.nom as type_nom,
            employes.nom,
            employes.prenom,
            employes.matricule
        ')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('employes', 'employes.id = conges.employe_id')
            ->whereIn('statut', ['approuve', 'refuse'])
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        $historique = is_array($historique) ? $historique : [];

        return view('RH/historique', [
            'historique' => $historique
        ]);
    }
    /**
     * SOLDES EMPLOYÉS
     */
    public function employes()
    {
        $this->checkAuth();

        $employeModel = new EmployeModel();
        $soldeModel = new SoldeModel();

        $employes = $employeModel->where('role', 'employee')->findAll();
        $employes = is_array($employes) ? $employes : [];

        foreach ($employes as &$e) {
            $e['soldes'] = $soldeModel
                ->select('soldes.*, types_conge.nom as type_nom')
                ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
                ->where('employe_id', $e['id'])
                ->where('annee', date('Y'))
                ->findAll();
        }

        return view('RH/employes', [
            'employes' => $employes
        ]);
    }

    /**
     * PROFIL RH
     */
    public function profil()
    {
        $this->checkAuth();

        return view('RH/profil', [
            'user' => session()->get('user')
        ]);
    }
}