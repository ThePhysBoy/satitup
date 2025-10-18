<?php
// ไฟล์แสดงประกาศและข่าวสาร
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/functions.php';

// ดึงข้อมูลประกาศจาก ID (ถ้ามี)
$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$announcement = null;

if ($announcement_id > 0) {
    // ถ้ามี ID ให้แสดงรายละเอียดประกาศ
    $announcement = getAnnouncementDetail($announcement_id, $conn);
    
    // ถ้าไม่พบประกาศ ให้กลับไปหน้าหลัก
    if (!$announcement) {
        header('Location: index.php');
        exit;
    }
    
    // แสดงหน้ารายละเอียดประกาศ
    include_once __DIR__ . '/templates/announcement_detail.php';
} else {
    // ถ้าไม่มี ID ให้แสดงรายการประกาศทั้งหมด
    $latest_news = getLatestNews($conn, 5);
    $announcements = getLatestAnnouncements($conn, 10);
    
    // ใช้ wrapper เดิมเพื่อรักษาความเข้ากันได้
    include_once __DIR__ . '/../news_announcements.php';
}
