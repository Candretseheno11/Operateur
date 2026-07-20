<?php

namespace App\Controllers;

use App\Models\ClientModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('client')) {
            return redirect()->to('/client/dashboard');
        }
        return view('Auth/login');
    }

    public function attemptLogin()
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
}