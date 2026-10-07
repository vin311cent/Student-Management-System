<?php
declare(strict_types=1);

namespace App\Core;

/** Base controller: shared helpers for rendering, redirecting, flashing and CSRF checks. */
abstract class Controller
{
    public function __construct(protected Request $request)
    {
        if ($request->method() === 'POST') {
            $this->verifyCsrf();
        }
    }

    /** @param array<string,mixed> $data */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        View::render($view, $data + ['flash' => Session::pullFlash()], $layout);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    private function verifyCsrf(): void
    {
        if (!Session::validCsrf($_POST['_token'] ?? null)) {
            http_response_code(419);
            exit('Your session expired. Go back, refresh the page and try again.');
        }
    }
}
