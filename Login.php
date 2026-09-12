<?php
session_start();

$errors = [];

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    session_start();
}

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both your username and password.';
    } elseif ($username !== 'admin' || $password !== 'admin123') {
        $errors[] = 'Invalid username or password.';
    } else {
        $_SESSION['user'] = [
            'username' => $username,
            'role' => 'administrator'
        ];
        $_SESSION['flash_message'] = 'Welcome back, ' . $username . '!';
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-shell">
        <div class="login-card">
            <h1>Student Management System</h1>
            <p class="subtitle">Sign in to continue</p>
            <p class="hint">Demo login: admin / admin123</p>

            <?php if (!empty($errors)) : ?>
                <div class="error-box" role="alert">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="Login.php" method="post" class="login-form">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>

                <button type="submit">Log In</button>
            </form>

            <p class="footnote">
                <a href="index.php">Back to home</a>
            </p>
        </div>
    </div>
</body>
</html>
