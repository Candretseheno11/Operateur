<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('client');

        // Doit être connecté (sécurité si le filtre est utilisé seul, sans "auth")
        if (!$user) {
            return redirect()->to('/login')->with('erreur', 'Connectez-vous pour accéder à cette page');
        }

        $role = $user['role'] ?? 'client';

        // $arguments contient le(s) rôle(s) autorisé(s) pour la route
        // ex: 'role' => ['operateur'] ou 'role' => ['operateur', 'client']
        if (is_array($arguments) && !empty($arguments) && !in_array($role, $arguments, true)) {
            // L'utilisateur est connecté mais n'a pas le bon rôle pour cette route
            // -> on le renvoie automatiquement vers SON espace, selon son rôle
            return $this->redirectToOwnSpace($role);
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Rien à faire après
    }

    /**
     * Redirige l'utilisateur vers l'espace correspondant à son rôle.
     */
    private function redirectToOwnSpace(string $role)
    {
        if ($role === 'operateur') {
            return redirect()->to('/operateur/dashboard');
        }

        return redirect()->to('/clients/dashboard');
    }
}