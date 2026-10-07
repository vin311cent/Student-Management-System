<?php
/**
 * @var string $username
 * @var \App\Models\Programme[] $programmes
 */
?>
<section class="welcome-card">
    <div>
        <p class="eyebrow">System configuration</p>
        <h2>System Settings</h2>
        <p>Manage programmes and account access.</p>
    </div>
</section>

<section class="panel-card settings-card">
    <div class="panel-heading">
        <div><h3>Account Information</h3><p>Currently logged-in administrator.</p></div>
    </div>
    <p>Logged in as: <strong><?= e($username) ?></strong></p>
    <p><a href="<?= e(url('/logout')) ?>" style="color:#dc2626;">Logout</a></p>
</section>

<section class="panel-card settings-card">
    <div class="panel-heading">
        <div>
            <h3>Programmes</h3>
            <p>These appear in the student registration form. Deleting a programme removes it from the dropdown only; existing student records keep their programme name.</p>
        </div>
    </div>
    <form method="post" action="<?= e(url('/settings/programmes')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <input type="text" name="program_name" placeholder="New programme name" maxlength="100" required>
        <button type="submit" class="btn-primary">Add Programme</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Programme</th><th style="width:120px;">Action</th></tr></thead>
            <tbody>
            <?php if (!$programmes): ?>
                <tr><td colspan="2">No programmes yet. Add one above.</td></tr>
            <?php else: foreach ($programmes as $p): ?>
                <tr>
                    <td><?= e($p->getName()) ?></td>
                    <td>
                        <form method="post" action="<?= e(url('/settings/programmes/delete')) ?>"
                              onsubmit="return confirm('Delete this programme? It will no longer show when registering new students.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="program_id" value="<?= (int) $p->getId() ?>">
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <p class="hint">Tip: linked courses (program_courses) are also removed when a programme is deleted (ON DELETE CASCADE).</p>
</section>
