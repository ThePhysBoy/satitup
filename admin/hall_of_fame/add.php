<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// จัดการเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $student_name = $_POST['student_name'];
    $class = $_POST['class'];
    $year = $_POST['year'];
    $achievement = $_POST['achievement'];
    $description = $_POST['description'];
    $date_achieved = $_POST['date_achieved'] ?: null;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];
    
    // จัดการอัพโหลดรูปภาพหลัก
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../uploads/hall_of_fame/' . $category . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'hall_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/hall_of_fame/' . $category . '/' . $new_filename;
            }
        }
    }
    
    // จัดการอัพโหลดใบประกาศ
    $certificate_path = null;
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $upload_dir = '../../uploads/hall_of_fame/' . $category . '/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION);
        $new_filename = 'cert_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                $certificate_path = 'uploads/hall_of_fame/' . $category . '/certificates/' . $new_filename;
            }
        }
    }
    
    // บันทึกลงฐานข้อมูล
    $sql = "INSERT INTO hall_of_fame 
            (category, title, student_name, class, year, achievement, description, 
             image_path, certificate_path, date_achieved, featured, status, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssisi", 
        $category, $title, $student_name, $class, $year, $achievement, $description,
        $image_path, $certificate_path, $date_achieved, $featured, $status, $created_by
    );
    
    if ($stmt->execute()) {
        $hall_id = $conn->insert_id;
        
        // จัดการอัพโหลดรูปภาพเพิ่มเติม (Gallery)
        if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
            $gallery_dir = '../../uploads/hall_of_fame/' . $category . '/gallery/';
            if (!is_dir($gallery_dir)) {
                mkdir($gallery_dir, 0777, true);
            }
            
            $gallery_stmt = $conn->prepare("INSERT INTO hall_of_fame_gallery (hall_id, image_path, caption, sort_order) VALUES (?, ?, ?, ?)");
            
            for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
                if ($_FILES['gallery']['error'][$i] == 0) {
                    $file_extension = pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION);
                    $new_filename = 'gallery_' . date('YmdHis') . '_' . $i . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $gallery_dir . $new_filename;
                    
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array(strtolower($file_extension), $allowed_types)) {
                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_file)) {
                            $gallery_path = 'uploads/hall_of_fame/' . $category . '/gallery/' . $new_filename;
                            $caption = isset($_POST['gallery_captions'][$i]) ? $_POST['gallery_captions'][$i] : '';
                            $sort_order = $i;
                            
                            $gallery_stmt->bind_param("issi", $hall_id, $gallery_path, $caption, $sort_order);
                            $gallery_stmt->execute();
                        }
                    }
                }
            }
        }
        
        $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
        header("Location: index.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มรางวัล - หอเกียรติยศ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: #f5f5f5;
        }
        
        .sidebar {
            background: #2c3e50;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            padding: 30px;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .form-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
        }
        
        .gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .gallery-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">
                    <i class="fas fa-trophy"></i> Admin Panel
                </h4>
                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="add.php">
                        <i class="fas fa-plus me-2"></i> เพิ่มรางวัล
                    </a>
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-2"></i> กลับหน้า Admin
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="form-card">
                    <div class="form-header">
                        <h3><i class="fas fa-plus-circle me-2"></i> เพิ่มรางวัลใหม่</h3>
                        <p class="text-muted mb-0">กรอกข้อมูลรางวัลและผลงานของนักเรียน</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">เลือกหมวดหมู่</option>
                                        <option value="academic">วิชาการ</option>
                                        <option value="sports">กีฬา</option>
                                        <option value="music">ดนตรี</option>
                                        <option value="scholarship">ทุนการศึกษา</option>
                                        <option value="outstanding">ความโดดเด่น</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="active">เผยแพร่</option>
                                        <option value="inactive">ไม่เผยแพร่</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อรางวัล/ผลงาน <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อนักเรียน <span class="text-danger">*</span></label>
                                    <input type="text" name="student_name" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">ระดับชั้น</label>
                                    <input type="text" name="class" class="form-control" 
                                           placeholder="เช่น ม.6/1">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">ปีการศึกษา</label>
                                    <input type="text" name="year" class="form-control" 
                                           placeholder="เช่น 2567">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">วันที่ได้รับรางวัล</label>
                                    <input type="date" name="date_achieved" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3 pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="featured" class="form-check-input" id="featured">
                                        <label class="form-check-label" for="featured">
                                            <i class="fas fa-star text-warning"></i> แนะนำ (Featured)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ผลงานและคำอธิบาย -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">ผลงานและความสำเร็จ</label>
                                    <textarea name="achievement" class="form-control" rows="4" 
                                              placeholder="อธิบายผลงาน รางวัล หรือความสำเร็จที่ได้รับ"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">รายละเอียดเพิ่มเติม</label>
                                    <textarea name="description" class="form-control" rows="4" 
                                              placeholder="รายละเอียดเพิ่มเติม เบื้องหลัง หรือเรื่องราวที่น่าสนใจ"></textarea>
                                </div>
                            </div>
                            
                            <!-- อัพโหลดไฟล์ -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพหลัก</label>
                                    <input type="file" name="image" class="form-control" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">รองรับ: JPG, PNG, GIF, WEBP</small>
                                    <div id="imagePreview"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ใบประกาศเกียรติคุณ</label>
                                    <input type="file" name="certificate" class="form-control" 
                                           accept=".pdf,image/*">
                                    <small class="text-muted">รองรับ: PDF, JPG, PNG</small>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพเพิ่มเติม (Gallery)</label>
                                    <input type="file" name="gallery[]" class="form-control" 
                                           accept="image/*" multiple onchange="previewGallery(this)">
                                    <small class="text-muted">สามารถเลือกหลายรูปได้</small>
                                    <div id="galleryPreview" class="gallery-preview"></div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function previewGallery(input) {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    </script>
</body>
</html>
