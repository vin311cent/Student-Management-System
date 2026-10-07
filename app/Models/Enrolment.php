<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Enrolment: links a Student to a Course and stores the mark.
 * Responsible for the composition link and for converting the mark to a letter grade.
 */
final class Enrolment
{
    private ?float $mark = null;
    private ?Grade $grade = null;

    public function __construct(
        private Student $student,
        private Course $course,
        ?float $mark = null,
        private ?int $id = null
    ) {
        if ($mark !== null) {
            $this->setMark($mark);
        }
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getStudent(): Student { return $this->student; }
    public function getCourse(): Course { return $this->course; }
    public function getMark(): ?float { return $this->mark; }
    public function getGradeObject(): ?Grade { return $this->grade; }

    /** Letter grade (A, B+, B, C+, C, D, F) or null when no mark has been recorded. */
    public function getGrade(): ?string
    {
        return $this->grade?->getLetter();
    }

    public function isGraded(): bool
    {
        return $this->grade !== null;
    }

    /** Validates 0-100 through Grade (throws InvalidArgumentException). */
    public function setMark(float $mark): void
    {
        $this->grade = new Grade($mark);
        $this->mark  = $mark;
    }

    public function getGradePoint(): float
    {
        return $this->grade?->getPoint() ?? 0.0;
    }
}
