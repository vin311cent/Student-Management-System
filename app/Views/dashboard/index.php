<?php
/**
 * @var string $username
 * @var string $dateLabel
 * @var int $totalStudents
 * @var int $totalCourses
 * @var int $totalEnrol
 * @var \App\Models\Student[] $recent
 */
?>
<section class="welcome-card">
    <div>
        <h2>Welcome, <?= e($username) ?></h2>
        <p>Overview of the student records system &mdash; <?= e($dateLabel) ?></p>
    </div>
</section>

<section class="stats-grid" aria-label="Administrative overview">
    <article class="stat-card stat-students">
        <p class="stat-label">Students</p>
        <p class="stat-number"><?= e($totalStudents) ?></p>
        <p class="stat-description">Total registered students</p>
    </article>
    <article class="stat-card stat-courses">
        <p class="stat-label">Courses</p>
        <p class="stat-number"><?= e($totalCourses) ?></p>
        <p class="stat-description">Available courses</p>
    </article>
    <article class="stat-card stat-enrolment">
        <p class="stat-label">Enrolment</p>
        <p class="stat-number"><?= e($totalEnrol) ?></p>
        <p class="stat-description">Current enrolments</p>
    </article>
</section>

<section class="panel-card">
    <div class="panel-heading">
        <h3>Recent Students</h3>
        <a href="<?= e(url('/students')) ?>">View all</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Student No</th><th>Name</th><th>Programme</th><th>Courses</th><th>GPA</th></tr>
            </thead>
            <tbody>
            <?php if ($recent): ?>
                <?php foreach ($recent as $s): $gpa = $s->calculateGpa(); ?>
                    <tr>
                        <td><?= e($s->getStudentNumber()) ?></td>
                        <td><?= e($s->getFullName()) ?></td>
                        <td><?= e($s->getProgramme()) ?></td>
                        <td><?= count($s->getEnrolments()) ?></td>
                        <td><?= $gpa !== null ? e(number_format($gpa, 2)) : 'Pending' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No recent students found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
