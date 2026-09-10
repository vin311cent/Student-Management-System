<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

if (($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/Database.php';

$message = $_SESSION['flash_message'] ?? 'You are logged in successfully.';
unset($_SESSION['flash_message']);

$username = $_SESSION['user']['username'] ?? 'Administrator';
date_default_timezone_set('Africa/Lusaka');
$dateLabel = date('l, F j, Y');

$database = Database::getInstance();
$db = $database->getConnection();

// Fetch summary counts
$totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses  = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEnrol    = $db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

// Fetch recent 5 students with course and grade details
$students = $db->query("
    SELECT 
        s.student_number AS student_no,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        c.course_name,
        e.grade
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id
    LEFT JOIN courses c ON e.course_id = c.id
    ORDER BY s.id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links" aria-label="Sidebar navigation">
                <a class="nav-item active" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
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
                    <h1>Dashboard</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content" aria-label="Administrator dashboard content">
                <section class="welcome-card">
                    <div>
                        <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
                        <p>Overview of the student records system — <?php echo htmlspecialchars($dateLabel); ?></p>
                    </div>
                </section>

                <section class="stats-grid" aria-label="Administrative overview">
                    <article class="stat-card stat-students">
                        <p class="stat-label">Students</p>
                        <p class="stat-number"><?php echo htmlspecialchars($totalStudents); ?></p>
                        <p class="stat-description">Total registered students</p>
                    </article>
                    <article class="stat-card stat-courses">
                        <p class="stat-label">Courses</p>
                        <p class="stat-number"><?php echo htmlspecialchars($totalCourses); ?></p>
                        <p class="stat-description">Available courses</p>
                    </article>
                    <article class="stat-card stat-enrolment">
                        <p class="stat-label">Enrolment</p>
                        <p class="stat-number"><?php echo htmlspecialchars($totalEnrol); ?></p>
                        <p class="stat-description">Current enrolments</p>
                    </article>
                </section>

                <section class="panel-card">
                    <div class="panel-heading">
                        <h3>Recent Students</h3>
                        <a href="Student.php">View all</a>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students)): ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['student_no'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['student_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['course_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($student['grade'] ?? 'Pending'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No recent students found.</td>
                                    </tr>
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
