<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Domain\Course;
use PDO;

final class CourseRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, course_code, course_name, credit_hours FROM courses ORDER BY course_code')->fetchAll();
    }

    public function create(Course $course): void
    {
        $statement = $this->db->prepare('INSERT INTO courses (course_code, course_name, credit_hours) VALUES (?, ?, ?)');
        $statement->execute([$course->code(), $course->name(), $course->creditHours()]);
    }
}
