<?php
declare(strict_types=1);

namespace App\Repositories;

use App\classes\Course;
use InvalidArgumentException;
use PDO;
use PDOException;

/** Loads and saves Course objects. */
final class CourseRepository
{
    public function __construct(private PDO $db) {}

    /** @return Course[] */
    public function all(): array
    {
        $rows = $this->db->query(
            'SELECT id, course_code, course_name, credit_hours FROM courses ORDER BY course_code'
        )->fetchAll();

        $links = [];
        $linkRows = $this->db->query(
            'SELECT pc.course_id, p.program_name
             FROM program_courses pc JOIN programs p ON p.id = pc.program_id
             ORDER BY p.program_name'
        )->fetchAll();
        foreach ($linkRows as $link) {
            $links[(int) $link['course_id']][] = $link['program_name'];
        }

        return array_map(static function (array $row) use ($links): Course {
            $course = new Course($row['course_code'], $row['course_name'], (int) $row['credit_hours'], (int) $row['id']);
            $course->setProgrammes($links[(int) $row['id']] ?? []);
            return $course;
        }, $rows);
    }

    public function find(int $id): ?Course
    {
        $stmt = $this->db->prepare('SELECT id, course_code, course_name, credit_hours FROM courses WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Course($row['course_code'], $row['course_name'], (int) $row['credit_hours'], (int) $row['id']) : null;
    }

    /** Save a course and optionally link it to a programme (single transaction). */
    public function save(Course $course, ?int $programmeId = null): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO courses (course_code, course_name, credit_hours) VALUES (:code, :name, :credits)'
            );
            $stmt->execute([
                ':code'    => $course->getCourseCode(),
                ':name'    => $course->getCourseName(),
                ':credits' => $course->getCreditHours(),
            ]);
            $course->setId((int) $this->db->lastInsertId());

            if ($programmeId !== null && $programmeId > 0) {
                $link = $this->db->prepare('INSERT INTO program_courses (program_id, course_id) VALUES (:p, :c)');
                $link->execute([':p' => $programmeId, ':c' => $course->getId()]);
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('That course code already exists.');
            }
            throw $e;
        }
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    }
}
