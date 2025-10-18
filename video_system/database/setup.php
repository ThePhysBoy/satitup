<?php
// สคริปต์สำหรับสร้างตารางฐานข้อมูลวิดีโอ
require_once '../includes/db_config.php';

echo "<h2>ตั้งค่าฐานข้อมูลระบบวิดีโอ</h2>";
echo "<hr>";

// อ่านไฟล์ SQL
$sql_file = __DIR__ . '/create_tables.sql';
if (!file_exists($sql_file)) {
    die("ไม่พบไฟล์ SQL: " . $sql_file);
}

$sql_content = file_get_contents($sql_file);
if ($sql_content === false) {
    die("ไม่สามารถอ่านไฟล์ SQL ได้");
}

// แบ่ง SQL เป็นคำสั่งแยกๆ
$sql_statements = array_filter(array_map('trim', explode(';', $sql_content)));

echo "<p>กำลังดำเนินการสร้างตาราง...</p>";

$success_count = 0;
$error_count = 0;

foreach ($sql_statements as $sql) {
    if (empty($sql) || strpos($sql, '--') === 0) {
        continue; // ข้ามบรรทัดว่างและคอมเมนต์
    }
    
    echo "<p>กำลังรัน: " . substr($sql, 0, 50) . "...</p>";
    
    if ($video_conn->query($sql)) {
        echo "<p style='color: green;'>✓ สำเร็จ</p>";
        $success_count++;
    } else {
        echo "<p style='color: red;'>✗ ล้มเหลว: " . $video_conn->error . "</p>";
        $error_count++;
    }
}

echo "<hr>";
echo "<h3>สรุปผลการดำเนินการ</h3>";
echo "<p>สำเร็จ: {$success_count} คำสั่ง</p>";
echo "<p>ล้มเหลว: {$error_count} คำสั่ง</p>";

if ($error_count === 0) {
    echo "<p style='color: green; font-weight: bold;'>ตั้งค่าฐานข้อมูลเสร็จสมบูรณ์!</p>";
    echo "<p><a href='../../admin/video_system/index.php'>ไปที่หน้าจัดการวิดีโอ</a></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>เกิดข้อผิดพลาดในการตั้งค่าฐานข้อมูล กรุณาตรวจสอบข้อผิดพลาดด้านบน</p>";
}

$video_conn->close();
?>
