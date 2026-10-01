<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';

$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $programme   = trim($_POST['programme'] ?? '');
    $year_of_study = (int)($_POST['year_of_study'] ?? 1);

    if ($first_name === '' || $last_name === '') {
        $message = "Error: Missing mandatory student name fields.";
        $messageClass = "error";
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            $currentYear = date('Y');

            // Prefer LGU-YYYY-NNN format (assignment style)
            $query = "SELECT student_number FROM students WHERE student_number LIKE :yearPattern ORDER BY student_number DESC LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([':yearPattern' => 'LGU-' . $currentYear . '-%']);
            $lastStudent = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lastStudent) {
                $parts = explode('-', $lastStudent['student_number']);
                $lastSequence = (int)end($parts);
                $nextSequence = $lastSequence + 1;
            } else {
                $nextSequence = 1;
            }

            $student_num = sprintf('LGU-%s-%03d', $currentYear, $nextSequence);

            if ($year_of_study < 1 || $year_of_study > 6) {
                $year_of_study = 1;
            }

            $sql = "INSERT INTO students (student_number, first_name, last_name, programme, year_of_study) 
                    VALUES (:student_num, :first_name, :last_name, :programme, :year)";
            $insertStmt = $db->prepare($sql);
            $insertStmt->execute([
                ':student_num' => $student_num,
                ':first_name'  => $first_name,
                ':last_name'   => $last_name,
                ':programme'   => $programme !== '' ? $programme : null,
                ':year'        => $year_of_study
            ]);

            header("Location: Student.php?added=1");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Database Error: Unique constraint violation. Please retry.";
            } else {
                $message = "System Error: " . $e->getMessage();
            }
            $messageClass = "error";
        }
    }
}

$programmes = ['Computer Science', 'Bachelor of Computer Science', 'Bachelor of Information Technology', 'Law', 'Social Work', 'Business'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links">
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
                    <h1>Add Student</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>
            <section class="dashboard-content">
                <section class="panel-card" style="max-width:520px;">
                    <div class="panel-heading"><h3>New student record</h3></div>
                    <?php if ($message): ?>
                        <p class="<?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></p>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="programme">Programme</label>
                            <select id="programme" name="programme">
                                <option value="">— Select —</option>
                                <?php foreach ($programmes as $p): ?>
                                    <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_of_study">Year of study</label>
                            <input type="number" id="year_of_study" name="year_of_study" min="1" max="6" value="1">
                        </div>
                        <button type="submit" class="btn btn-primary">Save student</button>
                        <a href="Student.php" class="btn">Cancel</a>
                    </form>
                    <p style="margin-top:1rem;color:#64748b;font-size:0.9rem;">Student number is auto-generated as LGU-YYYY-NNN.</p>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
