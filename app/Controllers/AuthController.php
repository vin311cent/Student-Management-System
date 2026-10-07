<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

/** Handles login and logout. */
final class AuthController extends Controller
{
    public function home(): void
    {
        $this->redirect(Auth::check() ? '/dashboard' : '/login');
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login', ['title' => 'Login'], 'plain');
    }

    public function login(): void
    {
        $username = $this->request->string('username');
        $password = $this->request->string('password');

        if ($username === '' || $password === '') {
            $this->flash('error', 'Please enter both your username and password.');
            $this->redirect('/login');
        }
        if (!Auth::attempt($username, $password)) {
            $this->flash('error', 'Invalid username or password.');
            $this->redirect('/login');
        }

        $this->flash('success', 'Welcome back, ' . $username . '!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
