<?php
declare(strict_types=1);

namespace App\Services;

final class AuthService
{
    public function authenticate(string $username, string $password): bool
    {
        $expectedUsername = getenv('ADMIN_USERNAME') ?: 'admin';
        $expectedPassword = getenv('ADMIN_PASSWORD') ?: 'admin123';

        if (!hash_equals($expectedUsername, $username) || !hash_equals($expectedPassword, $password)) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = ['username' => $expectedUsername, 'role' => 'administrator'];
        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parameters = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
        }
        session_destroy();
    }
}
