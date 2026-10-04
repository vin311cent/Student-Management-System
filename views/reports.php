<?php $title = 'Reports'; $activePage = 'reports'; ?>
<style>
    @media print {
        .sidebar, .topbar-actions, .report-actions { display: none; }
        .admin-shell, .main-panel { display: block; padding: 0; }
        .panel-card, .welcome-card, .stat-card { box-shadow: none; }
    }
</style>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Student Performance Report</h2><p>A summary of course enrolments, grades, credit hours, and GPA.</p></div><div class="report-actions"><button type="button" class="btn btn-primary" onclick="window.print()">Print Report</button></div></section>
    <section class="stats-grid report-summary">
        <?php foreach (['students' => 'Students', 'enrollments' => 'Enrolments', 'graded' => 'Graded Courses'] as $key => $label): ?><article class="stat-card"><p class="stat-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></p><p class="stat-number"><?= (int) $totals[$key] ?></p></article><?php endforeach; ?>
    </section>
    <section class="panel-card"><div class="panel-heading"><h3>Student Performance</h3></div><div class="table-wrap"><table class="report-table"><thead><tr><th>Student Number</th><th>Student</th><th>Programme</th><th>Enrolled</th><th>Graded</th><th>Credit Hours</th><th>GPA</th></tr></thead><tbody>
    <?php if ($rows === []): ?><tr><td colspan="7">No report data found.</td></tr>
    <?php else: foreach ($rows as $row): ?><tr><td><?= htmlspecialchars($row['student_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($row['student_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($row['programme'] ?? '', ENT_QUOTES, 'UTF-8') ?></td><td><?= (int) $row['total_courses'] ?></td><td><?= (int) $row['graded_courses'] ?></td><td><?= (int) $row['credit_hours'] ?></td><td><?= htmlspecialchars($row['gpa'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
