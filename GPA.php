<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

class GPA
{
    public static function calculate(mixed $courses): float
    {
        if (!is_array($courses)) {
            throw new InvalidArgumentException('Courses must be supplied as an array.');
        }
        $gpa = (new \App\Services\GradeService())->weightedGpa($courses);
        if ($gpa === null) {
            throw new InvalidArgumentException('At least one graded course with valid credit hours is required.');
        }
        return $gpa;
    }
}
