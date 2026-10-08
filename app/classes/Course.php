<?php
declare(strict_types=1);

namespace App\classes;

use InvalidArgumentException;

/**
 * Course: a university course with a code, name and credit hours.
 * Responsible for course identity and credit weighting.
 */
final class Course
{
    private ?int $id = null;
    private string $courseCode;
    private string $courseName;
    private int $creditHours;

    /** @var string[] names of programmes this course belongs to (display only) */
    private array $programmes = [];

    public function __construct(string $courseCode, string $courseName, int $creditHours, ?int $id = null)
    {
        $this->setCourseCode($courseCode);
        $this->setCourseName($courseName);
        $this->setCreditHours($creditHours);
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getCourseCode(): string { return $this->courseCode; }
    public function getCourseName(): string { return $this->courseName; }
    public function getCreditHours(): int { return $this->creditHours; }

    /** @return string[] */
    public function getProgrammes(): array { return $this->programmes; }

    /** @param string[] $programmes */
    public function setProgrammes(array $programmes): void { $this->programmes = $programmes; }

    public function setCourseCode(string $courseCode): void
    {
        $courseCode = strtoupper(trim($courseCode));
        if ($courseCode === '') {
            throw new InvalidArgumentException('Course code cannot be empty.');
        }
        $this->courseCode = $courseCode;
    }

    public function setCourseName(string $courseName): void
    {
        $courseName = trim($courseName);
        if ($courseName === '') {
            throw new InvalidArgumentException('Course name cannot be empty.');
        }
        $this->courseName = $courseName;
    }

    public function setCreditHours(int $creditHours): void
    {
        if ($creditHours < 1 || $creditHours > 12) {
            throw new InvalidArgumentException('Credit hours must be between 1 and 12.');
        }
        $this->creditHours = $creditHours;
    }
}
