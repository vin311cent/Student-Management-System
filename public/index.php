<?php
declare(strict_types=1);

/**
 * Front controller: every request enters here.
 * Run with:  php -S localhost:8000 -t public
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));
Session::start();

try {
    $router = new Router();
    (require __DIR__ . '/../config/routes.php')($router);
    $router->dispatch(new Request());
} catch (Throwable $e) {
    error_log($e::class . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    View::render('errors/500', [
        'title'   => 'Something went wrong',
        'active'  => '',
        'message' => Config::get('app.debug') ? $e->getMessage() : 'An unexpected error occurred. Please try again.',
    ], 'plain');
}
