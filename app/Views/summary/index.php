<?php /** @var \App\classes\Student[] $students */ ?>
<div class="welcome-card">
    <div>
        <h2>Student academic progress</h2>
        <p>Weighted GPA uses credit hours and the A / B+ / B / C+ / C / D / F scale.</p>
    </div>
</div>

<section class="panel-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Student No</th><th>Name</th><th>Programme</th><th>Enrolled</th><th>Graded</th><th>GPA</th><th>Transcript</th></tr>
            </thead>
            <tbody>
            <?php if ($students): ?>
                <?php foreach ($students as $s): $gpa = $s->calculateGpa(); ?>
                    <tr>
                        <td><?= e($s->getStudentNumber()) ?></td>
                        <td><?= e($s->getFullName()) ?></td>
                        <td><?= e($s->getProgramme()) ?></td>
                        <td><?= count($s->getEnrolments()) ?></td>
                        <td><?= $s->getGradedCount() ?></td>
                        <td>
                            <?php if ($gpa !== null): ?>
                                <span class="gpa"><?= e(number_format($gpa, 2)) ?></span>
                            <?php else: ?>
                                <span class="no-gpa">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><a class="text-link" href="<?= e(url('/transcript/' . $s->getId())) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No students found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
