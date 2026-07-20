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

// ==========================================
// ROUTES CÔTÉ CLIENT (Mobile Money)
// ==========================================

$routes->group('client', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->post('operation', 'ClientController::effectuerOperation');
});
/*
$routes->group('operateur', ['filter' => ''], function ($routes) {
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->post('operation', 'OperateurController::comptes');
    $routes->get('transactions', 'OperateurController::transactions');
    $routes->get('gains', 'OperateurController::gains');
    $routes->get('bareme', 'OperateurController::bareme');
    $routes->post('bareme/add', 'OperateurController::addBareme');
    $routes->get('bareme/edit/(:num)', 'OperateurController::editBareme/$1');
    $routes->post('bareme/update/(:num)', 'OperateurController::editBareme/$1');
    $routes->get('bareme/delete/(:num)', 'OperateurController::deleteBareme/$1');
    $routes->get('prefixes', 'OperateurController::prefixes');
    $routes->post('prefixes/add', 'OperateurController::addPrefix');
    $routes->get('prefixes/edit/(:num)', 'OperateurController::editPrefix/$1');
    $routes->post('prefixes/update/(:num)', 'OperateurController::editPrefix/$1');
    $routes->get('prefixes/delete/(:num)', 'OperateurController::deletePrefix/$1');

});
*/
$routes->group('operateur', function ($routes) {
    // Dashboard & Operations
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->post('operation', 'OperateurController::comptes');
    $routes->get('transactions', 'OperateurController::transactions');
    $routes->get('gains', 'OperateurController::gains');

    // --- GESTION DES BARÈMES ---
    $routes->get('bareme', 'OperateurController::bareme');                      // Affichage liste
    $routes->get('bareme/add', 'OperateurController::addBaremeForm');           // Formulaire d'ajout
    $routes->post('bareme/add', 'OperateurController::addBareme');              // Traitement ajout
    $routes->get('bareme/edit/(:num)', 'OperateurController::editFormBareme/$1'); // Formulaire d'édition
    $routes->post('bareme/update/(:num)', 'OperateurController::updateBareme/$1'); // Traitement édition
    $routes->get('bareme/delete/(:num)', 'OperateurController::deleteBareme/$1'); // Suppression

    // --- GESTION DES PRÉFIXES ---
    $routes->get('prefixes', 'OperateurController::prefixes');
    $routes->get('prefixes/add', 'OperateurController::addFormPrefix');
    $routes->post('prefixes/add', 'OperateurController::addPrefix');
    $routes->get('prefixes/edit/(:num)', 'OperateurController::editFormPrefix/$1');
    $routes->post('prefixes/update/(:num)', 'OperateurController::editPrefix/$1');
    $routes->get('prefixes/delete/(:num)', 'OperateurController::deletePrefix/$1');
});
// Garder aussi les routes directes si vous préférez y accéder sans le préfixe /client/ dans l'URL
$routes->get('client/login', 'AuthController::login');
$routes->post('client/auth', 'AuthController::attemptLogin');
$routes->get('client/dashboard', 'ClientController::dashboard');
$routes->post('client/operation', 'ClientController::effectuerOperation');
$routes->get('client/logout', 'AuthController::logout');