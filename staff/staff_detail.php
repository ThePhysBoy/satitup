<?php
// Include necessary files
require_once '../admin/includes/db_config.php';
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

// Include custom header
include 'header_fix.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($staff['title'] . $staff['first_name'] . ' ' . $staff['last_name']); ?> - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }
        .profile-img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .profile-content {
            background: white;
            border-radius: 20px;
            margin-top: -50px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #764ba2;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            display: inline-block;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            margin-right: 10px;
            display: inline-block;
            min-width: 120px;
        }
        .info-value {
            color: #333;
        }
        .position-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 5px;
        }
        .cv-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .cv-section h4 {
            color: #764ba2;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .cv-section ul {
            list-style: none;
            padding-left: 0;
        }
        .cv-section ul li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .cv-section ul li:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        .contact-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .contact-card i {
            color: #667eea;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline:before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #667eea;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #667eea;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .no-photo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'navbar_fix.php'; ?>

<!-- Profile Header -->
<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <?php if (!empty($staff['image_path']) && file_exists('../' . $staff['image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($staff['first_name']); ?>" 
                         class="profile-img rounded-circle">
                <?php else: ?>
                    <div class="profile-img rounded-circle no-photo">
                        <?php echo mb_substr($staff['first_name'], 0, 1); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h1 class="display-4">
                    <?php echo htmlspecialchars($staff['title'] . $staff['first_name'] . ' ' . $staff['last_name']); ?>
                </h1>
                <h3><?php echo htmlspecialchars($staff['position']); ?></h3>
                <p class="lead mb-0">
                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($staff['department_name']); ?>
                </p>
                <?php if (!empty($positions)): ?>
                    <div class="mt-3">
                        <?php foreach ($positions as $pos): ?>
                            <span class="position-badge">
                                <?php echo htmlspecialchars($pos['position_name']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Profile Content -->
<div class="container">
    <div class="profile-content p-5">
        <div class="row">
            <!-- Left Column - Contact & Basic Info -->
            <div class="col-md-4 mb-4">
                <!-- Contact Information -->
                <div class="contact-card">
                    <h4 class="mb-3">ข้อมูลติดต่อ</h4>
                    
                    <?php if (!empty($staff['email'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($staff['email']); ?>">
                                <?php echo htmlspecialchars($staff['email']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($staff['phone'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($staff['phone']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($staff['office'])): ?>
                        <p class="mb-0">
                            <i class="fas fa-door-open"></i>
                            ห้องทำงาน: <?php echo htmlspecialchars($staff['office']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Info -->
                <?php if (!empty($staff['employee_id'])): ?>
                <div class="contact-card">
                    <h4 class="mb-3">ข้อมูลพื้นฐาน</h4>
                    <p class="mb-2">
                        <i class="fas fa-id-badge"></i>
                        รหัสบุคลากร: <?php echo htmlspecialchars($staff['employee_id']); ?>
                    </p>
                    <?php if (!empty($staff['academic_rank'])): ?>
                    <p class="mb-0">
                        <i class="fas fa-graduation-cap"></i>
                        ตำแหน่งทางวิชาการ: <?php echo htmlspecialchars($staff['academic_rank']); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="javascript:history.back()" class="btn-back">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                </div>
            </div>
            
            <!-- Right Column - CV Details -->
            <div class="col-md-8">
                <!-- Bio/About -->
                <?php if (!empty($staff['bio'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-user"></i> ประวัติโดยย่อ</h4>
                    <p><?php echo nl2br(htmlspecialchars($staff['bio'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Education -->
                <?php if (!empty($staff['education'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-graduation-cap"></i> ประวัติการศึกษา</h4>
                    <?php 
                    $educations = explode("\n", $staff['education']);
                    if (count($educations) > 0): 
                    ?>
                    <div class="timeline">
                        <?php foreach ($educations as $edu): 
                            if (!empty(trim($edu))):
                        ?>
                        <div class="timeline-item">
                            <?php echo htmlspecialchars(trim($edu)); ?>
                        </div>
                        <?php 
                            endif;
                        endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Experience -->
                <?php if (!empty($staff['experience'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-briefcase"></i> ประสบการณ์การทำงาน</h4>
                    <?php 
                    $experiences = explode("\n", $staff['experience']);
                    if (count($experiences) > 0): 
                    ?>
                    <ul>
                        <?php foreach ($experiences as $exp): 
                            if (!empty(trim($exp))):
                        ?>
                        <li><?php echo htmlspecialchars(trim($exp)); ?></li>
                        <?php 
                            endif;
                        endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Expertise -->
                <?php if (!empty($staff['expertise'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-star"></i> ความเชี่ยวชาญ</h4>
                    <?php 
                    $expertises = explode(",", $staff['expertise']);
                    ?>
                    <div>
                        <?php foreach ($expertises as $exp): 
                            if (!empty(trim($exp))):
                        ?>
                        <span class="position-badge">
                            <?php echo htmlspecialchars(trim($exp)); ?>
                        </span>
                        <?php 
                            endif;
                        endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Research/Publications -->
                <?php if (!empty($staff['research'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-microscope"></i> ผลงานวิจัย/ตีพิมพ์</h4>
                    <?php 
                    $researches = explode("\n", $staff['research']);
                    ?>
                    <ul>
                        <?php foreach ($researches as $res): 
                            if (!empty(trim($res))):
                        ?>
                        <li><?php echo htmlspecialchars(trim($res)); ?></li>
                        <?php 
                            endif;
                        endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Awards -->
                <?php if (!empty($staff['awards'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-trophy"></i> รางวัลและเกียรติยศ</h4>
                    <?php 
                    $awards = explode("\n", $staff['awards']);
                    ?>
                    <ul>
                        <?php foreach ($awards as $award): 
                            if (!empty(trim($award))):
                        ?>
                        <li><?php echo htmlspecialchars(trim($award)); ?></li>
                        <?php 
                            endif;
                        endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Additional Info -->
                <?php if (!empty($staff['additional_info'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-info-circle"></i> ข้อมูลเพิ่มเติม</h4>
                    <p><?php echo nl2br(htmlspecialchars($staff['additional_info'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- CV PDF Viewer -->
                <?php if (!empty($staff['cv_file_path']) && file_exists('../' . $staff['cv_file_path'])): ?>
                <div class="cv-section">
                    <h4><i class="fas fa-file-pdf"></i> Curriculum Vitae (CV)</h4>
                    <div class="text-end mb-3">
                        <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           class="btn btn-danger btn-sm" target="_blank">
                            <i class="fas fa-external-link-alt"></i> เปิดในแท็บใหม่
                        </a>
                        <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                           class="btn btn-success btn-sm" download>
                            <i class="fas fa-download"></i> ดาวน์โหลด CV
                        </a>
                    </div>
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 10px;">
                        <embed src="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" 
                               type="application/pdf" 
                               width="100%" 
                               height="600px" 
                               style="border-radius: 5px;">
                        <p class="text-muted text-center mt-2 mb-0">
                            <small>หากไม่สามารถแสดง PDF ได้ กรุณา 
                            <a href="../<?php echo htmlspecialchars($staff['cv_file_path']); ?>" target="_blank">คลิกที่นี่</a>
                            เพื่อเปิดในแท็บใหม่</small>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_fix.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
