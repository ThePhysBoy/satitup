<?php
// เพิ่มข้อมูลวิดีโอตัวอย่าง
$conn = new mysqli('localhost', 'root', '', 'satitup');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>เพิ่มข้อมูลวิดีโอตัวอย่าง</h2>";

// เพิ่มหมวดหมู่ที่ขาด
$sql = "INSERT IGNORE INTO video_categories (id, name, description) VALUES 
    (9, 'ผลงานนักเรียน', 'ผลงานและโครงงานของนักเรียน')";

if ($conn->query($sql)) {
    echo "<p>✓ เพิ่มหมวดหมู่ 'ผลงานนักเรียน' สำเร็จ</p>";
}

// เพิ่มวิดีโอตัวอย่าง
$sample_videos = [
    [
        'title' => 'กิจกรรมวันวิทยาศาสตร์ ประจำปี 2567',
        'description' => 'กิจกรรมวันวิทยาศาสตร์แห่งชาติ ประจำปีการศึกษา 2567 โรงเรียนสาธิตมหาวิทยาลัยพะเยา',
        'youtube_url' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
        'category_id' => 1,
        'is_featured' => 0
    ],
    [
        'title' => 'การแข่งขันกีฬาสีประจำปี 2567',
        'description' => 'การแข่งขันกีฬาสีภายใน ประจำปีการศึกษา 2567',
        'youtube_url' => 'https://www.youtube.com/watch?v=BaW_jenozKc',
        'category_id' => 2,
        'is_featured' => 0
    ],
    [
        'title' => 'คอนเสิร์ตวงดนตรีโรงเรียน',
        'description' => 'การแสดงดนตรีของนักเรียนชมรมดนตรี',
        'youtube_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
        'category_id' => 3,
        'is_featured' => 0
    ],
    [
        'title' => 'ห้องปฏิบัติการวิทยาศาสตร์',
        'description' => 'แนะนำห้องปฏิบัติการวิทยาศาสตร์และการทดลอง',
        'youtube_url' => 'https://www.youtube.com/watch?v=_OBlgSz8sSM',
        'category_id' => 4,
        'is_featured' => 0
    ],
    [
        'title' => 'English Day Activities 2024',
        'description' => 'กิจกรรมวันภาษาอังกฤษ ประจำปี 2567',
        'youtube_url' => 'https://www.youtube.com/watch?v=2Z4m4lnjxkY',
        'category_id' => 6,
        'is_featured' => 0
    ],
    [
        'title' => 'นำเสนอผลงานวิจัยระดับนานาชาติ',
        'description' => 'นักเรียนนำเสนอผลงานวิจัยในงานประชุมวิชาการระดับนานาชาติ',
        'youtube_url' => 'https://www.youtube.com/watch?v=tgbNymZ7vqY',
        'category_id' => 7,
        'is_featured' => 0
    ],
    [
        'title' => 'โครงการ วมว. ปีการศึกษา 2567',
        'description' => 'กิจกรรมโครงการพัฒนาอัจฉริยภาพทางวิทยาศาสตร์และคณิตศาสตร์',
        'youtube_url' => 'https://www.youtube.com/watch?v=1Zv4R8sHB_o',
        'category_id' => 8,
        'is_featured' => 0
    ],
    [
        'title' => 'โครงงานหุ่นยนต์อัตโนมัติ',
        'description' => 'ผลงานโครงงานหุ่นยนต์อัตโนมัติของนักเรียนชั้น ม.6',
        'youtube_url' => 'https://www.youtube.com/watch?v=6JNUvGH9Xj0',
        'category_id' => 9,
        'is_featured' => 0
    ]
];

$success = 0;
$failed = 0;

foreach ($sample_videos as $video) {
    $title = $conn->real_escape_string($video['title']);
    $description = $conn->real_escape_string($video['description']);
    $youtube_url = $conn->real_escape_string($video['youtube_url']);
    
    $sql = "INSERT INTO videos (title, description, youtube_url, category_id, is_featured, upload_date, views, shares) 
            VALUES ('$title', '$description', '$youtube_url', {$video['category_id']}, {$video['is_featured']}, NOW(), 0, 0)";
    
    if ($conn->query($sql)) {
        echo "<p>✓ เพิ่มวิดีโอ: {$video['title']}</p>";
        $success++;
    } else {
        echo "<p>✗ ไม่สามารถเพิ่มวิดีโอ: {$video['title']} - " . $conn->error . "</p>";
        $failed++;
    }
}

echo "<h3>สรุป:</h3>";
echo "<p>เพิ่มสำเร็จ: $success วิดีโอ</p>";
echo "<p>ไม่สำเร็จ: $failed วิดีโอ</p>";

// แสดงจำนวนวิดีโอทั้งหมด
$result = $conn->query("SELECT COUNT(*) as total FROM videos");
$row = $result->fetch_assoc();
echo "<p>วิดีโอทั้งหมดในระบบ: {$row['total']} รายการ</p>";

echo '<p><a href="index.php">กลับไปหน้าหลัก</a> | <a href="admin/video_system/index.php">ไปหน้าจัดการวิดีโอ</a></p>';

$conn->close();
?>
