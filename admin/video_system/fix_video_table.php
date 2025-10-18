<?php
/**
 * Fix Video Table Structure
 * สคริปต์สำหรับแก้ไขโครงสร้างตาราง videos ให้ถูกต้อง
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satitup";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$messages = [];
$errors = [];

// 1. ตรวจสอบและสร้างตาราง video_categories ถ้ายังไม่มี
$check_table = $conn->query("SHOW TABLES LIKE 'video_categories'");
if ($check_table->num_rows == 0) {
    $sql = "CREATE TABLE `video_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `slug` varchar(255) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql)) {
        $messages[] = "✓ สร้างตาราง video_categories สำเร็จ";
    } else {
        $errors[] = "Error creating video_categories: " . $conn->error;
    }
} else {
    $messages[] = "✓ ตาราง video_categories มีอยู่แล้ว";
}

// 2. ตรวจสอบและสร้างตาราง videos ถ้ายังไม่มี
$check_table = $conn->query("SHOW TABLES LIKE 'videos'");
if ($check_table->num_rows == 0) {
    $sql = "CREATE TABLE `videos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `youtube_id` varchar(50) NOT NULL,
        `youtube_url` varchar(255) DEFAULT NULL,
        `category_id` int(11) DEFAULT 1,
        `views` int(11) NOT NULL DEFAULT 0,
        `featured` tinyint(1) NOT NULL DEFAULT 0,
        `active` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `event_date` date DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `featured` (`featured`),
        KEY `active` (`active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql)) {
        $messages[] = "✓ สร้างตาราง videos สำเร็จ";
    } else {
        $errors[] = "Error creating videos: " . $conn->error;
    }
} else {
    $messages[] = "✓ ตาราง videos มีอยู่แล้ว";
    
    // 3. ตรวจสอบและเพิ่ม columns ที่อาจขาดหายไป
    $columns_to_check = [
        'youtube_url' => "ALTER TABLE videos ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL AFTER youtube_id",
        'active' => "ALTER TABLE videos ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1",
        'event_date' => "ALTER TABLE videos ADD COLUMN event_date DATE DEFAULT NULL",
        'updated_at' => "ALTER TABLE videos ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    foreach ($columns_to_check as $column => $alter_sql) {
        $check = $conn->query("SHOW COLUMNS FROM videos LIKE '$column'");
        if ($check->num_rows == 0) {
            if ($conn->query($alter_sql)) {
                $messages[] = "✓ เพิ่ม column $column สำเร็จ";
            } else {
                $errors[] = "Error adding column $column: " . $conn->error;
            }
        } else {
            $messages[] = "✓ Column $column มีอยู่แล้ว";
        }
    }
}

// 4. เพิ่มหมวดหมู่เริ่มต้นถ้ายังไม่มี
$check = $conn->query("SELECT COUNT(*) as count FROM video_categories");
$row = $check->fetch_assoc();
if ($row['count'] == 0) {
    $categories = [
        ['ทั่วไป', 'general'],
        ['กิจกรรมวิชาการ', 'academic'],
        ['กีฬา', 'sports'],
        ['ศิลปะและดนตรี', 'arts'],
        ['แนะนำโรงเรียน', 'school-intro']
    ];
    
    foreach ($categories as $cat) {
        $name = $conn->real_escape_string($cat[0]);
        $slug = $cat[1];
        $sql = "INSERT INTO video_categories (name, slug) VALUES ('$name', '$slug')";
        if ($conn->query($sql)) {
            $messages[] = "✓ เพิ่มหมวดหมู่ $name";
        }
    }
} else {
    $messages[] = "✓ มีหมวดหมู่อยู่แล้ว ($row[count] หมวดหมู่)";
}

// 5. ตรวจสอบและอัปเดตวิดีโอที่มี youtube_url ว่าง
$update_urls = $conn->query("UPDATE videos SET youtube_url = CONCAT('https://www.youtube.com/watch?v=', youtube_id) WHERE youtube_url IS NULL OR youtube_url = ''");
if ($update_urls) {
    $affected = $conn->affected_rows;
    if ($affected > 0) {
        $messages[] = "✓ อัปเดต youtube_url สำหรับ $affected วิดีโอ";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Video Database - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
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
        .container-custom {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 30px;
            text-align: center;
        }
        .alert {
            border-radius: 10px;
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
        .status-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="text-center">
            <?php if (empty($errors)): ?>
                <i class="fas fa-check-circle status-icon success"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle status-icon error"></i>
            <?php endif; ?>
            
            <h1>🔧 Fix Video Database Structure</h1>
        </div>
        
        <?php if (!empty($messages)): ?>
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ:</h5>
                <ul class="mb-0">
                    <?php foreach ($messages as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> โครงสร้างฐานข้อมูลพร้อมใช้งาน!</h5>
                <p class="mb-0">ตาราง videos และ video_categories มีโครงสร้างที่ถูกต้องแล้ว</p>
            </div>
            
            <div class="text-center mt-4">
                <a href="simple_video_manager.php" class="btn btn-custom">
                    <i class="fas fa-video"></i> ไปที่หน้าจัดการวิดีโอ
                </a>
                <a href="../login.php" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> หน้า Login
                </a>
            </div>
        <?php else: ?>
            <div class="text-center mt-4">
                <a href="fix_video_table.php" class="btn btn-custom">
                    <i class="fas fa-redo"></i> ลองอีกครั้ง
                </a>
            </div>
        <?php endif; ?>
        
        <hr class="my-4">
        
        <div class="text-muted text-center">
            <small>
                Script นี้จะตรวจสอบและแก้ไขโครงสร้างตาราง videos ให้ถูกต้อง<br>
                รันได้หลายครั้ง ไม่มีผลกระทบกับข้อมูลเดิม
            </small>
        </div>
    </div>
</body>
</html>
