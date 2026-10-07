<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\Student;
use InvalidArgumentException;
use PDO;
use PDOException;

/** Loads and saves Student objects (with their Enrolments) using PDO. */
final class StudentRepository
{
    private const SELECT = "
        SELECT s.id, s.student_number, s.first_name, s.last_name, s.programme, s.year_of_study,
               e.id AS enrolment_id, e.marks,
               c.id AS course_id, c.course_code, c.course_name, c.credit_hours
        FROM students s
        LEFT JOIN enrollments e ON e.student_id = s.id
        LEFT JOIN courses c ON c.id = e.course_id";

    public function __construct(private PDO $db) {}

    /** @return Student[] */
    public function all(bool $newestFirst = false): array
    {
        $order = $newestFirst ? 'ORDER BY s.id DESC, c.course_code' : 'ORDER BY s.student_number, c.course_code';
        return $this->hydrate($this->db->query(self::SELECT . ' ' . $order)->fetchAll());
    }

    /** @return Student[] */
    public function recent(int $limit = 5): array
    {
        $limit = max(1, $limit);
        $sql = self::SELECT . "
            WHERE s.id IN (SELECT id FROM (SELECT id FROM students ORDER BY id DESC LIMIT {$limit}) AS recent)
            ORDER BY s.id DESC, c.course_code";
        return $this->hydrate($this->db->query($sql)->fetchAll());
    }

    /** Find by numeric id or by student number (LGU-2026-001). */
    public function find(int|string $identifier): ?Student
    {
        $column = ctype_digit((string) $identifier) ? 's.id' : 's.student_number';
        $stmt = $this->db->prepare(self::SELECT . " WHERE {$column} = :value ORDER BY c.course_code");
        $stmt->execute([':value' => $identifier]);
        return $this->hydrate($stmt->fetchAll())[0] ?? null;
    }

    public function findByEnrolmentId(int $enrolmentId): ?Student
    {
        $stmt = $this->db->prepare('SELECT student_id FROM enrollments WHERE id = :id');
        $stmt->execute([':id' => $enrolmentId]);
        $studentId = $stmt->fetchColumn();
        return $studentId === false ? null : $this->find((int) $studentId);
    }

    /**
     * Create and save a new student. The static counter on Student is first
     * synchronised with the highest stored number so numbers never repeat.
     */
    public function register(string $firstName, string $lastName, string $programme, int $yearOfStudy): Student
    {
        $stmt = $this->db->prepare(
            'SELECT student_number FROM students WHERE student_number LIKE :prefix
             ORDER BY LENGTH(student_number) DESC, student_number DESC LIMIT 1'
        );
        $stmt->execute([':prefix' => 'LGU-' . date('Y') . '-%']);
        $last = $stmt->fetchColumn();
        Student::seedCounter($last === false ? 0 : (int) substr((string) $last, strrpos((string) $last, '-') + 1));

        $student = new Student($firstName, $lastName, $programme, $yearOfStudy); // validates + numbers

        try {
            $insert = $this->db->prepare(
                'INSERT INTO students (student_number, first_name, last_name, programme, year_of_study)
                 VALUES (:number, :first, :last, :programme, :year)'
            );
            $insert->execute([
                ':number'    => $student->getStudentNumber(),
                ':first'     => $student->getFirstName(),
                ':last'      => $student->getLastName(),
                ':programme' => $student->getProgramme(),
                ':year'      => $student->getYearOfStudy(),
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('That student number was just taken by another request. Please try again.');
            }
            throw $e;
        }

        $student->setId((int) $this->db->lastInsertId());
        return $student;
    }

    /** Save changes to name, programme and year (the student number never changes). */
    public function update(Student $student): void
    {
        $stmt = $this->db->prepare(
            'UPDATE students SET first_name = :first, last_name = :last, programme = :programme, year_of_study = :year
             WHERE id = :id'
        );
        $stmt->execute([
            ':first'     => $student->getFirstName(),
            ':last'      => $student->getLastName(),
            ':programme' => $student->getProgramme(),
            ':year'      => $student->getYearOfStudy(),
            ':id'        => $student->getId(),
        ]);
    }

    /** Delete a student; their enrolments are removed by ON DELETE CASCADE. */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() < 1) {
            throw new InvalidArgumentException('Student not found or already deleted.');
        }
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM students')->fetchColumn();
    }

    /**
     * Turn joined rows into Student objects holding Enrolment objects.
     * @param array<int, array<string,mixed>> $rows
     * @return Student[]
     */
    private function hydrate(array $rows): array
    {
        $students = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            if (!isset($students[$id])) {
                $students[$id] = new Student(
                    $row['first_name'],
                    $row['last_name'],
                    $row['programme'] ?? 'Not provided',
                    (int) $row['year_of_study'],
                    $row['student_number'],
                    $id
                );
            }
            if ($row['enrolment_id'] !== null) {
                $course = new Course(
                    $row['course_code'],
                    $row['course_name'],
                    (int) $row['credit_hours'],
                    (int) $row['course_id']
                );
                $enrolment = $students[$id]->enrol($course);
                $enrolment->setId((int) $row['enrolment_id']);
                if ($row['marks'] !== null) {
                    $enrolment->setMark((float) $row['marks']);
                }
            }
        }
        return array_values($students);
    }
}
