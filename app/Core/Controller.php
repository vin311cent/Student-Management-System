<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected View $view)
    {
    }

    protected function render(string $template, array $data = []): void
    {
        $flash = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);
        $this->view->render($template, ['flash' => $flash] + $data);
    }

    protected function redirect(string $page): never
    {
        header('Location: index.php?route=' . rawurlencode($page));
        exit;
    }

    protected function requireAuthentication(bool $administratorOnly = false): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!is_array($user) || ($administratorOnly && ($user['role'] ?? '') !== 'administrator')) {
            $this->redirect('login');
        }
    }

    protected function postString(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}
