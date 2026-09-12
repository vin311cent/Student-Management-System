<?php
/**
 * Grade class
 * Converts a numeric mark (0–100) into a letter grade.
 * Responsible for grade scale logic used by Enrolment.
 */
class Grade
{
    private float $mark;
    private string $letter;

    public function __construct(float $mark)
    {
        $this->setMark($mark);
        $this->letter = self::convert($this->mark);
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
     * Common letter-grade scale used by the college.
     */
    public static function convert(float $mark): string
    {
        if ($mark >= 90) {
         return 'A+';
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

    public function __toString(): string
    {
        return $this->letter;
    }
}
