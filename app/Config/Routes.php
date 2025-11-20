<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. ROUTE PUBLIK
// --------------------------------------------------------------------
$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('login/process', 'AuthController::login');
$routes->get('register', 'AuthController::register');
$routes->post('register/store', 'AuthController::store');
$routes->get('logout', 'AuthController::logout');

// --------------------------------------------------------------------
// 2. ROUTE TERPROTEKSI 
// --------------------------------------------------------------------

$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('feed', 'FeedController::handleEmptyFeed');

    $routes->get('feed/(:segment)', 'FeedController::index/$1');

    $routes->get('post/create', 'PostController::create');
    $routes->post('post/store', 'PostController::store');
    $routes->get('post/like/(:num)', 'PostController::toggleLike/$1');
    $routes->post('post/comment/(:num)', 'PostController::addComment/$1');
    $routes->get('post/detail/(:num)', 'PostController::detail/$1');
    $routes->get('post/delete/(:num)', 'PostController::delete/$1');
    $routes->get('post/edit/(:num)', 'PostController::edit/$1');
    $routes->post('post/update/(:num)', 'PostController::update/$1');

    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::updateProfile');
    $routes->get('profile/toggleFollow/(:segment)', 'ProfileController::toggleFollow/$1');
    $routes->get('profile/(:segment)', 'ProfileController::index/$1');

    $routes->get('explore', 'ExploreController::index');
    $routes->get('explore/followers/(:segment)', 'ExploreController::index/followers/$1');
    $routes->get('explore/followings/(:segment)', 'ExploreController::index/followings/$1');
    $routes->post('explore/search', 'ExploreController::search');

    $routes->get('notifications', 'NotificationController::index');
    $routes->get('notifications/load', 'NotificationController::loadNotifications');

});