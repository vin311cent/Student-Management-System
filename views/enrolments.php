<?php $title = 'Enrolment'; $activePage = 'enrolments'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Enrol a student</h2><p>Select a student and course to create an enrolment.</p></div></section>
    <?php if ($error !== ''): ?><p class="error-box" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <section class="panel-card"><div class="panel-heading"><h3>Enrolment Information</h3></div>
        <form method="post" class="enrolment-form">
            <div class="form-group"><label for="student_id">Student</label><select name="student_id" id="student_id" required><option value="">Select Student</option><?php foreach ($students as $student): ?><option value="<?= (int) $student['id'] ?>"><?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="course_id">Course</label><select name="course_id" id="course_id" required><option value="">Select Course</option><?php foreach ($courses as $course): ?><option value="<?= (int) $course['id'] ?>"><?= htmlspecialchars($course['course_name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <button class="btn btn-primary" type="submit">Enroll Student</button>
        </form>
    </section>
    <section class="panel-card"><div class="panel-heading"><h3>Current Enrolments</h3></div><div class="table-wrap"><table><thead><tr><th>Student</th><th>Course</th></tr></thead><tbody>
    <?php if ($enrollments === []): ?><tr><td colspan="2">No current enrolments.</td></tr>
    <?php else: foreach ($enrollments as $enrollment): ?><tr><td><?= htmlspecialchars($enrollment['student_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($enrollment['course_name'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
