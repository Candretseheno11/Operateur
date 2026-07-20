<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom', 'prenom', 'date_creation', 'telephone', 'prefixe'];
    // Ajoutez cette méthode ici :
    public function getClientById($id)
    {
        return $this->find($id);
    }
    public function getClientByTelephone($telephone)
    {
        return $this->where('telephone', $telephone)->first();

    }

}