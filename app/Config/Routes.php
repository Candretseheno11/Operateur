<?php

namespace Config;

$routes = Services::routes();

// ==========================================
// ROUTES PUBLIQUES (Authentification)
// ==========================================
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

// ==========================================
// ROUTES CÔTÉ CLIENT (Mobile Money)
// ==========================================
$routes->group('client', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->post('operation', 'ClientController::effectuerOperation');
});

$routes->group('operateur', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->post('operation', 'OperateurController::comptes');
    $routes->get('transactions', 'OperateurController::transactions');
    $routes->get('gains', 'OperateurController::gains');

});

// Garder aussi les routes directes si vous préférez y accéder sans le préfixe /client/ dans l'URL
$routes->get('client/login', 'AuthController::login');
$routes->post('client/auth', 'AuthController::attemptLogin');
$routes->get('client/dashboard', 'ClientController::dashboard');
$routes->post('client/operation', 'ClientController::effectuerOperation');
$routes->get('client/logout', 'AuthController::logout');