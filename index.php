<?php
/**
 * Karenderia Multiple Restaurant System
 * Main entry point for the application
 */

// Start session
session_start();

// Include configuration
require_once 'config/config.php';

// Include database connection
require_once 'config/database.php';

// Include common functions
require_once 'includes/functions.php';

// Include routing
require_once 'includes/router.php';

// Initialize the application
$router = new Router();

// Define routes
$router->add('/', 'HomeController@index');
$router->add('/restaurant/{id}', 'RestaurantController@show');
$router->add('/menu/{restaurant_id}', 'MenuController@index');
$router->add('/cart', 'CartController@index');
$router->add('/checkout', 'CheckoutController@index');
$router->add('/login', 'AuthController@login');
$router->add('/register', 'AuthController@register');
$router->add('/admin', 'AdminController@index');
$router->add('/merchant', 'MerchantController@index');

// Get current URL
$url = $_SERVER['REQUEST_URI'];
$url = parse_url($url, PHP_URL_PATH);

// Dispatch the route
$router->dispatch($url);
?>