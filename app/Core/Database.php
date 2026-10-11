<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/** Singleton wrapper that owns the single PDO connection (Model layer infrastructure). */
final class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}
    private function __clone() {}

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $driver = strtolower((string) Config::get('db.driver', 'mysql'));
            if ($driver !== 'mysql') {
                throw new RuntimeException("Unsupported database driver '{$driver}'. This application requires MySQL.");
            }

            $host = (string) Config::get('db.host', '127.0.0.1');
            $port = (string) Config::get('db.port', '3306');
            $name = (string) Config::get('db.name', 'students_records');
            $user = (string) Config::get('db.user', 'root');
            $pass = (string) Config::get('db.pass', '');
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new RuntimeException('MySQL connection failed: ' . $e->getMessage(), 0, $e);
            }
        }

        return self::$connection;
    }
}
