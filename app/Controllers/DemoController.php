<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Student;
use InvalidArgumentException;
use RuntimeException;

/**
 * OOP demonstration for the assignment brief: 3 students, 3+ courses each,
 * marks, transcripts, and a caught InvalidArgumentException. Uses objects only (no database).
 */
final class DemoController extends Controller
{
    public function index(): void
    {
        Student::seedCounter(0);
        $log = [];

        $courses = [
            new Course('CSC101', 'Programming Fundamentals', 3),
            new Course('CSC205', 'Database Systems', 3),
            new Course('CSC210', 'Web Programming 2', 4),
            new Course('MAT120', 'Discrete Mathematics', 3),
        ];

        $students = [
            new Student('Lewis', 'Chingwamari', 'Computer Science', 2),
            new Student('Kieth', 'Tim', 'Computer Science', 3),
            new Student('Lumpombwe', 'Mwansa', 'Bachelor of Information Technology', 1),
        ];

        // Each student is enrolled in at least 3 courses.
        foreach ($students as $i => $student) {
            foreach (array_slice($courses, $i === 2 ? 1 : 0, 3) as $course) {
                $student->enrol($course);
            }
        }

        $marks = [
            [['CSC101', 85], ['CSC205', 72], ['CSC210', 91]],
            [['CSC101', 78], ['CSC205', 64], ['CSC210', 55]],
            [['CSC205', 49], ['CSC210', 67], ['MAT120', 120]], // 120 is invalid on purpose
        ];

        foreach ($students as $i => $student) {
            foreach ($marks[$i] as [$code, $mark]) {
                try {
                    $student->recordMark($code, $mark);
                    $log[] = ['success', "{$student->getStudentNumber()}: recorded {$mark} for {$code}."];
                } catch (InvalidArgumentException $e) {
                    $log[] = ['error', "{$student->getStudentNumber()}: {$e->getMessage()} (mark {$mark} rejected for {$code})"];
                }
            }
        }

        // Duplicate enrolment is rejected too.
        try {
            $students[0]->enrol($courses[0]);
        } catch (InvalidArgumentException $e) {
            $log[] = ['error', $e->getMessage()];
        }

        // Marks for a course the student is not enrolled in.
        try {
            $students[0]->recordMark('MAT120', 60);
        } catch (RuntimeException $e) {
            $log[] = ['error', $e->getMessage()];
        }

        $this->view('demo/index', [
            'title'    => 'OOP Demonstration',
            'students' => $students,
            'log'      => $log,
            'counter'  => Student::getCounter(),
        ], 'plain');
    }
}
