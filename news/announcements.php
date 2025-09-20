<?php
// ไฟล์ wrapper สำหรับจัดระเบียบข่าวภายใต้โฟลเดอร์ /news
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/../admin/news/news_functions.php';

// ดึงข่าวล่าสุด 5 รายการ
$latest_news = [];
if ($conn && !$conn->connect_error) {
    $stmt = $conn->prepare("SELECT n.id, n.title, n.slug, n.published_at, n.created_at, n.featured_image, n.views, u.full_name, u.username
                            FROM news n
                            LEFT JOIN users u ON u.id = n.author_id
                            WHERE n.status = 'published'
                            ORDER BY COALESCE(n.published_at, n.created_at) DESC
                            LIMIT 5");
    $stmt->execute();
    $res = $stmt->get_result();
    $latest_news = $res->fetch_all(MYSQLI_ASSOC);
}

// ใช้ wrapper เดิมเพื่อรักษาความเข้ากันได้
include_once __DIR__ . '/../news_announcements.php';
