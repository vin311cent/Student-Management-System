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

        return self::$connection;
    }
}   
