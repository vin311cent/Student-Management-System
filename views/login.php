<?php $title = 'Login'; $activePage = 'login'; ?>
<div class="page-shell">
    <div class="login-card">
        <h1>Student Management System</h1>
        <p class="subtitle">Sign in to continue</p>
        <p class="hint">Demo login: admin / admin123</p>
        <?php if ($errors !== []): ?>
            <div class="error-box" role="alert"><ul>
                <?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>
        <form action="index.php?route=login" method="post" class="login-form">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button type="submit">Log In</button>
        </form>
        <p class="footnote"><a href="index.php?route=index">Back to home</a></p>
    </div>
</div>
