<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';
$db = Database::getInstance()->getConnection();

$message = '';
$messageClass = '';

// Add programme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_program'])) {
    $name = trim($_POST['program_name'] ?? '');
    try {
        if ($name === '') {
            throw new InvalidArgumentException('Programme name cannot be empty.');
        }
        $stmt = $db->prepare('INSERT INTO programs (program_name) VALUES (?)');
        $stmt->execute([$name]);
        $message = 'Programme added successfully.';
        $messageClass = 'success';
    } catch (InvalidArgumentException $e) {
        $message = $e->getMessage();
        $messageClass = 'error';
    } catch (PDOException $e) {
        $message = ($e->getCode() == 23000)
            ? 'That programme already exists.'
            : 'Database error: ' . $e->getMessage();
        $messageClass = 'error';
    }
}

// Delete programme (removes it from dropdowns; existing student records keep the text)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_program'])) {
    $id = (int)($_POST['program_id'] ?? 0);
    try {
        if ($id < 1) {
            throw new InvalidArgumentException('Invalid programme selected.');
        }
        $stmt = $db->prepare('DELETE FROM programs WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() < 1) {
            throw new InvalidArgumentException('Programme not found or already deleted.');
        }
        $message = 'Programme deleted. It will no longer appear when registering new students.';
        $messageClass = 'success';
    } catch (InvalidArgumentException $e) {
        $message = $e->getMessage();
        $messageClass = 'error';
    } catch (PDOException $e) {
        $message = 'Could not delete programme: ' . $e->getMessage();
        $messageClass = 'error';
    }
}

$programs = $db->query('SELECT id, program_name FROM programs ORDER BY program_name')->fetchAll(PDO::FETCH_ASSOC);
$username = $_SESSION['user']['username'] ?? 'Administrator';
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
                <a class="nav-item" href="Students.php">Students</a>
                <a class="nav-item" href="Courses.php">Courses</a>
                <a class="nav-item" href="Enrolment.php">Enrolment</a>
                <a class="nav-item" href="Grades.php">Grades</a>
                <a class="nav-item" href="AcademicSummary.php">Academic Summary</a>
                <a class="nav-item" href="Reports.php">Reports</a>
                <a class="nav-item active" href="Settings.php">Settings</a>
            </nav>

        </aside>



        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1>Settings</h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="Login.php?logout=1">Logout</a>
                </div>
            </header>

            <section class="dashboard-content">
                <section class="welcome-card">
                    <div>
                        <p class="eyebrow">System configuration</p>
                        <h2>System Settings</h2>
                        <p>Manage programmes and account access.</p>
                    </div>
                </section>

                <?php if ($message !== ''): ?>
                    <div class="alert <?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <section class="panel-card settings-card">
                    <div class="panel-heading">
                        <div>
                            <h3>Account Information</h3>
                            <p>Currently logged-in administrator.</p>
                        </div>
                    </div>
                    <p>Logged in as: <strong><?= htmlspecialchars($username) ?></strong></p>
                    <p><a href="Login.php?logout=1" style="color:#dc2626;">Logout</a></p>
                </section>

                <section class="panel-card settings-card">
                    <div class="panel-heading">
                        <div>
                            <h3>Programmes</h3>
                            <p>These appear in the student registration form. Deleting a programme removes it from the dropdown only; existing student records keep their programme name.</p>
                        </div>
                    </div>

                    <form method="post" class="inline-form">
                        <input type="text" name="program_name" placeholder="New programme name" required>
                        <button type="submit" name="add_program" value="1" class="btn-primary">Add Programme</button>
                    </form>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Programme</th>
                                    <th style="width:120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($programs)): ?>
                                    <tr><td colspan="2">No programmes yet. Add one above.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($programs as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['program_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <form method="post" onsubmit="return confirm('Delete this programme? It will no longer show when registering new students.');">
                                                    <input type="hidden" name="program_id" value="<?= (int)$p['id'] ?>">
                                                    <button type="submit" name="delete_program" value="1" class="btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="hint">Tip: linked courses (program_courses) are also removed when a programme is deleted (ON DELETE CASCADE).</p>
                </section>
            </section>
        </main>

    </div>

</body>
</html>