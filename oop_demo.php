<?php
/**
 * Main demonstration script for Project 1 – Student Records Management System
 *
 * This script satisfies the core requirements:
 *  - Creates at least 3 students
 *  - Enrols each in at least 3 courses
 *  - Records marks
 *  - Displays each student’s transcript in an HTML table
 *  - Throws & catches InvalidArgumentException for marks outside 0–100
 *
 * Classes used: Student, Course, Enrolment, Grade (all OOP, PHP 8+)
 */
require_once __DIR__ . '/src/autoload.php';

// Reset static counter so demo numbers start cleanly
Student::resetCounter(0);

$errors = [];
$students = [];

try {
    // ---------- Courses ----------
    $courses = [
        new Course('CSC101', 'Programming Fundamentals', 3),
        new Course('CSC205', 'Database Systems', 3),
        new Course('CSC210', 'Web Programming 2', 4),
        new Course('MAT120', 'Discrete Mathematics', 3),
        new Course('ENG110', 'Academic Communication', 2),
    ];

    // ---------- Students (at least 3) ----------
    $students[] = new Student('Lewis Chingwamari', 'Bachelor of Computer Science', 2);
    $students[] = new Student('Chipo Banda', 'Bachelor of Computer Science', 1);
    $students[] = new Student('Thabo Mwila', 'Bachelor of Information Technology', 3);

    // Enrol each student in at least 3 courses
    $students[0]->enrol($courses[0]);
    $students[0]->enrol($courses[1]);
    $students[0]->enrol($courses[2]);
    $students[0]->enrol($courses[3]);

    $students[1]->enrol($courses[0]);
    $students[1]->enrol($courses[1]);
    $students[1]->enrol($courses[4]);

    $students[2]->enrol($courses[0]);
    $students[2]->enrol($courses[2]);
    $students[2]->enrol($courses[3]);
    $students[2]->enrol($courses[4]);

    // Record marks
    $students[0]->recordMark('CSC101', 85);
    $students[0]->recordMark('CSC205', 72);
    $students[0]->recordMark('CSC210', 91);
    $students[0]->recordMark('MAT120', 68);

    $students[1]->recordMark('CSC101', 78);
    $students[1]->recordMark('CSC205', 55);
    $students[1]->recordMark('ENG110', 82);

    $students[2]->recordMark('CSC101', 64);
    $students[2]->recordMark('CSC210', 88);
    $students[2]->recordMark('MAT120', 45);
    $students[2]->recordMark('ENG110', 70);

    // Deliberately trigger InvalidArgumentException (caught gracefully)
    try {
        $students[0]->recordMark('CSC101', 150); // invalid mark
    } catch (InvalidArgumentException $e) {
        $errors[] = 'Caught expected exception: ' . $e->getMessage();
    }

    try {
        $students[1]->recordMark('CSC205', -10); // invalid mark
    } catch (InvalidArgumentException $e) {
        $errors[] = 'Caught expected exception: ' . $e->getMessage();
    }

} catch (Throwable $e) {
    $errors[] = 'Unexpected error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOP Demo – Student Transcripts | Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .demo-wrap { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .demo-header { background: #1e3a5f; color: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .demo-header h1 { margin: 0 0 0.4rem; font-size: 1.5rem; }
        .demo-header p { margin: 0; opacity: 0.9; }
        .student-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .student-card h2 { background: #f1f5f9; margin: 0; padding: 0.9rem 1.2rem; font-size: 1.1rem; color: #1e3a5f; }
        .student-meta { padding: 0.6rem 1.2rem; font-size: 0.9rem; color: #475569; border-bottom: 1px solid #e2e8f0; }
        .student-card table { width: 100%; border-collapse: collapse; }
        .student-card th, .student-card td { padding: 0.65rem 1.2rem; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .student-card th { background: #f8fafc; font-weight: 600; color: #334155; }
        .gpa { font-weight: 700; color: #0f766e; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem 1.2rem; border-radius: 6px; margin-bottom: 1.5rem; }
        .error-box ul { margin: 0.4rem 0 0; padding-left: 1.2rem; }
        .nav-back { display: inline-block; margin-bottom: 1rem; color: #1e3a5f; text-decoration: none; font-weight: 600; }
        .nav-back:hover { text-decoration: underline; }
        .concepts { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1rem 1.2rem; border-radius: 6px; margin-top: 2rem; font-size: 0.9rem; }
        .concepts h3 { margin: 0 0 0.5rem; color: #166534; }
        .concepts ul { margin: 0; padding-left: 1.2rem; }
    </style>
</head>
<body>
    <div class="demo-wrap">
        <a class="nav-back" href="Login.php">← Back to Login / Dashboard</a>

        <div class="demo-header">
            <h1>Student Records – OOP Demonstration</h1>
            <p>Project 1 · Classes, Encapsulation, Composition, Static Members, Exceptions</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-box" role="alert">
                <strong>Exception handling (graceful catch):</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php foreach ($students as $student): ?>
            <div class="student-card">
                <h2><?= htmlspecialchars($student->getFullName()) ?></h2>
                <div class="student-meta">
                    <strong>Student No:</strong> <?= htmlspecialchars($student->getStudentNumber()) ?>
                    &nbsp;|&nbsp;
                    <strong>Programme:</strong> <?= htmlspecialchars($student->getProgramme()) ?>
                    &nbsp;|&nbsp;
                    <strong>Year:</strong> <?= (int)$student->getYearOfStudy() ?>
                    <?php $gpa = $student->calculateGpa(); ?>
                    <?php if ($gpa !== null): ?>
                        &nbsp;|&nbsp; <span class="gpa">GPA: <?= number_format($gpa, 2) ?></span>
                    <?php endif; ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Mark</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student->getTranscript() as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['courseCode']) ?></td>
                                <td><?= htmlspecialchars($row['courseName']) ?></td>
                                <td><?= (int)$row['creditHours'] ?></td>
                                <td><?= $row['mark'] !== null ? number_format($row['mark'], 1) : '—' ?></td>
                                <td><strong><?= htmlspecialchars($row['grade'] ?? '—') ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div class="concepts">
            <h3>OOP concepts demonstrated</h3>
            <ul>
                <li><strong>Classes &amp; objects</strong> – Student, Course, Enrolment, Grade</li>
                <li><strong>Encapsulation</strong> – private properties with validated getters/setters</li>
                <li><strong>Constructors</strong> – Student initialised with name, programme, year</li>
                <li><strong>Composition</strong> – Student holds an array of Enrolment objects</li>
                <li><strong>Static members</strong> – auto-generated student numbers (LGU-YYYY-NNN)</li>
                <li><strong>Exceptions</strong> – InvalidArgumentException for marks outside 0–100 (caught above)</li>
                <li><strong>Stretch</strong> – weighted GPA calculation by credit hours</li>
            </ul>
        </div>
    </div>
</body>
</html>
