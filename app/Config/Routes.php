<?php

namespace Config;

$routes = Services::routes();

// ==========================================
// ROUTES PUBLIQUES (Authentification)
// ==========================================
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::loginPost');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/login-operateur', 'AuthController::loginOperateur');
$routes->post('/login-operateur', 'AuthController::loginOperateurPost');

// ==========================================
// ROUTES CÔTÉ CLIENT (Mobile Money)
// ==========================================

$routes->group('client', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->post('operation', 'ClientController::effectuerOperation');
    $routes->get('logout', 'AuthController::logout');

    $routes->post('depot', 'ClientController::depot');
    $routes->post('retrait', 'ClientController::retrait');
    $routes->post('transfert', 'ClientController::transfert');
});

$routes->group('operateur', ['filter' => 'role'], function ($routes) {
    // Dashboard & Operations
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->post('operation', 'OperateurController::comptes');
    $routes->get('transactions', 'OperateurController::transactions');
    $routes->get('gains', 'OperateurController::gains');

    // --- GESTION DES BARÈMES ---
    $routes->get('bareme', 'OperateurController::bareme');                      // Affichage liste
    $routes->get('bareme/add', 'OperateurController::addBaremeForm');           // Formulaire d'ajout
    $routes->post('bareme/add', 'OperateurController::addBareme');              // Traitement ajout
    $routes->get('bareme/edit/(:num)', 'OperateurController::editFromBareme/$1'); // Formulaire d'édition
    $routes->post('bareme/update/(:num)', 'OperateurController::updateBareme/$1'); // Traitement édition
    $routes->get('bareme/delete/(:num)', 'OperateurController::deleteBareme/$1'); // Suppression

    // --- GESTION DES PRÉFIXES ---
    $routes->get('prefixes', 'OperateurController::prefixes');
    $routes->get('prefixes/add', 'OperateurController::addFormPrefix');
    $routes->post('prefixes/add', 'OperateurController::addPrefix');
    $routes->get('prefixes/edit/(:num)', 'OperateurController::editFormPrefix/$1');
    $routes->post('prefixes/update/(:num)', 'OperateurController::updatePrefix/$1');

    $routes->get('prefixes/delete/(:num)', 'OperateurController::deletePrefix/$1');
});