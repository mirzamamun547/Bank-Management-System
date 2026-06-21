<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Polyfill for OCI_DEFAULT which was removed in PHP 8.1+
if (!defined('OCI_DEFAULT')) {
    define('OCI_DEFAULT', defined('OCI_NO_AUTO_COMMIT') ? OCI_NO_AUTO_COMMIT : 0);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
