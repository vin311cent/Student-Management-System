<?php
declare(strict_types=1);

namespace App\Services;

use App\classes\Course;
use App\classes\Student;

/** Builds report figures from domain objects (no SQL, no HTML). */
final class ReportService
{
    /**
     * @param Student[] $students
     * @return array<int, array{student:Student,enrolled:int,graded:int,credits:int,gpa:?float}>
     */
    public function studentPerformance(array $students): array
    {
        return array_map(static fn (Student $s): array => [
            'student'  => $s,
            'enrolled' => count($s->getEnrolments()),
            'graded'   => $s->getGradedCount(),
            'credits'  => $s->getGradedCredits(),
            'gpa'      => $s->calculateGpa(),
        ], $students);
    }

    /**
     * @param Course[]  $courses
     * @param Student[] $students
     * @return array<int, array{code:string,name:string,credits:int,enrolled:int,graded:int,pending:int,average:?float}>
     */
    public function courseStats(array $courses, array $students): array
    {
        $stats = [];
        foreach ($courses as $course) {
            $stats[$course->getCourseCode()] = [
                'code' => $course->getCourseCode(), 'name' => $course->getCourseName(),
                'credits' => $course->getCreditHours(), 'enrolled' => 0, 'graded' => 0, 'pending' => 0,
                'marks' => [],
            ];
        }
        foreach ($students as $student) {
            foreach ($student->getEnrolments() as $enrolment) {
                $code = $enrolment->getCourse()->getCourseCode();
                if (!isset($stats[$code])) {
                    continue;
                }
                $stats[$code]['enrolled']++;
                if ($enrolment->isGraded()) {
                    $stats[$code]['graded']++;
                    $stats[$code]['marks'][] = $enrolment->getMark();
                } else {
                    $stats[$code]['pending']++;
                }
            }
        }

        $rows = [];
        foreach ($stats as $row) {
            $row['average'] = $row['marks'] ? round(array_sum($row['marks']) / count($row['marks']), 1) : null;
            unset($row['marks']);
            $rows[] = $row;
        }
        usort($rows, static fn (array $a, array $b): int => [$b['enrolled'], $a['code']] <=> [$a['enrolled'], $b['code']]);
        return $rows;
    }

    /**
     * @param Student[] $students
     * @return array<int, array{programme:string,students:int,enrolments:int}>
     */
    public function programmeStats(array $students): array
    {
        $stats = [];
        foreach ($students as $student) {
            $name = $student->getProgramme();
            $stats[$name] ??= ['programme' => $name, 'students' => 0, 'enrolments' => 0];
            $stats[$name]['students']++;
            $stats[$name]['enrolments'] += count($student->getEnrolments());
        }
        $rows = array_values($stats);
        usort($rows, static fn (array $a, array $b): int => $b['students'] <=> $a['students']);
        return $rows;
    }
}
