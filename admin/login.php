<?php
/**
 * Login Page
 */

// Include database connection and authentication functions
require_once 'includes/auth_functions.php';

// Check if db_config.php exists, if not redirect to reset_password.php
if (!file_exists('includes/db_config.php')) {
    header("Location: reset_password.php");
    exit;
}

$conn = require_once 'includes/db_config.php';

// Check if user is already logged in
    if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Initialize error variable
$error = "";
$success = "";

// Check if there's a logout success message
if (isset($_SESSION['logout_success']) && $_SESSION['logout_success'] === true) {
    $success = "ออกจากระบบสำเร็จ";
    unset($_SESSION['logout_success']);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน";
    } else {
        // Authenticate user
        if (authenticateUser($username, $password, $conn)) {
            // Set success login message
            $_SESSION['login_success'] = true;
            header("Location: index.php");
            exit;
        } else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating label {
            color: #666;
        }
        
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .home-link {
            margin-top: 20px;
            display: inline-block;
            color: #764ba2;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .home-link:hover {
            color: #667eea;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-icon {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../images/มหาวิทยาลัยพะเยา.png" alt="โลโก้โรงเรียน" class="login-logo">
        
        <h1 class="login-title">เข้าสู่ระบบ</h1>
        <p class="login-subtitle">ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-user input-icon"></i>
                </span>
                <div class="form-floating flex-grow-1">
                    <input type="text" class="form-control" id="username" name="username" placeholder="ชื่อผู้ใช้" required>
                    <label for="username">ชื่อผู้ใช้</label>
                </div>
            </div>
            
            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock input-icon"></i>
                </span>
                <div class="form-floating flex-grow-1">
                    <input type="password" class="form-control" id="password" name="password" placeholder="รหัสผ่าน" required>
                    <label for="password">รหัสผ่าน</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
            </button>
        </form>
        
        <div class="mt-4">
            <a href="../index.php" class="home-link">
                <i class="fas fa-home me-2"></i>กลับสู่หน้าหลักเว็บไซต์
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>