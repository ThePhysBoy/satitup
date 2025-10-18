<?php
/**
 * Template สำหรับหน้าบุคลากรแต่ละแผนก
 * Department Staff Page Template
 * 
 * ต้องกำหนดตัวแปรก่อน include ไฟล์นี้:
 * - $department_id: รหัสแผนก
 * - $page_title: ชื่อหน้า
 * - $breadcrumb_name: ชื่อสำหรับ breadcrumb
 */

// ตรวจสอบว่ามีการกำหนดตัวแปรที่จำเป็นหรือไม่
if (!isset($department_id) || !isset($page_title) || !isset($breadcrumb_name)) {
    die("Error: Required variables not set");
}

// การตั้งค่าพื้นฐาน (Basic Configuration)
error_reporting(E_ALL);
ini_set('display_errors', 0); // ปิดการแสดง error บนหน้าเว็บ
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

// Get current directory depth for proper path resolution
// เนื่องจากไฟล์นี้อยู่ใน staff/ folder ให้ใช้ ../ เสมอ
$path_prefix = '../';

// Include necessary files
$conn = require_once $path_prefix . 'admin/includes/db_config.php';
require_once $path_prefix . 'admin/staff/staff_functions.php';

// Get staff by department
$staff_data = getStaffByDepartment($department_id, $conn);

// Include header and navbar
include_once '../header.php';
//lude_once '../navbar.php';
?>

<!-- ตัวแบ่งส่วน (Section Separator) - เส้นคั่นระหว่างส่วนสไลด์โชว์กับเนวิเกชันบาร์ถัดไป -->
<div class="section-separator"></div>

