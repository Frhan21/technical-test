<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Routes untuk manage users
$routes->get('users', 'UserController::index');
$routes->get('users/create', 'UserController::create');
$routes->post('users/store', 'UserController::store');
$routes->get('users/edit/(:num)', 'UserController::edit/$1');
$routes->put('users/update/(:num)', 'UserController::update/$1');
$routes->delete('users/delete/(:num)', 'UserController::delete/$1');


// Routes untuk manage products
$routes->get('products', 'ProductController::index'); 
$routes->get('products/create', 'ProductController::create'); 
$routes->post('products/store', 'ProductController::store'); 
$routes->get('products/edit/(:num)', 'ProductController::edit/$1');
$routes->put('products/update/(:num)', 'ProductController::update/$1'); 
$routes->delete('products/delete/(:num)', 'ProductController::delete/$1'); 


// Routes untuk manage transactions
$routes->get('transaction', 'TransactionController::index');
$routes->get('transaction/create', 'TransactionController::create'); 
$routes->post('transaction/store', 'TransactionController::store'); 
$routes->get('transaction/edit/(:num)', 'TransactionController::edit/$1');
$routes->put('transaction/update/(:num)', 'TransactionController::update/$1'); 
$routes->delete('transaction/delete/(:num)', 'TransactionController::delete/$1'); 
