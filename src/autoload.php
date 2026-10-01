<?php
/**
 * Simple autoloader for project classes.
 * One class per file in the src/ directory.
 */
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
