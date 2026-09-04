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

$username = $_SESSION['user']['username'] ?? 'Administrator';
$database = Database::getInstance();
$db = $database->getConnection();

// Fetch students with joined course and grade details
$students = $db->query("
    SELECT 
        s.id AS student_no,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        c.course_name,
        e.grade
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id
    LEFT JOIN courses c ON e.course_id = c.id
    ORDER BY s.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Student Management System</title>
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
                    <h1>Students</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content" aria-label="Students management view">
                <div class="welcome-card">
                    <div>
                        <h2>Manage student records</h2>
                    </div>
                    <div class="action-buttons">
                        <a href="add_student.php" class="btn btn-primary">+ Add Student</a>
                    </div>
                </div>

                <section class="panel-card">
                    <div class="panel-heading">
                        <h3>Student Records</h3>
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
                                        <td colspan="4">No student records found.</td>
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
