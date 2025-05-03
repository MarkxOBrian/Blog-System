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

// Get student's statistics
$student_id = $_SESSION['user_id'];

// Total posts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM post WHERE studentid = ?");
$stmt->execute([$student_id]);
$total_posts = $stmt->fetchColumn();

// Published posts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM post WHERE studentid = ? AND status = 'published'");
$stmt->execute([$student_id]);
$published_posts = $stmt->fetchColumn();

// Get recent posts
$stmt = $pdo->prepare("
    SELECT p.*, m.modulecategoryname 
    FROM post p 
    JOIN modulecategory m ON p.modulecategoryid = m.modulecategoryid 
    WHERE p.studentid = ? 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$stmt->execute([$student_id]);
$recent_posts = $stmt->fetchAll();

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
    <title>Student Dashboard - <?php echo SITE_NAME; ?></title>
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

        <h1 class="mb-4">Student Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Posts</h5>
                        <h2 class="card-text"><?php echo $total_posts; ?></h2>
                        <a href="myposts.php" class="btn btn-light">View All Posts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Published Posts</h5>
                        <h2 class="card-text"><?php echo $published_posts; ?></h2>
                        <a href="addpost.php" class="btn btn-light">Add New Post</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Posts</h5>
                <a href="myposts.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-eye"></i> View All Posts
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_posts)): ?>
                    <div class="alert alert-info">
                        No posts found.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($recent_posts as $post): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php if ($post['photo']): ?>
                                        <img src="<?php echo htmlspecialchars($post['photo']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-folder"></i> <?php echo htmlspecialchars($post['modulecategoryname']); ?>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                            </small>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($post['status']); ?>
                                            </span>
                                            <a href="viewpost.php?id=<?php echo $post['postid']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 