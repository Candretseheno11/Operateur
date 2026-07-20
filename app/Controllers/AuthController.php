<?php

namespace App\Controllers;

use App\Models\ClientModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('client')) {
            return $this->redirectToDashboard(session()->get('client')['role'] ?? 'client');
        }
        return view('Auth/login');
    }

    public function loginPost()
    {
        $telephone = trim((string) $this->request->getPost('telephone'));
        if (empty($telephone)) {
            return redirect()->back()->with('error', 'Veuillez saisir un numéro de téléphone.');
        }

        $clientModel = new ClientModel();
        $prefixeData = $clientModel->getPrefixeByNumber($telephone);
        if (!$prefixeData) {
            return redirect()->back()->with('error', 'Ce préfixe d\'opérateur n\'est pas valide ou non supporté.');
        }

        $client = $clientModel->getClientByTelephone($telephone);
        if (!$client) {
            return redirect()->back()->with('error', 'Aucun compte trouvé pour ce numéro de téléphone. Veuillez vous inscrire.');
        }
        
        $role = $client['role'];
        // Stockage des données en session
        $sessionData = [
            'id' => $client['id'],
            'telephone' => $client['telephone'],
            'nom' => $client['nom'],
            'id_compte' => $client['id_compte'] ?? null,
            'solde' => $client['solde'] ?? 0.00,
            'role' => $client['role']
        ];

        session()->set('client', $sessionData);

        // Récupération du rôle
        $role = $client['role'] ?? 'client';

        // Redirection en fonction du rôle
        return $this->redirectToDashboard($role);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // Changement de private à protected ou public
    protected function redirectToDashboard(string $role)
    {

        log_message('debug', 'Redirection pour le rôle: ' . $role);

        if ($role === 'operateur') {
            return redirect()->to('/operateur/dashboard');
        }

        return redirect()->to('/client/dashboard');
    }
}