<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table = 'comptes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id_client', 'solde', 'date_creation'];

    // Dates
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getCompteByClientId($id_client)
    {
        return $this->where('id_client', $id_client)->first();
    }

    public function crediter($id_compte, $montant)
    {
        $compte = $this->find($id_compte);
        if ($compte) {
            $nouveau_solde = $compte['solde'] + $montant;
            return $this->update($id_compte, ['solde' => $nouveau_solde]);
        }
        return false;
    }

    public function debiter($id_compte, $montant)
    {
        $compte = $this->find($id_compte);
        if ($compte && $compte['solde'] >= $montant) {
            $nouveau_solde = $compte['solde'] - $montant;
            return $this->update($id_compte, ['solde' => $nouveau_solde]);
        }
        return false;
    }
    public function updateSolde($id_compte, $nouveau_solde)
    {
        return $this->update($id_compte, ['solde' => $nouveau_solde]);
    }

    public function getSolde($id_compte)
    {
        $compte = $this->find($id_compte);
        return $compte ? $compte['solde'] : null;
    }

    public function getCompteForUpdate($id_compte)
    {
        return $this->where('id', $id_compte)->first();
    }


}