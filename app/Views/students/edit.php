<?php
/**
 * @var \App\Models\Student $student
 * @var \App\Models\Programme[] $programmes
 */
$names = array_map(static fn ($p) => $p->getName(), $programmes);
if (!in_array($student->getProgramme(), $names, true)) {
    $names[] = $student->getProgramme(); // keep a programme that was later removed from Settings
}
?>
<section class="panel-card" style="max-width:520px;">
    <div class="panel-heading"><h3>Edit <?= e($student->getStudentNumber()) ?></h3></div>
    <form method="post" action="<?= e(url('/students/' . $student->getId() . '/update')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" maxlength="50" value="<?= e($student->getFirstName()) ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" maxlength="50" value="<?= e($student->getLastName()) ?>" required>
        </div>
        <div class="form-group">
            <label for="programme">Programme</label>
            <select id="programme" name="programme" required>
                <?php foreach ($names as $n): ?>
                    <option value="<?= e($n) ?>"<?= $n === $student->getProgramme() ? ' selected' : '' ?>><?= e($n) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year_of_study">Year of study</label>
            <input type="number" id="year_of_study" name="year_of_study" min="1" max="6" value="<?= e($student->getYearOfStudy()) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="<?= e(url('/students')) ?>" class="btn">Cancel</a>
    </form>
    <p style="margin-top:1rem;color:#64748b;font-size:0.9rem;">The student number cannot be changed.</p>
</section>
