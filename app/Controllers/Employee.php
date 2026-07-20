<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class Employee extends BaseController
{
    /**
     * Tableau de bord employé
     */
    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        // Dernières demandes
        $dernieresDemandes = $congeModel
            ->select('conges.*, types_conge.nom as type_nom')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('employe_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        // Soldes
        $soldes = $soldeModel
            ->select('soldes.*, types_conge.nom as type_nom, types_conge.jours_annuels')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('employe_id', $userId)
            ->where('annee', date('Y'))
            ->findAll();

        // Compteurs
        $demandesEnAttente = $congeModel
            ->where('employe_id', $userId)
            ->where('statut', 'en_attente')
            ->countAllResults();

        $demandesApprouvees = $congeModel
            ->where('employe_id', $userId)
            ->where('statut', 'approuve')
            ->countAllResults();

        $demandesRefusees = $congeModel
            ->where('employe_id', $userId)
            ->where('statut', 'refuse')
            ->countAllResults();

        return view('Employe/index', [
            'dernieresDemandes' => $dernieresDemandes,
            'soldes' => $soldes,
            'demandesEnAttente' => $demandesEnAttente,
            'demandesApprouvees' => $demandesApprouvees,
            'demandesRefusees' => $demandesRefusees
        ]);
    }

    /**
     * Formulaire nouvelle demande
     */
    public function create()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $typeCongeModel = new TypeCongeModel();
        $soldeModel = new SoldeModel();

        // Types de congés
        $typesConges = $typeCongeModel->findAll();

        // Soldes utilisateur
        $soldesData = $soldeModel
            ->where('employe_id', $userId)
            ->where('annee', date('Y'))
            ->findAll();

        $soldes = [];

        foreach ($soldesData as $solde) {
            $soldes[$solde['type_conge_id']] = $solde['solde'];
        }

        return view('Employe/create', [
            'typesConges' => $typesConges,
            'soldes' => $soldes
        ]);
    }

    /**
     * Liste des demandes
     */
    public function demandes()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $demandes = $congeModel
            ->select('conges.*, types_conge.nom as type_nom, employes.nom, employes.prenom, employes.matricule')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->join('employes', 'employes.id = conges.employe_id')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Ajouter le solde actuel pour chaque demande
        foreach ($demandes as &$demande) {
            $solde = $soldeModel
                ->where('employe_id', $demande['employe_id'])
                ->where('type_conge_id', $demande['type_conge_id'])
                ->where('annee', date('Y'))
                ->first();
            $demande['solde_actuel'] = $solde['solde'] ?? 0;
        }

        // Compter par statut
        $nbEnAttente = count(array_filter($demandes, fn($d) => $d['statut'] == 'en_attente'));
        $nbApprouve = count(array_filter($demandes, fn($d) => $d['statut'] == 'approuve'));
        $nbRefuse = count(array_filter($demandes, fn($d) => $d['statut'] == 'refuse'));

        return view('Rh/demandes', [
            'demandes' => $demandes,
            'demandesEnAttente' => array_filter($demandes, fn($d) => $d['statut'] == 'en_attente'),
            'nbEnAttente' => $nbEnAttente,
            'nbApprouve' => $nbApprouve,
            'nbRefuse' => $nbRefuse
        ]);
    }

    /**
     * Soumettre une demande
     */
    public function soumettre()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $rules = [
            'type_conge_id' => 'required|integer',
            'date_debut' => 'required|valid_date',
            'date_fin' => 'required|valid_date',
            'motif' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez vérifier les informations');
        }

        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $typeCongeId = $this->request->getPost('type_conge_id');
        $motif = $this->request->getPost('motif');

        // Vérification dates
        if ($dateDebut > $dateFin) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La date de début doit être avant la date de fin');
        }

        // Calcul nombre jours
        $nbJours = $this->calculerNbJours($dateDebut, $dateFin);

        // Vérifier solde
        $soldeModel = new SoldeModel();

        $solde = $soldeModel
            ->where('employe_id', $userId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', date('Y'))
            ->first();

        if (!$solde || $solde['solde'] < $nbJours) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant');
        }

        // Vérifier chevauchement
        if ($this->verifierChevauchement($userId, $dateDebut, $dateFin)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous avez déjà une demande sur cette période');
        }

        // Sauvegarde
        $congeModel = new CongeModel();

        $data = [
            'employe_id' => $userId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif,
            'statut' => 'en_attente'
        ];

        if ($congeModel->save($data)) {
            return redirect()->to('/employee/demandes')
                ->with('success', 'Demande envoyée avec succès');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Erreur lors de la soumission');
    }

    /**
     * Annuler une demande
     */
    public function annuler($id)
    {
        if (!session()->has('user')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non autorisé'
            ]);
        }

        $userId = session()->get('user')['id'];

        $congeModel = new CongeModel();

        $demande = $congeModel
            ->where('id', $id)
            ->where('employe_id', $userId)
            ->first();

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Demande introuvable'
            ]);
        }

        if ($demande['statut'] !== 'en_attente') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Impossible d’annuler cette demande'
            ]);
        }

        if ($congeModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }

    /**
     * Affichage des soldes
     */
    public function solde()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $soldeModel = new SoldeModel();

        $soldes = $soldeModel
            ->select('soldes.*, types_conge.nom as type_nom, types_conge.jours_annuels')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('employe_id', $userId)
            ->where('annee', date('Y'))
            ->findAll();

        return view('Employe/solde', [
            'soldes' => $soldes
        ]);
    }

    /**
     * Profil employé
     */
    public function profil()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        return view('Employe/profil', [
            'user' => session()->get('user')
        ]);
    }

    /**
     * Mise à jour profil
     */
    public function updateProfile()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $data = [
            'nom' => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
            'email' => $this->request->getPost('email'),
        ];

        $userModel = new EmployeModel();

        if ($userModel->update($userId, $data)) {

            $user = session()->get('user');

            $user['nom'] = $data['nom'];
            $user['prenom'] = $data['prenom'];
            $user['email'] = $data['email'];

            session()->set('user', $user);

            return redirect()->back()
                ->with('success', 'Profil mis à jour');
        }

        return redirect()->back()
            ->with('error', 'Erreur lors de la mise à jour');
    }

    /**
     * Changer mot de passe
     */
    public function changePassword()
    {
        if (!session()->has('user')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user')['id'];

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()
                ->with('error', 'Les mots de passe ne correspondent pas');
        }

        $userModel = new EmployeModel();

        $user = $userModel->find($userId);

        if (!password_verify($oldPassword, $user['password'])) {
            return redirect()->back()
                ->with('error', 'Ancien mot de passe incorrect');
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()
            ->with('success', 'Mot de passe modifié');
    }

    /**
     * Calcul nombre jours
     */
    private function calculerNbJours($dateDebut, $dateFin)
    {
        $debut = new \DateTime($dateDebut);
        $fin = new \DateTime($dateFin);

        $interval = $debut->diff($fin);

        return $interval->days + 1;
    }

    /**
     * Vérification chevauchement
     */
    private function verifierChevauchement($employeId, $dateDebut, $dateFin)
    {
        $congeModel = new CongeModel();

        $chevauchements = $congeModel
            ->where('employe_id', $employeId)
            ->where('statut !=', 'refuse')
            ->groupStart()
            ->where('date_debut <=', $dateFin)
            ->where('date_fin >=', $dateDebut)
            ->groupEnd()
            ->findAll();

        return count($chevauchements) > 0;
    }
}