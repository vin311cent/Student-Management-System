<?php $title = 'Academic Summary'; $activePage = 'academic-summary'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Student Academic Summary</h2><p>View enrolment progress and weighted GPA by student.</p></div></section>
    <section class="panel-card"><div class="panel-heading"><h3>Academic Records</h3></div><div class="table-wrap"><table class="summary-table"><thead><tr><th>Student No</th><th>Name</th><th>Enrolled</th><th>Graded</th><th>Credit Hours</th><th>GPA</th></tr></thead><tbody>
    <?php if ($summaries === []): ?><tr><td colspan="6">No academic records found.</td></tr>
    <?php else: foreach ($summaries as $summary): ?><tr><td><?= htmlspecialchars($summary['student_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($summary['student_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int) $summary['total_courses'] ?></td><td><?= (int) $summary['graded_courses'] ?></td><td><?= (int) $summary['credit_hours'] ?></td><td><?= htmlspecialchars($summary['gpa'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
