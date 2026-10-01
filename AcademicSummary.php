<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';

$database = Database::getInstance();
$db = $database->getConnection();

/*
|--------------------------------------------------------------------------
| Academic Summary + weighted GPA
| GPA = Sum(Grade Point × Credit Hours) / Sum(Credit Hours)
| Scale: A=4.0, B+=3.5, B=3.0, C+=2.5, C=2.0, D=1.0, F=0.0
|--------------------------------------------------------------------------
*/

$summaries = $db->query("
  SELECT 
    s.id,
    s.student_number AS student_no,
    CONCAT(s.first_name, ' ', s.last_name) AS name,
    s.programme,
    COUNT(e.id) AS total_courses,
    SUM(CASE WHEN e.grade IS NOT NULL OR e.marks IS NOT NULL THEN 1 ELSE 0 END) AS graded_courses
  FROM students s
  LEFT JOIN enrollments e ON s.id = e.student_id
  GROUP BY s.id, s.student_number, s.first_name, s.last_name, s.programme
  ORDER BY s.student_number
")->fetchAll(PDO::FETCH_ASSOC);

$columns = $db->query('SHOW COLUMNS FROM enrollments')->fetchAll(PDO::FETCH_COLUMN);
$hasMarks = in_array('marks', $columns, true);

foreach ($summaries as &$sum) {
    $markSelect = $hasMarks ? 'e.marks' : 'NULL';
    $stmt = $db->prepare("
        SELECT c.course_code, c.course_name, c.credit_hours, {$markSelect} AS mark, e.grade AS letter_grade
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.student_id = ?
    ");
    $stmt->execute([$sum['id']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $student = new Student($sum['name'], $sum['programme'] ?? 'N/A', 1);
    foreach ($rows as $row) {
        $course = new Course($row['course_code'], $row['course_name'], (int)$row['credit_hours']);
        $student->enrol($course);
        if ($row['mark'] !== null && $row['mark'] !== '') {
            $student->recordMark($row['course_code'], (float)$row['mark']);
        }
    }
    $sum['gpa'] = $student->calculateGpa();
}
unset($sum);

$username = $_SESSION['user']['username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Summary | Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .gpa { 
        font-weight: bold; 
        font-size: 16px; 
    }
    .no-gpa { 
        color: #777; 
    }
    .text-link{
        color:#2563eb;
        text-decoration:none;
        font-weight:700
    }

    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item active" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Academic Summary</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <div class="welcome-card">
                    <div>
                        <h2>Student academic progress</h2>
                        <p>Weighted GPA uses credit hours and the A / B+ / B / C+ / C / D / F scale.</p>
                    </div>
                </div>

                <section class="panel-card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Name</th>
                                    <th>Programme</th>
                                    <th>Enrolled</th>
                                    <th>Graded</th>
                                    <th>GPA</th>
                                    <th>Transcript</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($summaries)): ?>
                                    <?php foreach ($summaries as $sum): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($sum['student_no'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($sum['name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($sum['programme'] ?? 'N/A') ?></td>
                                            <td><?= (int)($sum['total_courses'] ?? 0) ?></td>
                                            <td><?= (int)($sum['graded_courses'] ?? 0) ?></td>
                                            <td>
                                                <?php if ($sum['gpa'] !== null): ?>
                                                    <span class="gpa"><?= number_format($sum['gpa'], 2) ?></span>
                                                <?php else: ?>
                                                    <span class="no-gpa">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a class="text-link" href="Transcript.php?id=<?= urlencode((string)$sum['id']) ?>">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7">No students found.</td></tr>
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
