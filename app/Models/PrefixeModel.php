<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefixe', 'actif'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'prefixe' => 'required|is_unique[prefixes.prefixe,id,{id}]',
        'actif' => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [
        'prefixe' => [
            'required' => 'Le prefixe est requis',
            'is_unique' => 'Ce prefixe existe déjà'
        ],
        'actif' => [
            'required' => 'L\'état est requis',
            'in_list' => 'État invalide'
        ]
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

    public function addPrefix($data)
    {
        return $this->insert($data);
    }

    public function updatePrefix($id, $data)
    {
        return $this->update($id, $data);
    }

}