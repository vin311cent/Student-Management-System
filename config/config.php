<?php
declare(strict_types=1);

/**
 * Application configuration.
 * Values can be overridden with environment variables (DB_HOST, DB_PORT, ...).
 */
return [
    'app' => [
        'name'     => 'LGU Manage',
        'timezone' => 'Africa/Lusaka',
        'debug'    => false, // set true while developing to see exception details
    ],
    'db' => [
        'driver' => getenv('DB_DRIVER') ?: 'mysql',            // 'mysql' (default) or 'sqlite' (tests / quick demo)
        'path'   => getenv('DB_PATH') ?: __DIR__ . '/../database/app.sqlite',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'students_records',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'auth' => [
        // Demo administrator: admin / admin123 (stored as a bcrypt hash, never plain text)
        'username'      => 'admin',
        'password_hash' => '$2y$10$3kx3hM21PM5Yp1YOgg8av.sgbNgJlA0XNFZ8xLuRd2e7p9mOwg0/G',
    ],
];
