<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Student;
use App\Repositories\EnrolmentRepository;
use App\Repositories\StudentRepository;
use InvalidArgumentException;
use RuntimeException;

/** Records and updates marks. */
final class GradeController extends Controller
{
    private StudentRepository $students;
    private EnrolmentRepository $enrolments;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $db = Database::connection();
        $this->students   = new StudentRepository($db);
        $this->enrolments = new EnrolmentRepository($db);
    }

    public function index(): void
    {
        $withEnrolments = array_filter(
            $this->students->all(),
            static fn (Student $s): bool => $s->getEnrolments() !== []
        );
        $this->view('grades/index', [
            'title'    => 'Grades',
            'active'   => 'grades',
            'students' => $withEnrolments,
        ]);
    }

    public function update(): void
    {
        try {
            $enrolmentId = $this->request->int('enrolment_id');
            $raw         = $this->request->string('marks');

            if ($enrolmentId < 1) {
                throw new InvalidArgumentException('Invalid enrolment selected.');
            }
            if (!is_numeric($raw)) {
                throw new InvalidArgumentException('Marks must be a valid number.');
            }

            $student   = $this->students->findByEnrolmentId($enrolmentId)
                ?? throw new InvalidArgumentException('Enrolment not found.');
            $enrolment = $student->findEnrolmentById($enrolmentId)
                ?? throw new InvalidArgumentException('Enrolment not found.');

            $student->recordMark($enrolment->getCourse()->getCourseCode(), (float) $raw); // validates 0-100
            $this->enrolments->saveMark($enrolment);

            $this->flash('success', sprintf(
                'Saved %s for %s: %s (%.1f%%).',
                $enrolment->getCourse()->getCourseCode(),
                $student->getFullName(),
                $enrolment->getGrade(),
                $enrolment->getMark()
            ));
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->flash('error', 'Validation error: ' . $e->getMessage());
        }
        $this->redirect('/grades');
    }
}
