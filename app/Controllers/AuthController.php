<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\UserModel;
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

        // 1. Vérifier si le préfixe est valide (ex: 033, 034, 038, 032, 037)
        $prefixeData = $clientModel->getPrefixeByNumber($telephone);
        if (!$prefixeData) {
            return redirect()->back()->with('error', 'Ce préfixe d\'opérateur n\'est pas valide ou non supporté.');
        }

        // 2. Vérifier si le client existe déjà
        $client = $clientModel->getClientByTelephone($telephone);

        // 3. Si le client n'existe pas, création atomique via Transaction
        if (!$client) {
            $db = \Config\Database::connect();
            $db->transStart(); // Début de la transaction

            // Insertion du client
            $db->table('clients')->insert([
                'telephone' => $telephone,
                'nom' => 'Client ' . substr($telephone, -4),
                'prefixe' => $prefixeData['id'], // <--- Utiliser 'prefixe' au lieu de 'id_prefixe'
                'role' => 'client'
            ]);
            $clientId = $db->insertID();

            // Création du compte associé
            $db->table('comptes')->insert([
                'id_client' => $clientId,
                'solde' => 0.00
            ]);

            $db->transComplete(); // Validation de la transaction

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Une erreur est survenue lors de la création de votre compte.');
            }

            // Recharger les données du client (avec la jointure sur le compte)
            $client = $clientModel->getClientByTelephone($telephone);
        }

        if (!$client) {
            return redirect()->back()->with('error', 'Impossible de récupérer les informations du compte.');
        }

        // 4. Détermination du rôle (avec valeur par défaut 'client')
        $userRole = $client['role'] ?? 'client';

        // 5. Stockage en session
        session()->set('client', [
            'id' => $client['id'],
            'telephone' => $client['telephone'],
            'nom' => $client['nom'],
            'id_compte' => $client['id_compte'] ?? null,
            'solde' => $client['solde'] ?? 0.00,
            'role' => $userRole
        ]);

        // 6. Redirection selon le rôle
        if ($userRole === 'operateur') {
            return redirect()->to('/operateur/dashboard');
        }

        return redirect()->to('/client/dashboard');
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

    public function loginOperateur()
    {
        if (session()->get('client')) {
            return $this->redirectToDashboard(session()->get('client')['role'] ?? 'client');
        }
        return view('Auth/login_operateur');
    }

    public function loginOperateurPost()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Veuillez saisir votre email et mot de passe.');
        }

        $userModel = new UserModel();
        $user = $userModel->getUserByUsername($username);

        if (!$user || $password !== $user['password']) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect.');
        }

        // Stockage en session
        session()->set('user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => 'operateur'
        ]);

        return $this->redirectToDashboard('operateur');
    }
}