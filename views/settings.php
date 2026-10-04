<?php $title = 'Settings'; $activePage = 'settings'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>System Settings</h2><p>Manage your account and system access.</p></div></section>
    <section class="panel-card"><div class="panel-heading"><h3>Account Information</h3></div><div class="setting-row"><span class="setting-label">Logged in as</span><strong class="setting-value"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong></div></section>
    <section class="panel-card"><div class="panel-heading"><h3>Account Actions</h3></div><a href="index.php?route=login&amp;logout=1" class="btn btn-primary">Logout</a></section>
</section>
