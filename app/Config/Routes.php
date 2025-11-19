<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. ROUTE PUBLIK (Tidak memerlukan login: Login, Registrasi, Logout)
// --------------------------------------------------------------------

// Default route (Jika user mengakses /)
$routes->get('/', 'AuthController::index'); 
$routes->get('login', 'AuthController::index'); 

// Memproses Form Login & Registrasi
$routes->post('login/process', 'AuthController::login'); 
$routes->get('register', 'AuthController::register');
$routes->post('register/store', 'AuthController::store');

// Logout (menghancurkan session)
$routes->get('logout', 'AuthController::logout'); 

// --------------------------------------------------------------------
// 2. ROUTE TERPROTEKSI (Memerlukan Filter 'auth' untuk akses)
// --------------------------------------------------------------------

// Semua rute di dalam grup ini akan melewati AuthFilter
$routes->group('', function($routes) {

    // ROUTE FEED
    $routes->get('feed/(:segment)', 'FeedController::index/$1'); 
    
    // ROUTE POST CRUD (Upload, Detail, Like, Comment)
    $routes->get('post/create', 'PostController::create');       
    $routes->post('post/store', 'PostController::store');        
    $routes->get('post/like/(:num)', 'PostController::toggleLike/$1'); 
    $routes->post('post/comment/(:num)', 'PostController::addComment/$1'); 
    $routes->get('post/detail/(:num)', 'PostController::detail/$1'); 

    // ROUTE PROFILE & FOLLOW
    $routes->get('profile/(:segment)', 'ProfileController::index/$1'); 
    $routes->get('profile/edit', 'ProfileController::edit');          
    $routes->post('profile/update', 'ProfileController::updateProfile'); 
    $routes->get('profile/toggleFollow/(:segment)', 'ProfileController::toggleFollow/$1'); 

    // ROUTE EXPLORE & SEARCH
    $routes->get('explore', 'ExploreController::index');
    $routes->get('explore/followers/(:segment)', 'ExploreController::index/followers/$1');
    $routes->get('explore/followings/(:segment)', 'ExploreController::index/followings/$1');
    $routes->post('explore/search', 'ExploreController::search');
});