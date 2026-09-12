<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}
require_once __DIR__ . '/src/autoload.php';


$database = Database::getInstance();
$db = $database->getConnection();

$reportRows = $db->query("
    SELECT
        s.student_number,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        s.programme,
        COUNT(e.id) AS enrolled_courses,
        SUM(CASE WHEN e.grade IS NOT NULL THEN 1 ELSE 0 END) AS graded_courses,
        COALESCE(SUM(CASE WHEN e.grade IS NOT NULL THEN c.credit_hours ELSE 0 END), 0) AS credit_hours,
        COALESCE(SUM(
            CASE e.grade
                WHEN 'A' THEN 4.0 * c.credit_hours
                WHEN 'B' THEN 3.0 * c.credit_hours
                WHEN 'C' THEN 2.0 * c.credit_hours
                WHEN 'D' THEN 1.0 * c.credit_hours
                WHEN 'F' THEN 0.0
                ELSE 0
            END
        ), 0) AS quality_points
    FROM students s
    LEFT JOIN enrollments e ON e.student_id = s.id
    LEFT JOIN courses c ON c.id = e.course_id
    GROUP BY s.id, s.student_number, s.first_name, s.last_name, s.programme
    ORDER BY s.student_number
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($reportRows as &$row) {
    $creditHours = (float)$row['credit_hours'];
    $qualityPoints = (float)$row['quality_points'];
    $row['gpa'] = $creditHours > 0 ? number_format($qualityPoints / $creditHours, 2) : 'N/A';
}
unset($row);

$totalStudents = count($reportRows);
$totalEnrolments = array_sum(array_column($reportRows, 'enrolled_courses'));
$totalGraded = array_sum(array_column($reportRows, 'graded_courses'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
<<<<<<< HEAD
                <a class="nav-item" href="Students.php">Students</a>
=======
                <a class="nav-item" href="Student.php">Students</a>
>>>>>>> 8bb8be0f6af6784b9a17839a449f0ab1a90ef57b
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item active" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>
        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Reports</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin ▼</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <section class="welcome-card">
                    <div>
                        <p class="eyebrow">Academic reporting</p>
                        <h2>Student Performance Report</h2>
                        <p class="muted-text">A summary of enrolments, completed grades, credit hours, and GPA.</p>
                    </div>
                    <div class="report-actions">
                        <button type="button" class="btn btn-primary" onclick="window.print()">Print Report</button>
                    </div>
                </section>

                <section class="report-summary">
                    <article class="stat-card">
                        <p class="stat-label">Students</p>
                        <p class="stat-number"><?php echo htmlspecialchars($totalStudents); ?></p>
                    </article>
                    <article class="stat-card">
                        <p class="stat-label">Enrolments</p>
                        <p class="stat-number"><?php echo htmlspecialchars((string)$totalEnrolments, ENT_QUOTES, 'UTF-8'); ?></p>
                    </article>
                    <article class="stat-card">
                        <p class="stat-label">Graded Courses</p>
                        <p class="stat-number"><?php echo htmlspecialchars((string)$totalGraded, ENT_QUOTES, 'UTF-8'); ?></p>
                    </article>
                </section>
                

                <section class="panel-card">
                    <div class="panel-heading">
                        <div>
                            <h3>Student Performance</h3>
                            <p>Weighted GPA is calculated from graded courses and their credit hours.</p>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Student Number</th>
                                    <th>Student</th>
                                    <th>Programme</th>
                                    <th>Enrolled</th>
                                    <th>Graded</th>
                                    <th>Credit Hours</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reportRows)): ?>
                                    <tr>
                                        <td colspan="7">No student records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reportRows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['student_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['programme'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string)$row['enrolled_courses'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string)$row['graded_courses'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string)$row['credit_hours'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><strong><?= htmlspecialchars($row['gpa'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
