<?php
/**
 * Simple PSR-4 style autoloader for the project classes.
 * Place all domain classes in the src/ folder (one class per file).
 */
spl_autoload_register(function (string $class): void {
    $baseDir = __DIR__ . '/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
