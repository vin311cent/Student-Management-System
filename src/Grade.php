<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

class Grade
{
    private static function service(): \App\Services\GradeService
    {
        return new \App\Services\GradeService();
    }

    public static function convert(mixed $marks): string
    {
        return self::service()->fromMark($marks);
    }

    public static function gradePoint(string $grade): float
    {
        return self::service()->points($grade);
    }
}
