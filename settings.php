<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: Login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Student Management System</title>
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
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item active" href="Settings.php">Settings</a>
            </nav>

        </aside>


        <main class="main-panel">

            <!-- Topbar -->
            <header class="topbar">

                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Settings</h1>
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
                        <p class="eyebrow">System configuration</p>

                        <h2>System Settings</h2>

                        <p>
                            Manage your account and system access.
                        </p>
                    </div>

                </section>


                <!-- Account Information -->
                <section class="panel-card settings-card">

                    <div class="panel-heading">

                        <div>
                            <h3>Account Information</h3>

                            <p>
                                Information about the currently logged-in administrator.
                            </p>
                        </div>

                    </div>


                    <div class="setting-row">

                        <div>
                            <span class="setting-label">Logged in as</span>

                            <strong class="setting-value">
                                <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Administrator') ?>
                            </strong>
                        </div>

                    </div>

                </section>


                <!-- Account Actions -->
                <section class="panel-card settings-card">

                    <div class="panel-heading">

                        <div>
                            <h3>Account Actions</h3>

                            <p>
                                Sign out of the administrator account.
                            </p>
                        </div>

                    </div>


                    <div class="settings-action">

                        <a
                            href="Login.php?logout=1"
                            class="btn btn-primary"
                        >
                            Logout
                        </a>

                    </div>

                </section>

            </section>

        </main>

    </div>

</body>
</html>