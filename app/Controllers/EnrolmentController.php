<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\CourseRepository;
use App\Repositories\EnrolmentRepository;
use App\Repositories\StudentRepository;
use InvalidArgumentException;

/** Enrols students in courses. */
final class EnrolmentController extends Controller
{
    private StudentRepository $students;
    private CourseRepository $courses;
    private EnrolmentRepository $enrolments;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $db = Database::connection();
        $this->students   = new StudentRepository($db);
        $this->courses    = new CourseRepository($db);
        $this->enrolments = new EnrolmentRepository($db);
    }

    public function index(): void
    {
        $this->view('enrolments/index', [
            'title'    => 'Enrolment',
            'active'   => 'enrolments',
            'students' => $this->students->all(),
            'courses'  => $this->courses->all(),
        ]);
    }

    public function store(): void
    {
        try {
            $studentId = $this->request->int('student_id');
            $courseId  = $this->request->int('course_id');
            if ($studentId < 1 || $courseId < 1) {
                throw new InvalidArgumentException('Please select both a student and a course.');
            }

            $student = $this->students->find($studentId) ?? throw new InvalidArgumentException('Student not found.');
            $course  = $this->courses->find($courseId) ?? throw new InvalidArgumentException('Course not found.');

            $enrolment = $student->enrol($course);   // domain rule: no duplicate enrolment
            $this->enrolments->insert($enrolment);

            $this->flash('success', "{$student->getFullName()} enrolled in {$course->getCourseCode()}.");
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/enrolments');
    }

    public function destroy(): void
    {
        try {
            $this->enrolments->delete($this->request->int('enrolment_id'));
            $this->flash('success', 'Enrolment removed.');
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/enrolments');
    }
}
