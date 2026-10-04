<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\EnrollmentRepository;
use App\Repositories\StudentRepository;
use DateTimeImmutable;
use DateTimeZone;

final class DashboardController extends Controller
{
    public function __construct(\App\Core\View $view, private EnrollmentRepository $enrollments, private StudentRepository $students)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $this->render('dashboard', [
            'counts' => $this->enrollments->counts(),
            'students' => $this->students->recent(),
            'username' => $_SESSION['user']['username'] ?? 'Administrator',
            'dateLabel' => (new DateTimeImmutable('now', new DateTimeZone('Africa/Lusaka')))->format('l, F j, Y'),
        ]);
    }
}
