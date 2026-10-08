<?php
/**
 * @var \App\classes\Student $student
 * @var array $transcript
 * @var ?float $gpa
 */
?>
<div class="welcome-card">
    <div>
        <h2><?= e($student->getFullName()) ?></h2>
        <p>
            <strong>Student No:</strong> <?= e($student->getStudentNumber()) ?>
            &nbsp;|&nbsp; <strong>Programme:</strong> <?= e($student->getProgramme()) ?>
            &nbsp;|&nbsp; <strong>Year:</strong> <?= e($student->getYearOfStudy()) ?>
        </p>
    </div>
    <div class="action-buttons">
        <button class="btn btn-primary" type="button" onclick="window.print()">Print transcript</button>
        <a class="btn" href="<?= e(url('/students')) ?>">Back to Students</a>
    </div>
</div>

<section class="panel-card">
    <h3>Course Enrolments &amp; Grades</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Course Code</th><th>Course Name</th><th>Credit Hours</th><th>Mark (%)</th><th>Grade</th></tr>
            </thead>
            <tbody>
            <?php if ($transcript): ?>
                <?php foreach ($transcript as $row): ?>
                    <tr>
                        <td><?= e($row['courseCode']) ?></td>
                        <td><?= e($row['courseName']) ?></td>
                        <td><?= e($row['creditHours']) ?></td>
                        <td><?= $row['mark'] !== null ? e($row['mark']) . '%' : 'N/A' ?></td>
                        <td><strong><?= e($row['grade'] ?? 'N/A') ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No course enrolments recorded.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:15px;">
        <strong>Cumulative GPA:</strong> <?= $gpa !== null ? e(number_format($gpa, 2)) : 'N/A' ?>
    </div>
</section>
