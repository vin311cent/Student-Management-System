<?php $title = 'Students'; $activePage = 'students'; ?>
<section class="dashboard-content">
    <section class="welcome-card"><div><h2>Manage student records</h2><p>Add a student or review existing records.</p></div></section>
    <?php if ($error !== ''): ?><p class="error-box" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <section class="panel-card">
        <div class="panel-heading"><h3>Add student</h3></div>
        <form method="post" class="course-form">
            <div class="form-group"><label for="first_name">First name</label><input id="first_name" name="first_name" required maxlength="50"></div>
            <div class="form-group"><label for="last_name">Last name</label><input id="last_name" name="last_name" required maxlength="50"></div>
            <div class="form-group"><label for="programme">Programme</label><input id="programme" name="programme" maxlength="100"></div>
            <div class="form-group"><label for="year_of_study">Year of study</label><input id="year_of_study" name="year_of_study" type="number" min="1" max="6" value="1" required></div>
            <button class="btn btn-primary" type="submit">Save Student</button>
        </form>
    </section>
    <section class="panel-card"><div class="panel-heading"><h3>Student Records</h3></div><div class="table-wrap"><table><thead><tr><th>Student No</th><th>Name</th><th>Programme</th><th>Year</th></tr></thead><tbody>
    <?php if ($students === []): ?><tr><td colspan="4">No student records found.</td></tr>
    <?php else: foreach ($students as $student): ?>
        <tr><td><?= htmlspecialchars($student['student_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($student['programme'] ?? '', ENT_QUOTES, 'UTF-8') ?></td><td><?= (int) $student['year_of_study'] ?></td></tr>
    <?php endforeach; endif; ?>
    </tbody></table></div></section>
</section>
