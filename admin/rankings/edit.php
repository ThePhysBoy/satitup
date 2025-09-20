<?php
/**
 * Edit University Ranking Item
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to have rankings management access
requireRankingsAccess();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Initialize variables
$title = '';
$description = '';
$link = '';
$ranking_type = '';
$display_order = 0;
$active = 1;
$current_image = '';
$errors = [];
$success_message = '';

// Get ranking types
$ranking_types = [
    'general' => 'ทั่วไป',
    'the_impact' => 'THE Impact Rankings',
    'the_world' => 'THE World University Rankings',
    'qs_world' => 'QS World University Rankings',
    'qs_asia' => 'QS Asia University Rankings',
    'ui_green' => 'UI GreenMetric',
    'scimago' => 'Scimago Institutions Rankings',
    'webometrics' => 'Webometrics'
];

// Get ranking item data
$stmt = $conn->prepare("SELECT * FROM university_rankings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$ranking = $result->fetch_assoc();
$title = $ranking['title'];
$description = $ranking['description'];
$link = $ranking['link'];
$ranking_type = $ranking['ranking_type'];
$display_order = $ranking['display_order'];
$active = $ranking['active'];
$current_image = $ranking['image_path'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $link = $_POST['link'] ?? '';
    $ranking_type = $_POST['ranking_type'] ?? 'general';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate form data
    if (empty($title)) {
        $errors[] = 'กรุณากรอกหัวข้อ';
    }
    
    // If no errors, process the update
    if (empty($errors)) {
        // Check if a new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_size = $_FILES['image']['size'];
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
            
            // If no errors, save the file and update database
            if (empty($errors)) {
                // Generate unique filename
                $new_file_name = 'ranking_' . uniqid() . '.' . $file_ext;
                $upload_path = 'uploads/' . $new_file_name;
                $db_image_path = 'images/rankings/' . $new_file_name; // เส้นทางสำหรับบันทึกในฐานข้อมูล
                
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Copy file to public folder
                    if (copy($upload_path, '../../' . $db_image_path)) {
                        // Delete old image if it exists
                        if (!empty($current_image)) {
                            $old_upload_path = '../../' . $current_image;
                            if (file_exists($old_upload_path)) {
                                unlink($old_upload_path);
                            }
                        }
                        
                        // Update image path
                        $image_path = $db_image_path;
                    } else {
                        $errors[] = 'เกิดข้อผิดพลาดในการคัดลอกไฟล์';
                        
                        // Delete uploaded file if copy fails
                        if (file_exists($upload_path)) {
                            unlink($upload_path);
                        }
                        
                        $image_path = $current_image;
                    }
                } else {
                    $errors[] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์';
                    $image_path = $current_image;
                }
            } else {
                $image_path = $current_image;
            }
        } else {
            // Keep current image
            $image_path = $current_image;
        }
        
        // If no errors, update the database
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE university_rankings SET title = ?, description = ?, image_path = ?, link = ?, ranking_type = ?, display_order = ?, active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("sssssiis", $title, $description, $image_path, $link, $ranking_type, $display_order, $active, $id);
            
            if ($stmt->execute()) {
                $success_message = "อัพเดตข้อมูลเรียบร้อยแล้ว";
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
            }
        }
    }
}

// Handle image deletion
if (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
    if (!empty($current_image)) {
        $old_upload_path = '../../' . $current_image;
        if (file_exists($old_upload_path)) {
            unlink($old_upload_path);
        }
        
        // Update database to remove image path
        $stmt = $conn->prepare("UPDATE university_rankings SET image_path = '', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
            $current_image = '';
            $success_message = "ลบรูปภาพเรียบร้อยแล้ว";
        } else {
            $errors[] = 'เกิดข้อผิดพลาดในการลบรูปภาพ: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขการจัดอันดับ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
            box-shadow: 5px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            z-index: 1;
        }
        
        .sidebar-brand-text {
            color: #fff;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            font-weight: 700;
            font-size: 0.85rem;
        }
        
        .nav-link:hover {
            color: #fff;
        }
        
        .nav-link.active {
            color: #fff;
            font-weight: 700;
        }
        
        .nav-link i {
            margin-right: 0.25rem;
            font-size: 0.85rem;
        }
        
        .current-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            display: none;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
        }
        
        .card-header h6 {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            box-shadow: 0 2px 5px rgba(78, 115, 223, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a5fc8 0%, #1a3ba5 100%);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #858796 0%, #60616f 100%);
            border: none;
            box-shadow: 0 2px 5px rgba(133, 135, 150, 0.2);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #717380 0%, #4e4f5c 100%);
            box-shadow: 0 5px 15px rgba(133, 135, 150, 0.3);
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            border: none;
            box-shadow: 0 2px 5px rgba(231, 74, 59, 0.2);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #d13a2d 0%, #ab3326 100%);
            box-shadow: 0 5px 15px rgba(231, 74, 59, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="sidebar-brand d-flex align-items-center justify-content-center">
                        <div class="sidebar-brand-text">ระบบจัดการเว็บไซต์</div>
                    </div>
                    
                    <hr class="sidebar-divider my-0">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                <span>แผงควบคุม</span>
                            </a>
                        </li>
                        
                        <hr class="sidebar-divider">
                        
                        <div class="sidebar-heading text-white-50 px-3 py-1 text-uppercase fs-6">
                            จัดการเนื้อหา
                        </div>
                        
                        <?php if (canManageSlideshow()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../slideshow/index.php">
                                <i class="fas fa-fw fa-images"></i>
                                <span>สไลด์โชว์</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (canManageRankings()): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-fw fa-award"></i>
                                <span>การจัดอันดับมหาวิทยาลัย</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (isAdmin()): ?>
                        <hr class="sidebar-divider">
                        
                        <div class="sidebar-heading text-white-50 px-3 py-1 text-uppercase fs-6">
                            จัดการผู้ใช้
                        </div>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../users.php">
                                <i class="fas fa-fw fa-users"></i>
                                <span>ผู้ใช้งาน</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <hr class="sidebar-divider d-none d-md-block">
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../profile.php">
                                <i class="fas fa-fw fa-user"></i>
                                <span>โปรไฟล์</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-fw fa-sign-out-alt"></i>
                                <span>ออกจากระบบ</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">แก้ไขการจัดอันดับ</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปยังรายการการจัดอันดับ
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Edit Ranking Form -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6><i class="fas fa-edit me-2"></i> แก้ไขข้อมูลการจัดอันดับ</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">หัวข้อ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="link" class="form-label">ลิงก์</label>
                                    <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($link); ?>" placeholder="เช่น https://www.up.ac.th/NewsRead.aspx?itemID=34799">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">คำอธิบาย</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ranking_type" class="form-label">ประเภทการจัดอันดับ</label>
                                    <select class="form-select" id="ranking_type" name="ranking_type">
                                        <?php foreach ($ranking_types as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo $ranking_type === $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="display_order" class="form-label">ลำดับการแสดงผล</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" <?php echo $active ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="active">
                                            แสดงรายการนี้
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">รูปภาพปัจจุบัน</label>
                                <?php
                                $image_path = $current_image;
                                $full_path = '../../' . $image_path;
                                $image_exists = file_exists($full_path);
                                
                                if ($image_exists): ?>
                                    <div class="position-relative">
                                        <img src="<?php echo htmlspecialchars('../../' . $image_path); ?>" alt="รูปภาพปัจจุบัน" class="current-image">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                                data-bs-toggle="modal" data-bs-target="#deleteImageModal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i> ไม่พบรูปภาพ (<?php echo htmlspecialchars($image_path); ?>)
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">อัพโหลดรูปภาพใหม่</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">ขนาดไฟล์ไม่เกิน 5MB (jpg, jpeg, png, gif)</small>
                                <img id="imagePreview" class="image-preview" src="#" alt="ตัวอย่างรูปภาพ">
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary me-md-2">รีเซ็ต</button>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Delete Image Modal -->
    <div class="modal fade" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteImageModalLabel">ยืนยันการลบรูปภาพ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>คุณต้องการลบรูปภาพนี้ใช่หรือไม่?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> การลบรูปภาพจะไม่สามารถกู้คืนได้</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form action="" method="post">
                        <input type="hidden" name="delete_image" value="1">
                        <button type="submit" class="btn btn-danger">ลบรูปภาพ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for Image Preview -->
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>
