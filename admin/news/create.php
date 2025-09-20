

<?php
/**
 * Create News
 * This page allows creating new news articles
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news management permission
requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header("Location: ../index.php");
    exit;
}

// Initialize variables
$title = '';
$slug = '';
$content = '';
$excerpt = '';
$category_id = '';
$status = 'draft';
$is_featured = 0;
$errors = [];

// Get categories for dropdown
$categories = [];
if ($conn && !$conn->connect_error) {
    $stmt = $conn->prepare("SELECT * FROM news_categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Validate form data
    if (empty($title)) {
        $errors[] = 'กรุณากรอกหัวข้อข่าว';
    }

    if (empty($content)) {
        $errors[] = 'กรุณากรอกเนื้อหาข่าว';
    }

    if ($category_id <= 0) {
        $errors[] = 'กรุณาเลือกหมวดหมู่';
    }

    // Generate slug if not provided
    if (empty($slug)) {
        $slug = preg_replace('/[^a-zA-Z0-9ก-๙]+/', '-', $title);
        $slug = trim($slug, '-');
        $slug = strtolower($slug);
    }

    // Check if slug is unique
    if ($conn && !$conn->connect_error) {
        $stmt = $conn->prepare("SELECT id FROM news WHERE slug = ?");
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $slug .= '-' . time();
        }
    }

    // Handle featured image upload
    $featured_image = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['featured_image']['name'];
        $file_tmp = $_FILES['featured_image']['tmp_name'];
        $file_size = $_FILES['featured_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = 'อนุญาตให้อัพโหลดไฟล์รูปภาพเท่านั้น (jpg, jpeg, png, gif)';
        }

        // Check file size (max 5MB)
        if ($file_size > 5242880) {
            $errors[] = 'ขนาดไฟล์ต้องไม่เกิน 5MB';
        }

        // Generate unique filename
        $new_file_name = 'featured_' . uniqid() . '.' . $file_ext;
        $upload_path = '../../uploads/' . $new_file_name;
        $db_image_path = 'uploads/' . $new_file_name;

        // If no errors, save the file
        if (empty($errors)) {
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $featured_image = $db_image_path;
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ';
            }
        }
    }

    // If no errors, save to database
    if (empty($errors)) {
        // Set published_at date if status is published
        $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        // Insert news into database
        $stmt = $conn->prepare("INSERT INTO news (title, slug, content, excerpt, category_id, featured_image, author_id, status, is_featured, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('sssissssss', $title, $slug, $content, $excerpt, $category_id, $featured_image, $_SESSION['user_id'], $status, $is_featured, $published_at);

        if ($stmt->execute()) {
            $news_id = $conn->insert_id;

            // Handle multiple image uploads
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $total_files = count($_FILES['images']['name']);

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_name = $_FILES['images']['name'][$i];
                        $file_tmp = $_FILES['images']['tmp_name'][$i];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        if (in_array($file_ext, $allowed_extensions)) {
                            $new_file_name = 'gallery_' . uniqid() . '.' . $file_ext;
                            $upload_path = '../../uploads/' . $new_file_name;
                            $db_image_path = 'uploads/' . $new_file_name;

                            if (move_uploaded_file($file_tmp, $upload_path)) {
                                $stmt = $conn->prepare("INSERT INTO news_images (news_id, image_path, created_at) VALUES (?, ?, NOW())");
                                $stmt->bind_param('is', $news_id, $db_image_path);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }

            // Redirect to edit page
            header("Location: edit.php?id=" . $news_id . "&created=1");
            exit;
        } else {
            $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
        }
    }
}

// Generate page title
$page_title = "เพิ่มข่าวใหม่";
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fdbb2d 0%, #f7931e 100%);
            --danger-gradient: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);

            --primary-color: #667eea;
            --secondary-color: #6c757d;
            --success-color: #20c997;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;

            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --dark-glass: rgba(0, 0, 0, 0.1);

            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px 0 rgba(31, 38, 135, 0.4);
            --text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="grad1" cx="50%" cy="50%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23grad1)"/><circle cx="800" cy="300" r="150" fill="url(%23grad1)"/><circle cx="400" cy="700" r="120" fill="url(%23grad1)"/><circle cx="900" cy="800" r="80" fill="url(%23grad1)"/></svg>') no-repeat center center;
            background-size: cover;
            pointer-events: none;
            z-index: -1;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--glass-border);
            color: #fff;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: var(--shadow-soft);
        }

        .sidebar .logo {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .sidebar .logo h4 {
            color: #fff;
            font-weight: 600;
            text-shadow: var(--text-shadow);
            margin: 0;
        }

        .sidebar .nav-item {
            margin: 0.5rem 1rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            color: #fff;
            transform: translateX(5px);
            box-shadow: 0 4px 15px 0 rgba(31, 38, 135, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            animation: slideInDown 0.6s ease-out;
        }

        .page-header h1 {
            color: #fff;
            font-weight: 700;
            font-size: 2rem;
            text-shadow: var(--text-shadow);
            margin: 0;
        }

        .btn-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: #fff;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: var(--shadow-soft);
        }

        .btn-glass:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: #fff;
            background: rgba(255, 255, 255, 0.35);
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary-gradient:hover::before {
            left: 100%;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            color: #fff;
        }

        .card-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideInUp 0.6s ease-out;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-header-modern {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.5rem;
        }

        .card-header-modern h6 {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            text-shadow: var(--text-shadow);
            display: flex;
            align-items: center;
        }

        .card-header-modern h6 i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-body-modern {
            padding: 2rem;
        }

        .form-control-modern,
        .form-select-modern {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #495057;
        }

        .form-control-modern:focus,
        .form-select-modern:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: scale(1.02);
        }

        .form-label-modern {
            color: #fff;
            font-weight: 500;
            margin-bottom: 0.75rem;
            text-shadow: var(--text-shadow);
            display: flex;
            align-items: center;
        }

        .form-label-modern i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .alert-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 15px;
            color: #fff;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .alert-success-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(32, 201, 151, 0.3);
            border-radius: 15px;
            color: #fff;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .preview-image {
            max-height: 200px;
            max-width: 100%;
            border-radius: 12px;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .image-preview-container {
            display: inline-block;
            position: relative;
            margin: 0.75rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .image-preview-container:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .image-preview-container .remove-image {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--danger-gradient);
            color: #fff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .remove-image:hover {
            transform: scale(1.1);
            background: linear-gradient(135deg, #ff6b95 0%, #ff8e9b 100%);
        }

        .note-editor {
            border-radius: 15px !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .note-toolbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }

        .note-editing-area {
            background: rgba(255, 255, 255, 0.95);
        }

        /* Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .status-draft {
            background: var(--warning-gradient);
            color: #fff;
        }

        .status-published {
            background: var(--success-gradient);
            color: #fff;
        }

        /* Small text styling */
        .text-muted-glass {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        /* Required asterisk */
        .text-danger {
            color: #ff6b95 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .card-body-modern {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include_once '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main>
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-plus-circle me-3"></i><?php echo $page_title; ?></h1>
                    <a href="index.php" class="btn-glass">
                        <i class="fas fa-arrow-left me-2"></i> กลับไปยังรายการข่าว
                    </a>
                </div>

                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-modern">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>เกิดข้อผิดพลาด!</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- News Form -->
                <form action="create.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Main Content Card -->
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <h6><i class="fas fa-edit"></i>รายละเอียดข่าว</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <label for="title" class="form-label-modern">
                                            <i class="fas fa-heading"></i>หัวข้อข่าว <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-modern" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required placeholder="กรอกหัวข้อข่าว">
                                    </div>

                                    <div class="mb-4">
                                        <label for="slug" class="form-label-modern">
                                            <i class="fas fa-link"></i>URL Slug
                                        </label>
                                        <input type="text" class="form-control form-control-modern" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" placeholder="url-slug">
                                        <small class="text-muted-glass mt-2 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            หากไม่กรอก ระบบจะสร้างจากหัวข้อข่าวอัตโนมัติ
                                        </small>
                                    </div>

                                    <div class="mb-4">
                                        <label for="excerpt" class="form-label-modern">
                                            <i class="fas fa-align-left"></i>สรุปย่อ
                                        </label>
                                        <textarea class="form-control form-control-modern" id="excerpt" name="excerpt" rows="3" placeholder="สรุปสั้นๆ ของข่าว จะแสดงในหน้ารายการข่าว"><?php echo htmlspecialchars($excerpt); ?></textarea>
                                        <small class="text-muted-glass mt-2 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            สรุปสั้นๆ ของข่าว จะแสดงในหน้ารายการข่าว
                                        </small>
                                    </div>

                                    <div class="mb-4">
                                        <label for="content" class="form-label-modern">
                                            <i class="fas fa-file-alt"></i>เนื้อหาข่าว <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control form-control-modern" id="content" name="content" rows="10" placeholder="กรอกเนื้อหาข่าว"><?php echo htmlspecialchars($content); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images Card -->
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <h6><i class="fas fa-images"></i>รูปภาพประกอบ (แกลเลอรี่)</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <label for="images" class="form-label-modern">
                                            <i class="fas fa-upload"></i>อัพโหลดรูปภาพหลายรูป
                                        </label>
                                        <input type="file" class="form-control form-control-modern" id="images" name="images[]" multiple accept="image/*">
                                        <small class="text-muted-glass mt-2 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            สามารถเลือกได้หลายรูปพร้อมกัน
                                        </small>
                                    </div>

                                    <div id="image-previews" class="mt-4"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Publish Card -->
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <h6><i class="fas fa-paper-plane"></i>เผยแพร่</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <label for="status" class="form-label-modern">
                                            <i class="fas fa-toggle-on"></i>สถานะ
                                        </label>
                                        <select class="form-select form-select-modern" id="status" name="status">
                                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>📝 ฉบับร่าง</option>
                                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>🚀 เผยแพร่</option>
                                        </select>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary-gradient">
                                            <i class="fas fa-save me-2"></i> บันทึกข่าว
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Card -->
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <h6><i class="fas fa-folder"></i>หมวดหมู่</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <label for="category_id" class="form-label-modern">
                                            <i class="fas fa-tag"></i>เลือกหมวดหมู่ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-modern" id="category_id" name="category_id" required>
                                            <option value="">-- เลือกหมวดหมู่ --</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image Card -->
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <h6><i class="fas fa-image"></i>รูปภาพหลัก</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <label for="featured_image" class="form-label-modern">
                                            <i class="fas fa-camera"></i>อัพโหลดรูปภาพหลัก
                                        </label>
                                        <input type="file" class="form-control form-control-modern" id="featured_image" name="featured_image" accept="image/*">
                                        <small class="text-muted-glass mt-2 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            รูปภาพหลักจะแสดงในหน้ารายการข่าวและเป็นรูปปกของข่าว
                                        </small>
                                    </div>

                                    <div id="featured-image-preview" class="text-center"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Summernote editor with enhanced configuration
            $('#content').summernote({
                height: 400,
                minHeight: 300,
                maxHeight: 600,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'กรอกเนื้อหาข่าว...',
                callbacks: {
                    onChange: function(contents, $editable) {
                        // Auto-save functionality could be added here
                    }
                }
            });

            // Auto-generate slug from title
            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title
                    .toLowerCase()
                    .replace(/[^\wก-๙]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                if (!$('#slug').val()) {
                    $('#slug').val(slug);
                }
            });

            // Featured image preview
            $('#featured_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#featured-image-preview').html(
                            '<img src="' + e.target.result + '" class="preview-image" alt="รูปภาพหลัก">'
                        );
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Gallery images preview
            $('#images').on('change', function() {
                const files = this.files;
                let previewHtml = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewHtml += `
                            <div class="image-preview-container">
                                <img src="${e.target.result}" class="preview-image" alt="Gallery image ${i+1}">
                                <div class="remove-image" onclick="removeImage(this)">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        `;
                        $('#image-previews').html(previewHtml);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Remove image preview
        function removeImage(element) {
            $(element).parent().remove();
        }
    </script>
</body>
</html>
