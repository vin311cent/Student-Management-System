<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\EnrollmentRepository;

final class ReportController extends Controller
{
    public function __construct(\App\Core\View $view, private EnrollmentRepository $enrollments)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $rows = $this->enrollments->academicSummary();
        $totals = ['students' => count($rows), 'enrollments' => 0, 'graded' => 0];
        foreach ($rows as &$row) {
            $hours = (float) $row['credit_hours'];
            $row['gpa'] = $hours > 0 ? number_format((float) $row['quality_points'] / $hours, 2) : 'N/A';
            $totals['enrollments'] += (int) $row['total_courses'];
            $totals['graded'] += (int) $row['graded_courses'];
        }
        unset($row);
        $this->render('reports', ['rows' => $rows, 'totals' => $totals]);
    }
}
