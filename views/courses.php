<?php $title = 'Courses'; $activePage = 'courses'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Course management</h2><p>Manage courses and credit-hour values.</p></div></section>
    <?php if ($error !== ''): ?><p class="error-box" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <section class="panel-card"><div class="panel-heading"><h3>Add a course</h3></div>
        <form method="post" class="course-form">
            <div class="form-group"><label for="course_code">Course code</label><input id="course_code" name="course_code" required maxlength="20"></div>
            <div class="form-group"><label for="course_name">Course name</label><input id="course_name" name="course_name" required maxlength="100"></div>
            <div class="form-group"><label for="credit_hours">Credit hours</label><input id="credit_hours" name="credit_hours" type="number" min="1" required></div>
            <button class="btn btn-primary" type="submit">Add Course</button>
        </form>
    </section>
    <section class="panel-card"><div class="panel-heading"><h3>Course Records</h3></div><div class="table-wrap"><table><thead><tr><th>Code</th><th>Course name</th><th>Credit hours</th></tr></thead><tbody>
    <?php if ($courses === []): ?><tr><td colspan="3">No courses found.</td></tr>
    <?php else: foreach ($courses as $course): ?><tr><td><?= htmlspecialchars($course['course_code'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($course['course_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int) $course['credit_hours'] ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
