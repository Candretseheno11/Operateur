<?php

namespace Config;

$routes = Services::routes();

// Routes publiques
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

$routes->group('employee', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/', 'Employee::index');

    // Formulaire nouvelle demande
    $routes->get('create', 'Employee::create');
    $routes->post('soumettre', 'Employee::soumettre');

    // Liste des demandes
    $routes->get('demandes', 'Employee::demandes');

    // Annulation
    $routes->post('annuler/(:num)', 'Employee::annuler/$1');

    // Solde
    $routes->get('solde', 'Employee::solde');

    // Profil
    $routes->get('profil', 'Employee::profil');
    $routes->post('updateProfile', 'Employee::updateProfile');
    $routes->post('changePassword', 'Employee::changePassword');
});

$routes->group('rh', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/', 'Rh::index');

    // Demandes
    $routes->get('demandes', 'Rh::demandes');

    // Actions AJAX
    $routes->post('approuver/(:num)', 'Rh::approuver/$1');
    $routes->post('refuser/(:num)', 'Rh::refuser/$1');

    // Historique
    $routes->get('historique', 'Rh::historique');

    // Employés
    $routes->get('employes', 'Rh::employes');

    // Profil
    $routes->get('profil', 'Rh::profil');
    $routes->post('updateProfile', 'Rh::updateProfile');
    $routes->post('changePassword', 'Rh::changePassword');

    // Voir détail demande
    $routes->get('voir/(:num)', 'Rh::voir/$1');
});

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('employes', 'Admin::employes');
    $routes->get('employes/ajouter', 'Admin::ajouterEmploye');
    $routes->post('employes/save', 'Admin::saveEmploye');
    $routes->get('employes/edit/(:num)', 'Admin::editEmploye/$1');
    $routes->post('employes/update/(:num)', 'Admin::updateEmploye/$1');
    $routes->delete('employes/delete/(:num)', 'Admin::deleteEmploye/$1');
    $routes->get('soldes', 'Admin::soldes');
    $routes->post('solde/update/(:num)', 'Admin::updateSolde/$1');
    $routes->get('types-conges', 'Admin::typesConges');
    $routes->post('types-conges/add', 'Admin::addTypeConge');
    $routes->get('profil', 'Admin::profil');
    $routes->post('updateProfile', 'Admin::updateProfile');
    $routes->post('changePassword', 'Admin::changePassword');
});