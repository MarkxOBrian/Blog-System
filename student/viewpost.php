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

// Check if post ID is provided
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$post_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

try {
    // Get post details
    $stmt = $pdo->prepare("
        SELECT p.*, m.modulecategoryname, s.studentname
        FROM post p 
        JOIN modulecategory m ON p.modulecategoryid = m.modulecategoryid
        JOIN student s ON p.studentid = s.studentid
        WHERE p.postid = ? AND p.studentid = ?
    ");
    $stmt->execute([$post_id, $student_id]);
    $post = $stmt->fetch();

    if (!$post) {
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching post: " . $e->getMessage());
    header('Location: dashboard.php');
    exit();
}

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
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .post-image {
            max-height: 400px;
            object-fit: cover;
        }
        .post-content {
            white-space: pre-wrap;
            line-height: 1.8;
        }
        .post-meta {
            color: #6c757d;
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

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="card-title h3"><?php echo htmlspecialchars($post['title']); ?></h1>
                            <a href="myposts.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Posts
                            </a>
                        </div>

                        <?php if ($post['photo']): ?>
                            <img src="<?php echo htmlspecialchars($post['photo']); ?>" 
                                 class="img-fluid rounded mb-4" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <?php endif; ?>

                        <div class="post-meta mb-4">
                            <div class="d-flex flex-wrap gap-3">
                                <span>
                                    <i class="bi bi-person"></i> 
                                    <?php echo htmlspecialchars($post['studentname']); ?>
                                </span>
                                <span>
                                    <i class="bi bi-folder"></i> 
                                    <?php echo htmlspecialchars($post['modulecategoryname']); ?>
                                </span>
                                <span>
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </span>
                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="post-content mb-4">
                            <?php echo nl2br(htmlspecialchars($post['posttext'])); ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="myposts.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Posts
                            </a>
                            <a href="myposts.php" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit in My Posts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 