<?php
declare(strict_types=1);

namespace App;

use App\Controllers\AcademicSummaryController;
use App\Controllers\CourseController;
use App\Controllers\DashboardController;
use App\Controllers\EnrollmentController;
use App\Controllers\GradeController;
use App\Controllers\LoginController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\StudentController;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\StudentRepository;
use App\Services\AuthService;
use App\Services\GradeService;

final class Application
{
    private Router $router;
    private View $view;
    private bool $databaseRoutesRegistered = false;

    public function __construct(string $rootDirectory)
    {
        $this->view = new View($rootDirectory . DIRECTORY_SEPARATOR . 'views');
        $this->router = new Router();
        $loginController = new LoginController($this->view, new AuthService());
        $this->router->add('index', $loginController, 'home');
        $this->router->add('login', $loginController, 'index');
        $this->router->add('settings', new SettingsController($this->view), 'index');
    }

    public function run(string $route): void
    {
        if (!in_array($route, ['index', 'login', 'settings'], true)) {
            if (($_SESSION['user']['role'] ?? '') !== 'administrator') {
                header('Location: index.php?route=login');
                exit;
            }
            $this->registerDatabaseRoutes();
        }
        $this->router->dispatch($route);
    }

    private function registerDatabaseRoutes(): void
    {
        if ($this->databaseRoutesRegistered) {
            return;
        }
        $database = Database::getInstance();
        $students = new StudentRepository($database);
        $courses = new CourseRepository($database);
        $enrollments = new EnrollmentRepository($database);
        $this->router->add('dashboard', new DashboardController($this->view, $enrollments, $students), 'index');
        $this->router->add('students', new StudentController($this->view, $students), 'index');
        $this->router->add('courses', new CourseController($this->view, $courses), 'index');
        $this->router->add('enrolments', new EnrollmentController($this->view, $enrollments), 'index');
        $this->router->add('grades', new GradeController($this->view, $enrollments, new GradeService()), 'index');
        $this->router->add('academic-summary', new AcademicSummaryController($this->view, $enrollments), 'index');
        $this->router->add('reports', new ReportController($this->view, $enrollments), 'index');
        $this->databaseRoutesRegistered = true;
    }
}
