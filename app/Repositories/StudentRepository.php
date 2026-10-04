<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Domain\Student;
use PDO;

final class StudentRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->connection();
    }

    public function all(): array
    {
        return $this->db->query("
            SELECT s.id, s.student_number, s.first_name, s.last_name, s.programme, s.year_of_study
            FROM students s ORDER BY s.id DESC
        ")->fetchAll();
    }

    public function recent(int $limit = 5): array
    {
        $statement = $this->db->prepare("
            SELECT s.student_number, CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                   c.course_name, e.grade
            FROM students s
            LEFT JOIN enrollments e ON e.student_id = s.id
            LEFT JOIN courses c ON c.id = e.course_id
            ORDER BY s.id DESC LIMIT :limit
        ");
        $statement->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function create(Student $student): void
    {
        $year = date('Y');
        $statement = $this->db->prepare("
            SELECT MAX(CAST(SUBSTRING(student_number, 5) AS UNSIGNED))
            FROM students WHERE student_number LIKE :pattern
        ");
        $statement->execute(['pattern' => $year . '%']);
        $sequence = ((int) $statement->fetchColumn()) + 1;
        $studentNumber = $year . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

        $insert = $this->db->prepare("
            INSERT INTO students (student_number, first_name, last_name, programme, year_of_study)
            VALUES (:number, :first_name, :last_name, :programme, :year)
        ");
        $insert->execute([
            'number' => $studentNumber,
            'first_name' => $student->firstName(),
            'last_name' => $student->lastName(),
            'programme' => $student->programme(),
            'year' => $student->yearOfStudy(),
        ]);
    }
}
