<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\StudentRepository;

/** Official transcript for one student (by id or student number). */
final class TranscriptController extends Controller
{
    public function show(string $id): void
    {
        $student = (new StudentRepository(Database::connection()))->find(urldecode($id));

        if ($student === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Student not found', 'active' => 'students']);
            return;
        }

        $this->view('transcript/show', [
            'title'      => 'Official Transcript',
            'active'     => 'students',
            'student'    => $student,
            'transcript' => $student->getTranscript(),
            'gpa'        => $student->calculateGpa(),
        ]);
    }
}
