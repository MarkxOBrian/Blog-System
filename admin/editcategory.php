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

// Check if category ID and name are provided
if (!isset($_POST['category_id']) || !isset($_POST['category_name'])) {
    $_SESSION['error_message'] = "Missing required fields";
    header('Location: managecategories.php');
    exit();
}

$category_id = $_POST['category_id'];
$category_name = trim($_POST['category_name']);

try {
    // Validate input
    if (empty($category_name)) {
        $_SESSION['error_message'] = "Category name is required";
        header('Location: managecategories.php');
        exit();
    }

    // Check if category exists
    $stmt = $pdo->prepare("SELECT * FROM modulecategory WHERE modulecategoryid = ?");
    $stmt->execute([$category_id]);
    if (!$stmt->fetch()) {
        $_SESSION['error_message'] = "Category not found";
        header('Location: managecategories.php');
        exit();
    }

    // Check if category already exists (excluding current category)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modulecategory WHERE modulecategoryname = ? AND modulecategoryid != ?");
    $stmt->execute([$category_name, $category_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error_message'] = "Category already exists";
        header('Location: managecategories.php');
        exit();
    }

    // Update category
    $stmt = $pdo->prepare("UPDATE modulecategory SET modulecategoryname = ? WHERE modulecategoryid = ?");
    $stmt->execute([$category_name, $category_id]);

    $_SESSION['success_message'] = "Category updated successfully";
} catch (PDOException $e) {
    error_log("Database error in edit category: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while updating the category";
}

header('Location: managecategories.php');
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - <?php echo SITE_NAME; ?></title>
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
                        <h4 class="mb-0">Edit Category</h4>
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
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" 
                                       value="<?php echo htmlspecialchars($category['modulecategoryname']); ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="managecategories.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Category</button>
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