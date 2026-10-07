<?php
declare(strict_types=1);

namespace App\Core;

/** Maps "METHOD /path" to Controller@action and enforces login on protected routes. */
final class Router
{
    /** @var array<int, array{method:string,regex:string,handler:string,auth:bool}> */
    private array $routes = [];

    public function get(string $pattern, string $handler, bool $auth = true): void
    {
        $this->add('GET', $pattern, $handler, $auth);
    }

    public function post(string $pattern, string $handler, bool $auth = true): void
    {
        $this->add('POST', $pattern, $handler, $auth);
    }

    private function add(string $method, string $pattern, string $handler, bool $auth): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = compact('method', 'regex', 'handler', 'auth');
    }

    public function dispatch(Request $request): void
    {
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method() || !preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            if ($route['auth'] && !Auth::check()) {
                header('Location: ' . url('/login'));
                exit;
            }

            [$class, $action] = explode('@', $route['handler']);
            $class  = 'App\\Controllers\\' . $class;
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            (new $class($request))->$action(...array_values($params));
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Not found', 'active' => ''], Auth::check() ? 'main' : 'plain');
    }
}
