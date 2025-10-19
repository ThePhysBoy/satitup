<?php
/**
 * Admin Dashboard
 */

// Include database connection and authentication functions
$conn = require_once 'includes/db_config.php';
require_once 'includes/auth_functions.php';

// Require user to be logged in
requireLogin();

// Check if there's a login success message
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $_SESSION['success_message'] = "เข้าสู่ระบบสำเร็จ ยินดีต้อนรับกลับ " . ($_SESSION['full_name'] ?? $_SESSION['username']);
    unset($_SESSION['login_success']);
}

// Get slideshow count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM slideshow");
$stmt->execute();
$result = $stmt->get_result();
$slideshow_count = $result->fetch_assoc()['count'];

// Get university rankings count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM university_rankings");
$stmt->execute();
$result = $stmt->get_result();
$rankings_count = $result->fetch_assoc()['count'];

// Get management count
$management_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM management");
    $stmt->execute();
    $result = $stmt->get_result();
    $management_count = $result->fetch_assoc()['count'];
}

// Get steering committee count
$steering_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM steering_committee WHERE status='active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $steering_count = $result->fetch_assoc()['count'];
}

// Get news count (for PR/Admin)
$news_count = 0;
if (isAdmin() || isPrOfficer()) {
	$stmt = $conn->prepare("SELECT COUNT(*) as count FROM news");
	$stmt->execute();
	$result = $stmt->get_result();
	$news_count = $result->fetch_assoc()['count'];
}

// Get users count (only for admins)
$users_count = 0;
if (isAdmin()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users_count = $result->fetch_assoc()['count'];
}

// Get official documents count (for PR/Admin)
$official_docs_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM official_documents WHERE status='active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $official_docs_count = $result->fetch_assoc()['count'];
}

// Get hall of fame count (for PR/Admin)
$hall_of_fame_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM hall_of_fame WHERE status='active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $hall_of_fame_count = $result->fetch_assoc()['count'];
}

// Get partners count (for PR/Admin)
$partners_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM partners WHERE status='active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $partners_count = $result->fetch_assoc()['count'];
}

// Get user information
$user_type = $_SESSION['user_type'] ?? 'general';
$full_name = $_SESSION['full_name'] ?? $_SESSION['username'];
$position = $_SESSION['position'] ?? '';

// Get procurement announcements count (for PR/Admin maybe)
$procurement_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM procurement_announcements");
    $stmt->execute();
    $result = $stmt->get_result();
    $procurement_count = $result->fetch_assoc()['count'];
}

// Get recruitment announcements count
$recruitment_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM recruitment_announcements");
    $stmt->execute();
    $result = $stmt->get_result();
    $recruitment_count = $result->fetch_assoc()['count'];
}

