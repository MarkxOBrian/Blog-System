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

$student_id = $_SESSION['user_id'];

try {
    // Get all posts for the current student
    $query = "
        SELECT p.*, m.modulecategoryname 
        FROM post p 
        JOIN modulecategory m ON p.modulecategoryid = m.modulecategoryid 
        WHERE p.studentid = ? 
        ORDER BY p.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$student_id]);
    $posts = $stmt->fetchAll();

    // Get success/error messages from session
    $success_message = $_SESSION['success_message'] ?? null;
    $error_message = $_SESSION['error_message'] ?? null;

    // Clear messages from session
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
} catch (PDOException $e) {
    error_log("Database error in my posts: " . $e->getMessage());
    $error_message = "An error occurred while loading posts";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .post-card {
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-5px);
        }
        .post-image {
            height: 200px;
            object-fit: cover;
        }
    </style>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>My Posts</h1>
            <a href="addpost.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Post
            </a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                You haven't created any posts yet. <a href="addpost.php">Create your first post</a>!
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 post-card">
                            <?php if ($post['photo']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($post['photo']); ?>" 
                                     class="card-img-top post-image" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <small>
                                        Category: <?php echo htmlspecialchars($post['modulecategoryname']); ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <?php echo nl2br(htmlspecialchars(substr($post['posttext'], 0, 150) . '...')); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="editpost.php?id=<?php echo $post['postid']; ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="deletepost.php" method="post" class="d-inline">
                                        <input type="hidden" name="post_id" value="<?php echo $post['postid']; ?>">
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this post?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>
                                    Status: <?php echo ucfirst($post['status']); ?> | 
                                    Created: <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 