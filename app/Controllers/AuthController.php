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
        $telephone = trim($this->request->getPost('telephone'));
        $clientModel = new ClientModel();

        // 1. Vérifier si le préfixe est valide (ex: 033 ou 037)
        $prefixeData = $clientModel->getPrefixeByNumber($telephone);
        if (!$prefixeData) {
            return redirect()->back()->with('error', 'Ce préfixe d\'opérateur n\'est pas valide ou non supporté.');
        }

        // 2. Vérifier si le client existe déjà
        $client = $clientModel->getClientByTelephone($telephone);

        // 3. Si le client n'existe pas, on le crée ainsi que son compte à 0 Ar
        if (!$client) {
            $db = \Config\Database::connect();

            // Insertion du client
            $db->table('clients')->insert([
                'telephone' => $telephone,
                'nom' => 'Client ' . substr($telephone, -4), // Nom par défaut
                'id_prefixe' => $prefixeData['id']
            ]);
            $clientId = $db->insertID();

            // Création du compte associé avec un solde initial de 0
            $db->table('comptes')->insert([
                'id_client' => $clientId,
                'solde' => 0.00
            ]);

            // Recharger les données complètes du client
            $client = $clientModel->getClientByTelephone($telephone);
        }

        // 4. Stocker les informations dans la session
        session()->set('client', [
            'id' => $client['id'],
            'telephone' => $client['telephone'],
            'nom' => $client['nom'],
            'id_compte' => $client['id_compte'],
            'solde' => $client['solde']
        ]);

        return redirect()->to('/client/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}