<?php
/**
 * Grade class
 * Represents a letter grade derived from a numeric mark (0–100).
 * Responsible for the college grading scale used by Enrolment.
 */
class Grade
{
    private float $mark;
    private string $letter;

    public function __construct(float $mark)
    {
        $this->setMark($mark);
    }

    public function getMark(): float
    {
        return $this->mark;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function setMark(float $mark): void
    {
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Mark must be between 0 and 100.');
        }
        $this->mark = $mark;
        $this->letter = self::convert($mark);
    }

    /**
     * Letter scale required by the assignment: A, B+, B, C+, C, D, F
     */
    public static function convert($mark): string
    {
        $mark = (float)$mark;
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Mark must be between 0 and 100.');
        }
        if ($mark >= 80) {
            return 'A';
        }
        if ($mark >= 75) {
            return 'B+';
        }
        if ($mark >= 70) {
            return 'B';
        }
        if ($mark >= 65) {
            return 'C+';
        }
        if ($mark >= 60) {
            return 'C';
        }
        if ($mark >= 50) {
            return 'D';
        }
        return 'F';
    }

    /**
     * Grade point for a letter grade (used by GPA / reports).
     */
    public static function gradePoint($grade): float
    {
        $grade = strtoupper(trim((string)$grade));
        $points = [
            'A'  => 4.0,
            'B+' => 3.5,
            'B'  => 3.0,
            'C+' => 2.5,
            'C'  => 2.0,
            'D'  => 1.0,
            'F'  => 0.0,
        ];
        if (!array_key_exists($grade, $points)) {
            throw new InvalidArgumentException('Invalid grade: ' . $grade);
        }
        return $points[$grade];
    }

    public function __toString(): string
    {
        return $this->letter;
    }
}
