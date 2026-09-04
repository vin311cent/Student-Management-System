<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/Database.php';
$database = Database::getInstance();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
  $code = trim($_POST['course_code'] ?? '');
$name = trim($_POST['course_name'] ?? '');

if ($code && $name) {
    $db = $database->getConnection();
    $stmt = $db->prepare("INSERT INTO courses (course_code, course_name) VALUES (?, ?)");
    $stmt->execute([$code, $name]);
}
    }


// Fetch courses
$db = $database->getConnection();
$courses = $db->query("SELECT * FROM courses ORDER BY course_code")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
                <a class="nav-item active" href="Courses.php">Courses</a>
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
            <h1>Courses</h1>
        </div>

        <div class="topbar-actions">
            <span class="topbar-pill">Admin ▼</span>
            <a class="logout-link" href="Login.php?logout=1">Logout</a>
        </div>
    </header>

    <section class="dashboard-content">

        <!-- Add Course Section -->
        <section class="welcome-card">
            <div>
                <p class="eyebrow">Course management</p>
                <h2>Add a new course</h2>
                <p>Enter the course details below to add it to the system.</p>
            </div>
        </section>

        <section class="panel-card course-form-card">

            <div class="panel-heading">
                <div>
                    <h3>Course Information</h3>
                    <p>Fill in the details for the new course.</p>
                </div>
            </div>

            <form method="POST" class="course-form">

                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input
                        type="text"
                        id="course_code"
                        name="course_code"
                        placeholder="e.g. CS101"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course_name">Course Name</label>
                    <input
                        type="text"
                        id="course_name"
                        name="course_name"
                        placeholder="e.g. Introduction to Programming"
                        required
                    >
                </div>

                <div class="form-action">
                    <button type="submit" name="add_course" class="btn btn-primary">
                        + Add Course
                    </button>
                </div>

            </form>

        </section>


        <!-- Course Records -->
        <section class="panel-card">

            <div class="panel-heading">
                <div>
                    <h3>Course Records</h3>
                    <p>Courses currently registered in the system.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>CODE</th>
                            <th>COURSE NAME</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($courses as $c): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($c['course_code'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($c['course_name'] ?? '') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </section>

    </section>

</main>