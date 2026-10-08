<?php
declare(strict_types=1);

namespace App\Repositories;

use App\classes\Enrolment;
use InvalidArgumentException;
use PDO;
use PDOException;

/** Saves Enrolment objects (the enrollments table). */
final class EnrolmentRepository
{
    public function __construct(private PDO $db) {}

    public function insert(Enrolment $enrolment): void
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO enrollments (student_id, course_id) VALUES (:student, :course)');
            $stmt->execute([
                ':student' => $enrolment->getStudent()->getId(),
                ':course'  => $enrolment->getCourse()->getId(),
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('This student is already enrolled in that course.');
            }
            throw $e;
        }
        $enrolment->setId((int) $this->db->lastInsertId());
    }

    /** Persist the mark and the letter grade derived from it. */
    public function saveMark(Enrolment $enrolment): void
    {
        $stmt = $this->db->prepare('UPDATE enrollments SET marks = :marks, grade = :grade WHERE id = :id');
        $stmt->execute([
            ':marks' => $enrolment->getMark(),
            ':grade' => $enrolment->getGrade(),
            ':id'    => $enrolment->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM enrollments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() < 1) {
            throw new InvalidArgumentException('Enrolment not found or already removed.');
        }
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();
    }
}
