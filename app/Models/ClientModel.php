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
    public function getClientByTelephones($telephone)
    {
        return $this->where('telephone', $telephone)->first();

    }
    public function getClientByTelephone(string $telephone)
    {
        return $this->select('clients.*, comptes.id as id_compte, comptes.solde, prefixes.prefixe')
            ->join('comptes', 'comptes.id_client = clients.id', 'left')
            ->join('prefixes', 'prefixes.id = clients.id_prefixe', 'left')
            ->where('clients.telephone', $telephone)
            ->first();
    }
    public function getPrefixeByNumber(string $telephone)
    {
        $codePrefixe = substr($telephone, 0, 3); // Extrait les 3 premiers chiffres (ex: 033 ou 037)

        $db = \Config\Database::connect();
        return $db->table('prefixes')
            ->where('prefixe', $codePrefixe)
            ->where('actif', 1)
            ->get()
            ->getRowArray();
    }
}