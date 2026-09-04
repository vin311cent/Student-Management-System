<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: Login.php'); exit; }

require_once __DIR__ . '/src/Database.php';
$database = Database::getInstance();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $student_id = $_POST['student_id'] ?? null;
    $course_id = $_POST['course_id'] ?? null;
    if ($student_id && $course_id) {
        $stmt = $db->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
        $stmt->execute([$student_id, $course_id]);
    }
}

$students = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM students")->fetchAll(PDO::FETCH_ASSOC);
$courses = $db->query("SELECT id, course_name FROM courses")->fetchAll(PDO::FETCH_ASSOC);
$enrollments = $db->query("
    SELECT 
        e.id, 
        CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
        c.course_name 
    FROM enrollments e 
    JOIN students s ON e.student_id = s.id 
    JOIN courses c ON e.course_id = c.id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolment | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="admin-shell">

        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>

            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item active" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>


        <main class="main-panel">

            <!-- Topbar -->
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Enrolment</h1>
                </div>

                <div class="topbar-actions">
                    <span class="topbar-pill">Admin ▼</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>


            <section class="dashboard-content">

                <!-- Enrolment Introduction -->
                <section class="welcome-card">
                    <div>
                        <p class="eyebrow">Student management</p>
                        <h2>Enrol a student</h2>
                        <p>
                            Select a student and course to create a new enrolment.
                        </p>
                    </div>
                </section>


                <!-- Enrolment Form -->
                <section class="panel-card enrolment-form-card">

                    <div class="panel-heading">
                        <div>
                            <h3>Enrolment Information</h3>
                            <p>Choose the student and course below.</p>
                        </div>
                    </div>

                    <form method="POST" class="enrolment-form">

                        <div class="form-group">
                            <label for="student_id">Student</label>

                            <select name="student_id" id="student_id" required>
                                <option value="">Select Student</option>

                                <?php foreach ($students as $s): ?>
                                    <option value="<?= $s['id'] ?>">
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>


                        <div class="form-group">
                            <label for="course_id">Course</label>

                            <select name="course_id" id="course_id" required>
                                <option value="">Select Course</option>

                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['course_name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>


                        <div class="form-action">
                            <button
                                type="submit"
                                name="enroll"
                                class="btn btn-primary"
                            >
                                Enroll Student
                            </button>
                        </div>

                    </form>

                </section>


                <!-- Current Enrolments -->
                <section class="panel-card">

                    <div class="panel-heading">
                        <div>
                            <h3>Current Enrolments</h3>
                            <p>Students currently enrolled in courses.</p>
                        </div>
                    </div>


                    <div class="table-wrap">

                        <table>

                            <thead>
                                <tr>
                                    <th>STUDENT</th>
                                    <th>ENROLLED COURSE</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($enrollments as $e): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($e['student_name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($e['course_name']) ?>
                                    </td>
                                </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </section>

            </section>

        </main>

    </div>
</body>
</html>