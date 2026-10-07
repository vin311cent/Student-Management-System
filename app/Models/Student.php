<?php
declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use RuntimeException;

/**
 * Student: a registered student and their course enrolments.
 * Responsible for identity, enrolling in courses, recording marks, transcripts,
 * GPA, and auto-generated student numbers through a static counter.
 */
final class Student
{
    private ?int $id;
    private string $studentNumber;
    private string $firstName;
    private string $lastName;
    private string $programme;
    private int $yearOfStudy;

    /** @var Enrolment[] Composition: a Student holds its Enrolment objects. */
    private array $enrolments = [];

    /** Static counter behind auto-generated student numbers (LGU-2026-001). */
    private static int $counter = 0;

    /**
     * New students get a generated number; students loaded from the database pass theirs in.
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $programme,
        int $yearOfStudy = 1,
        ?string $studentNumber = null,
        ?int $id = null
    ) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setProgramme($programme);
        $this->setYearOfStudy($yearOfStudy);
        $this->studentNumber = $studentNumber ?? self::generateStudentNumber();
        $this->id = $id;
    }

    // ---- static members -------------------------------------------------

    /** Next student number, e.g. LGU-2026-001. */
    public static function generateStudentNumber(): string
    {
        self::$counter++;
        return sprintf('LGU-%s-%03d', date('Y'), self::$counter);
    }

    /** Continue numbering after the highest number already stored. */
    public static function seedCounter(int $lastSequence): void
    {
        self::$counter = max(0, $lastSequence);
    }

    public static function getCounter(): int
    {
        return self::$counter;
    }

    // ---- behaviour ------------------------------------------------------

    /** Enrol in a course. A student cannot enrol in the same course twice. */
    public function enrol(Course $course): Enrolment
    {
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getCourse()->getCourseCode() === $course->getCourseCode()) {
                throw new InvalidArgumentException(
                    "{$this->getFullName()} is already enrolled in {$course->getCourseCode()}."
                );
            }
        }
        return $this->enrolments[] = new Enrolment($this, $course);
    }

    /** Record a mark (0-100) for a course the student is enrolled in. */
    public function recordMark(string $courseCode, float $mark): void
    {
        $courseCode = strtoupper(trim($courseCode));
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getCourse()->getCourseCode() === $courseCode) {
                $enrolment->setMark($mark); // throws InvalidArgumentException when out of range
                return;
            }
        }
        throw new RuntimeException("{$this->getFullName()} is not enrolled in {$courseCode}.");
    }

    /** @return array<int, array{courseCode:string,courseName:string,creditHours:int,mark:?float,grade:?string}> */
    public function getTranscript(): array
    {
        return array_map(static fn (Enrolment $e): array => [
            'courseCode'  => $e->getCourse()->getCourseCode(),
            'courseName'  => $e->getCourse()->getCourseName(),
            'creditHours' => $e->getCourse()->getCreditHours(),
            'mark'        => $e->getMark(),
            'grade'       => $e->getGrade(),
        ], $this->enrolments);
    }

    /** Weighted GPA = sum(grade point x credit hours) / sum(credit hours). Null if nothing graded. */
    public function calculateGpa(): ?float
    {
        $points  = 0.0;
        $credits = 0;
        foreach ($this->enrolments as $enrolment) {
            if (!$enrolment->isGraded()) {
                continue;
            }
            $hours    = $enrolment->getCourse()->getCreditHours();
            $points  += $enrolment->getGradePoint() * $hours;
            $credits += $hours;
        }
        return $credits > 0 ? round($points / $credits, 2) : null;
    }

    public function getGradedCount(): int
    {
        return count(array_filter($this->enrolments, static fn (Enrolment $e): bool => $e->isGraded()));
    }

    public function getGradedCredits(): int
    {
        $credits = 0;
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->isGraded()) {
                $credits += $enrolment->getCourse()->getCreditHours();
            }
        }
        return $credits;
    }

    public function findEnrolmentById(int $enrolmentId): ?Enrolment
    {
        foreach ($this->enrolments as $enrolment) {
            if ($enrolment->getId() === $enrolmentId) {
                return $enrolment;
            }
        }
        return null;
    }

    // ---- getters --------------------------------------------------------

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getStudentNumber(): string { return $this->studentNumber; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }
    public function getProgramme(): string { return $this->programme; }
    public function getYearOfStudy(): int { return $this->yearOfStudy; }

    /** @return Enrolment[] */
    public function getEnrolments(): array { return $this->enrolments; }

    // ---- setters with validation ---------------------------------------

    public function setFirstName(string $firstName): void
    {
        $this->firstName = self::requireText($firstName, 'First name', 50);
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = self::requireText($lastName, 'Last name', 50);
    }

    public function setProgramme(string $programme): void
    {
        $this->programme = self::requireText($programme, 'Programme', 100);
    }

    public function setYearOfStudy(int $yearOfStudy): void
    {
        if ($yearOfStudy < 1 || $yearOfStudy > 6) {
            throw new InvalidArgumentException('Year of study must be between 1 and 6.');
        }
        $this->yearOfStudy = $yearOfStudy;
    }

    private static function requireText(string $value, string $label, int $max): string
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException("{$label} cannot be empty.");
        }
        if (mb_strlen($value) > $max) {
            throw new InvalidArgumentException("{$label} must be {$max} characters or fewer.");
        }
        return $value;
    }
}
