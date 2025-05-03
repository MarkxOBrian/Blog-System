<?php
session_start();
require_once '../includes/DatabaseConnection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get post ID from URL
$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: dashboard.php');
    exit();
}

// Get all students and categories for the form
$students = $pdo->query("SELECT studentid, studentname FROM student")->fetchAll();
$categories = $pdo->query("SELECT modulecategoryid, modulecategoryname FROM modulecategory")->fetchAll();

// Get post data
$stmt = $pdo->prepare("SELECT * FROM post WHERE postid = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    
    $errors = [];

    if (empty($title)) {
        $errors[] = "Title is required";
    }

    if (empty($content)) {
        $errors[] = "Content is required";
    }

    if (empty($student_id)) {
        $errors[] = "Please select a student";
    }

    if (empty($category_id)) {
        $errors[] = "Please select a category";
    }

    // Handle file upload
    $photo = $post['photo']; // Keep existing photo by default
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        } else {
            $photo = file_get_contents($_FILES['photo']['tmp_name']);
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE post 
                SET title = ?, posttext = ?, studentid = ?, modulecategoryid = ?, photo = ?, status = ? 
                WHERE postid = ?
            ");
            $stmt->execute([$title, $content, $student_id, $category_id, $photo, $status, $post_id]);
            
            $_SESSION['success'] = "Post updated successfully!";
            header('Location: dashboard.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Failed to update post. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h3 class="mb-4">Admin Panel</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="dashboard.php" class="nav-link">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="posts.php" class="nav-link active">
                            <i class="bi bi-file-text me-2"></i>Posts
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="categories.php" class="nav-link">
                            <i class="bi bi-tags me-2"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="users.php" class="nav-link">
                            <i class="bi bi-people me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a href="../logout.php" class="nav-link">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Edit Post</h2>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['posttext']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student</label>
                                        <select class="form-select" id="student_id" name="student_id" required>
                                            <option value="">Select a student</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?php echo $student['studentid']; ?>" <?php echo $post['studentid'] == $student['studentid'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($student['studentname']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select a category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['modulecategoryid']; ?>" <?php echo $post['modulecategoryid'] == $category['modulecategoryid'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['modulecategoryname']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Photo</label>
                                        <?php if ($post['photo']): ?>
                                            <div class="mb-2">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($post['photo']); ?>" alt="Current photo" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current photo</small>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Post</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 