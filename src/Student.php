<?php
/**
 * Student class
 * Represents a student with personal details and a collection of enrolments.
 * Responsible for enrolment, mark recording, transcript generation and
 * auto-generation of student numbers via a static counter.
 */
class Student
{
    private string $studentNumber;
    private string $fullName;
    private string $programme;
    private int $yearOfStudy;

    /** @var Enrolment[] */
    private array $enrolments = [];

    /** Static counter used to auto-generate student numbers (LGU-YYYY-NNN) */
    private static int $counter = 0;

    public function __construct(string $fullName, string $programme, int $yearOfStudy = 1)
    {
        $this->studentNumber = self::generateStudentNumber();
        $this->setFullName($fullName);
        $this->setProgramme($programme);
        $this->setYearOfStudy($yearOfStudy);
    }

    /**
     * Auto-generate next student number, e.g. LGU-2026-001
     */
    public static function generateStudentNumber(): string
    {
        self::$counter++;
        $year = date('Y');
        return sprintf('LGU-%s-%03d', $year, self::$counter);
    }

    /**
     * Reset the static counter (useful for demos / testing).
     */
    public static function resetCounter(int $value = 0): void
    {
        self::$counter = $value;
    }

    /**
     * Enrol this student in a course (composition: Student holds Enrolment objects).
     */
    public function enrol(Course $course): void
    {
        // Prevent duplicate enrolment in the same course
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getCourse()->getCourseCode() === $course->getCourseCode()) {
                throw new InvalidArgumentException(
                    "Student {$this->studentNumber} is already enrolled in {$course->getCourseCode()}."
                );
            }
        }
<<<<<<< HEAD
        $this->enrolments[] = new Enrolment($course);
=======
        $this->enrolments[] = new Enrolment($this, $course);
>>>>>>> 8bb8be0f6af6784b9a17839a449f0ab1a90ef57b
    }

    /**
     * Record a mark for a specific course. Throws InvalidArgumentException
     * when the mark is outside 0–100.
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

        throw new InvalidArgumentException(
            "Student {$this->studentNumber} is not enrolled in course {$courseCode}."
        );
    }

    /**
     * Return transcript data for all enrolled courses.
     *
     * @return array<int, array{courseCode:string,courseName:string,creditHours:int,mark:?float,grade:?string}>
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
     * Weighted GPA calculation (stretch goal).
     * Uses credit hours as weight. Returns null if no graded courses.
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

    // ---------- Getters ----------
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

    // ---------- Setters with validation ----------
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
