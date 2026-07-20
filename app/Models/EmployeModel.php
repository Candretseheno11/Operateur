<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['matricule', 'nom', 'prenom', 'email', 'password', 'role', 'departement_id', 'date_embauche'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'matricule' => 'required|is_unique[employes.matricule,id,{id}]',
        'email' => 'required|valid_email|is_unique[employes.email,id,{id}]',
        'nom' => 'required',
        'prenom' => 'required',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[employee,rh,admin]'
    ];

    protected $validationMessages = [
        'matricule' => [
            'required' => 'Le matricule est requis',
            'is_unique' => 'Ce matricule existe déjà'
        ],
        'email' => [
            'required' => 'L\'email est requis',
            'valid_email' => 'Email invalide',
            'is_unique' => 'Cet email existe déjà'
        ],
        'password' => [
            'required' => 'Le mot de passe est requis',
            'min_length' => 'Le mot de passe doit avoir au moins 8 caractères'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    public function createUser($data)
    {
        return $this->insert($data);
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getUserById($id)
    {
        return $this->find($id);
    }

    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->delete($id);
    }

}