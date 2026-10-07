<?php
/**
 * @var \App\Models\Student[] $students
 * @var array<int, array{0:string,1:string}> $log
 * @var int $counter
 */
?>
<div style="max-width:960px;margin:2rem auto;padding:0 1rem;">
    <div class="welcome-card">
        <div>
            <h2>OOP Demonstration &mdash; Student Records</h2>
            <p>Objects only (no database): <?= count($students) ?> students created, static counter now at <?= e($counter) ?>.</p>
        </div>
        <div class="action-buttons"><a class="btn" href="<?= e(url('/login')) ?>">Admin login</a></div>
    </div>

    <section class="panel-card">
        <h3>Exceptions caught gracefully</h3>
        <?php foreach ($log as [$type, $text]): ?>
            <div class="<?= $type === 'success' ? 'alert-success' : 'alert-error' ?>"><?= e($text) ?></div>
        <?php endforeach; ?>
    </section>

    <?php foreach ($students as $s): $gpa = $s->calculateGpa(); ?>
        <section class="panel-card" style="margin-top:1rem;">
            <h3><?= e($s->getFullName()) ?> <small>(<?= e($s->getStudentNumber()) ?> &middot; <?= e($s->getProgramme()) ?>, Year <?= e($s->getYearOfStudy()) ?>)</small></h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Course Code</th><th>Course Name</th><th>Credits</th><th>Mark</th><th>Grade</th></tr></thead>
                    <tbody>
                    <?php foreach ($s->getTranscript() as $row): ?>
                        <tr>
                            <td><?= e($row['courseCode']) ?></td>
                            <td><?= e($row['courseName']) ?></td>
                            <td><?= e($row['creditHours']) ?></td>
                            <td><?= $row['mark'] !== null ? e($row['mark']) : 'N/A' ?></td>
                            <td><strong><?= e($row['grade'] ?? 'N/A') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p style="margin-top:10px;"><strong>Weighted GPA:</strong> <?= $gpa !== null ? e(number_format($gpa, 2)) : 'N/A' ?></p>
        </section>
    <?php endforeach; ?>
</div>
