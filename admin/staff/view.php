<?php
// Include necessary files
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once './staff_functions.php';

// Require user to be logged in and have permission
requireLogin();
if (!canManageStaff()) {
    header("Location: ../index.php");
    exit;
}

// Get staff ID from URL
$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if staff exists
$staff = getStaffById($staff_id, $conn);
if (!$staff) {
    header("Location: index.php?error=ไม่พบข้อมูลบุคลากร");
    exit;
}

// Get staff positions
$positions = getStaffPositions($staff_id, $conn);

// Set page title
$page_title = "ข้อมูลบุคลากร: " . formatThaiName($staff['title'], $staff['first_name'], $staff['last_name']);
$include_summernote = false;

// Set template variables
$page_header_icon = '<i class="fas fa-user me-3"></i>';
$back_button = true;
$back_url = 'index.php';
$back_text = 'กลับไปหน้ารายการ';

// Start content output
ob_start();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">ข้อมูลบุคลากร</h1>
        <div>
            <a href="edit.php?id=<?php echo $staff_id; ?>" class="btn btn-warning btn-sm rounded-pill px-4 me-2">
                <i class="fas fa-edit me-2"></i> แก้ไขข้อมูล
            </a>
            <a href="index.php" class="btn btn-secondary btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> กลับไปหน้ารายการ
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Staff Profile -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลส่วนตัว</h6>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($staff['image_path'])): ?>
                        <img src="../../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                             class="img-fluid rounded-circle mb-3" style="max-height: 200px; max-width: 200px;">
                    <?php else: ?>
                        <img src="../../assets/img/user-placeholder.png" 
                             class="img-fluid rounded-circle mb-3" style="max-height: 200px; max-width: 200px;">
                    <?php endif; ?>
                    
                    <h5 class="font-weight-bold mb-0">
                        <?php echo htmlspecialchars(formatThaiName($staff['title'], $staff['first_name'], $staff['last_name'])); ?>
                    </h5>
                    
                    <?php
                    // Get primary position
                    $primary_position = 'ไม่ระบุตำแหน่ง';
                    foreach ($positions as $pos) {
                        if ($pos['is_primary']) {
                            $primary_position = $pos['position_name'];
                            break;
                        }
                    }
                    ?>
                    <p class="text-primary mb-3"><?php echo htmlspecialchars($primary_position); ?></p>
                    
                    <?php if ($staff['is_head']): ?>
                        <div class="badge bg-primary mb-3 px-3 py-2">หัวหน้า<?php echo htmlspecialchars($staff['department_name'] ?? 'หน่วยงาน'); ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($staff['cv_file_path']) && file_exists('../../' . $staff['cv_file_path'])): ?>
                    <div class="mb-3">
                        <a href="../../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           class="btn btn-danger btn-block" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> ดาวน์โหลด CV (PDF)
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="list-group list-group-flush text-start mt-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building me-2"></i> หน่วยงาน/กลุ่มสาระ</span>
                            <span class="text-primary"><?php echo htmlspecialchars($staff['department_name'] ?? 'ไม่ระบุ'); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-tag me-2"></i> ประเภทบุคลากร</span>
                            <span>
                                <?php if ($staff['department_type'] === 'academic'): ?>
                                    <span class="badge bg-info">สายวิชาการ</span>
                                <?php elseif ($staff['department_type'] === 'service'): ?>
                                    <span class="badge bg-success">สายบริการ</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">ไม่ระบุ</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if (!empty($staff['email'])): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope me-2"></i> อีเมล</span>
                                <span class="text-primary"><?php echo htmlspecialchars($staff['email']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($staff['phone'])): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-phone me-2"></i> เบอร์โทรศัพท์</span>
                                <span class="text-primary"><?php echo htmlspecialchars($staff['phone']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-toggle-on me-2"></i> สถานะ</span>
                            <span>
                                <?php if ($staff['status'] === 'active'): ?>
                                    <span class="badge bg-success">เปิดใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">ปิดใช้งาน</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Staff Details -->
        <div class="col-lg-8">
            <?php if (!empty($staff['cv_file_path']) && file_exists('../../' . $staff['cv_file_path'])): ?>
            <!-- CV PDF Viewer -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-pdf me-2"></i> Curriculum Vitae (CV)
                    </h6>
                    <div>
                        <a href="../../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           class="btn btn-sm btn-danger" target="_blank">
                            <i class="fas fa-external-link-alt"></i> เปิดในแท็บใหม่
                        </a>
                        <a href="../../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           class="btn btn-sm btn-success" download>
                            <i class="fas fa-download"></i> ดาวน์โหลด
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <embed src="../../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           type="application/pdf" 
                           width="100%" 
                           height="600px" 
                           style="border: 1px solid #ddd; border-radius: 5px;">
                    <p class="text-muted text-center mt-2 mb-0">
                        <small>หากไม่สามารถแสดง PDF ได้ กรุณา 
                        <a href="../../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" target="_blank">คลิกที่นี่</a>
                        เพื่อเปิดในแท็บใหม่</small>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Positions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ตำแหน่ง</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($positions)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($positions as $position): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($position['position_name']); ?>
                                    <?php if ($position['is_primary']): ?>
                                        <span class="badge bg-primary rounded-pill">ตำแหน่งหลัก</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center">ไม่มีข้อมูลตำแหน่ง</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Education -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ประวัติการศึกษา</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($staff['education'])): ?>
                        <div class="p-2">
                            <?php echo nl2br(htmlspecialchars($staff['education'])); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">ไม่มีข้อมูลประวัติการศึกษา</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bio and Achievements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ประวัติและผลงาน</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($staff['bio'])): ?>
                        <div class="p-2">
                            <?php echo $staff['bio']; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">ไม่มีข้อมูลประวัติและผลงาน</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลระบบ</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ลำดับการแสดงผล:</strong> <?php echo (int)$staff['order_number']; ?></p>
                            <p><strong>วันที่สร้าง:</strong> <?php echo date('d/m/Y H:i:s', strtotime($staff['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>แก้ไขล่าสุด:</strong> <?php echo date('d/m/Y H:i:s', strtotime($staff['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Page Content -->

<?php
// End content output
$content = ob_get_clean();

// Include template
include '../news/template.php';
?>
