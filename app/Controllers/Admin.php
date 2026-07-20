<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use App\Models\DepartementModel;

class Admin extends BaseController
{
    /**
     * Dashboard : Statistiques et Vue d'ensemble
     */
    public function index()
    {
        $employeModel = new EmployeModel();
        $congeModel = new CongeModel();
        $depModel = new DepartementModel();
        $soldeModel = new SoldeModel();

        // Dates pour filtrage universel (Compatibilité SQLite strftime)
        $premierDuMois = date('Y-m-01 00:00:00');
        $dernierDuMois = date('Y-m-t 23:59:59');

        $data = [
            'totalEmployes'     => $employeModel->countAll(),
            'demandesEnAttente' => $congeModel->where('statut', 'en_attente')->countAllResults(),
            // Correction pour SQLite : on utilise des bornes de dates au lieu de MONTH()
            'approuveesMois'    => $congeModel->where('statut', 'approuve')
                                              ->where('created_at >=', $premierDuMois)
                                              ->where('created_at <=', $dernierDuMois)
                                              ->countAllResults(),
            'totalDeps'         => $depModel->countAll(),
            'absentsAujourdhui' => $congeModel->getAbsentsDuJour(), 

            'recentesDemandes'  => $congeModel->select('conges.*, employes.nom, employes.prenom')
                                              ->join('employes', 'employes.id = conges.employe_id')
                                              ->orderBy('conges.created_at', 'DESC')
                                              ->limit(5)
                                              ->findAll(),

            'soldesCritiques'   => $soldeModel->where('solde <=', 2)->countAllResults(),
        ];

        return view('Admin/index', $data);
    }

    /**
     * Liste des employés
     */
    public function employes()
    {
        $employeModel = new EmployeModel();
        $departementModel = new DepartementModel();

        $data = [
            'employes' => $employeModel->select('employes.*, departements.nom as departement_nom')
                                       ->join('departements', 'departements.id = employes.departement_id', 'left')
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll(),
            'departements' => $departementModel->findAll()
        ];

        return view('Admin/employes', $data);
    }

    /**
     * Sauvegarder un nouvel employé + Init Soldes
     */
    public function saveEmploye()
    {
        $rules = [
            'nom'            => 'required',
            'prenom'         => 'required',
            'email'          => 'required|valid_email|is_unique[employes.email]',
            'password'       => 'required|min_length[6]',
            'role'           => 'required|in_list[employee,rh,admin]',
            'departement_id' => 'required|integer',
            'date_embauche'  => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $employeModel = new EmployeModel();
        $soldeModel   = new SoldeModel();
        $typeModel    = new TypeCongeModel();

        $data = [
            'matricule'      => 'EMP-' . strtoupper(substr(uniqid(), -5)),
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche')
        ];

        if ($employeId = $employeModel->insert($data)) {
            // On initialise les soldes dynamiquement selon les types configurés en base
            $types = $typeModel->findAll();
            foreach ($types as $t) {
                $soldeModel->insert([
                    'employe_id'    => $employeId,
                    'type_conge_id' => $t['id'],
                    'solde'         => $t['jours_annuels'],
                    'annee'         => date('Y')
                ]);
            }
            return redirect()->to('/admin/employes')->with('success', 'Employé ajouté avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'insertion');
    }

    /**
     * Mettre à jour un employé
     */
    public function updateEmploye($id)
    {
        $rules = [
            'nom'            => 'required',
            'prenom'         => 'required',
            'email'          => "required|valid_email|is_unique[employes.email,id,{$id}]",
            'role'           => 'required|in_list[employee,rh,admin]',
            'departement_id' => 'required|integer',
            'date_embauche'  => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $employeModel = new EmployeModel();
        $data = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche')
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $employeModel->update($id, $data);
        return redirect()->to('/admin/employes')->with('success', 'Employé mis à jour');
    }

    /**
     * Supprimer un employé (AJAX ou Classique)
     */
    public function deleteEmploye($id)
    {
        if ($id == session()->get('user')['id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Impossible de supprimer votre propre compte']);
        }

        $employeModel = new EmployeModel();
        if ($employeModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }

    /**
     * Gestion globale des soldes
     */
    public function soldes()
    {
        $soldeModel = new SoldeModel();
        $data['soldes'] = $soldeModel
            ->select('soldes.*, employes.nom, employes.prenom, employes.matricule, types_conge.nom as type_nom')
            ->join('employes', 'employes.id = soldes.employe_id')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id')
            ->where('annee', date('Y'))
            ->orderBy('employes.nom', 'ASC')
            ->findAll();

        return view('Admin/soldes', $data);
    }

    /**
     * Types de congés
     */
    public function typesConges()
    {
        $typeCongeModel = new TypeCongeModel();
        $data['types'] = $typeCongeModel->findAll();
        return view('Admin/types_conges', $data);
    }

    /**
     * Ajout d'un type de congé
     */
    public function addTypeConge()
    {
        $typeCongeModel = new TypeCongeModel();
        $data = [
            'nom' => $this->request->getPost('nom'),
            'jours_annuels' => $this->request->getPost('jours_annuels')
        ];
        $typeCongeModel->insert($data);
        return redirect()->back()->with('success', 'Type ajouté');
    }

    /**
     * Profil Admin
     */
    public function profil()
    {
        $data['user'] = session()->get('user');
        return view('Admin/profil', $data);
    }
}