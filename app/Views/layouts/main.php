<?php
/**
 * Admin layout: sidebar + topbar around the page content.
 * @var string $content
 */
$flash  = $flash ?? [];
$active = $active ?? '';
$nav = [
    'dashboard'  => ['/dashboard',  'fa-th-large',       'Dashboard'],
    'students'   => ['/students',   'fa-users',          'Students'],
    'courses'    => ['/courses',    'fa-book',           'Courses'],
    'enrolments' => ['/enrolments', 'fa-user-plus',      'Enrolment'],
    'grades'     => ['/grades',     'fa-chart-bar',      'Grades'],
    'summary'    => ['/summary',    'fa-graduation-cap', 'Academic Summary'],
    'reports'    => ['/reports',    'fa-file-alt',       'Reports'],
    'settings'   => ['/settings',   'fa-cog',            'Settings'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'LGU Manage') ?> | Student Management System</title>
    <link rel="stylesheet" href="<?= e(asset('style.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <div class="brand">LGU Manage</div>
        <nav class="nav-links" aria-label="Sidebar navigation">
            <?php foreach ($nav as $key => [$path, $icon, $label]): ?>
                <a class="nav-item<?= $active === $key ? ' active' : '' ?>" href="<?= e(url($path)) ?>">
                    <i class="fas <?= e($icon) ?>"></i> <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">A</div>
                <div>
                    <div class="user-name">Admin</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Administrator access</p>
                <h1><?= e($title ?? '') ?></h1>
            </div>
            <div class="topbar-actions">
                <span class="topbar-pill">Admin</span>
                <a class="logout-link" href="<?= e(url('/logout')) ?>">Logout</a>
            </div>
        </header>

        <section class="dashboard-content">
            <?php foreach ($flash as $msg): ?>
                <div class="<?= $msg['type'] === 'success' ? 'alert-success' : 'alert-error' ?>" role="alert">
                    <?= e($msg['message']) ?>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </section>
    </main>
</div>
</body>
</html>
