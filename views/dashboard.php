<?php $title = 'Dashboard'; $activePage = 'dashboard'; ?>
<section class="dashboard-content">
    <section class="welcome-card">
        <div><h2>Welcome, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h2><p>Overview of the student records system — <?= htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8') ?></p></div>
    </section>
    <section class="stats-grid" aria-label="Administrative overview">
        <?php foreach (['students' => 'Students', 'courses' => 'Courses', 'enrollments' => 'Enrolment'] as $key => $label): ?>
            <article class="stat-card"><p class="stat-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></p><p class="stat-number"><?= (int) $counts[$key] ?></p></article>
        <?php endforeach; ?>
    </section>
    <section class="panel-card">
        <div class="panel-heading"><h3>Recent Students</h3><a href="index.php?route=students">View all</a></div>
        <div class="table-wrap"><table><thead><tr><th>Student No</th><th>Name</th><th>Course</th><th>Grade</th></tr></thead><tbody>
        <?php if ($students === []): ?><tr><td colspan="4">No recent students found.</td></tr>
        <?php else: foreach ($students as $student): ?>
            <tr><td><?= htmlspecialchars($student['student_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($student['student_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($student['course_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($student['grade'] ?? 'Pending', ENT_QUOTES, 'UTF-8') ?></td></tr>
        <?php endforeach; endif; ?>
        </tbody></table></div>
    </section>
</section>