<!-- Additional styles for staff pages (ไม่ override dropdown styles) -->
<style>
    body {
        background: #f5f5f5;
        font-family: 'Prompt', 'Sarabun', sans-serif;
    }
        
    .page-header {
        background: white;
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .staff-card {
        background: white;
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .staff-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
    }
    
    .staff-card-body {
        display: flex;
        padding: 20px;
        gap: 20px;
        height: 100%;
    }
    
    .staff-image-container {
        flex-shrink: 0;
    }
    
    .staff-image {
        width: 180px;
        height: 220px;
        object-fit: cover;
        border-radius: 8px;
        background: #f0f0f0;
    }
    
    .staff-info {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .staff-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .staff-field {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 6px;
        display: flex;
        align-items: flex-start;
    }
    
    .staff-field strong {
        color: #333;
        min-width: 80px;
        margin-right: 8px;
    }
    
    .staff-field-value {
        flex: 1;
        color: #666;
    }
    
    .staff-buttons {
        margin-top: auto;
        padding-top: 15px;
        display: flex;
        gap: 10px;
    }
    
    .btn-staff {
        padding: 6px 20px;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
    }
    
    .btn-cv {
        background: #FDB813;
        color: #333;
    }
    
    .btn-cv:hover {
        background: #FFA500;
        color: #333;
        transform: translateY(-2px);
    }
    
    .btn-link {
        background: #FDB813;
        color: #333;
    }
    
    .btn-link:hover {
        background: #FFA500;
        color: #333;
        transform: translateY(-2px);
    }
    
    .btn-view {
        background: #4CAF50;
        color: white;
    }
    
    .btn-view:hover {
        background: #45a049;
        color: white;
        transform: translateY(-2px);
    }
    
    .badge-head {
        background: #6B46C1;
        color: white;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        margin-left: 10px;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: #999;
    }
    
    .breadcrumb-item a {
        color: #666;
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: #333;
    }
    
    @media (max-width: 768px) {
        .staff-card-body {
            flex-direction: column;
        }
        
        .staff-image {
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
        }
    }
    </style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $path_prefix; ?>index.php">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="index.php">บุคลากรสายวิชาการ</a></li>
                <li class="breadcrumb-item active"><?php echo $breadcrumb_name; ?></li>
            </ol>
        </nav>
        <h1 class="h3 mt-3 mb-0"><?php echo $page_title; ?></h1>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <?php if (empty($staff_data)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> ไม่พบข้อมูล<?php echo $page_title; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($staff_data as $staff): ?>
                <div class="col-lg-6">
                    <div class="staff-card">
                        <div class="staff-card-body">
                            <!-- Staff Image -->
                            <div class="staff-image-container">
                                <?php if (!empty($staff['image_path']) && file_exists($path_prefix . $staff['image_path'])): ?>
                                    <img src="<?php echo $path_prefix . htmlspecialchars($staff['image_path']); ?>"
                                         class="staff-image"
                                         alt="<?php echo htmlspecialchars($staff['first_name']); ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/200x250/f0f0f0/969696?text=NO+IMAGE"
                                         class="staff-image" 
                                         alt="ไม่มีรูปภาพ">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Staff Info -->
                            <div class="staff-info">
                                <!-- Name and Badge -->
                                <div class="staff-name">
                                    <?php echo htmlspecialchars($staff['title'] . $staff['first_name'] . ' ' . $staff['last_name']); ?>
                                    <?php if ($staff['is_head']): ?>
                                        <span class="badge-head">หัวหน้า</span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Position -->
                                <?php
                                $position_text = '';
                                if (!empty($staff['position'])) {
                                    $position_text = $staff['position'];
                                } else {
                                    $staff_positions = getStaffPositions($staff['id'], $conn);
                                    foreach ($staff_positions as $pos) {
                                        if ($pos['is_primary']) {
                                            $position_text = $pos['position_name'];
                                            break;
                                        }
                                    }
                                }
                                if (!empty($position_text)): ?>
                                    <div class="staff-field">
                                        <strong>ตำแหน่ง :</strong>
                                        <span class="staff-field-value"><?php echo htmlspecialchars($position_text); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Email -->
                                <?php if (!empty($staff['email'])): ?>
                                    <div class="staff-field">
                                        <strong>อีเมล :</strong>
                                        <span class="staff-field-value"><?php echo htmlspecialchars($staff['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Phone -->
                                <?php if (!empty($staff['phone'])): ?>
                                    <div class="staff-field">
                                        <strong>โทร :</strong>
                                        <span class="staff-field-value"><?php echo htmlspecialchars($staff['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Work Status -->
                                <?php 
                                $work_status_text = 'ปฏิบัติงาน';
                                if (!empty($staff['work_status'])) {
                                    switch($staff['work_status']) {
                                        case 'working': $work_status_text = 'ปฏิบัติงาน'; break;
                                        case 'retired': $work_status_text = 'เกษียณอายุ'; break;
                                        case 'leave': $work_status_text = 'ลาศึกษาต่อ'; break;
                                        case 'resigned': $work_status_text = 'ลาออก'; break;
                                        default: $work_status_text = 'ปฏิบัติงาน';
                                    }
                                }
                                ?>
                                <div class="staff-field">
                                    <strong>สถานะ :</strong>
                                    <span class="staff-field-value"><?php echo $work_status_text; ?></span>
                                </div>
                                
                                <!-- Expertise -->
                                <?php 
                                $expertise_text = '';
                                if (!empty($staff['expertise'])) {
                                    $expertise_text = $staff['expertise'];
                                } elseif (!empty($staff['bio'])) {
                                    // Extract from bio if no expertise field
                                    $bio_lines = explode("\n", $staff['bio']);
                                    foreach ($bio_lines as $line) {
                                        if (stripos($line, 'ความเชี่ยวชาญ') !== false) {
                                            $expertise_text = trim(str_replace(['ความเชี่ยวชาญ:', 'ความเชี่ยวชาญ'], '', $line));
                                            break;
                                        }
                                    }
                                }
                                if (!empty($expertise_text)): ?>
                                    <div class="staff-field">
                                        <strong>ความเชี่ยวชาญ :</strong>
                                        <span class="staff-field-value"><?php echo htmlspecialchars($expertise_text); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Buttons -->
                                <div class="staff-buttons">
                                    <?php if (!empty($staff['cv_file_path']) && file_exists($path_prefix . $staff['cv_file_path'])): ?>
                                        <a href="<?php echo $path_prefix . htmlspecialchars($staff['cv_file_path']); ?>"
                                           class="btn-staff btn-cv" target="_blank">
                                            CV
                                        </a>
                                    <?php else: ?>
                                        <a href="public_view.php?id=<?php echo $staff['id']; ?>" 
                                           class="btn-staff btn-cv">
                                            CV
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($staff['google_scholar_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($staff['google_scholar_url']); ?>" 
                                           class="btn-staff btn-link" target="_blank">
                                            LINK
                                        </a>
                                    <?php else: ?>
                                        <a href="public_view.php?id=<?php echo $staff['id']; ?>" 
                                           class="btn-staff btn-link">
                                            LINK
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="public_view.php?id=<?php echo $staff['id']; ?>" 
                                       class="btn-staff btn-view">
                                        VIEW
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer spacing -->
<div style="height: 50px;"></div>

<?php include_once '../footer.php'; ?>

<!-- Back to top button -->
<button onclick="topFunction()" id="backToTop" class="btn btn-primary" style="display: none; position: fixed; bottom: 20px; right: 30px; z-index: 99; border-radius: 50%; width: 50px; height: 50px;">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// Back to top button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("backToTop").style.display = "block";
    } else {
        document.getElementById("backToTop").style.display = "none";
    }
}

function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
</script>