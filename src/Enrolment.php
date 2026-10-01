<?php
/**
 * Enrolment class
 * Links a Student to a Course and stores the mark and grade.
 * Responsible for the composition relationship and letter-grade conversion.
 */
class Enrolment
{
    private Student $student;
    private Course $course;
    private ?float $mark = null;
    private ?Grade $grade = null;

    public function __construct(Student $student, Course $course, ?float $mark = null)
    {
        $this->student = $student;
        $this->course = $course;
        if ($mark !== null) {
            $this->setMark($mark);
        }
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function getMark(): ?float
    {
        return $this->mark;
    }

    /**
     * Converts the stored mark to a letter grade (A, B+, B, C+, C, D, F).
     * Returns null if no mark has been recorded yet.
     */
    public function getGrade(): ?string
    {
        return $this->grade !== null ? $this->grade->getLetter() : null;
    }

    public function getGradeObject(): ?Grade
    {
        return $this->grade;
    }

    /**
     * Validates mark (0–100) inside the setter via the Grade class.
     */
    public function setMark(float $mark): void
    {
        $this->grade = new Grade($mark);
        $this->mark = $mark;
    }

    /**
     * Grade point value for GPA (stretch goal).
     */
    public function getGradePoint(): float
    {
        $letter = $this->getGrade();
        return match ($letter) {
            'A'  => 4.0,
            'B+' => 3.5,
            'B'  => 3.0,
            'C+' => 2.5,
            'C'  => 2.0,
            'D'  => 1.0,
            'F'  => 0.0,
            default => 0.0,
        };
    }
}
