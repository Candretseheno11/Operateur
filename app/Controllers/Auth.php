<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeModel;
class Auth extends BaseController
{


    public function index(): string
    {
        return view('Auth/login');
    }

    public function login()
    {
        $model = new EmployeModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();


        if (!$user) {
            return redirect()->back()->with('error', 'Email non repertorié ! Veuillez vous inscrire');
        }
        $password_verify = password_verify($password, $user['password']);
        if (!$password_verify) {
            return redirect()->back()->with('error', 'Mot de passe incorrect');
        }

        session()->set('user', [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'matricule' => $user['matricule'],
            'role' => $user['role'],
            'password' => $user['password'],
            'departement_id' => $user['departement_id'],
            'date_embauche' => $user['date_embauche'],

        ]);
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin');
        } else if ($user['role'] === 'rh') {
            return redirect()->to('/rh');
        }
        return redirect()->to('/employee');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }


}