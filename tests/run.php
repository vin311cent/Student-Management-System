<?php
declare(strict_types=1);

/**
 * Dependency-free tests for the domain layer.   Run:  php tests/run.php
 */
require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\{Course, Enrolment, Grade, Programme, Student};
use App\Services\ReportService;

$passed = 0; $failed = 0;

function check(string $name, bool $ok): void
{
    global $passed, $failed;
    $ok ? $passed++ : $failed++;
    echo ($ok ? "  ok    " : "  FAIL  ") . $name . "\n";
}

function throwsA(string $class, callable $fn): bool
{
    try { $fn(); } catch (Throwable $e) { return $e instanceof $class; }
    return false;
}

echo "Grade\n";
foreach ([[100,'A'],[80,'A'],[79.9,'B+'],[75,'B+'],[74,'B'],[70,'B'],[69,'C+'],[65,'C+'],[64,'C'],[60,'C'],[59,'D'],[50,'D'],[49,'F'],[0,'F']] as [$m, $l]) {
    check("mark {$m} => {$l}", Grade::convert((float) $m) === $l);
}
check('mark 101 rejected', throwsA(InvalidArgumentException::class, fn () => new Grade(101)));
check('mark -1 rejected',  throwsA(InvalidArgumentException::class, fn () => new Grade(-1)));

echo "Course / Programme\n";
check('credit hours 0 rejected',  throwsA(InvalidArgumentException::class, fn () => new Course('A1', 'x', 0)));
check('credit hours 13 rejected', throwsA(InvalidArgumentException::class, fn () => new Course('A1', 'x', 13)));
check('empty name rejected',      throwsA(InvalidArgumentException::class, fn () => new Course('A1', ' ', 3)));
check('code upper-cased',         (new Course(' csc101 ', 'Prog', 3))->getCourseCode() === 'CSC101');
check('empty programme rejected', throwsA(InvalidArgumentException::class, fn () => new Programme('  ')));

echo "Student\n";
Student::seedCounter(0);
$a = new Student('Lewis', 'Chingwamari', 'Computer Science', 2);
$b = new Student('Kieth', 'Tim', 'Computer Science', 3);
check('first number',  $a->getStudentNumber() === 'LGU-' . date('Y') . '-001');
check('second number', $b->getStudentNumber() === 'LGU-' . date('Y') . '-002');
Student::seedCounter(41);
check('seeded counter continues', (new Student('A', 'B', 'C', 1))->getStudentNumber() === 'LGU-' . date('Y') . '-042');
check('loaded student keeps number', (new Student('A', 'B', 'C', 1, 'LGU-2020-007'))->getStudentNumber() === 'LGU-2020-007');
check('failed construction does not burn a number', (function () {
    Student::seedCounter(10);
    try { new Student('', 'x', 'y'); } catch (InvalidArgumentException) {}
    return Student::getCounter() === 10;
})());
check('year 0 rejected', throwsA(InvalidArgumentException::class, fn () => new Student('A', 'B', 'C', 0)));
check('full name', $a->getFullName() === 'Lewis Chingwamari');

echo "Enrolment, marks, GPA\n";
$c1 = new Course('CSC101', 'Programming', 3);
$c2 = new Course('CSC210', 'Web Programming 2', 4);
$c3 = new Course('MAT120', 'Discrete Maths', 3);
$a->enrol($c1); $a->enrol($c2); $a->enrol($c3);
check('duplicate enrolment rejected', throwsA(InvalidArgumentException::class, fn () => $a->enrol($c1)));
check('gpa null when nothing graded', $a->calculateGpa() === null);
$a->recordMark('csc101', 85);   // A  4.0 x 3 = 12
$a->recordMark('CSC210', 72);   // B  3.0 x 4 = 12
$a->recordMark('MAT120', 40);   // F  0.0 x 3 = 0
check('weighted gpa = 2.4', $a->calculateGpa() === 2.4);
check('graded count', $a->getGradedCount() === 3);
check('graded credits', $a->getGradedCredits() === 10);
check('mark out of range rejected', throwsA(InvalidArgumentException::class, fn () => $a->recordMark('CSC101', 120)));
check('rejected mark does not overwrite', $a->getEnrolments()[0]->getMark() === 85.0);
check('not enrolled => RuntimeException', throwsA(RuntimeException::class, fn () => $b->recordMark('CSC101', 50)));
$t = $a->getTranscript();
check('transcript rows', count($t) === 3 && $t[0]['grade'] === 'A' && $t[1]['courseName'] === 'Web Programming 2');
check('enrolment lookup by id', (function () use ($a) {
    $a->getEnrolments()[1]->setId(99);
    return $a->findEnrolmentById(99)?->getCourse()->getCourseCode() === 'CSC210' && $a->findEnrolmentById(5) === null;
})());

echo "ReportService\n";
$b->enrol($c1); $b->recordMark('CSC101', 60);
$svc = new ReportService();
$perf = $svc->studentPerformance([$a, $b]);
check('performance gpa', $perf[0]['gpa'] === 2.4 && $perf[1]['gpa'] === 2.0);
$cs = $svc->courseStats([$c1, $c2, $c3], [$a, $b]);
check('course stats sorted by enrolment', $cs[0]['code'] === 'CSC101' && $cs[0]['enrolled'] === 2 && $cs[0]['average'] === 72.5);
$ps = $svc->programmeStats([$a, $b]);
check('programme stats', $ps[0]['programme'] === 'Computer Science' && $ps[0]['students'] === 2 && $ps[0]['enrolments'] === 4);

echo "\n{$passed} passed, {$failed} failed\n";
exit($failed === 0 ? 0 : 1);
