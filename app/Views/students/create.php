<?php /** @var \App\classes\Programme[] $programmes */ ?>
<section class="panel-card" style="max-width:520px;">
    <div class="panel-heading"><h3>New student record</h3></div>
    <form method="post" action="<?= e(url('/students')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" maxlength="50" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" maxlength="50" required>
        </div>
        <div class="form-group">
            <label for="programme">Programme</label>
            <select id="programme" name="programme" required>
                <option value="">&mdash; Select &mdash;</option>
                <?php foreach ($programmes as $p): ?>
                    <option value="<?= e($p->getName()) ?>"><?= e($p->getName()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year_of_study">Year of study</label>
            <input type="number" id="year_of_study" name="year_of_study" min="1" max="6" value="1">
        </div>
        <button type="submit" class="btn btn-primary">Save student</button>
        <a href="<?= e(url('/students')) ?>" class="btn">Cancel</a>
    </form>
    <p style="margin-top:1rem;color:#64748b;font-size:0.9rem;">Student number is auto-generated as LGU-YYYY-NNN.</p>
</section>
