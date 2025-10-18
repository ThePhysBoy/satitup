<?php
/**
 * หน้าจัดการเครือข่ายความร่วมมือ (Partners Management)
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in
requireLogin();

// Check permissions (only PR officers and admins can manage partners)
if (!isPrOfficer() && !isAdmin()) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนนี้";
    header('Location: ../index.php');
    exit;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    
    // ลบรูปภาพก่อน
    $img_query = "SELECT logo_image, featured_image FROM partners WHERE id = ?";
    $stmt = $conn->prepare($img_query);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // ลบไฟล์รูปภาพ
        if (!empty($row['logo_image']) && file_exists('../../' . $row['logo_image'])) {
            unlink('../../' . $row['logo_image']);
        }
        if (!empty($row['featured_image']) && file_exists('../../' . $row['featured_image'])) {
            unlink('../../' . $row['featured_image']);
        }
    }
    
    // ลบข้อมูลจากฐานข้อมูล (รูปแกลเลอรี่จะถูกลบอัตโนมัติด้วย CASCADE)
    $delete_query = "DELETE FROM partners WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "ลบข้อมูลพันธมิตรสำเร็จ";
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $conn->error;
    }
    
    header('Location: index.php');
    exit;
}

// Get all partners
$partners_query = "SELECT * FROM partners ORDER BY order_number ASC, created_at DESC";
$partners_result = $conn->query($partners_query);

// Get partner count
$count_query = "SELECT COUNT(*) as total FROM partners";
$count_result = $conn->query($count_query);
$total_partners = $count_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการเครือข่ายความร่วมมือ - ระบบจัดการเว็บไซต์</title>
    
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
        
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .partner-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .partner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .partner-logo-box {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 1rem;
        }
        
        .partner-logo-box img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }
        
        .partner-info {
            padding: 1.5rem;
        }
        
        .badge-status {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-handshake text-primary me-2"></i>
                        จัดการเครือข่ายความร่วมมือ
                    </h1>
                    <p class="text-muted mb-0">จัดการข้อมูลหน่วยงานพันธมิตรและโครงการความร่วมมือ</p>
                </div>
                <div>
                    <a href="create.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>เพิ่มพันธมิตรใหม่
                    </a>
                    <a href="../index.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php 
            echo $_SESSION['error_message']; 
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                        <h3 class="mb-1"><?php echo $total_partners; ?></h3>
                        <p class="text-muted mb-0">พันธมิตรทั้งหมด</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partners List -->
        <?php if ($partners_result && $partners_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($partner = $partners_result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="partner-card">
                    <div class="partner-logo-box">
                        <?php if (!empty($partner['logo_image']) && file_exists('../../' . $partner['logo_image'])): ?>
                            <img src="../../<?php echo htmlspecialchars($partner['logo_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($partner['name']); ?>">
                        <?php else: ?>
                            <i class="fas fa-image fa-4x text-muted"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="partner-info">
                        <h5 class="mb-2"><?php echo htmlspecialchars($partner['name']); ?></h5>
                        
                        <?php if (!empty($partner['project_name'])): ?>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-project-diagram me-1"></i>
                            <?php echo htmlspecialchars($partner['project_name']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <span class="badge badge-status <?php echo $partner['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo $partner['status'] === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                            </span>
                            <span class="badge badge-status bg-info">
                                ลำดับที่ <?php echo $partner['order_number']; ?>
                            </span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="../../partners/view.php?id=<?php echo $partner['id']; ?>" 
                               class="btn btn-sm btn-outline-primary btn-action flex-fill" 
                               target="_blank">
                                <i class="fas fa-eye me-1"></i>ดู
                            </a>
                            <a href="edit.php?id=<?php echo $partner['id']; ?>" 
                               class="btn btn-sm btn-warning btn-action flex-fill">
                                <i class="fas fa-edit me-1"></i>แก้ไข
                            </a>
                            <a href="manage_gallery.php?id=<?php echo $partner['id']; ?>" 
                               class="btn btn-sm btn-info btn-action flex-fill">
                                <i class="fas fa-images me-1"></i>แกลเลอรี่
                            </a>
                            <button onclick="confirmDelete(<?php echo $partner['id']; ?>)" 
                                    class="btn btn-sm btn-danger btn-action">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-handshake fa-5x text-muted mb-3"></i>
            <h4 class="text-muted">ยังไม่มีข้อมูลพันธมิตร</h4>
            <p class="text-muted">เริ่มต้นเพิ่มพันธมิตรคนแรกของคุณ</p>
            <a href="create.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>เพิ่มพันธมิตรใหม่
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Delete Confirmation -->
    <script>
    function confirmDelete(id) {
        if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบพันธมิตรนี้?\n\nการลบจะไม่สามารถกู้คืนได้')) {
            window.location.href = 'index.php?action=delete&id=' + id;
        }
    }
    </script>
</body>
</html>

