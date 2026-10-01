<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';

$error_message = '';
$success_message = '';

try {
    $db = Database::getInstance()->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {
        $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
        $raw_marks = trim($_POST['marks'] ?? '');

        if ($enrollment_id < 1) {
            throw new InvalidArgumentException('Invalid enrolment selected.');
        }
        if (!is_numeric($raw_marks)) {
            throw new InvalidArgumentException('Marks must be a valid numeric value.');
        }

        $marks = (float)$raw_marks;
        $gradeObj = new Grade($marks); // validates 0–100 and converts

        $columns = $db->query('SHOW COLUMNS FROM enrollments')->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('marks', $columns, true) && in_array('grade', $columns, true)) {
            $sql = 'UPDATE enrollments SET marks = :marks, grade = :grade WHERE id = :id';
            $params = [':marks' => $gradeObj->getMark(), ':grade' => $gradeObj->getLetter(), ':id' => $enrollment_id];
        } elseif (in_array('marks', $columns, true)) {
            $sql = 'UPDATE enrollments SET marks = :marks WHERE id = :id';
            $params = [':marks' => $gradeObj->getMark(), ':id' => $enrollment_id];
        } else {
            $sql = 'UPDATE enrollments SET grade = :grade WHERE id = :id';
            $params = [':grade' => $gradeObj->getLetter(), ':id' => $enrollment_id];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $success_message = 'Grade saved successfully: ' . $gradeObj->getLetter() . ' (' . number_format($gradeObj->getMark(), 1) . '%)';
    }
} catch (InvalidArgumentException $e) {
    $error_message = 'Validation Error: ' . $e->getMessage();
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $error_message = 'A database error occurred while saving the mark.';
}

// Fetch enrolments grouped by student
$grouped = [];
try {
    $db = Database::getInstance()->getConnection();
    $columns = $db->query('SHOW COLUMNS FROM enrollments')->fetchAll(PDO::FETCH_COLUMN);
    $markExpr = in_array('marks', $columns, true) ? 'e.marks' : 'NULL';
    $gradeExpr = in_array('grade', $columns, true) ? 'e.grade' : 'NULL';

    $query = "SELECT e.id AS enrollment_id,
                     {$markExpr} AS marks,
                     {$gradeExpr} AS letter_grade,
                     s.id AS student_id,
                     CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                     s.programme, s.year_of_study, s.student_number,
                     c.course_code, c.course_name, c.credit_hours
              FROM enrollments e
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id
              ORDER BY s.first_name, s.last_name, c.course_code";

    $rows = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $student = new Student(
            $row['full_name'],
            $row['programme'] ?? 'Undeclared',
            (int)($row['year_of_study'] ?? 1)
        );
        $course = new Course(
            $row['course_code'],
            $row['course_name'],
            (int)$row['credit_hours']
        );

        $mark = $row['marks'] !== null && $row['marks'] !== '' ? (float)$row['marks'] : null;
        $enrolment = new Enrolment($student, $course, $mark);

        $displayGrade = $enrolment->getGrade();
        if ($displayGrade === null && !empty($row['letter_grade'])) {
            $displayGrade = $row['letter_grade'];
        }

        $sid = $row['student_id'];
        if (!isset($grouped[$sid])) {
            $grouped[$sid] = [
                'student_number' => $row['student_number'] ?? '',
                'full_name'      => $row['full_name'],
                'programme'      => $row['programme'] ?? '',
                'rows'           => [],
            ];
        }

        $grouped[$sid]['rows'][] = [
            'id'           => (int)$row['enrollment_id'],
            'course_code'  => $course->getCourseCode(),
            'course_name'  => $course->getCourseName(),
            'credit_hours' => $course->getCreditHours(),
            'mark'         => $enrolment->getMark(),
            'grade'        => $displayGrade,
        ];
    }
} catch (Exception $e) {
    error_log('Error retrieving enrollments: ' . $e->getMessage());
    $error_message = $error_message ?: ('Could not load enrolment records: ' . $e->getMessage());
}

$username = $_SESSION['user']['username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links" aria-label="Sidebar navigation">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item active" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Grades</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <div class="welcome-card">
                    <div>
                        <h2>Record &amp; manage marks</h2>
                        <p>Marks must be 0–100. Letter grades use the scale: A, B+, B, C+, C, D, F.</p>
                    </div>
                </div>

                <?php if ($success_message): ?>
                    <div class="panel-card" style="border-left: 4px solid #0d9488; margin-bottom: 1rem;">
                        <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="panel-card" style="border-left: 4px solid #dc2626; margin-bottom: 1rem;">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($grouped)): ?>
                    <section class="panel-card">
                        <p>No enrolments found. Enrol students in courses first.</p>
                    </section>
                <?php else: ?>
                    <?php foreach ($grouped as $sid => $group): ?>
                        <section class="panel-card" style="margin-bottom: 1.25rem;">
                            <div class="panel-heading">
                                <h3><?= htmlspecialchars($group['full_name']) ?>
                                    <small>(<?= htmlspecialchars($group['student_number']) ?>)</small>
                                </h3>
                                <p><?= htmlspecialchars($group['programme'] ?: 'Programme not set') ?></p>
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Credits</th>
                                            <th>Current Mark</th>
                                            <th>Grade</th>
                                            <th>Update Mark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($group['rows'] as $row): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['course_code']) ?></strong><br>
                                                    <span style="color:#64748b;font-size:0.9em;"><?= htmlspecialchars($row['course_name']) ?></span>
                                                </td>
                                                <td><?= (int)$row['credit_hours'] ?></td>
                                                <td><?= $row['mark'] !== null ? number_format((float)$row['mark'], 1) : '—' ?></td>
                                                <td><strong><?= htmlspecialchars($row['grade'] ?? '—') ?></strong></td>
                                                <td>
                                                    <form method="post" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                                        <input type="hidden" name="enrollment_id" value="<?= (int)$row['id'] ?>">
                                                        <input type="number" name="marks" min="0" max="100" step="0.1"
                                                               class="form-control" style="width:90px;"
                                                               value="<?= $row['mark'] !== null ? htmlspecialchars((string)$row['mark']) : '' ?>"
                                                               placeholder="0–100" required>
                                                        <button type="submit" name="save_grade" value="1" class="btn btn-primary">Save</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
