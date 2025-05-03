<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

try {
    // Get statistics
    $total_posts = $pdo->query("SELECT COUNT(*) FROM post")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM student")->fetchColumn();
    $total_categories = $pdo->query("SELECT COUNT(*) FROM modulecategory")->fetchColumn();
    
    // Get recent posts
    $stmt = $pdo->query("
        SELECT p.*, s.studentname, m.modulecategoryname 
        FROM post p 
        JOIN student s ON p.studentid = s.studentid 
        JOIN modulecategory m ON p.modulecategoryid = m.modulecategoryid 
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $recent_posts = $stmt->fetchAll();

    // Get success/error messages
    $success_message = $_SESSION['success_message'] ?? null;
    $error_message = $_SESSION['error_message'] ?? null;

    // Clear messages from session
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
} catch (PDOException $e) {
    error_log("Database error in admin dashboard: " . $e->getMessage());
    $error_message = "An error occurred while loading the dashboard. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../templates/admin_navigation.php'; ?>

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

        <h1 class="mb-4">Admin Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Posts</h5>
                        <h2 class="card-text"><?php echo $total_posts; ?></h2>
                        <a href="manageposts.php" class="btn btn-light">Manage Posts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="card-text"><?php echo $total_users; ?></h2>
                        <a href="manageusers.php" class="btn btn-light">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Categories</h5>
                        <h2 class="card-text"><?php echo $total_categories; ?></h2>
                        <a href="managecategories.php" class="btn btn-light">Manage Categories</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Posts</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_posts)): ?>
                    <div class="alert alert-info">
                        No posts found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $post): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                                        <td><?php echo htmlspecialchars($post['studentname']); ?></td>
                                        <td><?php echo htmlspecialchars($post['modulecategoryname']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <a href="editpost.php?id=<?php echo $post['postid']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>