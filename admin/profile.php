<?php
/**
 * User Profile Page
 */

// Include database connection and authentication functions
$conn = require_once 'includes/db_config.php';
require_once 'includes/auth_functions.php';

// Require user to be logged in
requireLogin();

// Initialize variables
$success_message = '';
$error_message = '';

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // Redirect to login if user not found
    header("Location: logout.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $position = $_POST['position'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Update profile information
    if (!empty($full_name) || !empty($email) || !empty($position)) {
        $update_query = "UPDATE users SET";
        $params = [];
        $types = "";
        
        if (!empty($full_name)) {
            $update_query .= " full_name = ?,";
            $params[] = $full_name;
            $types .= "s";
        }
        
        if (!empty($email)) {
            $update_query .= " email = ?,";
            $params[] = $email;
            $types .= "s";
        }
        
        if (!empty($position)) {
            $update_query .= " position = ?,";
            $params[] = $position;
            $types .= "s";
        }
        
        // Remove trailing comma
        $update_query = rtrim($update_query, ',');
        
        $update_query .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success_message = "อัพเดตข้อมูลส่วนตัวเรียบร้อยแล้ว";
            
            // Update session variables
            if (!empty($full_name)) {
                $_SESSION['full_name'] = $full_name;
            }
            
            if (!empty($position)) {
                $_SESSION['position'] = $position;
            }
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $error_message = "เกิดข้อผิดพลาดในการอัพเดตข้อมูล";
        }
    }
    
    // Change password
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $error_message = "รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน";
        } elseif (!password_verify($current_password, $user['password'])) {
            $error_message = "รหัสผ่านปัจจุบันไม่ถูกต้อง";
        } else {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password_hash, $user_id);
            
            if ($stmt->execute()) {
                $success_message = "เปลี่ยนรหัสผ่านเรียบร้อยแล้ว";
            } else {
                $error_message = "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ใช้ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
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

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
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
                            <a class="nav-link" href="index.php">
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
                            <a class="nav-link active" href="profile.php">
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
                    <h1 class="h2">โปรไฟล์ผู้ใช้</h1>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Profile Information Card -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลส่วนตัว</h6>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                        <div class="form-text text-muted">ไม่สามารถเปลี่ยนชื่อผู้ใช้ได้</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">ชื่อ-นามสกุล</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="position" class="form-label">ตำแหน่ง</label>
                                        <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($user['position'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">ประเภทผู้ใช้</label>
                                        <input type="text" class="form-control" value="<?php echo $user['user_type'] === 'pr_officer' ? 'นักประชาสัมพันธ์' : 'ทั่วไป'; ?>" disabled>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">สิทธิ์การใช้งาน</label>
                                        <input type="text" class="form-control" value="<?php echo $user['role'] === 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้ใช้ทั่วไป'; ?>" disabled>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Change Password Card -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">เปลี่ยนรหัสผ่าน</h6>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                                </form>
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
