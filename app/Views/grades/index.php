<?php /** @var \App\classes\Student[] $students (only students with enrolments) */ ?>
<div class="welcome-card">
    <div>
        <h2>Record &amp; manage marks</h2>
        <p>Marks must be 0&ndash;100. Letter grades use the scale: A, B+, B, C+, C, D, F.</p>
    </div>
</div>

<?php if (!$students): ?>
    <section class="panel-card"><p>No enrolments found. Enrol students in courses first.</p></section>
<?php else: ?>
    <?php foreach ($students as $s): ?>
        <section class="panel-card" style="margin-bottom:1.25rem;">
            <div class="panel-heading">
                <h3><?= e($s->getFullName()) ?> <small>(<?= e($s->getStudentNumber()) ?>)</small></h3>
                <p><?= e($s->getProgramme()) ?></p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Course</th><th>Credits</th><th>Current Mark</th><th>Grade</th><th>Update Mark</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($s->getEnrolments() as $en): ?>
                        <tr>
                            <td>
                                <strong><?= e($en->getCourse()->getCourseCode()) ?></strong><br>
                                <span style="color:#64748b;font-size:0.9em;"><?= e($en->getCourse()->getCourseName()) ?></span>
                            </td>
                            <td><?= e($en->getCourse()->getCreditHours()) ?></td>
                            <td><?= $en->getMark() !== null ? e(number_format($en->getMark(), 1)) : '—' ?></td>
                            <td><strong><?= e($en->getGrade() ?? '—') ?></strong></td>
                            <td>
                                <form method="post" action="<?= e(url('/grades')) ?>" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="enrolment_id" value="<?= (int) $en->getId() ?>">
                                    <input type="number" name="marks" min="0" max="100" step="0.1" class="form-control"
                                           style="width:90px;" placeholder="0–100"
                                           value="<?= $en->getMark() !== null ? e($en->getMark()) : '' ?>" required>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>
