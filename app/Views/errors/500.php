<?php /** @var string $message */ ?>
<div class="page-shell">
    <div class="login-card">
        <h1>Something went wrong</h1>
        <p class="subtitle"><?= e($message) ?></p>
        <p class="footnote"><a href="<?= e(url('/')) ?>">Back to home</a></p>
    </div>
</div>
