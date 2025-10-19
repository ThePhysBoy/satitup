<?php
/**
 * Create New University Ranking Item
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to have rankings management access
requireRankingsAccess();

// Initialize variables
$title = '';
$ranking_organization = '';
$ranking_year = date('Y');
$ranking_category = '';
$ranking_position = '';
$ranking_score = '';
$ranking_criteria = '';
$achievement_highlights = '';
$publication_date = '';
$description = '';
$link = '';
$additional_links = '';
$display_order = 0;
$active = 1;
$featured = 0;
$color_theme = '';
$errors = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $ranking_organization = $_POST['ranking_organization'] ?? '';
    $ranking_year = $_POST['ranking_year'] ?? date('Y');
    $ranking_category = $_POST['ranking_category'] ?? '';
    $ranking_position = $_POST['ranking_position'] ?? '';
    $ranking_score = isset($_POST['ranking_score']) ? (float)$_POST['ranking_score'] : null;
    $ranking_criteria = $_POST['ranking_criteria'] ?? '';
    $achievement_highlights = $_POST['achievement_highlights'] ?? '';
    $publication_date = $_POST['publication_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $link = $_POST['link'] ?? '';
    $additional_links = $_POST['additional_links'] ?? '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $active = isset($_POST['active']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $color_theme = $_POST['color_theme'] ?? '';
    
    // Validate form data
    if (empty($title)) {
        $errors[] = 'กรุณากรอกหัวข้อ';
    }
    
    // Handle file upload
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
        $new_file_name = 'ranking_' . uniqid() . '.' . $file_ext;
        $upload_path = 'uploads/' . $new_file_name;
        $db_image_path = 'images/rankings/' . $new_file_name; // เส้นทางสำหรับบันทึกในฐานข้อมูล
        
        // If no errors, save the file and insert into database
        if (empty($errors)) {
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Copy file to public folder
                if (copy($upload_path, '../../' . $db_image_path)) {
                    // Get current user ID for created_by field
                    $user_id = $_SESSION['user_id'] ?? null;
                    
                    // Format publication date for database
                    $formatted_pub_date = !empty($publication_date) ? date('Y-m-d', strtotime($publication_date)) : null;
                    
                    // Insert into database with new fields
                    $stmt = $conn->prepare("INSERT INTO university_rankings (
                        title, ranking_organization, ranking_year, ranking_category, 
                        ranking_position, ranking_score, ranking_criteria, achievement_highlights,
                        publication_date, description, image_path, logo_path, link, additional_links, 
                        display_order, active, featured, color_theme, created_by
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, ?, ?, ?, ?, ?, ?
                    )");
                    
                    $stmt->bind_param(
                        "sssssdsssssssiiiis", 
                        $title, $ranking_organization, $ranking_year, $ranking_category,
                        $ranking_position, $ranking_score, $ranking_criteria, $achievement_highlights,
                        $formatted_pub_date, $description, $db_image_path, $link, $additional_links,
                        $display_order, $active, $featured, $color_theme, $user_id
                    );
                    
                    if ($stmt->execute()) {
                        // Redirect to rankings management page
                        header("Location: index.php?success=1");
                        exit;
                    } else {
                        $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
                        
                        // Delete uploaded files if database insert fails
                        if (file_exists($upload_path)) {
                            unlink($upload_path);
                        }
                        if (file_exists('../../' . $db_image_path)) {
                            unlink('../../' . $db_image_path);
                        }
                    }
                } else {
                    $errors[] = 'เกิดข้อผิดพลาดในการคัดลอกไฟล์';
                    
                    // Delete uploaded file if copy fails
                    if (file_exists($upload_path)) {
                        unlink($upload_path);
                    }
                }
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์';
            }
        }
    } else {
        $errors[] = 'กรุณาเลือกไฟล์รูปภาพ';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการจัดอันดับใหม่ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
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
                    <h1 class="h2">เพิ่มการจัดอันดับใหม่</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปยังรายการการจัดอันดับ
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
                
                <!-- Create Ranking Form -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6><i class="fas fa-plus me-2"></i> ข้อมูลการจัดอันดับ</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary bg-opacity-75">
                                    <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i> ข้อมูลพื้นฐาน</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="title" class="form-label">หัวข้อ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="ranking_organization" class="form-label">องค์กรจัดอันดับ</label>
                                            <input type="text" class="form-control" id="ranking_organization" name="ranking_organization" value="<?php echo htmlspecialchars($ranking_organization); ?>" placeholder="เช่น QS, THE, U-Multirank">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="ranking_year" class="form-label">ปีที่จัดอันดับ</label>
                                            <input type="text" class="form-control" id="ranking_year" name="ranking_year" value="<?php echo htmlspecialchars($ranking_year); ?>" placeholder="เช่น 2023, 2024">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="ranking_category" class="form-label">หมวดหมู่การจัดอันดับ</label>
                                            <input type="text" class="form-control" id="ranking_category" name="ranking_category" value="<?php echo htmlspecialchars($ranking_category); ?>" placeholder="เช่น World University Rankings">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="publication_date" class="form-label">วันที่ประกาศผล</label>
                                            <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?php echo htmlspecialchars($publication_date); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="ranking_position" class="form-label">อันดับที่ได้รับ</label>
                                            <input type="text" class="form-control" id="ranking_position" name="ranking_position" value="<?php echo htmlspecialchars($ranking_position); ?>" placeholder="เช่น 1, 2-5, Top 100">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="ranking_score" class="form-label">คะแนนที่ได้รับ</label>
                                            <input type="number" step="0.01" class="form-control" id="ranking_score" name="ranking_score" value="<?php echo htmlspecialchars($ranking_score); ?>" placeholder="เช่น 85.5">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">คำอธิบายทั่วไป</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ข้อมูลเพิ่มเติม -->
                            <div class="card mb-4">
                                <div class="card-header bg-info bg-opacity-75">
                                    <h6 class="mb-0 text-white"><i class="fas fa-file-alt me-2"></i> ข้อมูลเพิ่มเติม</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="ranking_criteria" class="form-label">เกณฑ์การจัดอันดับ</label>
                                        <textarea class="form-control" id="ranking_criteria" name="ranking_criteria" rows="3"><?php echo htmlspecialchars($ranking_criteria); ?></textarea>
                                        <small class="form-text text-muted">อธิบายเกณฑ์หรือตัวชี้วัดที่ใช้ในการจัดอันดับ</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="achievement_highlights" class="form-label">จุดเด่นที่ทำให้ได้รับการจัดอันดับ</label>
                                        <textarea class="form-control" id="achievement_highlights" name="achievement_highlights" rows="3"><?php echo htmlspecialchars($achievement_highlights); ?></textarea>
                                        <small class="form-text text-muted">ระบุจุดเด่นหรือความสำเร็จที่ทำให้ได้รับการจัดอันดับนี้</small>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="link" class="form-label">ลิงก์หลัก</label>
                                            <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($link); ?>" placeholder="เช่น https://www.up.ac.th/NewsRead.aspx?itemID=34799">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="additional_links" class="form-label">ลิงก์เพิ่มเติม (JSON)</label>
                                            <input type="text" class="form-control" id="additional_links" name="additional_links" value="<?php echo htmlspecialchars($additional_links); ?>" placeholder='{"ข่าวประชาสัมพันธ์": "https://example.com", "รายละเอียด": "https://example.com/detail"}'>
                                            <small class="form-text text-muted">ระบุในรูปแบบ JSON เช่น {"ชื่อลิงก์": "URL", "ชื่อลิงก์2": "URL2"}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- การแสดงผล -->
                            <div class="card mb-4">
                                <div class="card-header bg-success bg-opacity-75">
                                    <h6 class="mb-0 text-white"><i class="fas fa-desktop me-2"></i> การแสดงผล</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="display_order" class="form-label">ลำดับการแสดงผล</label>
                                            <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="color_theme" class="form-label">ธีมสี</label>
                                            <input type="text" class="form-control" id="color_theme" name="color_theme" value="<?php echo htmlspecialchars($color_theme); ?>" placeholder="เช่น blue, green, red หรือ #FF5733">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="active" name="active" <?php echo $active ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="active">
                                                    แสดงรายการนี้
                                                </label>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="featured" name="featured" <?php echo $featured ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="featured">
                                                    รายการที่โดดเด่น
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">รูปภาพ <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <div class="alert alert-info mt-2">
                                    <i class="fas fa-info-circle me-2"></i> <strong>คำแนะนำขนาดรูปภาพที่เหมาะสม:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>ความกว้าง: 960 พิกเซล</li>
                                        <li>ความสูง: 540 พิกเซล (อัตราส่วน 16:9)</li>
                                        <li>เพื่อการแสดงผลที่ดีที่สุด ใช้รูปภาพที่มีอัตราส่วน 16:9 และมีความละเอียดสูง</li>
                                    </ul>
                                </div>
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