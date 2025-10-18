<?php
session_start();

// If user is already logged in, redirect to news_announcements.php
if (isset($_SESSION['user_id'])) {
    header("Location: news_announcements.php");
    exit();
}

require_once 'db_connect.php';

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        try {
            // Prepare SQL statement using MySQLi
            $sql = "SELECT id, username, password, full_name as fullname, email, role, position 
                    FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['full_name'] = $user['fullname']; // Alternative key
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['position'] = $user['position'];
                $_SESSION['user_type'] = $user['role']; // Set user_type for admin check
                $_SESSION['login_success'] = true; // For welcome message
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                
                $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        } catch (Exception $e) {
            $error = 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบจัดการข่าวประชาสัมพันธ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Sarabun', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e0e0e0;
            border-right: none;
            background: #f8f9fa;
        }
        
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 18px;
            font-weight: 500;
            width: 100%;
            transition: transform 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-danger {
            background-color: #fee;
            color: #c33;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        
        .show-password {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
        }
        
        .password-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h2>ระบบจัดการข่าวประชาสัมพันธ์</h2>
            <p>โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="username">
                        <i class="fas fa-user"></i> ชื่อผู้ใช้งาน
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="กรอกชื่อผู้ใช้งาน"
                               required 
                               autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i> รหัสผ่าน
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="กรอกรหัสผ่าน"
                               required>
                        <span class="show-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            จดจำการเข้าสู่ระบบ
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p class="mb-0">© 2024 โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            <small>ระบบจัดการข่าวประชาสัมพันธ์ v1.0</small>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
