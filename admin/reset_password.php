<?php
/**
 * Reset Admin Password - หน้า Reset รหัสผ่านสำหรับผู้ดูแลระบบ
 */

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satitup";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if database exists, if not create it
if ($conn->connect_error && strpos($conn->connect_error, "Unknown database") !== false) {
    // Connect without database to create it
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>
              <strong>Success:</strong> Database created successfully</div>";
        // Reconnect with database
        $conn = new mysqli($servername, $username, $password, $dbname);
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$messages = [];
$errors = [];

// Create users table if not exists
$sql_create_table = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    role VARCHAR(20) DEFAULT 'user',
    user_type VARCHAR(50) DEFAULT NULL,
    position VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_create_table)) {
    $messages[] = "✓ ตรวจสอบตาราง users เรียบร้อย";
} else {
    $errors[] = "Error creating table: " . $conn->error;
}

// Admin accounts to create/reset
$accounts = [
    [
        'username' => 'admin',
        'password' => 'admin1234',
        'full_name' => 'ผู้ดูแลระบบ',
        'role' => 'admin',
        'user_type' => 'admin',
        'position' => 'System Administrator'
    ],
    [
        'username' => 'admin01',
        'password' => '1234',
        'full_name' => 'นักประชาสัมพันธ์',
        'role' => 'admin',
        'user_type' => 'pr_officer',
        'position' => 'เจ้าหน้าที่ประชาสัมพันธ์'
    ],
    [
        'username' => 'demo',
        'password' => 'demo1234',
        'full_name' => 'ผู้ใช้ทดสอบ',
        'role' => 'admin',
        'user_type' => 'demo',
        'position' => 'Demo User'
    ]
];

// Process each account
foreach ($accounts as $account) {
    $username = $account['username'];
    $password_hash = password_hash($account['password'], PASSWORD_DEFAULT);
    $full_name = $account['full_name'];
    $role = $account['role'];
    $user_type = $account['user_type'];
    $position = $account['position'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing user
        $stmt = $conn->prepare("UPDATE users SET password = ?, full_name = ?, role = ?, user_type = ?, position = ? WHERE username = ?");
        $stmt->bind_param("ssssss", $password_hash, $full_name, $role, $user_type, $position, $username);
        
        if ($stmt->execute()) {
            $messages[] = "✓ อัปเดต: {$username} / รหัสผ่าน: {$account['password']}";
        } else {
            $errors[] = "Error updating {$username}: " . $stmt->error;
        }
    } else {
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role, user_type, position) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $password_hash, $full_name, $role, $user_type, $position);
        
        if ($stmt->execute()) {
            $messages[] = "✓ สร้างใหม่: {$username} / รหัสผ่าน: {$account['password']}";
        } else {
            $errors[] = "Error creating {$username}: " . $stmt->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 700px;
            width: 100%;
        }
        .header-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .account-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .account-username {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .account-password {
            font-size: 1.1rem;
            color: #dc3545;
            font-weight: 500;
        }
        .account-info {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .copy-btn {
            cursor: pointer;
            padding: 5px 10px;
            background: #e9ecef;
            border-radius: 5px;
            font-size: 0.8rem;
            margin-left: 10px;
            transition: all 0.2s;
        }
        .copy-btn:hover {
            background: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="text-center">
            <i class="fas fa-key header-icon"></i>
            <h1>Reset Admin Password</h1>
        </div>
        
        <?php if (!empty($messages)): ?>
            <div class="success-message">
                <h5><i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ</h5>
                <ul class="mb-0">
                    <?php foreach ($messages as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <h5><i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <h4 class="mb-3"><i class="fas fa-users"></i> บัญชีผู้ใช้งานที่สามารถใช้ได้:</h4>
        
        <div class="account-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="account-username">
                        <i class="fas fa-user-shield"></i> admin
                    </div>
                    <div class="account-password">
                        Password: admin1234
                        <span class="copy-btn" onclick="copyToClipboard('admin1234')">
                            <i class="fas fa-copy"></i> Copy
                        </span>
                    </div>
                    <div class="account-info">
                        ผู้ดูแลระบบ - มีสิทธิ์เข้าถึงทุกส่วน
                    </div>
                </div>
            </div>
        </div>
        
        <div class="account-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="account-username">
                        <i class="fas fa-user-tie"></i> admin01
                    </div>
                    <div class="account-password">
                        Password: 1234
                        <span class="copy-btn" onclick="copyToClipboard('1234')">
                            <i class="fas fa-copy"></i> Copy
                        </span>
                    </div>
                    <div class="account-info">
                        นักประชาสัมพันธ์ - จัดการข่าวสารและประชาสัมพันธ์
                    </div>
                </div>
            </div>
        </div>
        
        <div class="account-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="account-username">
                        <i class="fas fa-user"></i> demo
                    </div>
                    <div class="account-password">
                        Password: demo1234
                        <span class="copy-btn" onclick="copyToClipboard('demo1234')">
                            <i class="fas fa-copy"></i> Copy
                        </span>
                    </div>
                    <div class="account-info">
                        ผู้ใช้ทดสอบ - สำหรับทดลองใช้งานระบบ
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h5><i class="fas fa-info-circle"></i> วิธีใช้งาน:</h5>
            <ol class="mb-0">
                <li>เลือกบัญชีที่ต้องการใช้จากด้านบน</li>
                <li>คลิก "Copy" เพื่อคัดลอกรหัสผ่าน</li>
                <li>คลิกปุ่ม "เข้าสู่ระบบ Admin" ด้านล่าง</li>
                <li>ใส่ Username และ Password ที่คัดลอกไว้</li>
            </ol>
        </div>
        
        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-custom">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ Admin
            </a>
            <a href="../" class="btn btn-secondary">
                <i class="fas fa-home"></i> กลับหน้าหลัก
            </a>
        </div>
    </div>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const btn = event.target.closest('.copy-btn');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.background = '#d4edda';
                
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.style.background = '#e9ecef';
                }, 2000);
            });
        }
    </script>
</body>
</html>
