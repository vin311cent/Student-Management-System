<?php
// C:\xampp\htdocs\hex\src\StudentRepository.php

class StudentRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all students
     * @return Student[]
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $students = [];
        foreach ($rows as $row) {
            $student = new Student(
                $row['first_name'] . ' ' . $row['last_name'],
                $row['programme'] ?? 'N/A',
                (int)($row['year_of_study'] ?? 1)
            );
            $students[] = $student;
        }

        return $students;
    }

    /**
     * Find a student by ID
     */
    public function find(int $id): ?Student
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Student(
            $row['first_name'] . ' ' . $row['last_name'],
            $row['programme'] ?? 'N/A',
            (int)($row['year_of_study'] ?? 1)
        );
    }
}