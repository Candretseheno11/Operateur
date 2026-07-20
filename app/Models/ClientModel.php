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

    public function getClientByTelephones($telephone)
    {
        return $this->where('telephone', $telephone)->first();
    }

    /**
     * Récupère un client avec son compte via son numéro
     */
    public function getClientByTelephone(string $telephone)
    {
        return $this->select('clients.*, comptes.id as id_compte, comptes.solde')
            ->join('comptes', 'comptes.id_client = clients.id', 'left')
            ->where('clients.telephone', $telephone)
            ->first();
    }

    /**
     * Vérifie si le préfixe existe dans la table prefixes (ex: 033, 037)
     */
    public function getPrefixeByNumber(string $telephone)
    {
        $codePrefixe = substr($telephone, 0, 3); // Extrait 033, 037, etc.

        $db = \Config\Database::connect();
        return $db->table('prefixes')
            ->where('prefixe', $codePrefixe)
            ->where('actif', 1)
            ->get()
            ->getRowArray();
    }

    public function getCountClients()
    {
        return $this->countAllResults();
    }
}