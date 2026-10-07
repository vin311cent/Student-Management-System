<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Repositories\CourseRepository;
use App\Repositories\EnrolmentRepository;
use App\Repositories\StudentRepository;

/** Overview page: totals and the most recently registered students. */
final class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::connection();
        $students = new StudentRepository($db);

        $this->view('dashboard/index', [
            'title'         => 'Dashboard',
            'active'        => 'dashboard',
            'username'      => Auth::username(),
            'dateLabel'     => date('l, F j, Y'),
            'totalStudents' => $students->count(),
            'totalCourses'  => (new CourseRepository($db))->count(),
            'totalEnrol'    => (new EnrolmentRepository($db))->count(),
            'recent'        => $students->recent(5),
        ]);
    }
}
