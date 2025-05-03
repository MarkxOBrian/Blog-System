<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Get all categories
$stmt = $pdo->query("SELECT * FROM modulecategory ORDER BY modulecategoryname");
$categories = $stmt->fetchAll();

// Get success/error messages and form data
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
$form_data = $_SESSION['form_data'] ?? [];

// Clear messages and form data from session
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-label.required:after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <?php include '../templates/student_navigation.php'; ?>

    <div class="container py-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Add New Post</h4>
                    </div>
                    <div class="card-body">
                        <form action="savepost.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label required">Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label required">Category</label>
                                <select class="form-select" id="category" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['modulecategoryid']; ?>"
                                                <?php echo (isset($form_data['category_id']) && $form_data['category_id'] == $category['modulecategoryid']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['modulecategoryname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label required">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php 
                                    echo htmlspecialchars($form_data['content'] ?? ''); 
                                ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo (optional)</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="form-text">Supported formats: JPEG, PNG, GIF. Maximum size: 2MB.</div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Post
                                </button>
                                <a href="myposts.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to My Posts
                                </a>
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