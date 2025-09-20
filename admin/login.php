<?php
/**
 * Login Page
 */

// Include database connection and authentication functions
$conn = require_once 'includes/db_config.php';
require_once 'includes/auth_functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Initialize error variable
$error = "";

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
    <title>เข้าสู่ระบบ - โรงเรียนสาธิต ม.พะเยา</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font Awesome & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Noto Sans Thai', sans-serif;
            height: 100vh;
            background: linear-gradient(-45deg, #42a5f5, #7e57c2, #26c6da, #66bb6a);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .login-card {
            backdrop-filter: blur(15px);
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            color: #fff;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.95);}
            to {opacity: 1; transform: scale(1);}
        }

        .login-card h4 {
            font-weight: bold;
            margin-top: 1rem;
        }

        .form-control {
            background-color: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        .form-control:focus {
            background-color: rgba(255,255,255,0.3);
            box-shadow: none;
            border: none;
            color: #fff;
        }

        .btn-login {
            background-color: #ffffff;
            color: #1976d2;
            font-weight: bold;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .back-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .logo-img {
            max-width: 100px;
        }

        .alert {
            background-color: rgba(255, 0, 0, 0.2);
            border: none;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card text-center">
        <img src="../images/logo.png" alt="โรงเรียนสาธิต ม.พะเยา" class="logo-img mb-3">
        <h4>ระบบจัดการเว็บไซต์</h4>
        <p class="mb-4">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3 text-start">
                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-white"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="กรอกชื่อผู้ใช้" required>
                </div>
            </div>

            <div class="mb-4 text-start">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-white"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่าน" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-login">เข้าสู่ระบบ</button>
            </div>
        </form>

        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> กลับหน้าหลัก
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>