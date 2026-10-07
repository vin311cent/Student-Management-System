<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Course;
use App\Repositories\CourseRepository;
use App\Repositories\ProgrammeRepository;
use InvalidArgumentException;

/** Lists courses and adds new ones. */
final class CourseController extends Controller
{
    private CourseRepository $courses;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->courses = new CourseRepository(Database::connection());
    }

    public function index(): void
    {
        $this->view('courses/index', [
            'title'      => 'Courses',
            'active'     => 'courses',
            'courses'    => $this->courses->all(),
            'programmes' => (new ProgrammeRepository(Database::connection()))->all(),
        ]);
    }

    public function store(): void
    {
        try {
            $course = new Course(
                $this->request->string('course_code'),
                $this->request->string('course_name'),
                $this->request->int('credit_hours', 3)
            );
            $this->courses->save($course, $this->request->int('program_id'));
            $this->flash('success', "Course {$course->getCourseCode()} added successfully.");
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/courses');
    }
}
