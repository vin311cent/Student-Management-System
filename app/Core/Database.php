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
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                if (Config::get('db.driver') === 'sqlite') {
                    self::$connection = new PDO('sqlite:' . Config::get('db.path'), null, null, $options);
                    self::$connection->exec('PRAGMA foreign_keys = ON');
                } else {
                    $dsn = sprintf(
                        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                        Config::get('db.host'),
                        Config::get('db.port'),
                        Config::get('db.name')
                    );
                    self::$connection = new PDO($dsn, Config::get('db.user'), Config::get('db.pass'), $options);
                }
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new RuntimeException('Could not connect to the database. Check config/config.php and that MySQL is running.');
            }
        }
        return self::$connection;
    }
}
