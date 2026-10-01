<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';

$studentIdentifier = $_GET['id'] ?? null;

if (!$studentIdentifier) {
    header('Location: Student.php');
    exit;
}

$db = Database::getInstance()->getConnection();

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

// Support both marks column and grade-only schemas
$columns = $db->query('SHOW COLUMNS FROM enrollments')->fetchAll(PDO::FETCH_COLUMN);
$markCol = in_array('marks', $columns, true) ? 'e.marks' : 'NULL';

$stmt = $db->prepare("
    SELECT 
        c.course_code,
        c.course_name,
        c.credit_hours,
        {$markCol} AS mark
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = :student_id
");
$stmt->execute([':student_id' => $studentData['id']]);
$enrollmentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$studentName = $studentData['first_name'] . ' ' . $studentData['last_name'];
$student = new Student($studentName, $studentData['programme'] ?? 'N/A', (int)$studentData['year_of_study']);

foreach ($enrollmentRows as $row) {
    $course = new Course($row['course_code'], $row['course_name'], (int)$row['credit_hours']);
    $student->enrol($course);
    if ($row['mark'] !== null && $row['mark'] !== '') {
        $student->recordMark($row['course_code'], (float)$row['mark']);
    }
}

$transcript = $student->getTranscript();
$gpa = $student->calculateGpa();
$username = $_SESSION['user']['username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript - <?= htmlspecialchars($studentData['student_number']) ?> | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links" aria-label="Sidebar navigation">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item active" href="Student.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Official Transcript</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <div class="welcome-card">
                    <div>
                        <h2><?= htmlspecialchars($studentName) ?></h2>
                        <p>
                            <strong>Student No:</strong> <?= htmlspecialchars($studentData['student_number']) ?>
                            &nbsp;|&nbsp;
                            <strong>Programme:</strong> <?= htmlspecialchars($studentData['programme'] ?? 'Not provided') ?>
                            &nbsp;|&nbsp;
                            <strong>Year:</strong> <?= htmlspecialchars((string)$studentData['year_of_study']) ?>
                        </p>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-primary" type="button" onclick="window.print()">Print transcript</button>
                        <a class="btn" href="Student.php">Back to Students</a>
                    </div>
                </div>

                <section class="panel-card">
                    <h3>Course Enrolments &amp; Grades</h3>
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
                                            <td><?= htmlspecialchars((string)$item['creditHours']) ?></td>
                                            <td><?= $item['mark'] !== null ? htmlspecialchars((string)$item['mark']) . '%' : 'N/A' ?></td>
                                            <td><strong><?= htmlspecialchars($item['grade'] ?? 'N/A') ?></strong></td>
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
                        <strong>Cumulative GPA:</strong>
                        <?= $gpa !== null ? number_format($gpa, 2) : 'N/A' ?>
                    </div>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
