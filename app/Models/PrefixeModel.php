<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefixe', 'actif', 'est_autre_operateur', 'pourcentage_extra'];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'prefixe' => 'required|is_unique[prefixes.prefixe,id,{id}]',
        'actif' => 'permit_empty|in_list[0,1]',
        'est_autre_operateur' => 'permit_empty|in_list[0,1]',
        'pourcentage_extra' => 'permit_empty|numeric',
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
        // Use the Model's built-in update method, which respects allowedFields
        return $this->update($id, $data);
    }
}