
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
    s.student_number AS student_no,
    CONCAT(s.first_name, ' ', s.last_name) AS name,
    COUNT(e.id) AS total_courses,
    SUM(CASE WHEN e.grade IS NOT NULL THEN 1 ELSE 0 END) AS graded_courses
  FROM students s
  LEFT JOIN enrollments e ON s.id = e.student_id
  GROUP BY s.id
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Summary | Student Management System</title>
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

        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>

            <nav class="nav-links">
                <a class="nav-item" href="dashboard.php">Dashboard</a>
                <a class="nav-item" href="Student.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item active" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item" href="Settings.php">Settings</a>
            </nav>
        </aside>


        <main class="main-panel">

            <!-- Topbar -->
            <header class="topbar">

                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Academic Summary</h1>
                </div>

                <div class="topbar-actions">
                    <span class="topbar-pill">Admin ▼</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>

            </header>


            <section class="dashboard-content">

                <!-- Introduction -->
                <section class="welcome-card">

                    <div>
                        <p class="eyebrow">Academic overview</p>

                        <h2>Student Academic Summary</h2>

                        <p>
                            View enrolment and grading progress for each student.
                        </p>
                    </div>

                </section>


                <!-- Summary Table -->
                <section class="panel-card">

                    <div class="panel-heading">

                        <div>
                            <h3>Academic Records</h3>

                            <p>
                                Overview of enrolled and graded courses.
                            </p>
                        </div>

                    </div>


                    <div class="table-wrap">

                        <table class="summary-table">

                            <thead>
                                <tr>
                                    <th>STUDENT</th>
                                    <th>NAME</th>
                                    <th>ENROLLED COURSES</th>
                                    <th>GRADED COURSES</th>
                                </tr>
                            </thead>


                            <tbody>

                                <?php foreach ($summaries as $sum): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($sum['student_no'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($sum['name'] ?? '') ?>
                                    </td>

                                    <td>
                                        <span class="summary-number">
                                            <?= htmlspecialchars($sum['total_courses']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="summary-number">
                                            <?= htmlspecialchars($sum['graded_courses']) ?>
                                        </span>
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

