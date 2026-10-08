<?php
declare(strict_types=1);

namespace App\Repositories;

use App\classes\Programme;
use InvalidArgumentException;
use PDO;
use PDOException;

/** Loads and saves Programme objects. */
final class ProgrammeRepository
{
    public function __construct(private PDO $db) {}

    /** @return Programme[] */
    public function all(): array
    {
        $rows = $this->db->query('SELECT id, program_name FROM programs ORDER BY program_name')->fetchAll();
        return array_map(
            static fn (array $r): Programme => new Programme($r['program_name'], (int) $r['id']),
            $rows
        );
    }

    public function add(Programme $programme): void
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO programs (program_name) VALUES (:name)');
            $stmt->execute([':name' => $programme->getName()]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('That programme already exists.');
            }
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Invalid programme selected.');
        }
        $stmt = $this->db->prepare('DELETE FROM programs WHERE id = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() < 1) {
            throw new InvalidArgumentException('Programme not found or already deleted.');
        }
    }
}
