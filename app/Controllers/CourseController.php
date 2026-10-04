<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Domain\Course;
use App\Repositories\CourseRepository;
use InvalidArgumentException;
use PDOException;

final class CourseController extends Controller
{
    public function __construct(\App\Core\View $view, private CourseRepository $courses)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $hours = filter_var($_POST['credit_hours'] ?? '', FILTER_VALIDATE_INT);
                if ($hours === false) {
                    throw new InvalidArgumentException('Credit hours must be a positive whole number.');
                }
                $this->courses->create(new Course(
                    $this->postString('course_code'),
                    $this->postString('course_name'),
                    $hours
                ));
                $_SESSION['flash_message'] = 'Course created successfully.';
                $this->redirect('courses');
            } catch (InvalidArgumentException $exception) {
                $error = $exception->getMessage();
            } catch (PDOException $exception) {
                error_log('Course creation failed: ' . $exception->getMessage());
                $error = 'Could not create the course. The course code may already exist.';
            }
        }
        $this->render('courses', ['courses' => $this->courses->all(), 'error' => $error]);
    }
}
