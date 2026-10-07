<?php
declare(strict_types=1);

namespace App\Core;

/** Administrator authentication (credentials live in config, password is hashed). */
final class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        $validUser = hash_equals((string) Config::get('auth.username'), $username);
        $validPass = password_verify($password, (string) Config::get('auth.password_hash'));

        if ($validUser && $validPass) {
            Session::regenerate();
            Session::set('user', ['username' => $username, 'role' => 'administrator']);
            return true;
        }
        return false;
    }

    public static function check(): bool
    {
        return (Session::get('user')['role'] ?? '') === 'administrator';
    }

    public static function username(): string
    {
        return (string) (Session::get('user')['username'] ?? 'Administrator');
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}
