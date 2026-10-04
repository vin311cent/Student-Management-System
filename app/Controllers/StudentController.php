<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Domain\Student;
use App\Repositories\StudentRepository;
use InvalidArgumentException;
use PDOException;

final class StudentController extends Controller
{
    public function __construct(\App\Core\View $view, private StudentRepository $students)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $year = filter_var($_POST['year_of_study'] ?? '1', FILTER_VALIDATE_INT);
                if ($year === false) {
                    throw new InvalidArgumentException('Year of study must be a number from 1 to 6.');
                }
                $student = new Student(
                    $this->postString('first_name'),
                    $this->postString('last_name'),
                    $this->postString('programme'),
                    $year
                );
                $this->students->create($student);
                $_SESSION['flash_message'] = 'Student record created successfully.';
                $this->redirect('students');
            } catch (InvalidArgumentException $exception) {
                $error = $exception->getMessage();
            } catch (PDOException $exception) {
                error_log('Student creation failed: ' . $exception->getMessage());
                $error = 'Could not create the student record. Please check the details and try again.';
            }
        }
        $this->render('students', ['students' => $this->students->all(), 'error' => $error]);
    }
}
