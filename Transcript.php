<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';

$studentIdentifier = $_GET['id'] ?? null;

if (!$studentIdentifier) {
    header('Location: Students.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch student details
$stmt = $db->prepare("
    SELECT id, student_number, first_name, last_name, programme, year_of_study 
    FROM students 
    WHERE id = :id OR student_number = :student_number 
    LIMIT 1
");
$stmt->execute([
    ':id' => $studentIdentifier,
    ':student_number' => $studentIdentifier
]);
$studentData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$studentData) {
    die("Student record not found.");
}

// Fixed Query: e.marks AS mark (plural) matches table schema in database
$stmt = $db->prepare("
    SELECT 
        c.course_code,
        c.course_name,
        c.credit_hours,
        e.marks AS mark
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = :student_id
");
$stmt->execute([':student_id' => $studentData['id']]);
$enrollmentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Instantiate OOP models to calculate GPA and formatted grades
$studentName = $studentData['first_name'] . ' ' . $studentData['last_name'];
$student = new Student($studentName, $studentData['programme'] ?? 'N/A', (int)$studentData['year_of_study']);

foreach ($enrollmentRows as $row) {
    $course = new Course($row['course_code'], $row['course_name'], (int)$row['credit_hours']);
    $student->enrol($course);
    if ($row['mark'] !== null) {
        $student->recordMark($row['course_code'], (float)$row['mark']);
    }
}

$transcript = $student->getTranscript();
$gpa = $student->calculateGpa();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript - <?= htmlspecialchars($studentData['student_number']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item active" href="Students.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolments.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Academic Records</p>
                    <h1>Official Transcript</h1>
                </div>
                <div class="topbar-actions">
                    <a class="btn" href="Students.php">Back to Students</a>
                </div>
            </header>

            <section class="dashboard-content">
                <section class="panel-card">
                    <h2>Student Information</h2>
                    <p><strong>Student Number:</strong> <?= htmlspecialchars($studentData['student_number']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($studentName) ?></p>
                    <p><strong>Programme:</strong> <?= htmlspecialchars($studentData['programme'] ?? 'Not provided') ?></p>
                    <p><strong>Year of Study:</strong> <?= htmlspecialchars($studentData['year_of_study']) ?></p>
                </section>
                <button class="btn secondary" onclick="window.print()">Print transcript</button>

                <section class="panel-card" style="margin-top: 20px;">
                    <h3>Course Enrolments & Grades</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credit Hours</th>
                                    <th>Mark (%)</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transcript)): ?>
                                    <?php foreach ($transcript as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['courseCode']) ?></td>
                                            <td><?= htmlspecialchars($item['courseName']) ?></td>
                                            <td><?= htmlspecialchars($item['creditHours']) ?></td>
                                            <td><?= $item['mark'] !== null ? htmlspecialchars($item['mark']) . '%' : 'N/A' ?></td>
                                            <td><?= htmlspecialchars($item['grade'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No course enrolments recorded.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 15px;">
                        <strong>Cumulative GPA:</strong> <?= $gpa !== null ? number_format($gpa, 2) : 'N/A' ?>
                    </div>
                </section>
            </section>
        </main>
    </div>
</body>
</html>