<?php
/**
 * @var \App\classes\Student[] $students
 * @var \App\classes\Course[] $courses
 */
?>
<section class="welcome-card">
    <div>
        <p class="eyebrow">Student management</p>
        <h2>Enrol a student</h2>
        <p>Select a student and course to create a new enrolment.</p>
    </div>
</section>

<section class="panel-card enrolment-form-card">
    <div class="panel-heading">
        <div><h3>Enrolment Information</h3><p>Choose the student and course below.</p></div>
    </div>
    <form method="post" action="<?= e(url('/enrolments')) ?>" class="enrolment-form">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="student_id">Student</label>
            <select name="student_id" id="student_id" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= (int) $s->getId() ?>"><?= e($s->getFullName()) ?> (<?= e($s->getStudentNumber()) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="course_id">Course</label>
            <select name="course_id" id="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= (int) $c->getId() ?>"><?= e($c->getCourseCode()) ?> &ndash; <?= e($c->getCourseName()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-action">
            <button type="submit" class="btn btn-primary">Enroll Student</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="panel-heading">
        <div><h3>Current Enrolments</h3><p>Students currently enrolled in courses.</p></div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>STUDENT</th><th>ENROLLED COURSE</th><th>GRADE</th><th>ACTION</th></tr></thead>
            <tbody>
            <?php $any = false; foreach ($students as $s): foreach ($s->getEnrolments() as $en): $any = true; ?>
                <tr>
                    <td><?= e($s->getFullName()) ?></td>
                    <td><?= e($en->getCourse()->getCourseName()) ?></td>
                    <td><?= e($en->getGrade() ?? '—') ?></td>
                    <td>
                        <form method="post" action="<?= e(url('/enrolments/delete')) ?>"
                              onsubmit="return confirm('Remove this enrolment? Any mark recorded for it will be lost.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="enrolment_id" value="<?= (int) $en->getId() ?>">
                            <button type="submit" class="btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endforeach; ?>
            <?php if (!$any): ?>
                <tr><td colspan="4">No enrolments yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
