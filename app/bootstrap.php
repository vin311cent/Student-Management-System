<?php
declare(strict_types=1);

use App\Application;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

return new Application(dirname(__DIR__));
