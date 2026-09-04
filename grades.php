<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Grades.php';

$database = Database::getInstance();
$db = $database->getConnection();

$message = '';
$error = '';

/*
|--------------------------------------------------------------------------
| Save Marks
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {

    $enrollment_id = $_POST['enrollment_id'] ?? '';
    $marks = $_POST['marks'] ?? '';

    try {

        // Validate enrollment ID
        if (!filter_var($enrollment_id, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException(
                "Invalid enrolment ID."
            );
        }

        // Convert marks to grade
        $grade = Grade::convert($marks);

        // Convert marks to numeric value
        $marks = (float)$marks;

        // Update both marks and grade
        $stmt = $db->prepare("
            UPDATE enrollments
            SET marks = ?, grade = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $marks,
            $grade,
            $enrollment_id
        ]);

        $message = "Marks saved successfully. Grade assigned: " . $grade;

    } catch (InvalidArgumentException $e) {

        $error = $e->getMessage();

    } catch (PDOException $e) {

        $error = "A database error occurred. Please try again.";

    } catch (Exception $e) {

        $error = "An unexpected error occurred.";
    }
}


/*
|--------------------------------------------------------------------------
| Retrieve Enrolments
|--------------------------------------------------------------------------
*/

$enrollments = $db->query("
    SELECT 
        e.id,
        e.marks,
        e.grade,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        c.course_name,
        c.credit_hours
    FROM enrollments e
    JOIN students s ON e.student_id = s.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY student_name, c.course_name
")->fetchAll(PDO::FETCH_ASSOC);

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

        <div class="brand">
            COLLEGE ADMIN
        </div>

        <nav class="nav-links">

            <a class="nav-item" href="dashboard.php">
                Dashboard
            </a>

            <a class="nav-item" href="Student.php">
                Students
            </a>

            <a class="nav-item" href="Courses.php">
                Courses
            </a>

            <a class="nav-item" href="Enrolment.php">
                Enrolment
            </a>

            <a class="nav-item active" href="Grades.php">
                Grades
            </a>

            <a class="nav-item" href="AcademicSummary.php">
                Academic Summary
            </a>

            <a class="nav-item" href="Reports.php">
                Reports
            </a>

            <a class="nav-item" href="Settings.php">
                Settings
            </a>

        </nav>

    </aside>


    <main class="main-panel">

        <h2>Grade Management</h2>


        <?php if ($message): ?>

            <div class="success-message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <table>

            <thead>

                <tr>

                    <th>STUDENT</th>

                    <th>COURSE</th>

                    <th>CREDIT HOURS</th>

                    <th>MARKS</th>

                    <th>CURRENT GRADE</th>

                    <th>ASSIGN MARKS</th>

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

                        <td>
                            <?= htmlspecialchars($e['credit_hours']) ?>
                        </td>

                        <td>
                            <?= $e['marks'] !== null
                                ? htmlspecialchars($e['marks'])
                                : 'N/A'
                            ?>
                        </td>

                        <td>

                            <?php if ($e['grade']): ?>

                                <strong>
                                    <?= htmlspecialchars($e['grade']) ?>
                                </strong>

                            <?php else: ?>

                                N/A

                            <?php endif; ?>

                        </td>

                        <td>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="enrollment_id"
                                    value="<?= htmlspecialchars($e['id']) ?>"
                                >

                                <input
                                    type="number"
                                    name="marks"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="0 - 100"
                                    required
                                >

                                <button
                                    type="submit"
                                    name="save_grade"
                                >
                                    Save
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </main>

</div>

</body>

</html>