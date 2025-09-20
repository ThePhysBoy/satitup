<?php
/**
 * Edit Slideshow Item
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in and have slideshow management permission
requireLogin();
if (!canManageSlideshow()) {
    header("Location: ../index.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Get slideshow item
$stmt = $conn->prepare("SELECT * FROM slideshow WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if slideshow item exists
if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit;
}

$slide = $result->fetch_assoc();

// Initialize variables
$title = $slide['title'];
$description = $slide['description'];
$link = $slide['link'];
$display_order = $slide['display_order'];
$active = $slide['active'];
$current_image = $slide['image_path'];
$errors = [];

// Check for image deletion messages
$image_deleted = isset($_GET['image_deleted']) ? $_GET['image_deleted'] : null;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $link = $_POST['link'] ?? '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate form data
    if (empty($title)) {
        $errors[] = 'กรุณากรอกหัวข้อสไลด์';
    }
    
    // Handle file upload if new image is provided
    $image_path = $current_image;
    
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
        
        // Generate unique filename
        $new_file_name = 'slide_' . uniqid() . '.' . $file_ext;
        $upload_path = 'uploads/' . $new_file_name;
        $db_image_path = 'admin/slideshow/uploads/' . $new_file_name; // เส้นทางสำหรับบันทึกในฐานข้อมูล
        
        // If no errors, save the file
        if (empty($errors)) {
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old image if it exists
                if (file_exists($current_image)) {
                    unlink($current_image);
                }
                
                $image_path = $db_image_path;
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์';
            }
        }
    }
    
    // If no errors, update the database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE slideshow SET title = ?, description = ?, image_path = ?, link = ?, display_order = ?, active = ? WHERE id = ?");
        $stmt->bind_param("ssssiis", $title, $description, $image_path, $link, $display_order, $active, $id);
        
        if ($stmt->execute()) {
            // Redirect to slideshow management page
            header("Location: index.php?updated=1");
            exit;
        } else {
            $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
            
            // If there was an error and we uploaded a new image, delete it
            if ($image_path !== $current_image && file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสไลด์ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
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
            margin-bottom: 10px;
            display: block;
        }
        
        .position-relative {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        
        .position-absolute {
            position: absolute;
        }
        
        .top-0 {
            top: 0;
        }
        
        .end-0 {
            right: 0;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            display: none;
            margin-top: 10px;
            border-radius: 5px;
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
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-fw fa-images"></i>
                                <span>สไลด์โชว์</span>
                            </a>
                        </li>
                        
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
                        
                        <hr class="sidebar-divider d-none d-md-block">
                        
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
                    <h1 class="h2">แก้ไขสไลด์</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปยังรายการสไลด์
                        </a>
                    </div>
                </div>
                
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
                
                <?php if ($image_deleted === '1'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> ลบรูปภาพเรียบร้อยแล้ว
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($image_deleted === '0'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> เกิดข้อผิดพลาดในการลบรูปภาพ
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Edit Slideshow Form -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสไลด์</h6>
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
                                    <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($link); ?>" placeholder="เช่น about-history.php">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">คำอธิบาย</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="image" class="form-label">รูปภาพ</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="form-text text-muted">ขนาดไฟล์ไม่เกิน 5MB (jpg, jpeg, png, gif)</small>
                                    <small class="form-text text-muted d-block">หากไม่เลือกรูปภาพใหม่ จะใช้รูปภาพเดิม</small>
                                    
                                    <div class="mt-2">
                                        <label>รูปภาพปัจจุบัน:</label>
                                        <?php if (!empty($current_image)): ?>
                                            <?php
                                            $image_path = $current_image;
                                            $full_path = '../../' . $image_path;
                                            $image_exists = file_exists($full_path);
                                            ?>
                                            <?php if ($image_exists): ?>
                                                <div class="position-relative d-inline-block">
                                                    <img src="<?php echo htmlspecialchars('../../' . $image_path); ?>" alt="รูปภาพปัจจุบัน" class="current-image">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                                                            data-bs-toggle="modal" data-bs-target="#deleteImageModal"
                                                            title="ลบรูปภาพ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i> ไม่พบไฟล์รูปภาพ
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i> ยังไม่มีรูปภาพ
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <img id="imagePreview" class="image-preview" src="#" alt="ตัวอย่างรูปภาพใหม่">
                                </div>
                                <div class="col-md-3">
                                    <label for="display_order" class="form-label">ลำดับการแสดงผล</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" <?php echo $active ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="active">
                                            แสดงสไลด์นี้
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Delete Image Confirmation Modal -->
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
                    <form action="delete_image.php" method="post">
                        <input type="hidden" name="slide_id" value="<?php echo $id; ?>">
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
