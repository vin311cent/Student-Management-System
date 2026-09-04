
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/Database.php';

$database = Database::getInstance();
$db = $database->getConnection();

/*
|--------------------------------------------------------------------------
| Academic Summary + GPA
|--------------------------------------------------------------------------
|
| GPA is calculated using:
|
| GPA = Sum(Grade Point × Credit Hours) / Sum(Credit Hours)
|
| Grade points:
| A = 4.0
| B = 3.0
| C = 2.0
| D = 1.0
| F = 0.0
|
|--------------------------------------------------------------------------
*/

$summaries = $db->query("
    SELECT
        s.id,
        s.student_number,
        CONCAT(s.first_name, ' ', s.last_name) AS name,

        COUNT(e.id) AS total_courses,

        SUM(
            CASE
                WHEN e.grade IS NOT NULL AND e.grade != ''
                THEN 1
                ELSE 0
            END
        ) AS graded_courses,

        SUM(
            CASE
                WHEN e.grade IS NOT NULL
                     AND e.grade != ''
                     AND c.credit_hours IS NOT NULL
                THEN c.credit_hours
                ELSE 0
            END
        ) AS total_credit_hours,

        SUM(
            CASE
                WHEN e.grade = 'A' THEN 4.0 * c.credit_hours
                WHEN e.grade = 'B' THEN 3.0 * c.credit_hours
                WHEN e.grade = 'C' THEN 2.0 * c.credit_hours
                WHEN e.grade = 'D' THEN 1.0 * c.credit_hours
                WHEN e.grade = 'F' THEN 0.0 * c.credit_hours
                ELSE 0
            END
        ) AS total_quality_points

    FROM students s

    LEFT JOIN enrollments e
        ON s.id = e.student_id

    LEFT JOIN courses c
        ON e.course_id = c.id

    GROUP BY
        s.id,
        s.student_number,
        s.first_name,
        s.last_name

    ORDER BY
        s.last_name,
        s.first_name
")->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Calculate GPA for each student
|--------------------------------------------------------------------------
*/

foreach ($summaries as &$sum) {

    $creditHours = (float)($sum['total_credit_hours'] ?? 0);
    $qualityPoints = (float)($sum['total_quality_points'] ?? 0);

    if ($creditHours > 0) {
        $sum['gpa'] = round(
            $qualityPoints / $creditHours,
            2
        );
    } else {
        $sum['gpa'] = null;
    }
}

unset($sum);

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Academic Summary | Student Management System
    </title>

    <link rel="stylesheet" href="style.css">

    <style>

        .gpa {
            font-weight: bold;
            font-size: 16px;
        }

        .no-gpa {
            color: #777;
        }

    </style>

</head>

<body>

<div class="admin-shell">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">
            COLLEGE ADMIN
        </div>

        <nav class="nav-links">

            <a class="nav-item"
               href="dashboard.php">
                Dashboard
            </a>

            <a class="nav-item"
               href="Student.php">
                Students
            </a>

            <a class="nav-item"
               href="Courses.php">
                Courses
            </a>

            <a class="nav-item"
               href="Enrolment.php">
                Enrolment
            </a>

            <a class="nav-item"
               href="Grades.php">
                Grades
            </a>

            <a class="nav-item active"
               href="AcademicSummary.php">
                Academic Summary
            </a>

            <a class="nav-item"
               href="Reports.php">
                Reports
            </a>

            <a class="nav-item"
               href="Settings.php">
                Settings
            </a>

        </nav>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-panel">

        <h2>
            Academic Summary
        </h2>


        <table>

            <thead>

                <tr>

                    <th>STUDENT NO</th>

                    <th>NAME</th>

                    <th>ENROLLED COURSES</th>

                    <th>GRADED COURSES</th>

                    <th>TOTAL CREDITS</th>

                    <th>GPA</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($summaries)): ?>

                    <tr>

                        <td colspan="6">
                            No students found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($summaries as $sum): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $sum['student_number'] ?? ''
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $sum['name'] ?? ''
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $sum['total_courses']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $sum['graded_courses']
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $sum['total_credit_hours']
                                ) ?>
                            </td>


                            <td>

                                <?php if ($sum['gpa'] !== null): ?>

                                    <span class="gpa">

                                        <?= number_format(
                                            $sum['gpa'],
                                            2
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span class="no-gpa">
                                        N/A
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </main>

</div>

</body>

</html>

