<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Check if post ID is provided
if (!isset($_GET['id'])) {
    header('Location: myposts.php');
    exit();
}

$post_id = (int)$_GET['id'];

// Get post details
$stmt = $pdo->prepare("
    SELECT p.*, m.modulecategoryname 
    FROM post p 
    JOIN modulecategory m ON p.modulecategoryid = m.modulecategoryid 
    WHERE p.postid = ? AND p.studentid = ?
");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: myposts.php');
    exit();
}

// Get all categories
$stmt = $pdo->query("SELECT * FROM modulecategory ORDER BY modulecategoryname");
$categories = $stmt->fetchAll();

// Get success/error messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

// Clear messages from session
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../templates/student_navigation.php'; ?>

    <!-- Main Content -->
    <div class="container py-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Post</h4>
                    </div>
                    <div class="card-body">
                        <form action="updatepost.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="post_id" value="<?php echo $post['postid']; ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($post['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['modulecategoryid']; ?>"
                                                <?php echo ($category['modulecategoryid'] == $post['modulecategoryid']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['modulecategoryname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php 
                                    echo htmlspecialchars($post['posttext']); 
                                ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo (optional)</label>
                                <?php if ($post['photo']): ?>
                                    <div class="mb-2">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($post['photo']); ?>" 
                                             class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="form-text">Supported formats: JPEG, PNG, GIF. Maximum size: 2MB.</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Post
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