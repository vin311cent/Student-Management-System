<?php
/**
 * Main demonstration script — Student Records Management System
 *
 * Satisfies assignment requirements:
 *  - Creates at least 3 students
 *  - Enrols each in at least 3 courses
 *  - Records marks
 *  - Displays each student's transcript in an HTML table
 *  - Throws and catches InvalidArgumentException for marks outside 0–100
 *
 * OOP: Student, Course, Enrolment, Grade (PHP 8+, src/ folder)
 * Admin UI: Login.php (credentials admin / admin123)
 */
require_once __DIR__ . '/src/autoload.php';

Student::resetCounter(0);

$errors = [];
$students = [];

try {
    // Courses
    $courses = [
        new Course('CSC101', 'Programming Fundamentals', 3),
        new Course('CSC205', 'Database Systems', 3),
        new Course('CSC210', 'Web Programming 2', 4),
        new Course('MAT120', 'Discrete Mathematics', 3),
        new Course('ENG110', 'Academic Communication', 2),
    ];

    // At least 3 students
    $students[] = new Student('Lewis Chingwamari', 'Bachelor of Computer Science', 2);
    $students[] = new Student('Chipo Banda', 'Bachelor of Computer Science', 1);
    $students[] = new Student('Thabo Mwila', 'Bachelor of Information Technology', 3);

    // Each enrolled in at least 3 courses
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

    // Deliberately invalid marks — must be caught gracefully
    try {
        $students[0]->recordMark('CSC101', 150);
    } catch (InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    }

    try {
        $students[1]->recordMark('CSC205', -5);
    } catch (InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    }

} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records Demo | Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .demo-wrap { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .demo-wrap h1 { margin-bottom: 0.25rem; }
        .demo-meta { color: #64748b; margin-bottom: 1.5rem; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .student-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .student-card h2 { margin: 0 0 0.35rem; font-size: 1.15rem; }
        .student-meta { color: #64748b; font-size: 0.95rem; margin-bottom: 0.75rem; }
        .student-meta .gpa { color: #0f766e; font-weight: 700; }
        .student-card table { width: 100%; border-collapse: collapse; }
        .student-card th, .student-card td { text-align: left; padding: 0.5rem 0.65rem; border-bottom: 1px solid #f1f5f9; }
        .student-card th { background: #f8fafc; font-size: 0.85rem; color: #475569; }
        .concepts { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.25rem; margin-top: 1.5rem; }
        .concepts h3 { margin-top: 0; }
        .concepts ul { margin: 0.5rem 0 0; padding-left: 1.25rem; }
        .top-links { margin-bottom: 1rem; }
        .top-links a { margin-right: 1rem; font-weight: 600; color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="demo-wrap">
        <div class="top-links">
            <a href="Login.php">Admin Login</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
        <h1>Student Records — OOP Demonstration</h1>
        <p class="demo-meta">PHP 8+ · Classes in <code>src/</code> · At least 3 students × 3 courses · Invalid marks caught</p>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Caught InvalidArgumentException(s):</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php foreach ($students as $student): ?>
            <div class="student-card">
                <h2><?= htmlspecialchars($student->getFullName()) ?>
                    <small>(<?= htmlspecialchars($student->getStudentNumber()) ?>)</small>
                </h2>
                <div class="student-meta">
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
                                <td><?= $row['mark'] !== null ? number_format((float)$row['mark'], 1) : '—' ?></td>
                                <td><strong><?= htmlspecialchars($row['grade'] ?? '—') ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div class="concepts">
            <h3>OOP concepts demonstrated (assignment checklist)</h3>
            <ul>
                <li><strong>Classes &amp; objects</strong> — Student, Course, Enrolment, Grade</li>
                <li><strong>Encapsulation</strong> — private properties with validated getters/setters</li>
                <li><strong>Constructors</strong> — Student / Course / Enrolment / Grade</li>
                <li><strong>Composition</strong> — Student holds an array of Enrolment objects</li>
                <li><strong>Static members</strong> — auto student numbers (LGU-YYYY-NNN)</li>
                <li><strong>Exceptions</strong> — InvalidArgumentException for marks outside 0–100 (caught above)</li>
                <li><strong>Stretch</strong> — weighted GPA + optional MySQL admin app (Login)</li>
            </ul>
        </div>
    </div>
</body>
</html>
