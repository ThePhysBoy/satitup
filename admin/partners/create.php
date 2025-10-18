<?php
/**
 * หน้าเพิ่มพันธมิตรใหม่
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in
requireLogin();

// Check permissions
if (!isPrOfficer() && !isAdmin()) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนนี้";
    header('Location: ../index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $website_url = trim($_POST['website_url']);
    $contact_info = trim($_POST['contact_info']);
    $order_number = intval($_POST['order_number']);
    $status = $_POST['status'];
    
    $errors = [];
    
    // Validation
    if (empty($name)) {
        $errors[] = "กรุณากรอกชื่อหน่วยงานพันธมิตร";
    }
    
    // Upload logo image
    $logo_image = '';
    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/partners/logos/';
        $file_ext = strtolower(pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = 'logo_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $upload_path)) {
                $logo_image = 'uploads/partners/logos/' . $new_filename;
            } else {
                $errors[] = "เกิดข้อผิดพลาดในการอัพโหลดรูปโลโก้";
            }
        } else {
            $errors[] = "ไฟล์โลโก้ต้องเป็น JPG, PNG หรือ GIF เท่านั้น";
        }
    }
    
    // Upload featured image
    $featured_image = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/partners/featured/';
        $file_ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = 'featured_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                $featured_image = 'uploads/partners/featured/' . $new_filename;
            } else {
                $errors[] = "เกิดข้อผิดพลาดในการอัพโหลดรูปหลัก";
            }
        } else {
            $errors[] = "ไฟล์รูปหลักต้องเป็น JPG, PNG หรือ GIF เท่านั้น";
        }
    }
    
    // Insert into database
    if (empty($errors)) {
        $insert_query = "INSERT INTO partners (name, logo_image, featured_image, project_name, description, content, website_url, contact_info, order_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param('ssssssssss', $name, $logo_image, $featured_image, $project_name, $description, $content, $website_url, $contact_info, $order_number, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "เพิ่มพันธมิตรใหม่สำเร็จ";
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มพันธมิตรใหม่ - ระบบจัดการเว็บไซต์</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: #2d3748;
        }
        
        .required {
            color: red;
        }
        
        .image-preview {
            max-width: 300px;
            margin-top: 1rem;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            display: none;
        }
        
        .image-preview img {
            max-width: 100%;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="form-container mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        เพิ่มพันธมิตรใหม่
                    </h1>
                    <p class="text-muted mb-0">เพิ่มข้อมูลหน่วยงานพันธมิตรและโครงการความร่วมมือ</p>
                </div>
                <div>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>พบข้อผิดพลาด:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Basic Info -->
                    <div class="col-12">
                        <h4 class="mb-3 pb-2 border-bottom">
                            <i class="fas fa-info-circle me-2"></i>ข้อมูลพื้นฐาน
                        </h4>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            ชื่อหน่วยงานพันธมิตร <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="project_name" class="form-label">
                            ชื่อโครงการความร่วมมือ
                        </label>
                        <input type="text" class="form-control" id="project_name" name="project_name" 
                               value="<?php echo isset($_POST['project_name']) ? htmlspecialchars($_POST['project_name']) : ''; ?>">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">
                            รายละเอียดความร่วมมือ (สั้น)
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <small class="text-muted">รายละเอียดสั้นๆ สำหรับแสดงบนหน้าดูรายละเอียด</small>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label for="content" class="form-label">
                            เนื้อหาแบบยาว
                        </label>
                        <textarea class="form-control" id="content" name="content" rows="6"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        <small class="text-muted">เนื้อหาแบบละเอียดเกี่ยวกับความร่วมมือ</small>
                    </div>
                    
                    <!-- Images -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3 pb-2 border-bottom">
                            <i class="fas fa-images me-2"></i>รูปภาพ
                        </h4>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="logo_image" class="form-label">
                            รูปโลโก้ (สำหรับแสดงในหน้าหลัก)
                        </label>
                        <input type="file" class="form-control" id="logo_image" name="logo_image" 
                               accept="image/*" onchange="previewImage(this, 'logo_preview')">
                        <small class="text-muted">ขนาดแนะนำ: 400x400 พิกเซล, ไฟล์ JPG, PNG หรือ GIF</small>
                        <div id="logo_preview" class="image-preview"></div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="featured_image" class="form-label">
                            รูปหลัก (สำหรับแสดงในหน้ารายละเอียด)
                        </label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" 
                               accept="image/*" onchange="previewImage(this, 'featured_preview')">
                        <small class="text-muted">ขนาดแนะนำ: 1200x800 พิกเซล, ไฟล์ JPG, PNG หรือ GIF</small>
                        <div id="featured_preview" class="image-preview"></div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3 pb-2 border-bottom">
                            <i class="fas fa-address-book me-2"></i>ข้อมูลติดต่อ
                        </h4>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="website_url" class="form-label">
                            เว็บไซต์
                        </label>
                        <input type="url" class="form-control" id="website_url" name="website_url" 
                               value="<?php echo isset($_POST['website_url']) ? htmlspecialchars($_POST['website_url']) : ''; ?>"
                               placeholder="https://example.com">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="contact_info" class="form-label">
                            ข้อมูลติดต่อ
                        </label>
                        <textarea class="form-control" id="contact_info" name="contact_info" rows="3"><?php echo isset($_POST['contact_info']) ? htmlspecialchars($_POST['contact_info']) : ''; ?></textarea>
                        <small class="text-muted">เบอร์โทร, อีเมล, ที่อยู่ ฯลฯ</small>
                    </div>
                    
                    <!-- Settings -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3 pb-2 border-bottom">
                            <i class="fas fa-cog me-2"></i>การตั้งค่า
                        </h4>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="order_number" class="form-label">
                            ลำดับการแสดงผล
                        </label>
                        <input type="number" class="form-control" id="order_number" name="order_number" 
                               value="<?php echo isset($_POST['order_number']) ? intval($_POST['order_number']) : 0; ?>" min="0">
                        <small class="text-muted">เลขน้อยแสดงก่อน</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">
                            สถานะ
                        </label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : 'selected'; ?>>เปิดใช้งาน</option>
                            <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>ปิดใช้งาน</option>
                        </select>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>บันทึก
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Image Preview Script -->
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
            preview.style.display = 'none';
        }
    }
    </script>
</body>
</html>

