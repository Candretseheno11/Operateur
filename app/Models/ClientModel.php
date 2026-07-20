<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';

    // N'incluez dans allowedFields que les vraies colonnes de votre table
    protected $allowedFields = ['nom', 'telephone', 'date_creation', 'role'];

    public function getClientById($id)
    {
        return $this->find($id);
    }

    public function getClientByTelephone($telephone)
    {
        return $this->where('telephone', $telephone)->first();
    }

    /**
     * Récupère un client avec son compte via son numéro
     */
    public function getClientWithCompteByTelephones($telephone)
    {
        return $this->select('clients.*, comptes.id as id_compte, comptes.solde
')
            ->join('comptes', 'clients.id = comptes.id_client', 'left')
            ->where('clients.telephone', $telephone)
            ->first();
    }

    /**
     * Vérifie si le préfixe existe dans la table prefixes (ex: 033, 037)
     */
    public function getPrefixeByNumber(string $telephone)
    {
        $codePrefixe = substr($telephone, 0, 3); // Extrait 033, 037, etc.

        return $this->db->table('prefixes')
            ->where('prefixe', $codePrefixe)
            ->get()
            ->getRowArray();
    }

    public function getCountClients()
    {
        return $this->countAllResults();
    }
}