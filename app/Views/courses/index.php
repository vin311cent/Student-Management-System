<?php
/**
 * @var \App\Models\Course[] $courses
 * @var \App\Models\Programme[] $programmes
 */
?>
<section class="welcome-card">
    <div>
        <p class="eyebrow">Course management</p>
        <h2>Add a new course</h2>
        <p>Course code, name and credit hours are validated by the Course class (1&ndash;12 credits).</p>
    </div>
</section>

<section class="panel-card course-form-card">
    <div class="panel-heading">
        <div><h3>Course Information</h3><p>Fill in the details for the new course.</p></div>
    </div>
    <form method="post" action="<?= e(url('/courses')) ?>" class="course-form">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" id="course_code" name="course_code" placeholder="e.g. CSC101" maxlength="20" required>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" id="course_name" name="course_name" placeholder="e.g. Programming Fundamentals" maxlength="100" required>
        </div>
        <div class="form-group">
            <label for="credit_hours">Credit Hours</label>
            <input type="number" id="credit_hours" name="credit_hours" min="1" max="12" value="3" required>
        </div>
        <?php if ($programmes): ?>
            <div class="form-group">
                <label for="program_id">Programme (optional)</label>
                <select id="program_id" name="program_id">
                    <option value="0">&mdash; None &mdash;</option>
                    <?php foreach ($programmes as $p): ?>
                        <option value="<?= (int) $p->getId() ?>"><?= e($p->getName()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
</section>

<section class="panel-card">
    <div class="panel-heading"><h3>All Courses</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Programme</th><th>Code</th><th>Name</th><th>Credits</th></tr></thead>
            <tbody>
            <?php if ($courses): ?>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><?= e($c->getProgrammes() ? implode(', ', $c->getProgrammes()) : '—') ?></td>
                        <td><?= e($c->getCourseCode()) ?></td>
                        <td><?= e($c->getCourseName()) ?></td>
                        <td><?= e($c->getCreditHours()) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No courses found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
