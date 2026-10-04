<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array{object, string}> */
    private array $routes = [];

    public function add(string $route, object $controller, string $action): void
    {
        $this->routes[$route] = [$controller, $action];
    }

    public function dispatch(string $route): void
    {
        if (!isset($this->routes[$route])) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        [$controller, $action] = $this->routes[$route];
        $controller->{$action}();
    }
}
