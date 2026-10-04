<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Enrollment
{
    public function __construct(private int $studentId, private int $courseId)
    {
        if ($this->studentId < 1 || $this->courseId < 1) {
            throw new InvalidArgumentException('A valid student and course are required.');
        }
    }

    public function studentId(): int { return $this->studentId; }
    public function courseId(): int { return $this->courseId; }
}
