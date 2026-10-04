<?php
$title = $title ?? 'Student Management System';
$activePage = $activePage ?? '';
$isLoginPage = $activePage === 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (!$isLoginPage): ?>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">COLLEGE ADMIN</div>
            <nav class="nav-links" aria-label="Main navigation">
                <?php
                $links = [
                    'dashboard' => 'Dashboard',
                    'students' => 'Students',
                    'courses' => 'Courses',
                    'enrolments' => 'Enrolment',
                    'grades' => 'Grades',
                    'academic-summary' => 'Academic Summary',
                    'reports' => 'Reports',
                    'settings' => 'Settings',
                ];
                foreach ($links as $key => $label):
                    $url = 'index.php?route=' . rawurlencode($key);
                ?>
                    <a class="nav-item<?= $activePage === $key ? ' active' : '' ?>" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="main-panel">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Administrator access</p>
                    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-pill">Admin</span>
                    <a class="logout-link" href="index.php?route=login&amp;logout=1">Logout</a>
                </div>
            </header>
            <?php if (!empty($flash)): ?>
                <div class="dashboard-content"><p class="success-message" role="status"><?= htmlspecialchars((string) $flash, ENT_QUOTES, 'UTF-8') ?></p></div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
<?php else: ?>
    <?= $content ?>
<?php endif; ?>
</body>
</html>
