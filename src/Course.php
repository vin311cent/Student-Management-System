<?php
/**
 * Course class
 * Represents a university course with code, name and credit hours.
 * Responsible for holding course identity and credit weighting.
 */
class Course
{
    private string $courseCode;
    private string $courseName;
    private int $creditHours;

    public function __construct(string $courseCode, string $courseName, int $creditHours)
    {
        $this->setCourseCode($courseCode);
        $this->setCourseName($courseName);
        $this->setCreditHours($creditHours);
    }

    public function getCourseCode(): string
    {
        return $this->courseCode;
    }

    public function getCourseName(): string
    {
        return $this->courseName;
    }

    public function getCreditHours(): int
    {
        return $this->creditHours;
    }

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
