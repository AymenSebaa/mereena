<?php
// die('IATF 2025');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
    define('LARAVEL_START', microtime(true));

    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__ . '../../iatf/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    require __DIR__ . '../../iatf/vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__ . '../../iatf/bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    die($e);
}
