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
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getCompteByClientId($id_client)
    {
        return $this->where('id_client', $id_client)->first();
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


}