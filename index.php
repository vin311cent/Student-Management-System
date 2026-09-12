<?php
/**
 * Entry point – Student Management System
 * Redirects authenticated users to dashboard; otherwise shows options.
 */
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .home-actions { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem; }
        .home-actions a { display: inline-block; padding: 0.75rem 1.4rem; background: #1e3a5f; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .home-actions a.secondary { background: #0f766e; }
        .home-actions a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="login-card">
            <h1>Student Management System</h1>
            <p class="subtitle">Web Programming 2 – Project 1 (OOP)</p>
            <p class="hint">A small college system for student records, enrolment and grades.</p>
            <div class="home-actions">
                <a href="Login.php">Admin Login</a>
                <a class="secondary" href="oop_demo.php">Run OOP Demo (Transcripts)</a>
            </div>
            <p class="footnote" style="margin-top:1.5rem;">Demo credentials: <code>admin</code> / <code>admin123</code></p>
        </div>
    </div>
</body>
</html>
