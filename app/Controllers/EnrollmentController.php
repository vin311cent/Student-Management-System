<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Domain\Enrollment;
use App\Repositories\EnrollmentRepository;
use InvalidArgumentException;
use PDOException;

final class EnrollmentController extends Controller
{
    public function __construct(\App\Core\View $view, private EnrollmentRepository $enrollments)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $studentId = filter_var($_POST['student_id'] ?? '', FILTER_VALIDATE_INT);
                $courseId = filter_var($_POST['course_id'] ?? '', FILTER_VALIDATE_INT);
                if ($studentId === false || $courseId === false) {
                    throw new InvalidArgumentException('Select a valid student and course.');
                }
                $this->enrollments->create(new Enrollment($studentId, $courseId));
                $_SESSION['flash_message'] = 'Student enrolled successfully.';
                $this->redirect('enrolments');
            } catch (InvalidArgumentException $exception) {
                $error = $exception->getMessage();
            } catch (PDOException $exception) {
                error_log('Enrollment creation failed: ' . $exception->getMessage());
                $error = 'Could not create enrolment. It may already exist or refer to an invalid record.';
            }
        }
        $this->render('enrolments', $this->enrollments->options() + [
            'enrollments' => $this->enrollments->all(),
            'error' => $error,
        ]);
    }
}
