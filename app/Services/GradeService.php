<?php
declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class GradeService
{
    public function fromMark(mixed $mark): string
    {
        if (!is_numeric($mark)) {
            throw new InvalidArgumentException('Marks must be a number.');
        }
        $mark = (float) $mark;
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Marks must be between 0 and 100.');
        }

        return match (true) {
            $mark >= 80 => 'A',
            $mark >= 70 => 'B',
            $mark >= 60 => 'C',
            $mark >= 50 => 'D',
            default => 'F',
        };
    }

    public function points(string $grade): float
    {
        $points = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0];
        $grade = strtoupper(trim($grade));
        if (!array_key_exists($grade, $points)) {
            throw new InvalidArgumentException('Invalid grade: ' . $grade);
        }
        return $points[$grade];
    }

    public function weightedGpa(array $courses): ?float
    {
        if ($courses === []) {
            throw new InvalidArgumentException('At least one course is required to calculate GPA.');
        }

        $qualityPoints = 0.0;
        $creditHours = 0;
        foreach ($courses as $course) {
            if (!is_array($course) || !isset($course['grade'], $course['credit_hours'])) {
                throw new InvalidArgumentException('Each course must have a grade and credit hours.');
            }
            if (!is_numeric($course['credit_hours']) || (float) $course['credit_hours'] <= 0) {
                throw new InvalidArgumentException('Credit hours must be greater than zero.');
            }
            $hours = (float) $course['credit_hours'];
            $qualityPoints += $this->points((string) $course['grade']) * $hours;
            $creditHours += $hours;
        }
        return $creditHours > 0 ? round($qualityPoints / $creditHours, 2) : null;
    }
}
