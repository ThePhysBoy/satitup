<?php
/**
 * Logout Page
 */

// Include authentication functions
require_once 'includes/auth_functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Store username before logout for personalized message
$username = $_SESSION['username'] ?? 'ผู้ใช้';
$full_name = $_SESSION['full_name'] ?? $username;

// Logout user
logoutUser();

// Set success message
$_SESSION['logout_success'] = true;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ออกจากระบบ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
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
        
        .logout-container {
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
        }
        
        .logout-icon {
            font-size: 4rem;
            color: #764ba2;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        .logout-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .logout-message {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
        }
        
        .countdown {
            font-size: 1.5rem;
            font-weight: 600;
            color: #764ba2;
            margin-bottom: 20px;
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
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .school-logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <img src="../images/มหาวิทยาลัยพะเยา.png" alt="โลโก้โรงเรียน" class="school-logo">
        
        <div class="logout-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="logout-title">ออกจากระบบสำเร็จ</h1>
        
        <p class="logout-message">
            คุณ <?php echo htmlspecialchars($full_name); ?> ได้ออกจากระบบเรียบร้อยแล้ว<br>
            ขอบคุณที่ใช้งานระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา
        </p>
        
        <div class="countdown" id="countdown">
            กำลังนำคุณกลับสู่หน้าเข้าสู่ระบบใน <span id="timer">5</span> วินาที
        </div>
        
        <a href="login.php" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบอีกครั้ง
        </a>
        
        <div class="mt-4">
            <a href="../index.php" class="home-link">
                <i class="fas fa-home me-2"></i>กลับสู่หน้าหลักเว็บไซต์
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Countdown script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timeLeft = 5;
            const timerElement = document.getElementById('timer');
            
            const countdownTimer = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    window.location.href = 'login.php';
                }
            }, 1000);
        });
    </script>
</body>
</html>