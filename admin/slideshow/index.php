<?php
/**
 * Slideshow Management Page
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

$warning_message = null;
$warning_details = [];

// Delete slideshow item if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM slideshow WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $slide = $result->fetch_assoc();
        $image_path = $slide['image_path'];
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM slideshow WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Delete image file if it exists
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            $success_message = "ลบสไลด์เรียบร้อยแล้ว";
        } else {
            $error_message = "เกิดข้อผิดพลาดในการลบสไลด์";
        }
    }
}

// Handle display order update
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['display_order'])
    && is_array($_POST['display_order'])
) {
    $orders = $_POST['display_order'];
    $stmt = $conn->prepare("UPDATE slideshow SET display_order = ? WHERE id = ?");

    if ($stmt) {
        $update_error = false;
        foreach ($orders as $slide_id => $order_value) {
            if (!ctype_digit((string) $slide_id)) {
                continue;
            }

            $display_order = is_numeric($order_value) ? (int) $order_value : 0;
            $slide_id_int = (int) $slide_id;

            $stmt->bind_param("ii", $display_order, $slide_id_int);
            if (!$stmt->execute()) {
                $update_error = true;
                $error_text = 'เกิดข้อผิดพลาดในการบันทึกลำดับสไลด์ ID ' . $slide_id_int . ': ' . $stmt->error;
                if (isset($error_message)) {
                    $error_message .= '<br>' . $error_text;
                } else {
                    $error_message = $error_text;
                }
            }
        }
        $stmt->close();

        if (!$update_error) {
            $success_text = "บันทึกลำดับสไลด์เรียบร้อยแล้ว";
            header('Location: index.php?updated_order=1');
            exit;
        }
    } else {
        $error_text = 'ไม่สามารถเตรียมคำสั่งอัปเดตลำดับสไลด์ได้: ' . $conn->error;
        if (isset($error_message)) {
            $error_message .= '<br>' . $error_text;
        } else {
            $error_message = $error_text;
        }
    }
}

// Handle import result messages
if (isset($_GET['imported'])) {
    $inserted = isset($_GET['inserted']) ? (int)$_GET['inserted'] : 0;
    $skipped = isset($_GET['skipped']) ? (int)$_GET['skipped'] : 0;
    $missing = isset($_GET['missing']) ? (int)$_GET['missing'] : 0;
    $success_text = "นำเข้ารูปสไลด์สำเร็จ: เพิ่ม {$inserted} รายการ, ข้าม {$skipped} รายการ";
    if (isset($success_message)) {
        $success_message .= '<br>' . $success_text;
    } else {
        $success_message = $success_text;
    }
    if ($missing > 0 && !empty($_GET['missing_files'])) {
        $decoded = json_decode(base64_decode($_GET['missing_files']), true);
        if (is_array($decoded)) {
            $warning_details = $decoded;
        }
        $warning_message = "ไม่พบไฟล์รูปจำนวน {$missing} ไฟล์ จึงไม่ได้เพิ่มเข้าสู่ระบบ";
    }
}

// Display order update success message
if (isset($_GET['updated_order'])) {
    $success_text = "บันทึกลำดับสไลด์เรียบร้อยแล้ว";
    if (isset($success_message)) {
        $success_message .= '<br>' . $success_text;
    } else {
        $success_message = $success_text;
    }
}

// Get all slideshow items
$stmt = $conn->prepare("SELECT * FROM slideshow ORDER BY display_order ASC, id ASC");
$stmt->execute();
$result = $stmt->get_result();
$slides = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสไลด์โชว์ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
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
        
        .slide-image-preview {
            width: 150px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .slide-image-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .border-left-primary {
            border-left: 4px solid #4e73df;
        }
        
        .border-left-success {
            border-left: 4px solid #1cc88a;
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
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #f8f9fc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #4e73df;
            border-bottom: 2px solid #e3e6f0;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fc;
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
        
        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #1cc88a 0%, #169a6f 100%) !important;
        }
        
        .badge.bg-secondary {
            background: linear-gradient(135deg, #858796 0%, #60616f 100%) !important;
        }
        
        .sidebar {
            box-shadow: 5px 0 10px rgba(0,0,0,0.05);
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <h1 class="h2"><i class="fas fa-sliders-h me-2 text-primary"></i>จัดการสไลด์โชว์</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="fix_images.php" class="btn btn-warning me-2">
                            <i class="fas fa-wrench me-1"></i> แก้ไขปัญหารูปภาพ
                        </a>
                        <a href="import_from_static.php" class="btn btn-info me-2">
                            <i class="fas fa-file-import me-1"></i> นำเข้าจากสไลด์เดิม
                        </a>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> เพิ่มสไลด์ใหม่
                        </a>
                    </div>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            จำนวนสไลด์ทั้งหมด
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($slides); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-images fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            สไลด์ที่กำลังแสดง
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $active_count = 0;
                                            foreach ($slides as $slide) {
                                                if ($slide['active']) $active_count++;
                                            }
                                            echo $active_count;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($warning_message)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>คำเตือน:</strong> <?php echo htmlspecialchars($warning_message); ?>
                        <?php if (!empty($warning_details)): ?>
                            <details class="mt-2">
                                <summary>ดูรายชื่อไฟล์ที่ไม่พบ</summary>
                                <ul class="mb-0">
                                    <?php foreach ($warning_details as $missing_file): ?>
                                        <li><?php echo htmlspecialchars($missing_file); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Slideshow Table -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6><i class="fas fa-images me-2"></i> รายการสไลด์ทั้งหมด</h6>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="15%">รูปภาพ</th>
                                            <th width="25%">หัวข้อ</th>
                                            <th width="15%">คำอธิบาย</th>
                                            <th width="10%">ลำดับแสดงผล</th>
                                            <th width="10%">สถานะ</th>
                                            <th width="20%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($slides)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">ไม่พบข้อมูลสไลด์</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($slides as $index => $slide): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td>
                                                        <?php
                                                        $image_path = $slide['image_path'];
                                                        $full_path = '../../' . $image_path;
                                                        $image_exists = file_exists($full_path);
                                                        
                                                        if ($image_exists): ?>
                                                            <img src="<?php echo htmlspecialchars('../../' . $image_path); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="slide-image-preview">
                                                        <?php else: ?>
                                                            <div class="text-center p-3 bg-light rounded">
                                                                <i class="fas fa-image text-secondary fa-2x mb-2"></i>
                                                                <p class="small text-muted mb-0">ไม่พบรูปภาพ</p>
                                                                <small class="text-danger"><?php echo htmlspecialchars($image_path); ?></small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($slide['title']); ?></td>
                                                    <td><?php echo htmlspecialchars(substr($slide['description'], 0, 80)) . (strlen($slide['description']) > 80 ? '...' : ''); ?></td>
                                                    <td style="min-width:120px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" class="form-control" name="display_order[<?php echo $slide['id']; ?>]" value="<?php echo (int)$slide['display_order']; ?>" min="0">
                                                            <span class="input-group-text"><i class="fas fa-sort-numeric-down"></i></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($slide['active']): ?>
                                                            <span class="badge bg-success">แสดง</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">ซ่อน</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <a href="edit.php?id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> แก้ไข
                                                            </a>
                                                            <a href="preview.php?id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-info" title="ดูตัวอย่าง">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="index.php?delete=<?php echo $slide['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณต้องการลบสไลด์นี้ใช่หรือไม่?')">
                                                                <i class="fas fa-trash"></i> ลบ
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (!empty($slides)): ?>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-3">
                                        <i class="fas fa-save me-1"></i> บันทึกลำดับการแสดงผล
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
