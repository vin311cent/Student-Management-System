<?php
/**
 * @var array $performance
 * @var int $totalStudents
 * @var int $totalEnrolments
 * @var int $totalGraded
 * @var array $courseStats
 * @var array $programmeStats
 */
?>
<section class="welcome-card no-print">
    <div>
        <p class="eyebrow">Academic reporting</p>
        <h2>Reports &amp; Exports</h2>
        <p class="muted-text">Generate performance reports and download student or enrolment data.</p>
    </div>
    <div class="report-actions">
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
</section>

<div class="reports-grid no-print">
    <div class="report-card">
        <i class="fas fa-file-excel"></i>
        <h3>Student List (Excel)</h3>
        <p>Export complete student records &mdash; names, programmes, year, enrolments.</p>
        <a class="btn btn-primary" href="<?= e(url('/reports/export/students')) ?>"><i class="fas fa-download"></i> Download CSV</a>
    </div>
    <div class="report-card">
        <i class="fas fa-chart-line"></i>
        <h3>Enrolment Report</h3>
        <p>Enrolment trends and statistics by course and programme.</p>
        <a class="btn btn-primary" href="<?= e(url('/reports/export/enrolment')) ?>"><i class="fas fa-download"></i> Download CSV</a>
    </div>
    <div class="report-card">
        <i class="fas fa-user-graduate"></i>
        <h3>Performance Report</h3>
        <p>GPA, credit hours and graded courses for all students (on this page).</p>
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('performance').scrollIntoView({behavior:'smooth'})">
            <i class="fas fa-eye"></i> View Below
        </button>
    </div>
</div>

<section class="report-summary">
    <article class="stat-card stat-students">
        <p class="stat-label">Students</p><p class="stat-number"><?= e($totalStudents) ?></p>
        <p class="stat-description">Total registered</p>
    </article>
    <article class="stat-card stat-enrol">
        <p class="stat-label">Enrolments</p><p class="stat-number"><?= e($totalEnrolments) ?></p>
        <p class="stat-description">Course enrolments</p>
    </article>
    <article class="stat-card stat-grades">
        <p class="stat-label">Graded Courses</p><p class="stat-number"><?= e($totalGraded) ?></p>
        <p class="stat-description">With marks entered</p>
    </article>
</section>

<section class="panel-card section-spacer">
    <div class="panel-heading">
        <div><h3>Enrolment Trends</h3><p>Students and enrolments by programme, and enrolment per course.</p></div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Programme</th><th>Students</th><th>Enrolments</th></tr></thead>
            <tbody>
            <?php if (!$programmeStats): ?>
                <tr><td colspan="3">No programme data.</td></tr>
            <?php else: foreach ($programmeStats as $p): ?>
                <tr><td><?= e($p['programme']) ?></td><td><?= e($p['students']) ?></td><td><?= e($p['enrolments']) ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="table-wrap" style="border-top:1px solid #e2e8f0;">
        <table>
            <thead><tr><th>Course Code</th><th>Course Name</th><th>Enrolled</th><th>Graded</th><th>Pending</th><th>Average</th></tr></thead>
            <tbody>
            <?php if (!$courseStats): ?>
                <tr><td colspan="6">No course data.</td></tr>
            <?php else: foreach ($courseStats as $c): ?>
                <tr>
                    <td><?= e($c['code']) ?></td><td><?= e($c['name']) ?></td>
                    <td><?= e($c['enrolled']) ?></td><td><?= e($c['graded']) ?></td><td><?= e($c['pending']) ?></td>
                    <td><?= $c['average'] !== null ? e($c['average']) : 'N/A' ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <hr>
</section>

<section class="panel-card section-spacer" id="performance">
    <div class="panel-heading">
        <div><h3>Student Performance</h3><p>Weighted GPA from graded courses and credit hours.</p></div>
        <a class="btn btn-secondary no-print" href="<?= e(url('/reports/export/students')) ?>"><i class="fas fa-file-excel"></i> Export Student List</a>
    </div>
    <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr><th>Student Number</th><th>Student</th><th>Programme</th><th>Enrolled</th><th>Graded</th><th>Credit Hours</th><th>GPA</th></tr>
            </thead>
            <tbody>
            <?php if (!$performance): ?>
                <tr><td colspan="7">No student records found.</td></tr>
            <?php else: foreach ($performance as $row): $s = $row['student']; ?>
                <tr>
                    <td><?= e($s->getStudentNumber()) ?></td>
                    <td><?= e($s->getFullName()) ?></td>
                    <td><?= e($s->getProgramme()) ?></td>
                    <td><?= e($row['enrolled']) ?></td>
                    <td><?= e($row['graded']) ?></td>
                    <td><?= e($row['credits']) ?></td>
                    <td><strong><?= $row['gpa'] !== null ? e(number_format($row['gpa'], 2)) : 'N/A' ?></strong></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>
