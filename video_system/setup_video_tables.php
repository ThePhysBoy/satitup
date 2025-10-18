<?php
// Setup Video Tables - สร้างตารางสำหรับระบบวิดีโอ
require_once 'includes/db_config.php';

// ตรวจสอบการเชื่อมต่อ
if (!$video_conn) {
    die("Error: ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
}

$messages = [];
$errors = [];

// SQL สำหรับสร้างตาราง video_categories
$sql_categories = "CREATE TABLE IF NOT EXISTS `video_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// SQL สำหรับสร้างตาราง videos
$sql_videos = "CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `youtube_id` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `event_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `featured` (`featured`),
  KEY `active` (`active`),
  CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `video_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// สร้างตาราง video_categories
if (mysqli_query($video_conn, $sql_categories)) {
    $messages[] = "✓ สร้างตาราง video_categories สำเร็จ";
} else {
    $errors[] = "Error creating video_categories table: " . mysqli_error($video_conn);
}

// สร้างตาราง videos
if (mysqli_query($video_conn, $sql_videos)) {
    $messages[] = "✓ สร้างตาราง videos สำเร็จ";
} else {
    $errors[] = "Error creating videos table: " . mysqli_error($video_conn);
}

// ตรวจสอบว่ามีข้อมูลหมวดหมู่หรือยัง
$check_categories = mysqli_query($video_conn, "SELECT COUNT(*) as count FROM video_categories");
$row = mysqli_fetch_assoc($check_categories);

if ($row['count'] == 0) {
    // เพิ่มข้อมูลหมวดหมู่เริ่มต้น
    $sql_insert_categories = "INSERT INTO `video_categories` (`name`, `description`, `slug`) VALUES
    ('บรรยากาศการเรียนการสอน', 'วิดีโอแสดงบรรยากาศการเรียนการสอนภายในโรงเรียน', 'learning-atmosphere'),
    ('กีฬา', 'กิจกรรมกีฬาและการแข่งขันต่างๆ', 'sports'),
    ('ดนตรี', 'การแสดงดนตรีและกิจกรรมด้านศิลปะการแสดง', 'music'),
    ('ห้องปฏิบัติการ', 'กิจกรรมในห้องปฏิบัติการต่างๆ', 'laboratory'),
    ('แนะนำมหาวิทยาลัย', 'วิดีโอแนะนำมหาวิทยาลัยพะเยา', 'university-introduction'),
    ('การเรียนภาษาอังกฤษ', 'กิจกรรมการเรียนการสอนภาษาอังกฤษ', 'english-learning'),
    ('การนำเสนอผลงานทางวิชาการ', 'การนำเสนอผลงานทางวิชาการทั้งในและต่างประเทศ', 'academic-presentation'),
    ('กิจกรรมวิชาการ', 'กิจกรรมทางวิชาการต่างๆ', 'academic-activities'),
    ('ผลงานนักเรียน', 'ผลงานและความสำเร็จของนักเรียน', 'student-achievements'),
    ('โครงการ วมว.', 'กิจกรรมโครงการวิทยาศาสตร์ วมว.', 'scius-project')";
    
    if (mysqli_query($video_conn, $sql_insert_categories)) {
        $messages[] = "✓ เพิ่มข้อมูลหมวดหมู่เริ่มต้น 10 หมวดหมู่";
    } else {
        $errors[] = "Error inserting categories: " . mysqli_error($video_conn);
    }
} else {
    $messages[] = "✓ มีข้อมูลหมวดหมู่อยู่แล้ว (" . $row['count'] . " หมวดหมู่)";
}

// ตรวจสอบว่ามีวิดีโอหรือยัง
$check_videos = mysqli_query($video_conn, "SELECT COUNT(*) as count FROM videos");
$row = mysqli_fetch_assoc($check_videos);

if ($row['count'] == 0) {
    // เพิ่มวิดีโอตัวอย่าง
    $sql_insert_sample = "INSERT INTO `videos` (`title`, `description`, `youtube_id`, `category_id`, `featured`, `event_date`) VALUES
    ('แนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา', 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา แสดงบรรยากาศการเรียนการสอน สิ่งอำนวยความสะดวก และกิจกรรมต่างๆ', 'dQw4w9WgXcQ', 5, 1, '2024-01-15'),
    ('กิจกรรมวันวิทยาศาสตร์แห่งชาติ', 'กิจกรรมวันวิทยาศาสตร์แห่งชาติ ประจำปี 2567 นำเสนอผลงานทางวิทยาศาสตร์ของนักเรียน', 'dQw4w9WgXcQ', 8, 0, '2024-08-18'),
    ('การแข่งขันกีฬาสีประจำปี', 'การแข่งขันกีฬาสีประจำปีการศึกษา 2567 ทั้ง 4 สี แข่งขันกีฬาประเภทต่างๆ', 'dQw4w9WgXcQ', 2, 0, '2024-11-20')";
    
    if (mysqli_query($video_conn, $sql_insert_sample)) {
        $messages[] = "✓ เพิ่มวิดีโอตัวอย่าง 3 รายการ";
    } else {
        $errors[] = "Error inserting sample videos: " . mysqli_error($video_conn);
    }
} else {
    $messages[] = "✓ มีวิดีโอในระบบอยู่แล้ว (" . $row['count'] . " วิดีโอ)";
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Video Tables - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #764ba2;
            margin-bottom: 30px;
        }
        .alert {
            border-radius: 10px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
        }
        .btn-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎬 Setup Video System Database</h1>
        
        <?php if (!empty($messages)): ?>
            <div class="alert alert-success">
                <h5>✅ ดำเนินการสำเร็จ:</h5>
                <ul class="mb-0">
                    <?php foreach ($messages as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5>❌ พบข้อผิดพลาด:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="alert alert-info">
                <h5>📋 สรุปการติดตั้ง:</h5>
                <ul class="mb-0">
                    <li>✅ ฐานข้อมูล: <strong>satitup</strong></li>
                    <li>✅ ตาราง: <strong>videos</strong> และ <strong>video_categories</strong></li>
                    <li>✅ หมวดหมู่วิดีโอ: 10 หมวดหมู่พร้อมใช้งาน</li>
                    <li>✅ วิดีโอตัวอย่าง: 3 วิดีโอ (แก้ไข youtube_id เพื่อใช้วิดีโอจริง)</li>
                </ul>
            </div>
            
            <div class="text-center mt-4">
                <a href="../all_videos.php" class="btn btn-custom me-2">
                    🎥 ดูหน้าแสดงวิดีโอทั้งหมด
                </a>
                <a href="../../video_quick_links.php" class="btn btn-secondary me-2">
                    📺 ดูหน้า Video Quick Links
                </a>
                <a href="../../admin/video_system/" class="btn btn-warning">
                    ⚙️ จัดการวิดีโอ (Admin)
                </a>
            </div>
        <?php endif; ?>
        
        <hr class="my-4">
        
        <div class="alert alert-warning">
            <h5>⚠️ หมายเหตุ:</h5>
            <p>1. วิดีโอตัวอย่างใช้ YouTube ID ทดสอบ (dQw4w9WgXcQ) กรุณาเปลี่ยนเป็น ID วิดีโอจริงในหน้า Admin</p>
            <p>2. หากต้องการ reset ข้อมูล ให้ลบตารางผ่าน phpMyAdmin แล้วรันหน้านี้ใหม่</p>
            <p class="mb-0">3. ตรวจสอบให้แน่ใจว่า Apache และ MySQL ทำงานอยู่</p>
        </div>
        
        <div class="text-center">
            <a href="../../" class="btn btn-outline-secondary">
                🏠 กลับหน้าหลัก
            </a>
        </div>
    </div>
</body>
</html>
