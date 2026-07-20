<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefixe', 'actif'];

    // La table SQLite actuelle ne contient pas de colonnes created_at/updated_at.
    // On désactive donc les timestamps automatiques pour éviter l'erreur lors de l'insertion.
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'prefixe' => 'required|is_unique[prefixes.prefixe,id,{id}]',
        'actif' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'required' => 'Le préfixe est requis',
            'is_unique' => 'Ce préfixe existe déjà',
        ],
        'actif' => [
            'in_list' => 'État invalide',
        ],
    ];

    public function getActivePrefixes()
    {
        return $this->where('actif', 1)->findAll();
    }

    public function getInactivePrefixes()
    {
        return $this->where('actif', 0)->findAll();
    }

    public function getPrefixById($id)
    {
        return $this->find($id);
    }

    /**
     * Récupère un préfixe actif à partir d'un numéro de téléphone complet.
     * Centralise ici la logique déjà dupliquée en SQL brut dans ClientModel::getPrefixeByNumber().
     */
    public function getPrefixeByNumber(string $telephone)
    {
        $codePrefixe = substr($telephone, 0, 3);

        return $this->where('prefixe', $codePrefixe)
            ->where('actif', 1)
            ->first();
    }

    public function addPrefix(array $data)
    {
        // insert() retourne l'ID inséré, ou false si la validation échoue
        return $this->insert($data);
    }

    public function updatePrefix($id, array $data)
    {
        return $this->update($id, $data);
    }
}