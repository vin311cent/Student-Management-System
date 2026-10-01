<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';
$db = Database::getInstance()->getConnection();
$errors = [];
$success = '';

// Ensure programs table exists (safe no-op if already present)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        program_name VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS program_courses (
        program_id INT NOT NULL,
        course_id INT NOT NULL,
        PRIMARY KEY (program_id, course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("INSERT IGNORE INTO programs (program_name) VALUES
        ('Computer Science'), ('Law'), ('Social Work'), ('Business'),
        ('Bachelor of Computer Science'), ('Bachelor of Information Technology')");
} catch (PDOException $e) {
    // ignore if privileges limited
}

$programs = [];
try {
    $programs = $db->query('SELECT id, program_name FROM programs ORDER BY program_name')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $programs = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $credits = (int)($_POST['credit_hours'] ?? 0);
    $programId = (int)($_POST['program_id'] ?? 0);
    try {
        $course = new Course($code, $name, $credits > 0 ? $credits : 3);
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT INTO courses (course_code, course_name, credit_hours) VALUES (?, ?, ?)');
        $stmt->execute([$course->getCourseCode(), $course->getCourseName(), $course->getCreditHours()]);
        $courseId = (int)$db->lastInsertId();
        if ($programId > 0) {
            $link = $db->prepare('INSERT IGNORE INTO program_courses (program_id, course_id) VALUES (?, ?)');
            $link->execute([$programId, $courseId]);
        }
        $db->commit();
        $success = 'Course added successfully.';
    } catch (InvalidArgumentException $e) {
        if ($db->inTransaction()) $db->rollBack();
        $errors[] = $e->getMessage();
    } catch (PDOException $e) {
        if ($db->inTransaction()) $db->rollBack();
        $errors[] = $e->getCode() == '23000' ? 'That course code already exists.' : 'Database error while adding course.';
    }
}

try {
    $courses = $db->query("
        SELECT c.id, c.course_code, c.course_name, c.credit_hours,
               GROUP_CONCAT(p.program_name ORDER BY p.program_name SEPARATOR ', ') AS programs
        FROM courses c
        LEFT JOIN program_courses pc ON pc.course_id = c.id
        LEFT JOIN programs p ON p.id = pc.program_id
        GROUP BY c.id, c.course_code, c.course_name, c.credit_hours
        ORDER BY c.course_code
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $courses = $db->query("SELECT id, course_code, course_name, credit_hours, NULL AS programs FROM courses ORDER BY course_code")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <section class="welcome-card">
                    <div>
                        <p class="eyebrow">Course management</p>
                        <h2>Add a new course</h2>
                        <p>Course code, name and credit hours are validated by the Course class (1–12 credits).</p>
                    </div>
                </section>

                <?php if ($success): ?>
                    <div class="panel-card" style="border-left:4px solid #0d9488;margin-bottom:1rem;"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php foreach ($errors as $err): ?>
                    <div class="panel-card" style="border-left:4px solid #dc2626;margin-bottom:1rem;"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>

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
                            <input type="text" id="course_code" name="course_code" placeholder="e.g. CSC101" required>
                        </div>
                        <div class="form-group">
                            <label for="course_name">Course Name</label>
                            <input type="text" id="course_name" name="course_name" placeholder="e.g. Programming Fundamentals" required>
                        </div>
                        <div class="form-group">
                            <label for="credit_hours">Credit Hours</label>
                            <input type="number" id="credit_hours" name="credit_hours" min="1" max="12" value="3" required>
                        </div>
                        <?php if (!empty($programs)): ?>
                        <div class="form-group">
                            <label for="program_id">Programme (optional)</label>
                            <select id="program_id" name="program_id">
                                <option value="0">— None —</option>
                                <?php foreach ($programs as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['program_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <button type="submit" name="add_course" value="1" class="btn btn-primary">Add Course</button>
                    </form>
                </section>

                <section class="panel-card">
                    <div class="panel-heading"><h3>All Courses</h3></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Credits</th>
                                    <th>Programmes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($courses)): ?>
                                    <?php foreach ($courses as $c): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($c['course_code']) ?></td>
                                            <td><?= htmlspecialchars($c['course_name']) ?></td>
                                            <td><?= (int)$c['credit_hours'] ?></td>
                                            <td><?= htmlspecialchars($c['programs'] ?? '—') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No courses found.</td></tr>
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
