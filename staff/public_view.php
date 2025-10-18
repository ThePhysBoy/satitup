<?php
// Public Staff View Page - ไม่ต้อง Login ก็ดูได้
$conn = require_once '../admin/includes/db_config.php';
require_once '../admin/staff/staff_functions.php';

// Get staff ID from URL
$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($staff_id == 0) {
    header('Location: index.php');
    exit;
}

// Get staff data
$staff = getStaffById($staff_id, $conn);

if (!$staff) {
    header('Location: index.php');
    exit;
}

// Get staff positions
$positions = getStaffPositions($staff_id, $conn);
?>

<?php
// Set page title
$page_title = htmlspecialchars($staff['title'] . $staff['first_name'] . ' ' . $staff['last_name']) . ' - โรงเรียนสาธิตมหาวิทยาลัยพะเยา';

// Include header from index
include '../header.php';
?>
    
<style>
    body {
        background: #f8f9fa;
        padding-top: 20px;
    }
    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: -30px;
    }
    .profile-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
        padding: 30px;
    }
    .profile-img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .info-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }
    .info-label {
        font-weight: 600;
        color: #666;
        margin-right: 10px;
    }
    .cv-viewer {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .btn-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-custom:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .badge-position {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        display: inline-block;
        margin: 5px;
    }
    .academic-link {
        display: inline-block;
        margin: 5px;
        padding: 8px 15px;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .academic-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>

<!-- Include Font Awesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" rel="stylesheet">

<?php 
// Include navbar from index
require_once '../header.php';
?>

<!-- Header Section -->
<div class="header-section">
    <div class="container">
        <div class="text-center">
            <h1>ข้อมูลบุคลากร</h1>
            <p class="lead">Staff Information</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mb-5">
    <div class="profile-card">
        <div class="row">
            <!-- Profile Image & Basic Info -->
            <div class="col-md-3 text-center">
                <?php if (!empty($staff['image_path']) && file_exists('../' . $staff['image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($staff['first_name']); ?>" 
                         class="profile-img rounded-circle mb-3">
                <?php else: ?>
                    <div class="profile-img rounded-circle mb-3 bg-gradient d-flex align-items-center justify-content-center" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 4rem;">
                        <?php echo mb_substr($staff['first_name'], 0, 1); ?>
                    </div>
                <?php endif; ?>
                
                <h4><?php echo htmlspecialchars($staff['title'] . $staff['first_name'] . ' ' . $staff['last_name']); ?></h4>
                
                <?php if (!empty($staff['position'])): ?>
                    <p class="text-primary"><?php echo htmlspecialchars($staff['position']); ?></p>
                <?php endif; ?>

                <p class="text-muted"><?php echo htmlspecialchars($staff['department_name'] ?? 'ไม่ระบุแผนก'); ?></p>
                
                <?php if (!empty($staff['cv_file_path']) && file_exists('../' . $staff['cv_file_path'])): ?>
                    <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>"
                       class="btn btn-danger mt-3" target="_blank">
                        <i class="fas fa-file-pdf"></i> ดาวน์โหลด CV
                    </a>
                <?php endif; ?>

                <?php if (!empty($staff['office'])): ?>
                    <p class="text-muted mt-2"><?php echo htmlspecialchars($staff['office']); ?></p>
                <?php endif; ?>

                <!-- Academic Links -->
                <?php if (!empty($staff['google_scholar_url'])): ?>
                    <div class="mt-3">
                        <a href="<?php echo htmlspecialchars($staff['google_scholar_url']); ?>"
                           class="academic-link" 
                           style="background: #4285f4; color: white;"
                           target="_blank">
                            <i class="fab fa-google"></i> Google Scholar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Detailed Information -->
            <div class="col-md-9">
                <!-- Contact Information -->
                <?php if (!empty($staff['email']) || !empty($staff['phone'])): ?>
                <div class="info-section">
                    <h5 class="mb-3"><i class="fas fa-address-card"></i> ข้อมูลติดต่อ</h5>
                    <div class="row">
                        <?php if (!empty($staff['email'])): ?>
                        <div class="col-md-6">
                            <p><span class="info-label">อีเมล:</span> 
                               <a href="mailto:<?php echo htmlspecialchars($staff['email']); ?>">
                                   <?php echo htmlspecialchars($staff['email']); ?>
                               </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($staff['phone'])): ?>
                        <div class="col-md-6">
                            <p><span class="info-label">โทรศัพท์:</span> 
                               <?php echo htmlspecialchars($staff['phone']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($staff['office'])): ?>
                        <div class="col-md-6">
                            <p><span class="info-label">ห้องทำงาน:</span> 
                               <?php echo htmlspecialchars($staff['office']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Positions -->
                <?php if (!empty($positions)): ?>
                <div class="info-section">
                    <h5 class="mb-3"><i class="fas fa-user-tie"></i> ตำแหน่ง</h5>
                    <?php foreach ($positions as $pos): ?>
                        <span class="badge-position">
                            <?php echo htmlspecialchars($pos['position_name']); ?>
                            <?php if ($pos['is_primary']): ?>
                                <i class="fas fa-star ms-1"></i>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Education -->
                <?php if (!empty($staff['education'])): ?>
                <div class="info-section">
                    <h5 class="mb-3"><i class="fas fa-graduation-cap"></i> ประวัติการศึกษา</h5>
                    <?php 
                    $educations = explode("\n", $staff['education']);
                    foreach ($educations as $edu): 
                        if (!empty(trim($edu))):
                    ?>
                    <p class="mb-2">• <?php echo htmlspecialchars(trim($edu)); ?></p>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>
                
                <!-- Bio -->
                <?php if (!empty($staff['bio'])): ?>
                <div class="info-section">
                    <h5 class="mb-3"><i class="fas fa-user"></i> ประวัติโดยย่อ</h5>
                    <p><?php echo nl2br(htmlspecialchars($staff['bio'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- CV PDF Viewer -->
        <?php if (!empty($staff['cv_file_path']) && file_exists('../' . $staff['cv_file_path'])): ?>
        <div class="cv-viewer mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-file-pdf text-danger"></i> Curriculum Vitae</h5>
                <div>
                    <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                       class="btn btn-sm btn-danger" target="_blank">
                        <i class="fas fa-external-link-alt"></i> เปิดในแท็บใหม่
                    </a>
                    <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                       class="btn btn-sm btn-success" download>
                        <i class="fas fa-download"></i> ดาวน์โหลด
                    </a>
                </div>
            </div>
            <div style="border: 1px solid #ddd; border-radius: 10px; padding: 10px;">
                <embed src="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                       type="application/pdf" 
                       width="100%" 
                       height="600px">
                <p class="text-muted text-center mt-2">
                    <small>หากไม่สามารถแสดง PDF ได้ กรุณา 
                    <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" target="_blank">คลิกที่นี่</a>
                    เพื่อเปิดในแท็บใหม่</small>
                </p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="javascript:history.back()" class="btn-custom">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>
</div>

<?php 
// Include footer from index
include '../footer.php';
?>
