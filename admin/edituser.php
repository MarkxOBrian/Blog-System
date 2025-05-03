<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /blog-project/login.php');
    exit();
}

// Check if student ID is provided
if (!isset($_GET['id'])) {
    header('Location: manageusers.php');
    exit();
}

$student_id = $_GET['id'];
$errors = [];

try {
    // Get student details
    $stmt = $pdo->prepare("SELECT * FROM student WHERE studentid = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['error_message'] = "Student not found";
        header('Location: manageusers.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentname = trim($_POST['studentname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate input
        if (empty($studentname)) {
            $errors[] = "Student name is required";
        }
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
        }

        if (empty($errors)) {
            // Check if email already exists (excluding current student)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE email = ? AND studentid != ?");
            $stmt->execute([$email, $student_id]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email already exists";
            } else {
                // Update student
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE student SET studentname = ?, email = ?, password = ? WHERE studentid = ?");
                    $stmt->execute([$studentname, $email, $hashed_password, $student_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE student SET studentname = ?, email = ? WHERE studentid = ?");
                    $stmt->execute([$studentname, $email, $student_id]);
                }

                $_SESSION['success_message'] = "Student updated successfully";
                header('Location: manageusers.php');
                exit();
            }
        }
    }
} catch (PDOException $e) {
    error_log("Database error in edit user: " . $e->getMessage());
    $errors[] = "An error occurred while updating the student";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../templates/admin_navigation.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Student</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="studentname" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="studentname" name="studentname" 
                                       value="<?php echo htmlspecialchars($student['studentname']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($student['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="manageusers.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 