// Get training announcements count
$training_count = 0;
if (isAdmin() || isPrOfficer()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM training_announcements");
    $stmt->execute();
    $result = $stmt->get_result();
    $training_count = $result->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผงควบคุม - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #224abe;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #2d3748;
            --sidebar-width: 280px;
            --header-height: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            color: #fff;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.05rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem;
        }

        .sidebar-heading {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1rem;
            color: rgba(255, 255, 255, 0.4);
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        /* Main Content Styles */
        main {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }
        
        .chart-area {
            position: relative;
            height: 10rem;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .chart-area {
                height: 20rem;
            }
        }
        
        .dropdown-menu {
            font-size: 0.85rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        
        .dropdown-header {
            font-size: 0.65rem;
            font-weight: 800;
            color: #b7b9cc;
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
                            <a class="nav-link active" href="index.php">
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
                            <a class="nav-link" href="slideshow/index.php">
                                <i class="fas fa-fw fa-images"></i>
                                <span>สไลด์โชว์</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (canManageRankings()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="rankings/index.php">
                                <i class="fas fa-fw fa-award"></i>
                                <span>การจัดอันดับมหาวิทยาลัย</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (isPrOfficer() || isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="management/index.php">
                                <i class="fas fa-fw fa-user-tie"></i>
                                <span>คณะกรรมการบริหาร</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="steering/index.php">
                                <i class="fas fa-fw fa-university"></i>
                                <span>คณะกรรมการอำนวยการ</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (isPrOfficer() || isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="news/index.php">
                                <i class="fas fa-fw fa-newspaper"></i>
                                <span>ข่าวและกิจกรรม</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (isPrOfficer() || isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="video_system/simple_video_manager.php">
                                <i class="fas fa-fw fa-video"></i>
                                <span>วิดีโอ</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="official_documents/index.php">
                                <i class="fas fa-fw fa-file-alt"></i>
                                <span>เอกสารราชการ</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="hall_of_fame/index.php">
                                <i class="fas fa-fw fa-trophy"></i>
                                <span>หอเกียรติยศ</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="partners/index.php">
                                <i class="fas fa-fw fa-handshake"></i>
                                <span>เครือข่ายความร่วมมือ</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (canManageStaff()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="staff/index.php">
                                <i class="fas fa-fw fa-chalkboard-teacher"></i>
                                <span>บุคลากร</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (isAdmin()): ?>
                        <hr class="sidebar-divider">
                        
                        <div class="sidebar-heading text-white-50 px-3 py-1 text-uppercase fs-6">
                            จัดการผู้ใช้
                        </div>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-fw fa-users"></i>
                                <span>ผู้ใช้งาน</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <hr class="sidebar-divider d-none d-md-block">
                        
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-fw fa-user"></i>
                                <span>โปรไฟล์</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
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
                    <div>
                        <h1 class="h2">แผงควบคุม</h1>
                        <?php if (!empty($position)): ?>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($position); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                <?php echo htmlspecialchars($full_name); ?>
                                <?php if ($user_type === 'pr_officer'): ?>
                                <span class="badge bg-info ms-1">นักประชาสัมพันธ์</span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php">ข้อมูลส่วนตัว</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Content -->
                <div class="row">
                    <?php if (canManageSlideshow()): ?>
                    <!-- Slideshow Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            สไลด์โชว์
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $slideshow_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-images fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (canManageRankings()): ?>
                    <!-- Rankings Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            การจัดอันดับ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $rankings_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-award fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isPrOfficer() || isAdmin()): ?>
                    <!-- Management Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-dark shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                            คณะกรรมการบริหาร
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $management_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Steering Committee Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-purple shadow h-100 py-2" style="border-left: 0.25rem solid #8B7AA8!important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-purple text-uppercase mb-1" style="color: #8B7AA8;">
                                            คณะกรรมการอำนวยการ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $steering_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-university fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isPrOfficer() || isAdmin()): ?>
                    <!-- News Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            ข่าวและกิจกรรม
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $news_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isPrOfficer() || isAdmin()): ?>
                    <!-- Videos Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            วิดีโอ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php
                                            // นับจำนวนวิดีโอ
                                            $video_count = 0;
                                            try {
                                                // ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลแล้วหรือยัง
                                                if (!isset($video_conn)) {
                                                    if (file_exists('../video_system/includes/db_config.php')) {
                                                        // ใช้ include_once แทน require_once เพื่อป้องกันการโหลดซ้ำ
                                                        include_once '../video_system/includes/db_config.php';
                                                    }
                                                }
                                                
                                                // ตรวจสอบว่าตารางมีอยู่หรือไม่
                                                if (isset($video_conn)) {
                                                    $check_table = $video_conn->query("SHOW TABLES LIKE 'videos'");
                                                    if ($check_table && $check_table->num_rows > 0) {
                                                        $result = $video_conn->query("SELECT COUNT(*) as count FROM videos");
                                                        if ($result) {
                                                            $row = $result->fetch_assoc();
                                                            $video_count = $row['count'];
                                                        }
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                // ถ้ามีข้อผิดพลาด ให้แสดง 0
                                                $video_count = 0;
                                            }
                                            echo $video_count;
                                            ?> รายการ
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-video fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Official Documents Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            เอกสารราชการ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $official_docs_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            หอเกียรติยศ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $hall_of_fame_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Partners Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            เครือข่ายความร่วมมือ
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $partners_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-handshake fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isAdmin()): ?>
                    <!-- Users Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            ผู้ใช้งาน
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $users_count; ?> คน</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isPrOfficer() || isAdmin()): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ประกาศจัดซื้อจัดจ้าง</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $procurement_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ประกาศรับสมัครงาน</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $recruitment_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ประกาศอบรม</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $training_count; ?> รายการ</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">เมนูลัด</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php if (canManageSlideshow()): ?>
                                    <div class="col-md-3 mb-3">
                                        <a href="slideshow/index.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-images me-2"></i> จัดการสไลด์โชว์
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="slideshow/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มสไลด์ใหม่
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (canManageRankings()): ?>
                                    <div class="col-md-3 mb-3">
                                        <a href="rankings/index.php" class="btn btn-info btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-award me-2"></i> จัดการการจัดอันดับ
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="rankings/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มการจัดอันดับใหม่
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (isPrOfficer() || isAdmin()): ?>
                                    <div class="col-md-3 mb-3">
                                        <a href="news/index.php" class="btn btn-warning btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-newspaper me-2"></i> จัดการข่าวและกิจกรรม
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="news/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มข่าวใหม่
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="news/dashboard.php" class="btn btn-info btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-tachometer-alt me-2"></i> แดชบอร์ดข่าว
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="management/index.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-user-tie me-2"></i> จัดการกรรมการบริหาร
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="management/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-user-plus me-2"></i> เพิ่มกรรมการบริหาร
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="steering/index.php" class="btn btn-block d-flex align-items-center justify-content-center p-3" style="background-color: #8B7AA8; color: white;">
                                            <i class="fas fa-university me-2"></i> จัดการกรรมการอำนวยการ
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="steering/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-user-plus me-2"></i> เพิ่มกรรมการอำนวยการ
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (isPrOfficer() || isAdmin()): ?>
                                    <div class="col-md-3 mb-3">
                                        <a href="video_system/simple_video_manager.php" class="btn btn-danger btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-video me-2"></i> จัดการวิดีโอ (ง่าย)
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="video_system/simple_video_manager.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่ม/แก้ไขวิดีโอ
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="video_system/setup_database.php" class="btn btn-secondary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-database me-2"></i> ตั้งค่าฐานข้อมูลวิดีโอ
                                        </a>
                                    </div>
                                    
                                    <!-- เอกสารราชการ -->
                                    <div class="col-md-3 mb-3">
                                        <a href="official_documents/index.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-file-alt me-2"></i> จัดการเอกสารราชการ
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="official_documents/add.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มเอกสารใหม่
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="official_documents/dashboard.php" class="btn btn-info btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-chart-pie me-2"></i> แดชบอร์ดเอกสาร
                                        </a>
                                    </div>
                                    
                                    <!-- หอเกียรติยศ -->
                                    <div class="col-md-3 mb-3">
                                        <a href="hall_of_fame/index.php" class="btn btn-warning btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-trophy me-2"></i> จัดการหอเกียรติยศ
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="hall_of_fame/add.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มรางวัลใหม่
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="../hall_of_fame/index.php" target="_blank" class="btn btn-info btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-eye me-2"></i> ดูหอเกียรติยศ
                                        </a>
                                    </div>
                                    
                                    <!-- เครือข่ายความร่วมมือ -->
                                    <div class="col-md-3 mb-3">
                                        <a href="partners/index.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-handshake me-2"></i> จัดการพันธมิตร
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="partners/create.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-plus me-2"></i> เพิ่มพันธมิตรใหม่
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="../index.php#partners" target="_blank" class="btn btn-info btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-eye me-2"></i> ดูส่วนพันธมิตร
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (canManageStaff()): ?>
                                    <div class="col-md-3 mb-3">
                                        <a href="staff/index.php" class="btn btn-purple btn-block d-flex align-items-center justify-content-center p-3" style="background-color: #8B7AA8; color: white;">
                                            <i class="fas fa-users me-2"></i> จัดการบุคลากร
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="staff/create.php" class="btn btn-success btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-user-plus me-2"></i> เพิ่มบุคลากรใหม่
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <a href="procurements/index.php" class="btn btn-lg btn-success text-white w-100 shadow-sm" target="_blank" rel="noopener">
                                            <i class="fas fa-shopping-cart me-2"></i> จัดการประกาศจัดซื้อจัดจ้าง
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <a href="recruitments/index.php" class="btn btn-lg btn-info text-white w-100 shadow-sm" target="_blank" rel="noopener">
                                            <i class="fas fa-user-tie me-2"></i> จัดการประกาศรับสมัครงาน
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <a href="trainings/index.php" class="btn btn-lg btn-warning text-white w-100 shadow-sm" target="_blank" rel="noopener">
                                            <i class="fas fa-chalkboard-teacher me-2"></i> จัดการประกาศอบรม
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <a href="international/index.php" class="btn btn-lg btn-primary text-white w-100 shadow-sm" target="_blank" rel="noopener">
                                            <i class="fas fa-globe me-2"></i> จัดการประกาศไปต่างประเทศ
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <a href="../index.php" target="_blank" class="btn btn-secondary btn-block d-flex align-items-center justify-content-center p-3">
                                            <i class="fas fa-globe me-2"></i> ดูหน้าเว็บไซต์
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Information -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลผู้ใช้งาน</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($full_name); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <strong>ตำแหน่ง:</strong> <?php echo htmlspecialchars($position); ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>ประเภทผู้ใช้:</strong> 
                                            <?php if ($user_type === 'pr_officer'): ?>
                                                <span class="badge bg-info">นักประชาสัมพันธ์</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">ทั่วไป</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>