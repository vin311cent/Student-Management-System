<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\classes\Student;
use App\Repositories\ProgrammeRepository;
use App\Repositories\StudentRepository;
use InvalidArgumentException;

/** Lists students and registers new ones. */
final class StudentController extends Controller
{
    private StudentRepository $students;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->students = new StudentRepository(Database::connection());
    }

    public function index(): void
    {
        $query    = $this->request->string('q');
        $students = $this->students->all(newestFirst: true);

        if ($query !== '') {
            $students = array_values(array_filter($students, static function (Student $s) use ($query): bool {
                $haystack = $s->getFullName() . ' ' . $s->getStudentNumber() . ' ' . $s->getProgramme();
                return mb_stripos($haystack, $query) !== false;
            }));
        }

        $this->view('students/index', [
            'title'    => 'Students',
            'active'   => 'students',
            'students' => $students,
            'query'    => $query,
        ]);
    }

    public function create(): void
    {
        $this->view('students/create', [
            'title'      => 'Add Student',
            'active'     => 'students',
            'programmes' => (new ProgrammeRepository(Database::connection()))->all(),
        ]);
    }

    public function store(): void
    {
        try {
            $student = $this->students->register(
                $this->request->string('first_name'),
                $this->request->string('last_name'),
                $this->request->string('programme'),
                $this->request->int('year_of_study', 1)
            );
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/students/create');
        }

        $this->flash('success', "Student registered: {$student->getFullName()} ({$student->getStudentNumber()}).");
        $this->redirect('/students');
    }

    public function edit(string $id): void
    {
        $student = $this->students->find((int) $id);
        if ($student === null) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/students');
        }

        $this->view('students/edit', [
            'title'      => 'Edit Student',
            'active'     => 'students',
            'student'    => $student,
            'programmes' => (new ProgrammeRepository(Database::connection()))->all(),
        ]);
    }

    public function update(string $id): void
    {
        $student = $this->students->find((int) $id);
        if ($student === null) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/students');
        }

        try {
            // Setters validate; nothing is saved unless all four succeed.
            $student->setFirstName($this->request->string('first_name'));
            $student->setLastName($this->request->string('last_name'));
            $student->setProgramme($this->request->string('programme'));
            $student->setYearOfStudy($this->request->int('year_of_study', 1));
            $this->students->update($student);
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/students/' . (int) $id . '/edit');
        }

        $this->flash('success', "Updated {$student->getFullName()} ({$student->getStudentNumber()}).");
        $this->redirect('/students');
    }

    public function destroy(string $id): void
    {
        try {
            $this->students->delete((int) $id);
            $this->flash('success', 'Student deleted together with their enrolments and marks.');
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/students');
    }
}
