<?php
// Reset and Setup Video Database with Sample Data
require_once 'includes/db_config.php';

// ตรวจสอบการเชื่อมต่อ
if (!$video_conn) {
    die("Error: ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
}

$messages = [];
$errors = [];

// 1. ลบตารางเดิม (ถ้ามี)
$drop_tables = [
    "DROP TABLE IF EXISTS `videos`",
    "DROP TABLE IF EXISTS `video_categories`"
];

foreach ($drop_tables as $sql) {
    if (mysqli_query($video_conn, $sql)) {
        $messages[] = "✓ ลบตารางเดิมสำเร็จ";
    }
}

// 2. สร้างตาราง video_categories ใหม่
$sql_categories = "CREATE TABLE `video_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($video_conn, $sql_categories)) {
    $messages[] = "✓ สร้างตาราง video_categories สำเร็จ";
} else {
    $errors[] = "Error: " . mysqli_error($video_conn);
}

// 3. สร้างตาราง videos ใหม่ 
$sql_videos = "CREATE TABLE `videos` (
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

if (mysqli_query($video_conn, $sql_videos)) {
    $messages[] = "✓ สร้างตาราง videos สำเร็จ";
} else {
    $errors[] = "Error: " . mysqli_error($video_conn);
}

// 4. เพิ่มหมวดหมู่
$sql_insert_categories = "INSERT INTO `video_categories` (`name`, `description`, `slug`) VALUES
('บรรยากาศการเรียนการสอน', 'วิดีโอแสดงบรรยากาศการเรียนการสอนภายในโรงเรียน', 'learning-atmosphere'),
('กิจกรรมกีฬา', 'กิจกรรมกีฬาและการแข่งขันต่างๆ', 'sports'),
('ดนตรีและศิลปะ', 'การแสดงดนตรีและกิจกรรมด้านศิลปะการแสดง', 'music-arts'),
('ห้องปฏิบัติการ', 'กิจกรรมในห้องปฏิบัติการวิทยาศาสตร์และคอมพิวเตอร์', 'laboratory'),
('แนะนำโรงเรียน', 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา', 'school-introduction'),
('ภาษาอังกฤษ', 'กิจกรรมการเรียนการสอนภาษาอังกฤษ', 'english-learning'),
('ผลงานทางวิชาการ', 'การนำเสนอผลงานทางวิชาการและโครงงานนักเรียน', 'academic-presentation'),
('กิจกรรมวิชาการ', 'กิจกรรมทางวิชาการและการแข่งขันต่างๆ', 'academic-activities'),
('โครงการ วมว.', 'กิจกรรมโครงการวิทยาศาสตร์ วมว.มพ.', 'scius-project'),
('พิธีการและงานสำคัญ', 'พิธีการต่างๆ และงานสำคัญของโรงเรียน', 'ceremonies')";

if (mysqli_query($video_conn, $sql_insert_categories)) {
    $messages[] = "✓ เพิ่มหมวดหมู่ 10 หมวดหมู่";
} else {
    $errors[] = "Error: " . mysqli_error($video_conn);
}

// 5. เพิ่มวิดีโอตัวอย่างที่ดูสมจริง
$sample_videos = [
    // วิดีโอแนะนำ (Featured)
    [
        'title' => 'แนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา ปีการศึกษา 2567',
        'description' => 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา นำเสนอบรรยากาศการเรียนการสอน สิ่งอำนวยความสะดวก หลักสูตรการเรียน และกิจกรรมพัฒนานักเรียน',
        'youtube_id' => 'xxxxxxxxxxx',
        'category_id' => 5,
        'featured' => 1,
        'views' => 2543,
        'event_date' => '2024-01-15'
    ],
    
    // กิจกรรมวิชาการ
    [
        'title' => 'งานวันวิทยาศาสตร์แห่งชาติ ประจำปี 2567',
        'description' => 'กิจกรรมวันวิทยาศาสตร์แห่งชาติ จัดแสดงผลงานทางวิทยาศาสตร์ของนักเรียน การทดลองวิทยาศาสตร์ และนิทรรศการต่างๆ',
        'youtube_id' => 'yyyyyyyyyyy',
        'category_id' => 8,
        'featured' => 0,
        'views' => 1823,
        'event_date' => '2024-08-18'
    ],
    
    // โครงการ วมว.
    [
        'title' => 'เปิดบ้าน วมว.มพ. Open House 2567',
        'description' => 'งานเปิดบ้านโครงการห้องเรียนวิทยาศาสตร์ วมว.มพ. แสดงผลงานและกิจกรรมของนักเรียนในโครงการ',
        'youtube_id' => 'zzzzzzzzzzz',
        'category_id' => 9,
        'featured' => 0,
        'views' => 3421,
        'event_date' => '2024-03-20'
    ],
    
    // กีฬา
    [
        'title' => 'พิธีเปิดการแข่งขันกีฬาสีภายใน ปีการศึกษา 2567',
        'description' => 'พิธีเปิดการแข่งขันกีฬาสีภายใน 4 สี ได้แก่ สีแดง สีเหลือง สีน้ำเงิน และสีเขียว พร้อมการแสดงของแต่ละสี',
        'youtube_id' => 'aaaaaaaaaaa',
        'category_id' => 2,
        'featured' => 0,
        'views' => 4567,
        'event_date' => '2024-11-15'
    ],
    
    // ดนตรี
    [
        'title' => 'คอนเสิร์ตวงดุริยางค์โรงเรียนสาธิต ครั้งที่ 12',
        'description' => 'การแสดงคอนเสิร์ตประจำปีของวงดุริยางค์โรงเรียนสาธิตมหาวิทยาลัยพะเยา',
        'youtube_id' => 'bbbbbbbbbbb',
        'category_id' => 3,
        'featured' => 0,
        'views' => 892,
        'event_date' => '2024-09-10'
    ],
    
    // ห้องปฏิบัติการ
    [
        'title' => 'ห้องปฏิบัติการวิทยาศาสตร์แห่งใหม่',
        'description' => 'แนะนำห้องปฏิบัติการวิทยาศาสตร์ที่ได้รับการปรับปรุงใหม่ พร้อมอุปกรณ์ทันสมัย',
        'youtube_id' => 'ccccccccccc',
        'category_id' => 4,
        'featured' => 0,
        'views' => 645,
        'event_date' => '2024-07-01'
    ],
    
    // ภาษาอังกฤษ
    [
        'title' => 'English Day 2024: Journey Around the World',
        'description' => 'กิจกรรมวันภาษาอังกฤษ นักเรียนแสดงละครภาษาอังกฤษ และกิจกรรมส่งเสริมการใช้ภาษาอังกฤษ',
        'youtube_id' => 'ddddddddddd',
        'category_id' => 6,
        'featured' => 0,
        'views' => 1234,
        'event_date' => '2024-02-14'
    ],
    
    // ผลงานวิชาการ
    [
        'title' => 'นักเรียนคว้ารางวัลชนะเลิศการแข่งขันโอลิมปิกวิชาการ',
        'description' => 'นักเรียนโรงเรียนสาธิตคว้ารางวัลชนะเลิศการแข่งขันโอลิมปิกวิชาการระดับประเทศ สาขาคณิตศาสตร์',
        'youtube_id' => 'eeeeeeeeeee',
        'category_id' => 7,
        'featured' => 0,
        'views' => 2987,
        'event_date' => '2024-10-05'
    ],
    
    // บรรยากาศการเรียน
    [
        'title' => 'บรรยากาศการเรียนการสอนแบบ Active Learning',
        'description' => 'การจัดการเรียนการสอนแบบ Active Learning ในห้องเรียนวิทยาศาสตร์ ม.ปลาย',
        'youtube_id' => 'fffffffffff',
        'category_id' => 1,
        'featured' => 0,
        'views' => 567,
        'event_date' => '2024-06-12'
    ],
    
    // พิธีการ
    [
        'title' => 'พิธีไหว้ครู ประจำปีการศึกษา 2567',
        'description' => 'พิธีไหว้ครูประจำปีการศึกษา 2567 เพื่อแสดงความกตัญญูกตเวทีต่อครูอาจารย์',
        'youtube_id' => 'ggggggggggg',
        'category_id' => 10,
        'featured' => 0,
        'views' => 3456,
        'event_date' => '2024-06-13'
    ],
    
    // เพิ่มอีก 10 วิดีโอ
    [
        'title' => 'การแข่งขันหุ่นยนต์ระดับชาติ',
        'description' => 'ทีมหุ่นยนต์โรงเรียนสาธิตเข้าร่วมการแข่งขันหุ่นยนต์ระดับชาติ',
        'youtube_id' => 'hhhhhhhhhhh',
        'category_id' => 8,
        'featured' => 0,
        'views' => 789,
        'event_date' => '2024-09-25'
    ],
    
    [
        'title' => 'โครงการปลูกป่าเฉลิมพระเกียรติ',
        'description' => 'กิจกรรมปลูกป่าเฉลิมพระเกียรติ โดยนักเรียนและคณะครู',
        'youtube_id' => 'iiiiiiiiiii',
        'category_id' => 8,
        'featured' => 0,
        'views' => 432,
        'event_date' => '2024-07-28'
    ],
    
    [
        'title' => 'การแสดงนาฏศิลป์ไทย งานวันภาษาไทย',
        'description' => 'การแสดงนาฏศิลป์ไทยโดยนักเรียนชุมนุมนาฏศิลป์ ในงานวันภาษาไทย',
        'youtube_id' => 'jjjjjjjjjjj',
        'category_id' => 3,
        'featured' => 0,
        'views' => 1567,
        'event_date' => '2024-07-29'
    ],
    
    [
        'title' => 'ค่ายวิทยาศาสตร์ ม.ต้น ประจำปี 2567',
        'description' => 'กิจกรรมค่ายวิทยาศาสตร์สำหรับนักเรียนระดับมัธยมศึกษาตอนต้น',
        'youtube_id' => 'kkkkkkkkkkk',
        'category_id' => 9,
        'featured' => 0,
        'views' => 2345,
        'event_date' => '2024-05-20'
    ],
    
    [
        'title' => 'นักเรียนแลกเปลี่ยนจากประเทศญี่ปุ่น',
        'description' => 'การต้อนรับคณะนักเรียนแลกเปลี่ยนจากโรงเรียนพี่น้องประเทศญี่ปุ่น',
        'youtube_id' => 'lllllllllll',
        'category_id' => 8,
        'featured' => 0,
        'views' => 4321,
        'event_date' => '2024-04-15'
    ],
    
    [
        'title' => 'การแข่งขันบาสเกตบอลระหว่างโรงเรียน',
        'description' => 'การแข่งขันบาสเกตบอลระหว่างโรงเรียนในจังหวัดพะเยา',
        'youtube_id' => 'mmmmmmmmmmm',
        'category_id' => 2,
        'featured' => 0,
        'views' => 987,
        'event_date' => '2024-12-10'
    ],
    
    [
        'title' => 'Science Show: ความมหัศจรรย์ของวิทยาศาสตร์',
        'description' => 'การแสดงทางวิทยาศาสตร์โดยนักเรียนชั้น ม.ปลาย',
        'youtube_id' => 'nnnnnnnnnnn',
        'category_id' => 4,
        'featured' => 0,
        'views' => 1678,
        'event_date' => '2024-08-18'
    ],
    
    [
        'title' => 'พิธีมอบรางวัลนักเรียนดีเด่น',
        'description' => 'พิธีมอบรางวัลให้แก่นักเรียนที่มีผลการเรียนดีเด่นและมีความประพฤติดี',
        'youtube_id' => 'ooooooooooo',
        'category_id' => 10,
        'featured' => 0,
        'views' => 2890,
        'event_date' => '2024-03-29'
    ],
    
    [
        'title' => 'English Camp 2024',
        'description' => 'ค่ายภาษาอังกฤษกับครูชาวต่างชาติ 3 วัน 2 คืน',
        'youtube_id' => 'ppppppppppp',
        'category_id' => 6,
        'featured' => 0,
        'views' => 1456,
        'event_date' => '2024-10-18'
    ],
    
    [
        'title' => 'การประกวดโครงงานวิทยาศาสตร์ระดับภาค',
        'description' => 'นักเรียนนำเสนอโครงงานวิทยาศาสตร์ในการประกวดระดับภาคเหนือ',
        'youtube_id' => 'qqqqqqqqqqq',
        'category_id' => 7,
        'featured' => 0,
        'views' => 3210,
        'event_date' => '2024-11-30'
    ]
];

// Insert videos
$success_count = 0;
foreach ($sample_videos as $video) {
    $title = mysqli_real_escape_string($video_conn, $video['title']);
    $description = mysqli_real_escape_string($video_conn, $video['description']);
    $youtube_id = $video['youtube_id'];
    $category_id = $video['category_id'];
    $featured = $video['featured'];
    $views = $video['views'];
    $event_date = $video['event_date'];
    
    $sql = "INSERT INTO videos (title, description, youtube_id, category_id, featured, views, event_date, active) 
            VALUES ('$title', '$description', '$youtube_id', $category_id, $featured, $views, '$event_date', 1)";
    
    if (mysqli_query($video_conn, $sql)) {
        $success_count++;
    }
}

$messages[] = "✓ เพิ่มวิดีโอตัวอย่าง $success_count รายการ";

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset & Setup Video Database - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            background: white;
            border-radius: 15px;
            padding: 40px;
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
            margin: 5px;
        }
        .btn-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .stats-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">🔄 Reset & Setup Video Database</h1>
        
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
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-number">10</div>
                    <div>หมวดหมู่วิดีโอ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-number">20</div>
                    <div>วิดีโอตัวอย่าง</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-number">1</div>
                    <div>วิดีโอแนะนำ</div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h5>📋 ข้อมูลที่ถูกสร้าง:</h5>
            <ul>
                <li><strong>หมวดหมู่:</strong> 10 หมวดหมู่ (กีฬา, วิชาการ, ดนตรี, วมว., ฯลฯ)</li>
                <li><strong>วิดีโอ:</strong> 20 วิดีโอตัวอย่างพร้อมข้อมูลครบถ้วน</li>
                <li><strong>Featured:</strong> วิดีโอแนะนำโรงเรียน (แสดงเด่นในหน้าแรก)</li>
                <li><strong>Views:</strong> ตัวเลขการดูที่ดูสมจริง</li>
            </ul>
        </div>
        
        <div class="text-center mt-4">
            <h5 class="mb-3">🎯 ทดสอบระบบได้ที่:</h5>
            <a href="../../video_quick_links.php" class="btn btn-custom">
                <i class="fas fa-video"></i> Video Quick Links
            </a>
            <a href="../all_videos.php" class="btn btn-custom">
                <i class="fas fa-film"></i> All Videos
            </a>
            <a href="../../test_video_system.php" class="btn btn-custom">
                <i class="fas fa-vial"></i> Test System
            </a>
            <a href="../../admin/video_system/" class="btn btn-warning">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="alert alert-warning">
            <h5>⚠️ หมายเหตุสำหรับผู้บริหาร:</h5>
            <ol>
                <li>ข้อมูลวิดีโอเป็นตัวอย่างเท่านั้น YouTube ID ยังไม่ใช่ของจริง</li>
                <li>สามารถแก้ไข YouTube ID ผ่านหน้า Admin Panel</li>
                <li>ระบบพร้อมใช้งานจริงทันที เพียงเปลี่ยน YouTube ID</li>
                <li>รองรับการเพิ่ม/ลบ/แก้ไขวิดีโอผ่านหน้า Admin</li>
            </ol>
        </div>
        
        <div class="text-center">
            <a href="../../" class="btn btn-outline-secondary">
                <i class="fas fa-home"></i> กลับหน้าหลัก
            </a>
        </div>
    </div>
</body>
</html>
