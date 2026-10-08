<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\classes\Student;
use App\Repositories\CourseRepository;
use App\Repositories\StudentRepository;
use App\Services\ReportService;

/** On-screen reports and CSV exports. */
final class ReportController extends Controller
{
    private StudentRepository $students;
    private CourseRepository $courses;
    private ReportService $reports;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $db = Database::connection();
        $this->students = new StudentRepository($db);
        $this->courses  = new CourseRepository($db);
        $this->reports  = new ReportService();
    }

    public function index(): void
    {
        $students    = $this->students->all();
        $performance = $this->reports->studentPerformance($students);

        $this->view('reports/index', [
            'title'           => 'Reports',
            'active'          => 'reports',
            'performance'     => $performance,
            'totalStudents'   => count($students),
            'totalEnrolments' => array_sum(array_column($performance, 'enrolled')),
            'totalGraded'     => array_sum(array_column($performance, 'graded')),
            'courseStats'     => $this->reports->courseStats($this->courses->all(), $students),
            'programmeStats'  => $this->reports->programmeStats($students),
        ]);
    }

    public function export(string $type): void
    {
        match ($type) {
            'students'  => $this->exportStudents(),
            'enrolment' => $this->exportEnrolment(),
            default     => $this->unknownExport(),
        };
    }

    private function exportStudents(): never
    {
        $rows = [['Student Number', 'First Name', 'Last Name', 'Programme', 'Year of Study', 'Enrolled Courses', 'Graded Courses', 'GPA']];
        foreach ($this->students->all() as $s) {
            /** @var Student $s */
            $rows[] = [
                $s->getStudentNumber(), $s->getFirstName(), $s->getLastName(), $s->getProgramme(),
                $s->getYearOfStudy(), count($s->getEnrolments()), $s->getGradedCount(), $s->calculateGpa() ?? 'N/A',
            ];
        }
        $this->streamCsv('student_list_' . date('Y-m-d') . '.csv', $rows);
    }

    private function exportEnrolment(): never
    {
        $students = $this->students->all();

        $rows = [['ENROLMENT REPORT - Course Statistics'], ['Generated', date('Y-m-d H:i')], [],
                 ['Course Code', 'Course Name', 'Credit Hours', 'Total Enrolled', 'Graded', 'Pending', 'Average Marks']];
        foreach ($this->reports->courseStats($this->courses->all(), $students) as $c) {
            $rows[] = [$c['code'], $c['name'], $c['credits'], $c['enrolled'], $c['graded'], $c['pending'], $c['average'] ?? 'N/A'];
        }
        $rows[] = [];
        $rows[] = ['ENROLMENT BY PROGRAMME'];
        $rows[] = ['Programme', 'Students', 'Enrolments'];
        foreach ($this->reports->programmeStats($students) as $p) {
            $rows[] = [$p['programme'], $p['students'], $p['enrolments']];
        }
        $this->streamCsv('enrolment_report_' . date('Y-m-d') . '.csv', $rows);
    }

    private function unknownExport(): never
    {
        $this->flash('error', 'Unknown report type.');
        $this->redirect('/reports');
    }

    /** @param array<int, array<int, mixed>> $rows */
    private function streamCsv(string $filename, array $rows): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel opens it correctly
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }
}
