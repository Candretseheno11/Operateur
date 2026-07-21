<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteEpargneModel extends Model
{
    protected $table = 'comptes_epargne';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['id_compte', 'solde', 'pourcentage', 'actif'];
    protected $useTimestamps = false;

    /**
     * Récupère le compte d'épargne d'un compte principal s'il existe.
     */
    public function getEpargneByCompteId(int $idCompte): ?array
    {
        return $this->where('id_compte', $idCompte)->first();
    }

    /**
     * Définit ou met à jour le pourcentage d'épargne pour un compte.
     */
    public function configurerEpargne(int $idCompte, float $pourcentage): bool
    {
        $epargne = $this->getEpargneByCompteId($idCompte);

        if ($epargne) {
            return $this->update($epargne['id'], [
                'pourcentage' => $pourcentage,
                'actif' => $pourcentage > 0 ? 1 : 0
            ]);
        }

        return $this->insert([
            'id_compte' => $idCompte,
            'solde' => 0,
            'pourcentage' => $pourcentage,
            'actif' => $pourcentage > 0 ? 1 : 0
        ]) !== false;
    }

    /**
     * Crédite le compte d'épargne.
     */
    public function crediter(int $idEpargne, float $montant): bool
    {
        $epargne = $this->find($idEpargne);
        if ($epargne) {
            $nouveauSolde = (float) $epargne['solde'] + $montant;
            return $this->update($idEpargne, ['solde' => $nouveauSolde]);
        }
        return false;
    }
}