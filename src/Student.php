<?php
/**
 * Student class
 * Represents a registered student and their course enrolments.
 * Responsible for identity, enrolment, mark recording, transcripts and
 * auto-generated student numbers via a static counter.
 */
class Student
{
    private string $studentNumber;
    private string $fullName;
    private string $programme;
    private int $yearOfStudy;

    /** @var Enrolment[] Composition: Student holds Enrolment objects */
    private array $enrolments = [];

    /** Static counter for auto-generated student numbers (e.g. LGU-2026-001) */
    private static int $counter = 0;

    /**
     * Initialise a Student with name, programme and year of study.
     * Student number is assigned automatically by the static generator.
     */
    public function __construct(string $fullName, string $programme, int $yearOfStudy = 1)
    {
        $this->studentNumber = self::generateStudentNumber();
        $this->setFullName($fullName);
        $this->setProgramme($programme);
        $this->setYearOfStudy($yearOfStudy);
    }

    /**
     * Static method: generates the next student number (e.g. LGU-2026-001).
     */
    public static function generateStudentNumber(): string
    {
        self::$counter++;
        $year = date('Y');
        return sprintf('LGU-%s-%03d', $year, self::$counter);
    }

    /**
     * Reset the static counter (useful for demos and tests).
     */
    public static function resetCounter(int $value = 0): void
    {
        self::$counter = $value;
    }

    public static function getCounter(): int
    {
        return self::$counter;
    }

    /**
     * Enrol this student in a course (composition).
     */
    public function enrol(Course $course): void
    {
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getCourse()->getCourseCode() === $course->getCourseCode()) {
                throw new InvalidArgumentException(
                    "Student {$this->studentNumber} is already enrolled in {$course->getCourseCode()}."
                );
            }
        }
        $this->enrolments[] = new Enrolment($this, $course);
    }

    /**
     * Record a mark for a specific course code.
     * Throws InvalidArgumentException when the mark is outside 0–100.
     */
    public function recordMark(string $courseCode, float $mark): void
    {
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException("Mark for {$courseCode} must be between 0 and 100.");
        }

        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getCourse()->getCourseCode() === $courseCode) {
                $enrolment->setMark($mark);
                return;
            }
        }

        throw new Exception("Student {$this->studentNumber} is not enrolled in course {$courseCode}.");
    }

    /**
     * Return transcript data as an array of rows.
     */
    public function getTranscript(): array
    {
        $transcript = [];
        foreach ($this->enrolments as $enrolment) {
            $transcript[] = [
                'courseCode'  => $enrolment->getCourse()->getCourseCode(),
                'courseName'  => $enrolment->getCourse()->getCourseName(),
                'creditHours' => $enrolment->getCourse()->getCreditHours(),
                'mark'        => $enrolment->getMark(),
                'grade'       => $enrolment->getGrade(),
            ];
        }
        return $transcript;
    }

    /**
     * Stretch goal: weighted GPA using credit hours.
     */
    public function calculateGpa(): ?float
    {
        $totalPoints = 0.0;
        $totalCredits = 0;

        $gradePoints = [
            'A'  => 4.0,
            'B+' => 3.5,
            'B'  => 3.0,
            'C+' => 2.5,
            'C'  => 2.0,
            'D'  => 1.0,
            'F'  => 0.0,
        ];

        foreach ($this->enrolments as $enrolment) {
            $letter = $enrolment->getGrade();
            if ($letter === null) {
                continue;
            }
            $credits = $enrolment->getCourse()->getCreditHours();
            $totalPoints += ($gradePoints[$letter] ?? 0) * $credits;
            $totalCredits += $credits;
        }

        if ($totalCredits === 0) {
            return null;
        }

        return round($totalPoints / $totalCredits, 2);
    }

    public function getStudentNumber(): string
    {
        return $this->studentNumber;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getProgramme(): string
    {
        return $this->programme;
    }

    public function getYearOfStudy(): int
    {
        return $this->yearOfStudy;
    }

    /** @return Enrolment[] */
    public function getEnrolments(): array
    {
        return $this->enrolments;
    }

    public function setFullName(string $fullName): void
    {
        $fullName = trim($fullName);
        if ($fullName === '') {
            throw new InvalidArgumentException('Full name cannot be empty.');
        }
        $this->fullName = $fullName;
    }

    public function setProgramme(string $programme): void
    {
        $programme = trim($programme);
        if ($programme === '') {
            throw new InvalidArgumentException('Programme cannot be empty.');
        }
        $this->programme = $programme;
    }

    public function setYearOfStudy(int $yearOfStudy): void
    {
        if ($yearOfStudy < 1 || $yearOfStudy > 6) {
            throw new InvalidArgumentException('Year of study must be between 1 and 6.');
        }
        $this->yearOfStudy = $yearOfStudy;
    }
}
