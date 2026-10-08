<?php
declare(strict_types=1);

namespace App\classes;

use InvalidArgumentException;
use Stringable;

/**
 * Grade: a letter grade derived from a numeric mark (0-100).
 * Responsible for the college grading scale and grade points.
 */
final class Grade implements Stringable
{
    /** Letter => grade point. */
    private const POINTS = ['A' => 4.0, 'B+' => 3.5, 'B' => 3.0, 'C+' => 2.5, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0];

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

    public function getPoint(): float
    {
        return self::POINTS[$this->letter];
    }

    /** Validation lives in the setter: marks outside 0-100 are rejected. */
    public function setMark(float $mark): void
    {
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Mark must be between 0 and 100.');
        }
        $this->mark   = $mark;
        $this->letter = self::convert($mark);
    }

    /** Scale: A, B+, B, C+, C, D, F. */
    public static function convert(float $mark): string
    {
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Mark must be between 0 and 100.');
        }
        return match (true) {
            $mark >= 80 => 'A',
            $mark >= 75 => 'B+',
            $mark >= 70 => 'B',
            $mark >= 65 => 'C+',
            $mark >= 60 => 'C',
            $mark >= 50 => 'D',
            default     => 'F',
        };
    }

    public function __toString(): string
    {
        return $this->letter;
    }
}
