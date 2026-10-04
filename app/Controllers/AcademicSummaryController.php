<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\EnrollmentRepository;

final class AcademicSummaryController extends Controller
{
    public function __construct(\App\Core\View $view, private EnrollmentRepository $enrollments)
    {
        parent::__construct($view);
    }

    public function index(): void
    {
        $this->requireAuthentication(true);
        $summaries = $this->enrollments->academicSummary();
        foreach ($summaries as &$summary) {
            $hours = (float) $summary['credit_hours'];
            $summary['gpa'] = $hours > 0 ? number_format((float) $summary['quality_points'] / $hours, 2) : 'N/A';
        }
        unset($summary);
        $this->render('academic-summary', ['summaries' => $summaries]);
    }
}
