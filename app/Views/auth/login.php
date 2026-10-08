<?php $flash = $flash ?? []; ?>
<div class="page-shell">
    <div class="login-card">
        <h1>LGU Manage | Student Management System</h1>
        <p class="subtitle">Sign in to continue</p>
        <p class="hint">Demo login: admin / admin123</p>

        <?php if ($flash): ?>
            <div class="error-box" role="alert">
                <ul>
                    <?php foreach ($flash as $msg): ?>
                        <li><?= e($msg['message']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= e(url('/login')) ?>" method="post" class="login-form">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Log In</button>
        </form>
    </div>
</div>
