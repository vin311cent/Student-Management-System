<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\EnrollmentRepository;
use App\Services\GradeService;
use InvalidArgumentException;
use PDOException;

final class GradeController extends Controller
{
    public function __construct(\App\Core\View $view, private EnrollmentRepository $enrollments, private GradeService $grades)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = filter_var($_POST['enrollment_id'] ?? '', FILTER_VALIDATE_INT);
                if ($id === false || $id < 1) {
                    throw new InvalidArgumentException('Invalid enrolment ID.');
                }
                $mark = $this->postString('marks');
                $grade = $this->grades->fromMark($mark);
                if (!$this->enrollments->saveMark($id, (float) $mark, $grade)) {
                    throw new InvalidArgumentException('No enrolment was updated. Refresh the page and try again.');
                }
                $_SESSION['flash_message'] = "Marks saved successfully. Grade assigned: {$grade}.";
                $this->redirect('grades');
            } catch (InvalidArgumentException $exception) {
                $error = $exception->getMessage();
            } catch (PDOException $exception) {
                error_log('Grade update failed: ' . $exception->getMessage());
                $error = 'Could not save marks. Please try again.';
            }
        }
        $this->render('grades', ['enrollments' => $this->enrollments->all(), 'error' => $error]);
    }
}
