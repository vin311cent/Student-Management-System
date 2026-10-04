<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAuthentication(true);
        $this->render('settings', ['username' => $_SESSION['user']['username'] ?? 'Administrator']);
    }
}
