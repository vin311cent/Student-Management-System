<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Domain\Enrollment;
use PDO;

final class EnrollmentRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->connection();
    }

    public function all(): array
    {
        return $this->db->query("
            SELECT e.id, e.marks, e.grade,
                   CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                   s.student_number, c.course_name, c.course_code, c.credit_hours
            FROM enrollments e
            JOIN students s ON s.id = e.student_id
            JOIN courses c ON c.id = e.course_id
            ORDER BY student_name, c.course_name
        ")->fetchAll();
    }

    public function options(): array
    {
        return [
            'students' => $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM students ORDER BY first_name")->fetchAll(),
            'courses' => $this->db->query('SELECT id, course_name FROM courses ORDER BY course_code')->fetchAll(),
        ];
    }

    public function create(Enrollment $enrollment): void
    {
        $statement = $this->db->prepare('INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)');
        $statement->execute([$enrollment->studentId(), $enrollment->courseId()]);
    }

    public function saveMark(int $enrollmentId, float $mark, string $grade): bool
    {
        $this->db->beginTransaction();
        try {
            $exists = $this->db->prepare('SELECT id FROM enrollments WHERE id = ? FOR UPDATE');
            $exists->execute([$enrollmentId]);
            if ($exists->fetchColumn() === false) {
                $this->db->commit();
                return false;
            }
            $statement = $this->db->prepare('UPDATE enrollments SET marks = ?, grade = ? WHERE id = ?');
            $statement->execute([$mark, $grade, $enrollmentId]);
            $this->db->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    public function academicSummary(): array
    {
        return $this->db->query("
            SELECT s.student_number, CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.programme,
                   COUNT(e.id) AS total_courses,
                   SUM(CASE WHEN e.grade IS NOT NULL THEN 1 ELSE 0 END) AS graded_courses,
                   COALESCE(SUM(CASE WHEN e.grade IS NOT NULL THEN c.credit_hours ELSE 0 END), 0) AS credit_hours,
                   COALESCE(SUM(CASE e.grade
                       WHEN 'A' THEN 4 * c.credit_hours WHEN 'B' THEN 3 * c.credit_hours
                       WHEN 'C' THEN 2 * c.credit_hours WHEN 'D' THEN c.credit_hours ELSE 0 END), 0) AS quality_points
            FROM students s
            LEFT JOIN enrollments e ON e.student_id = s.id
            LEFT JOIN courses c ON c.id = e.course_id
            GROUP BY s.id, s.student_number, s.first_name, s.last_name, s.programme
            ORDER BY s.student_number
        ")->fetchAll();
    }

    public function counts(): array
    {
        return [
            'students' => (int) $this->db->query('SELECT COUNT(*) FROM students')->fetchColumn(),
            'courses' => (int) $this->db->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
            'enrollments' => (int) $this->db->query('SELECT COUNT(*) FROM enrollments')->fetchColumn(),
        ];
    }
}
