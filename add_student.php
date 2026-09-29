<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';


$message = '';
$messageClass = '';

$db = Database::getInstance()->getConnection();

// Load active programmes from the database (deleted ones will not appear)
$programs = $db->query('SELECT id, program_name FROM programs ORDER BY program_name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $programme   = trim($_POST['programme'] ?? '');
    $year        = (int)($_POST['year_of_study'] ?? 1);

    $fullName = trim($first_name . ' ' . $last_name);

    try {
        // Only allow programmes that still exist in the programs table
        $check = $db->prepare('SELECT program_name FROM programs WHERE program_name = ? LIMIT 1');
        $check->execute([$programme]);
        $validProgramme = $check->fetchColumn();

        if (!$validProgramme) {
            throw new InvalidArgumentException('Selected programme is not available. It may have been removed.');
        }

        $programme = (string)$validProgramme;

        // Sync static counter with existing student records
        $stmt = $db->query('SELECT COUNT(*) FROM students');
        $existingCount = (int)$stmt->fetchColumn();
        Student::resetCounter($existingCount);

        $student = new Student($fullName, $programme, $year);

        $sql = 'INSERT INTO students (student_number, first_name, last_name, programme, year_of_study)
                VALUES (:student_num, :first_name, :last_name, :programme, :year_of_study)';

        $insertStmt = $db->prepare($sql);
        $insertStmt->execute([
            ':student_num'   => $student->getStudentNumber(),
            ':first_name'    => $first_name,
            ':last_name'     => $last_name,
            ':programme'     => $student->getProgramme(),
            ':year_of_study' => $student->getYearOfStudy(),
        ]);

        header('Location: dashboard.php?added=1');
        exit;

    } catch (InvalidArgumentException $e) {
        $message = 'Validation Error: ' . $e->getMessage();
        $messageClass = 'error';
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = 'Database Error: Duplicate student number detected. Please try again.';
        } else {
            $message = 'System Error: ' . $e->getMessage();
        }
        $messageClass = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student Record | Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 40px; color: #333; }
        .form-card { max-width: 450px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin: 0 auto; }
        h2 { margin-top: 0; color: #111; }
        input[type="text"], input[type="number"], select {
            width: 100%; padding: 10px; margin: 8px 0 16px 0;
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        button { background: #1d4ed8; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        button:hover { background: #1e40af; }
        .cancel-lnk { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .error { background-color: #ffeeef; color: #dc2626; border: 1px solid #fca5a5; }
        .hint { font-size: 0.85rem; color: #64748b; margin-top: -8px; margin-bottom: 16px; }
    </style>
</head>
<body>

    <div class="form-card">
        <h2>Add Student Record</h2>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="Enter first name" required
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Enter last name" required
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <label>Programme / Degree Department</label>
            <select name="programme" id="programme" required>
                <option value="">Select Programme</option>
                <?php foreach ($programs as $p): ?>
                    <option value="<?= htmlspecialchars($p['program_name'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= (isset($_POST['programme']) && $_POST['programme'] === $p['program_name']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['program_name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Year of Study (1-6)</label>
            <input type="number" name="year_of_study" value="<?= (int)($_POST['year_of_study'] ?? 1) ?>" min="1" max="6" required>

            <button type="submit" <?= empty($programs) ? 'disabled' : '' ?>>Save Student to System</button>
            <a href="Students.php" class="cancel-lnk">Cancel and Go Back</a>
        </form>
    </div>

</body>
</html>
