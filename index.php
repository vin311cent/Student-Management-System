<?php
declare(strict_types=1);

$application = require __DIR__ . '/app/bootstrap.php';
$route = $_GET['route'] ?? 'index';
$application->run(is_string($route) ? $route : 'not-found');
