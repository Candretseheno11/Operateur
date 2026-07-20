<?php

namespace App\Models;
use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id';

    protected $allowedFields = ['email', 'password'];

    public function login($email, $password){
        $admin = $this->where('email', $email)->first();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return false;
    }
}