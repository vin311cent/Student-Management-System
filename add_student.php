<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrator') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/src/autoload.php';


$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $programme   = trim($_POST['programme'] ?? '');
    $year        = (int)($_POST['year_of_study'] ?? 1);

    $fullName = trim($first_name . ' ' . $last_name);

    try {
        $db = Database::getInstance()->getConnection();

        // Query the database to set the static counter based on existing student records
        $stmt = $db->query("SELECT COUNT(*) FROM students");
        $existingCount = (int)$stmt->fetchColumn();
        
        // Synchronize static counter in Student class with DB record count
        Student::resetCounter($existingCount);

        // Instantiating Student auto-generates studentNumber via self::generateStudentNumber()
        $student = new Student($fullName, $programme, $year);

        // Save the generated student details into MySQL database
        $sql = "INSERT INTO students (student_number, first_name, last_name, programme, year_of_study) 
                VALUES (:student_num, :first_name, :last_name, :programme, :year_of_study)";
        
        $insertStmt = $db->prepare($sql);
        $insertStmt->execute([
            ':student_num'    => $student->getStudentNumber(),
            ':first_name'     => $first_name,
            ':last_name'      => $last_name,
            ':programme'      => $student->getProgramme(),
            ':year_of_study'  => $student->getYearOfStudy()
        ]);

        header("Location: dashboard.php?added=1");
        exit;

    } catch (InvalidArgumentException $e) {
        $message = "Validation Error: " . $e->getMessage();
        $messageClass = "error";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Database Error: Duplicate student number detected. Please try again.";
        } else {
            $message = "System Error: " . $e->getMessage();
        }
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student Record | Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 40px; color: #333; }
        .form-card { max-width: 450px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin: 0 auto; }
        h2 { margin-top: 0; color: #111; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #1d4ed8; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        button:hover { background: #1e40af; }
        .cancel-lnk { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .error { background-color: #ffeeef; color: #dc2626; border: 1px solid #fca5a5; }
        select { width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
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
            <input type="text" name="first_name" placeholder="Enter first name" required>

            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Enter last name" required>

            <label>Programme / Degree Department</label>
            
            <select name="programme" id="programme" required>
                <option value="">Select Programme</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Law">Law</option>
                <option value="Social Work">Social Work</option>
                <option value="Business ">Business </option>
            </select>

            <label>Year of Study (1-6)</label>
            <input type="number" name="year_of_study" value="1" min="1" max="6" required>

            <button type="submit">Save Student to System</button>
            <a href="Student.php" class="cancel-lnk">Cancel and Go Back</a>
        </form>
    </div>

</body>
</html>