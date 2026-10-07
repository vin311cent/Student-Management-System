<?php
/**
 * @var \App\Models\Student[] $students
 * @var string $query
 */
?>
<div class="welcome-card">
    <div><h2>Manage student records</h2></div>
    <div class="action-buttons">
        <a href="<?= e(url('/students/create')) ?>" class="btn btn-primary">+ Add Student</a>
    </div>
</div>

<section class="panel-card">
    <div class="panel-heading">
        <h3>Student Records (<?= count($students) ?>)</h3>
        <form method="get" action="<?= e(url('/students')) ?>" class="inline-form" style="margin:0;">
            <input type="search" name="q" value="<?= e($query) ?>" placeholder="Search name, number or programme">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($query !== ''): ?><a class="btn" href="<?= e(url('/students')) ?>">Clear</a><?php endif; ?>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Student No</th><th>Name</th><th>Programme</th><th>Year</th><th>Courses</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php if ($students): ?>
                <?php foreach ($students as $s):
                    $names = array_map(static fn ($en) => $en->getCourse()->getCourseName(), $s->getEnrolments()); ?>
                    <tr>
                        <td><?= e($s->getStudentNumber()) ?></td>
                        <td><?= e($s->getFullName()) ?></td>
                        <td><?= e($s->getProgramme()) ?></td>
                        <td><?= e($s->getYearOfStudy()) ?></td>
                        <td><?= e($names ? implode(', ', $names) : 'N/A') ?></td>
                        <td style="white-space:nowrap;">
                            <a class="text-link" href="<?= e(url('/transcript/' . $s->getId())) ?>">Transcript</a>
                            &nbsp;<a class="text-link" href="<?= e(url('/students/' . $s->getId() . '/edit')) ?>">Edit</a>
                            <form method="post" action="<?= e(url('/students/' . $s->getId() . '/delete')) ?>" style="display:inline;"
                                  onsubmit="return confirm('Delete <?= e(addslashes($s->getFullName())) ?> and all their enrolments and marks?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-link" style="background:none;border:0;cursor:pointer;color:#dc2626;padding:0 0 0 8px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6"><?= $query !== '' ? 'No students match your search.' : 'No student records found.' ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
