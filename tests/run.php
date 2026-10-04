<?php
declare(strict_types=1);

use App\Domain\Course;
use App\Domain\Enrollment;
use App\Domain\Student;
use App\Services\GradeService;

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = 'login';
ob_start();
require __DIR__ . '/../index.php';
$loginHtml = (string) ob_get_clean();
require_once __DIR__ . '/../GPA.php';
require_once __DIR__ . '/../src/Grade.php';

$gradeService = new GradeService();
$checks = [
    $gradeService->fromMark(80) === 'A',
    $gradeService->fromMark(79.99) === 'B',
    $gradeService->fromMark(50) === 'D',
    $gradeService->fromMark(0) === 'F',
    $gradeService->points('b') === 3.0,
    Grade::convert(80) === 'A',
    GPA::calculate([['grade' => 'A', 'credit_hours' => 3]]) === 4.0,
    $gradeService->weightedGpa([
        ['grade' => 'A', 'credit_hours' => 3],
        ['grade' => 'B', 'credit_hours' => 4],
    ]) === 3.43,
    (new Student(' Ada ', 'Lovelace', 'Computing'))->firstName() === 'Ada',
    (new Course('cs101', 'Programming', 3))->code() === 'CS101',
    (new Enrollment(1, 2))->courseId() === 2,
];

$expectInvalid = static function (callable $operation): bool {
    try {
        $operation();
    } catch (InvalidArgumentException) {
        return true;
    }
    return false;
};
$checks[] = $expectInvalid(static fn() => $gradeService->fromMark(101));
$checks[] = $expectInvalid(static fn() => new Student('', 'Lovelace', null));
$checks[] = $expectInvalid(static fn() => new Course('CS101', 'Programming', 0));
$checks[] = $expectInvalid(static fn() => new Enrollment(0, 1));
$checks[] = $expectInvalid(static fn() => $gradeService->weightedGpa([]));
$checks[] = $expectInvalid(static fn() => $gradeService->weightedGpa([['grade' => 'Z', 'credit_hours' => 3]]));
$checks[] = $expectInvalid(static fn() => $gradeService->weightedGpa([['grade' => 'A', 'credit_hours' => 0]]));

$checks[] = str_contains($loginHtml, 'Student Management System');
$checks[] = str_contains($loginHtml, 'name="password"');

if (in_array(false, $checks, true)) {
    fwrite(STDERR, "One or more OOP domain/service checks failed.\n");
    exit(1);
}

echo count($checks) . " OOP domain/service checks passed.\n";
