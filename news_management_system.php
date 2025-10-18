<?php
// Start session and check authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db_connect.php';

// Handle form submissions
$message = '';
$messageType = '';

// Handle announcement creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? 'announcement';
        
        // Handle file upload
        $filePath = null;
        $fileName = null;
        
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
            $uploadDir = 'uploads/announcements/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
            if ($fileExt == 'pdf') {
                $fileName = $_FILES['pdf_file']['name'];
                $filePath = $uploadDir . uniqid() . '_' . $fileName;
                
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $filePath)) {
                    // File uploaded successfully
                } else {
                    $message = 'ไม่สามารถอัพโหลดไฟล์ได้';
                    $messageType = 'danger';
                }
            } else {
                $message = 'กรุณาอัพโหลดไฟล์ PDF เท่านั้น';
                $messageType = 'danger';
            }
        }
        
        if (empty($message)) {
            try {
                $sql = "INSERT INTO announcements (title, content, category, file_path, file_name, user_id) 
                        VALUES (:title, :content, :category, :file_path, :file_name, :user_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'title' => $title,
                    'content' => $content,
                    'category' => $category,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'user_id' => $_SESSION['user_id']
                ]);
                $message = 'เพิ่มประกาศเรียบร้อยแล้ว';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    }
    
    // Handle announcement deletion (superadmin only)
    if ($_POST['action'] == 'delete' && $_SESSION['role'] == 'superadmin') {
        $id = $_POST['announcement_id'] ?? 0;
        
        try {
            // Get file path first
            $stmt = $pdo->prepare("SELECT file_path FROM announcements WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $announcement = $stmt->fetch();
            
            // Delete the file if exists
            if ($announcement && $announcement['file_path'] && file_exists($announcement['file_path'])) {
                unlink($announcement['file_path']);
            }
            
            // Delete from database
            $sql = "DELETE FROM announcements WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $message = 'ลบประกาศเรียบร้อยแล้ว';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Handle announcement update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_action']) && $_SESSION['role'] == 'superadmin') {
    $id = $_POST['announcement_id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'announcement';
    
    try {
        $sql = "UPDATE announcements SET title = :title, content = :content, category = :category 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'id' => $id
        ]);
        $message = 'แก้ไขประกาศเรียบร้อยแล้ว';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get filter parameters
$categoryFilter = $_GET['category'] ?? 'all';
$searchTerm = $_GET['search'] ?? '';

// Build query
$sql = "SELECT a.*, u.fullname, u.position 
        FROM announcements a 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE 1=1";

$params = [];

if ($categoryFilter != 'all') {
    $sql .= " AND a.category = :category";
    $params['category'] = $categoryFilter;
}

if (!empty($searchTerm)) {
    $sql .= " AND (a.title LIKE :search OR a.content LIKE :search)";
    $params['search'] = '%' . $searchTerm . '%';
}

$sql .= " ORDER BY a.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $announcements = $stmt->fetchAll();
} catch (PDOException $e) {
    $announcements = [];
    $message = 'เกิดข้อผิดพลาดในการดึงข้อมูล';
    $messageType = 'danger';
}

// Get statistics for dashboard
$stats = [];
try {
    // Total announcements
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM announcements");
    $stats['total'] = $stmt->fetch()['total'];
    
    // Announcements by category
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM announcements GROUP BY category");
    $categoryStats = $stmt->fetchAll();
    foreach ($categoryStats as $cat) {
        $stats[$cat['category']] = $cat['count'];
    }
    
    // Today's announcements
    $stmt = $pdo->query("SELECT COUNT(*) as today FROM announcements WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $stmt->fetch()['today'];
} catch (PDOException $e) {
    // Handle error silently
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการข่าวประชาสัมพันธ์ - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Sarabun', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-weight: bold;
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 20px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Form Styles */
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .form-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        /* Announcement List */
        .announcement-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .announcement-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left-color: #667eea;
        }
        
        .announcement-item.announcement {
            border-left-color: #667eea;
        }
        
        .announcement-item.procurement {
            border-left-color: #28a745;
        }
        
        .announcement-item.recruitment {
            border-left-color: #ffc107;
        }
        
        .announcement-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .announcement-content {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .announcement-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .meta-info {
            display: flex;
            gap: 20px;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
        }
        
        .meta-info i {
            color: #667eea;
        }
        
        .category-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .category-badge.announcement {
            background: #e8eaf6;
            color: #5c6bc0;
        }
        
        .category-badge.procurement {
            background: #e8f5e9;
            color: #43a047;
        }
        
        .category-badge.recruitment {
            background: #fff3e0;
            color: #fb8c00;
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-pills .nav-link {
            border-radius: 20px;
            padding: 8px 20px;
            margin: 0 5px;
            transition: all 0.3s;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Button Styles */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            transition: transform 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #fafafa;
            transition: all 0.3s;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: #f5f7ff;
        }
        
        .file-upload-area i {
            font-size: 3rem;
            color: #999;
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .announcement-meta {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                width: 100%;
                display: flex;
                gap: 10px;
            }
            
            .action-buttons .btn {
                flex: 1;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-in {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-bullhorn"></i> ระบบจัดการข่าวประชาสัมพันธ์
                    </h1>
                    <small>โรงเรียนสาธิตมหาวิทยาลัยพะเยา</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="user-info justify-content-md-end">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['fullname'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
                            <small><?php echo htmlspecialchars($_SESSION['position']); ?></small>
                        </div>
                        <a href="logout.php" class="btn btn-light btn-sm ms-3">
                            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Dashboard Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
                    <div class="stat-label">ประกาศทั้งหมด</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="stat-number"><?php echo $stats['procurement'] ?? 0; ?></div>
                    <div class="stat-label">จัดซื้อจัดจ้าง</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                    <div class="stat-number"><?php echo $stats['recruitment'] ?? 0; ?></div>
                    <div class="stat-label">รับสมัครงาน</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <div class="stat-number"><?php echo $stats['today'] ?? 0; ?></div>
                    <div class="stat-label">ประกาศวันนี้</div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show animate-in" role="alert">
                <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Announcement Form -->
        <div class="form-section animate-in">
            <div class="form-header">
                <h4><i class="fas fa-plus-circle"></i> เพิ่มประกาศใหม่</h4>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">หัวข้อประกาศ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="announcement">คำสั่งและประกาศ</option>
                                <option value="procurement">การจัดซื้อจัดจ้าง</option>
                                <option value="recruitment">การรับสมัครงาน</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">เนื้อหาประกาศ <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="pdf_file" class="form-label">แนบไฟล์ PDF (ถ้ามี)</label>
                    <div class="file-upload-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mb-2">ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                        <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                        <small class="text-muted">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 10MB</small>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> ล้างข้อมูล
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save"></i> บันทึกประกาศ
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $categoryFilter == 'all' ? 'active' : ''; ?>" 
                               href="?category=all">
                                ทั้งหมด
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $categoryFilter == 'announcement' ? 'active' : ''; ?>" 
                               href="?category=announcement">
                                คำสั่งและประกาศ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $categoryFilter == 'procurement' ? 'active' : ''; ?>" 
                               href="?category=procurement">
                                จัดซื้อจัดจ้าง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $categoryFilter == 'recruitment' ? 'active' : ''; ?>" 
                               href="?category=recruitment">
                                รับสมัครงาน
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <input type="search" class="form-control" name="search" 
                               placeholder="ค้นหาประกาศ..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <button type="submit" class="btn btn-gradient ms-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Announcements List -->
        <div class="announcements-list">
            <?php if (empty($announcements)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted">ไม่พบประกาศในขณะนี้</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-item <?php echo $announcement['category']; ?> animate-in">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="announcement-title">
                                <?php echo htmlspecialchars($announcement['title']); ?>
                            </h5>
                            <span class="category-badge <?php echo $announcement['category']; ?>">
                                <?php 
                                    $categoryLabels = [
                                        'announcement' => 'คำสั่งและประกาศ',
                                        'procurement' => 'จัดซื้อจัดจ้าง',
                                        'recruitment' => 'รับสมัครงาน'
                                    ];
                                    echo $categoryLabels[$announcement['category']] ?? 'อื่นๆ';
                                ?>
                            </span>
                        </div>
                        
                        <div class="announcement-content">
                            <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                        </div>
                        
                        <?php if ($announcement['file_path']): ?>
                            <div class="mb-3">
                                <a href="<?php echo htmlspecialchars($announcement['file_path']); ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> 
                                    <?php echo htmlspecialchars($announcement['file_name']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="announcement-meta">
                            <div class="meta-info">
                                <span>
                                    <i class="fas fa-user"></i> 
                                    <?php echo htmlspecialchars($announcement['fullname'] ?? 'ไม่ระบุ'); ?>
                                </span>
                                <span>
                                    <i class="fas fa-briefcase"></i> 
                                    <?php echo htmlspecialchars($announcement['position'] ?? 'ไม่ระบุ'); ?>
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('d/m/Y H:i', strtotime($announcement['created_at'])); ?>
                                </span>
                            </div>
                            
                            <?php if ($_SESSION['role'] == 'superadmin'): ?>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?php echo $announcement['id']; ?>">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>
                                    <form method="POST" action="" style="display: inline;" 
                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบประกาศนี้?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Edit Modal -->
                    <?php if ($_SESSION['role'] == 'superadmin'): ?>
                        <div class="modal fade" id="editModal<?php echo $announcement['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-edit"></i> แก้ไขประกาศ
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="update_action" value="update">
                                            <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">หัวข้อประกาศ</label>
                                                <input type="text" class="form-control" name="title" 
                                                       value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">หมวดหมู่</label>
                                                <select class="form-select" name="category" required>
                                                    <option value="announcement" <?php echo $announcement['category'] == 'announcement' ? 'selected' : ''; ?>>
                                                        คำสั่งและประกาศ
                                                    </option>
                                                    <option value="procurement" <?php echo $announcement['category'] == 'procurement' ? 'selected' : ''; ?>>
                                                        การจัดซื้อจัดจ้าง
                                                    </option>
                                                    <option value="recruitment" <?php echo $announcement['category'] == 'recruitment' ? 'selected' : ''; ?>>
                                                        การรับสมัครงาน
                                                    </option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">เนื้อหาประกาศ</label>
                                                <textarea class="form-control" name="content" rows="5" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                ยกเลิก
                                            </button>
                                            <button type="submit" class="btn btn-gradient">
                                                <i class="fas fa-save"></i> บันทึกการแก้ไข
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5" style="background: #f8f9fa;">
        <div class="container">
            <p class="mb-0 text-muted">
                © 2024 โรงเรียนสาธิตมหาวิทยาลัยพะเยา | ระบบจัดการข่าวประชาสัมพันธ์
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload enhancement
        document.getElementById('pdf_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'ไม่ได้เลือกไฟล์';
            const fileInfo = e.target.nextElementSibling;
            if (e.target.files[0]) {
                const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);
                fileInfo.textContent = `ไฟล์ที่เลือก: ${fileName} (${fileSize} MB)`;
                if (fileSize > 10) {
                    fileInfo.classList.add('text-danger');
                    fileInfo.textContent += ' - ไฟล์ใหญ่เกินไป!';
                } else {
                    fileInfo.classList.remove('text-danger');
                }
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
