<?php $title = 'Grades'; $activePage = 'grades'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Manage student grades</h2><p>Assign marks to enrolled students. Letter grades are calculated automatically.</p></div></section>
    <?php if ($error !== ''): ?><p class="error-box" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <section class="panel-card"><div class="panel-heading"><h3>Grade Records</h3></div><div class="table-wrap"><table class="grades-table"><thead><tr><th>Student</th><th>Course</th><th>Current Grade</th><th>Assign Mark</th></tr></thead><tbody>
    <?php if ($enrollments === []): ?><tr><td colspan="4">No enrolments found.</td></tr>
    <?php else: foreach ($enrollments as $enrollment): ?>
        <tr><td><?= htmlspecialchars($enrollment['student_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($enrollment['course_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($enrollment['grade'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8') ?></td><td><form method="post" class="grade-form"><input type="hidden" name="enrollment_id" value="<?= (int) $enrollment['id'] ?>"><input type="number" name="marks" min="0" max="100" step="0.01" value="<?= htmlspecialchars((string) ($enrollment['marks'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required><button class="btn btn-primary" type="submit">Save</button></form></td></tr>
    <?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
