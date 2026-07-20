<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employe_id', 'type_conge_id', 'date_debut', 'date_fin', 'nb_jours', 'statut', 'motif', 'commentaire'];

    // Ajoutez cette méthode ici :
    public function getAbsentsDuJour()
    {
        $today = date('Y-m-d');

        return $this->select('conges.*, employes.nom, employes.prenom, types_conge.nom as type_nom')
            ->join('employes', 'employes.id = conges.employe_id')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            // Un employé est absent si AUJOURD'HUI est compris entre le début et la fin
            ->where('date_debut <=', $today)
            ->where('date_fin >=', $today)
            // Et si le congé est officiellement approuvé
            ->where('conges.statut', 'approuve')
            ->findAll();
    }
}