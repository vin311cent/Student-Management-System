<?php
session_start();
if (!isset($_SESSION['user'])) { 
    header('Location: Login.php'); 
    exit; 
}

// Load classes automatically using your autoloader
require_once __DIR__ . '/src/autoload.php';

$error_message = '';
$success_message = '';

try {
    $db = Database::getInstance()->getConnection();

    // 1. PROCESS POST FORM SUBMISSION
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {
        $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
        $raw_marks = trim($_POST['marks'] ?? '');

        if (!is_numeric($raw_marks)) {
            throw new InvalidArgumentException("Marks must be a valid numeric value.");
        }

        $marks = (float)$raw_marks;

        // Domain-level validation using the Grade class
        $gradeObj = new Grade($marks);

        // Update database record securely
        $sql = "UPDATE enrollments SET marks = :marks WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':marks' => $gradeObj->getMark(),
            ':id'    => $enrollment_id
        ]);

        $success_message = "Grade updated successfully!";
    }

} catch (InvalidArgumentException $e) {
    $error_message = "Validation Error: " . $e->getMessage();
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "A database error occurred while updating the mark.";
}

// 2. FETCH ENROLLMENTS & BUILD DOMAIN OBJECTS
$enrollmentsList = [];

try {
    $db = Database::getInstance()->getConnection();
    
    // Concatenate first_name and last_name from the students table
    $query = "SELECT e.id AS enrollment_id, e.marks, 
                     s.id AS student_id, CONCAT(s.first_name, ' ', s.last_name) AS full_name, 
                     s.programme, s.year_of_study, s.student_number,
                     c.id AS course_id, c.course_code, c.course_name, c.credit_hours
              FROM enrollments e
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id
              ORDER BY full_name ASC";

    $stmt = $db->query($query);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map DB rows into Domain Objects
    foreach ($rows as $row) {
        $student = new Student(
            $row['full_name'],
            $row['programme'],
            (int)$row['year_of_study']
        );

        $course = new Course(
            $row['course_code'], 
            $row['course_name'], 
            (int)$row['credit_hours']
        );

        $mark = $row['marks'] !== null ? (float)$row['marks'] : null;
        
        $enrolment = new Enrolment($course, $mark);

        $enrollmentsList[] = [
            'id'           => $row['enrollment_id'],
            'student_name' => $student->getFullName(),
            'course_name'  => $course->getCourseName(),
            'course_code'  => $course->getCourseCode(),
            'mark'         => $enrolment->getMark(),
            'grade'        => $enrolment->getGrade(),
            'grade_point'  => $enrolment->getGradePoint()
        ];
    }
} catch (Exception $e) {
    error_log("Error retrieving enrollments: " . $e->getMessage());
    $error_message = "Could not load enrollment records: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades | Student Management System</title>
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
                <a class="nav-item" href="Enrolments.php">Enrolment</a>
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
                    <span class="topbar-pill">Admin ▼</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <section class="welcome-card">
                    <div>
                        <p class="eyebrow">Academic management</p>
                        <h2>Manage student grades</h2>
                        <p>View current grades and assign grades to enrolled students.</p>
                    </div>
                </section>

                <h2>Grade Management</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert-error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>

                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>STUDENT</th>
                            <th>COURSE</th>
                            <th>CURRENT MARKS</th>
                            <th>CURRENT GRADE</th>
                            <th>ENTER MARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($enrollmentsList)): ?>
                            <?php foreach ($enrollmentsList as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['student_name']) ?></td>
                                <td><?= htmlspecialchars($e['course_code'] . ' - ' . $e['course_name']) ?></td>
                                <td><?= $e['mark'] !== null ? htmlspecialchars(number_format($e['mark'], 1)) . '%' : 'N/A' ?></td>
                                <td><strong><?= htmlspecialchars($e['grade']) ?></strong></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="enrollment_id" value="<?= $e['id'] ?>">
                                        <input type="number" step="0.1" min="0" max="100" name="marks" placeholder="0 - 100" style="width: 80px;" required>
                                        <button type="submit" name="save_grade">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No enrollment records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>