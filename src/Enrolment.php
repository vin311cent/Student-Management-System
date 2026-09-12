<?php
/**
<<<<<<< HEAD
 * LUSAKA GOLDSMITHS UNIVERSITY (LGU)
 * BSc. Comp 222 - Web Programming 2
 *
 * Class Enrolment (Lumpobwe - Backend & Domain Logic)
 * Connects a Student to a Course and maintains the evaluation mark.
 * Demonstrates: Encapsulation, Mark Validation (0-100), Letter Grade Conversion, Exception Handling.
 */

class Enrolment {
    private Course $course;
    private ?float $mark;

    public function __construct(Course $course, ?float $mark = null) {
        $this->course = $course;
        if ($mark !== null) {
            $this->setMark($mark);
        } else {
            $this->mark = null;
        }
    }

    public function getCourse(): Course { return $this->course; }

    public function getMark(): ?float { return $this->mark; }

    public function setMark(float $mark): void {
        // Enforces strictly valid percentage marks between 0 and 100
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException("Invalid mark: {$mark}%. Marks must be strictly between 0 and 100.");
        }
        $this->mark = $mark;
    }

    /**
     * Converts numeric mark to letter grade based on standard university grading scale.
     */
    public function getGrade(): string {
        if ($this->mark === null) return "N/A";
        if ($this->mark >= 80) return "A";
        if ($this->mark >= 70) return "B+";
        if ($this->mark >= 60) return "B";
        if ($this->mark >= 55) return "C+";
        if ($this->mark >= 50) return "C";
        if ($this->mark >= 40) return "D";
        return "F";
    }

    /**
     * Converts letter grade to grade point value for GPA calculation.
     */
    
    public function getGradePoint(): float {
        $grade = $this->getGrade();
        switch ($grade) {
            case "A":  return 4.0;
            case "B+": return 3.5;
            case "B":  return 3.0;
            case "C+": return 2.5;
            case "C":  return 2.0;
            case "D":  return 1.0;
            case "F":  return 0.0;
            default:   return 0.0;
        }
    }
}

=======
 * Enrolment class
 * Links a Student to a Course and stores the mark/grade.
 * Responsible for composition relationship and grade calculation.
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
     * Returns the letter grade (or null if no mark recorded yet).
     */
    public function getGrade(): ?string
    {
        return $this->grade !== null ? $this->grade->getLetter() : null;
    }

    public function getGradeObject(): ?Grade
    {
        return $this->grade;
    }

    public function setMark(float $mark): void
    {
        // Validation lives inside Grade
        $this->grade = new Grade($mark);
        $this->mark = $mark;
    }
}
>>>>>>> 8bb8be0f6af6784b9a17839a449f0ab1a90ef57b
