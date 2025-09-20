<?php
/**
 * Fix Image Paths in Database
 * This script updates image paths in the database to use the correct path
 */

// Include database connection
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require admin login
requireAdmin();

// Process form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'fix_paths') {
            // Fix paths in database
            $stmt = $conn->prepare("SELECT id, image_path FROM slideshow");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $updated = 0;
            while ($slide = $result->fetch_assoc()) {
                $id = $slide['id'];
                $old_path = $slide['image_path'];
                
                // Check if path needs fixing
                if (strpos($old_path, 'admin/slideshow/uploads/') === 0) {
                    // Extract filename
                    $filename = basename($old_path);
                    $new_path = 'images/slideshow/' . $filename;
                    
                    // Update in database
                    $update_stmt = $conn->prepare("UPDATE slideshow SET image_path = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $new_path, $id);
                    
                    if ($update_stmt->execute()) {
                        $updated++;
                    }
                }
            }
            
            $message = "แก้ไขเส้นทางรูปภาพเรียบร้อยแล้ว $updated รายการ";
        }
        else if ($_POST['action'] === 'copy_images') {
            // Copy images from admin/slideshow/uploads to images/slideshow
            $source_dir = 'uploads/';
            $target_dir = '../../images/slideshow/';
            
            // Create target directory if not exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $copied = 0;
            $files = scandir($source_dir);
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $source = $source_dir . $file;
                $target = $target_dir . $file;
                
                if (is_file($source) && copy($source, $target)) {
                    $copied++;
                }
            }
            
            $message = "คัดลอกรูปภาพเรียบร้อยแล้ว $copied รายการ";
        }
    }
}

// Get slideshow items with problematic paths
$stmt = $conn->prepare("SELECT id, title, image_path FROM slideshow");
$stmt->execute();
$result = $stmt->get_result();

$problem_slides = [];
while ($slide = $result->fetch_assoc()) {
    $image_path = $slide['image_path'];
    $full_path = '../../' . $image_path;
    
    if (!file_exists($full_path)) {
        $problem_slides[] = $slide;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขปัญหารูปภาพ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            box-shadow: 0 2px 5px rgba(78, 115, 223, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a5fc8 0%, #1a3ba5 100%);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2"><i class="fas fa-wrench me-2 text-primary"></i>แก้ไขปัญหารูปภาพ</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการสไลด์
            </a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools me-2"></i>เครื่องมือแก้ไขปัญหา</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>1. แก้ไขเส้นทางรูปภาพในฐานข้อมูล</h6>
                            <p class="text-muted">เปลี่ยนเส้นทางรูปภาพจาก <code>admin/slideshow/uploads/...</code> เป็น <code>images/slideshow/...</code></p>
                            <form method="post">
                                <input type="hidden" name="action" value="fix_paths">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-database me-2"></i>แก้ไขเส้นทางในฐานข้อมูล
                                </button>
                            </form>
                        </div>
                        
                        <div>
                            <h6>2. คัดลอกรูปภาพ</h6>
                            <p class="text-muted">คัดลอกรูปภาพจาก <code>admin/slideshow/uploads/</code> ไปยัง <code>images/slideshow/</code></p>
                            <form method="post">
                                <input type="hidden" name="action" value="copy_images">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกรูปภาพ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>รูปภาพที่มีปัญหา</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($problem_slides)): ?>
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>ไม่พบปัญหารูปภาพ
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ไอดี</th>
                                            <th>หัวข้อ</th>
                                            <th>เส้นทางรูปภาพ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($problem_slides as $slide): ?>
                                            <tr>
                                                <td><?php echo $slide['id']; ?></td>
                                                <td><?php echo htmlspecialchars($slide['title']); ?></td>
                                                <td><code><?php echo htmlspecialchars($slide['image_path']); ?></code></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>คำแนะนำการแก้ไขปัญหา</h5>
            </div>
            <div class="card-body">
                <ol>
                    <li>คลิกปุ่ม <strong>"แก้ไขเส้นทางในฐานข้อมูล"</strong> เพื่อแก้ไขเส้นทางรูปภาพในฐานข้อมูล</li>
                    <li>คลิกปุ่ม <strong>"คัดลอกรูปภาพ"</strong> เพื่อคัดลอกรูปภาพไปยังโฟลเดอร์ที่ถูกต้อง</li>
                    <li>ตรวจสอบว่าโฟลเดอร์ <code>images/slideshow/</code> มีอยู่จริงและมีสิทธิ์ในการเขียนไฟล์</li>
                    <li>หากยังพบปัญหา ให้อัพโหลดรูปภาพใหม่ผ่านหน้าแก้ไขสไลด์</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
