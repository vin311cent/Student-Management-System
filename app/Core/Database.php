<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_NAME') ?: 'students_records';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') ?: '';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function connection(): PDO
    {
        return $this->connection;
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new \LogicException('The database service cannot be unserialized.');
    }
}
