<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

final class LoginController extends Controller
{
    public function __construct(\App\Core\View $view, private AuthService $auth)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        if (isset($_GET['logout'])) {
            $this->auth->logout();
        }
        if (isset($_SESSION['user'])) {
            $this->redirect('dashboard');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->postString('username');
            $password = $this->postString('password');
            if ($username === '' || $password === '') {
                $errors[] = 'Please enter both your username and your password.';
            } elseif (!$this->auth->authenticate($username, $password)) {
                $errors[] = 'Invalid username or password.';
            } else {
                $_SESSION['flash_message'] = 'Welcome back, ' . $username . '!';
                $this->redirect('dashboard');
            }
        }

        $this->render('login', ['errors' => $errors]);
    }

    public function home(): void
    {
        header('Location: index.php?route=login');
        exit;
    }
}
