<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\StudentRepository;

/** Academic summary: enrolled/graded counts and weighted GPA per student. */
final class SummaryController extends Controller
{
    public function index(): void
    {
        $this->view('summary/index', [
            'title'    => 'Academic Summary',
            'active'   => 'summary',
            'students' => (new StudentRepository(Database::connection()))->all(),
        ]);
    }
}
