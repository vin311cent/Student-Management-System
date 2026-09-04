<?php

class Grade
{
    /**
     * Convert marks into a letter grade.
     *
     * @param float $marks
     * @return string
     * @throws InvalidArgumentException
     */
    public static function convert($marks)
    {
        if (!is_numeric($marks)) {
            throw new InvalidArgumentException(
                "Marks must be a number."
            );
        }

        $marks = (float)$marks;

        if ($marks < 0 || $marks > 100) {
            throw new InvalidArgumentException(
                "Marks must be between 0 and 100."
            );
        }

        if ($marks >= 80) {
            return 'A';
        } elseif ($marks >= 70) {
            return 'B';
        } elseif ($marks >= 60) {
            return 'C';
        } elseif ($marks >= 50) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Convert a letter grade to a grade point.
     *
     * @param string $grade
     * @return float
     * @throws InvalidArgumentException
     */
    public static function gradePoint($grade)
    {
        $grade = strtoupper(trim($grade));

        $points = [
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            'F' => 0.0
        ];

        if (!array_key_exists($grade, $points)) {
            throw new InvalidArgumentException(
                "Invalid grade: " . $grade
            );
        }

        return $points[$grade];
    }
}