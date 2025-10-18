<?php
/**
 * ไฟล์ฟังก์ชันสำหรับระบบข่าวสาร
 * ใช้เป็นศูนย์กลางฟังก์ชันที่ใช้ร่วมกันระหว่างไฟล์ต่างๆ ในระบบข่าว
 */

/**
 * ดึงข่าวล่าสุดจากฐานข้อมูล
 * 
 * @param mysqli $conn การเชื่อมต่อฐานข้อมูล
 * @param int $limit จำนวนข่าวที่ต้องการดึง
 * @param int $category_id รหัสหมวดหมู่ (0 = ทั้งหมด)
 * @return array ข้อมูลข่าวล่าสุด
 */
if (!function_exists('getLatestNews')) {
function getLatestNews($conn, $limit = 6, $category_id = 0) {
    $news = [];
    
    if (!$conn || $conn->connect_error) {
        return $news;
    }
    
    // สร้างเงื่อนไขสำหรับหมวดหมู่
    $category_condition = "";
    $params = [];
    $types = "";
    
    if (is_array($category_id) && !empty($category_id)) {
        $placeholders = implode(',', array_fill(0, count($category_id), '?'));
        $category_condition = "AND n.category_id IN ($placeholders)";
        foreach ($category_id as $cat) {
            $params[] = (int)$cat;
            $types .= "i";
        }
    } elseif ($category_id > 0) {
        $category_condition = "AND n.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }
    
    $query = "SELECT n.id, n.title, n.slug, n.excerpt, COALESCE(n.published_at, n.created_at) AS display_date,
                     n.featured_image, (SELECT image_path FROM news_images WHERE news_id = n.id ORDER BY id ASC LIMIT 1) AS gallery_image,
                     n.views as view_count, u.full_name, u.username
              FROM news n
              LEFT JOIN users u ON n.author_id = u.id
              WHERE n.status = 'published' $category_condition
              ORDER BY COALESCE(n.published_at, n.created_at) DESC
              LIMIT ?";

    // เพิ่มพารามิเตอร์ limit
    $params[] = $limit;
    $types .= "i";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    
    return $news;
}
}

/**
 * ดึงประกาศล่าสุดจากฐานข้อมูล
 * 
 * @param mysqli $conn การเชื่อมต่อฐานข้อมูล
 * @param int $limit จำนวนประกาศที่ต้องการดึง
 * @return array ข้อมูลประกาศล่าสุด
 */
if (!function_exists('getLatestAnnouncements')) {
function getLatestAnnouncements($conn, $limit = 10) {
    $announcements = [];
    
    if (!$conn || $conn->connect_error) {
        return $announcements;
    }
    
    // ตรวจสอบว่ามีตาราง announcements หรือไม่
    $table_check = $conn->query("SHOW TABLES LIKE 'announcements'");
    if ($table_check && $table_check->num_rows > 0) {
        $query = "SELECT id, title, content, category, created_at, updated_at
                  FROM announcements
                  ORDER BY created_at DESC
                  LIMIT ?";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $announcements = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }
    
    return $announcements;
}
}

/**
 * แปลง path รูปภาพให้ถูกต้อง
 * 
 * @param string $image_path path รูปภาพที่ต้องการแปลง
 * @return string path รูปภาพที่ถูกต้อง
 */
if (!function_exists('fixImagePath')) {
function fixImagePath($image_path) {
    if (empty($image_path)) {
        return 'images/comingsoon.png';
    }
    
    // ตัด admin/ ออกจาก path ถ้ามี
    if (strpos($image_path, 'admin/') === 0) {
        return substr($image_path, 6);
    }
    
    return $image_path;
}
}

/**
 * เพิ่มจำนวนการเข้าชมข่าว
 * 
 * @param int $news_id รหัสข่าว
 * @param mysqli $conn การเชื่อมต่อฐานข้อมูล
 * @return bool สถานะการอัปเดต
 */
if (!function_exists('incrementNewsViewCount')) {
function incrementNewsViewCount($news_id, $conn) {
    if (!$conn || $conn->connect_error || !$news_id) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $news_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}
}

/**
 * ดึงข้อมูลข่าวตาม ID หรือ Slug
 * 
 * @param mixed $id_or_slug ID หรือ Slug ของข่าว
 * @param mysqli $conn การเชื่อมต่อฐานข้อมูล
 * @param bool $increment_views เพิ่มจำนวนการเข้าชมหรือไม่
 * @return array|null ข้อมูลข่าว
 */
if (!function_exists('getNewsDetail')) {
function getNewsDetail($id_or_slug, $conn, $increment_views = true) {
    if (!$conn || $conn->connect_error) {
        return null;
    }
    
    $is_id = is_numeric($id_or_slug);
    $query = "SELECT n.*, u.full_name, u.username"
             . ", n.slug"
             . " FROM news n"
             . " LEFT JOIN users u ON n.author_id = u.id"
             . " WHERE " . ($is_id ? "n.id = ?" : "n.slug = ?") . " AND n.status = 'published'";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($is_id ? "i" : "s", $id_or_slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_assoc();
        $stmt->close();
        
        // เพิ่มจำนวนการเข้าชม
        if ($news && $increment_views) {
            incrementNewsViewCount($news['id'], $conn);
        }
        
        return $news;
    }
    
    return null;
}
}

/**
 * ดึงข้อมูลประกาศตาม ID
 * 
 * @param int $id ID ของประกาศ
 * @param mysqli $conn การเชื่อมต่อฐานข้อมูล
 * @return array|null ข้อมูลประกาศ
 */
if (!function_exists('getAnnouncementDetail')) {
function getAnnouncementDetail($id, $conn) {
    if (!$conn || $conn->connect_error || !$id) {
        return null;
    }
    
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcement = $result->fetch_assoc();
        $stmt->close();
        return $announcement;
    }
    
    return null;
}
}

if (!function_exists('satitup_slugify')) {
function satitup_slugify($text) {
    if (empty($text)) {
        return 'general';
    }
    $slug = $text;
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if ($converted !== false) {
            $slug = $converted;
        }
    }
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($slug));
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'general';
}
}

if (!function_exists('getNewsCategorySlug')) {
function getNewsCategorySlug($newsItem) {
    if (!empty($newsItem['category_slug'])) {
        return satitup_slugify($newsItem['category_slug']);
    }
    if (!empty($newsItem['category_name'])) {
        return satitup_slugify($newsItem['category_name']);
    }
    return 'general';
}
}

if (!function_exists('getNewsFeaturedImage')) {
function getNewsFeaturedImage($newsItem, $conn) {
    if (!empty($newsItem['featured_image'])) {
        $path = $newsItem['featured_image'];
        if (strpos($path, 'admin/') === 0) {
            $path = substr($path, 6);
        }
        return '/' . ltrim($path, '/');
    }
    if (!$conn || $conn->connect_error || empty($newsItem['id'])) {
        return '/images/comingsoon.png';
    }
    $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ? ORDER BY is_primary DESC, id ASC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $newsItem['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        if ($row && !empty($row['image_path'])) {
            $path = $row['image_path'];
            if (strpos($path, 'admin/') === 0) {
                $path = substr($path, 6);
            }
            return '/' . ltrim($path, '/');
        }
    }
    if (!function_exists('getNewsFeaturedImage')) {
        if (!empty($newsItem['gallery_image'])) {
            $path = $newsItem['gallery_image'];
            if (strpos($path, 'admin/') === 0) {
                $path = substr($path, 6);
            }
            return '/' . ltrim($path, '/');
        }
    }
    return '/images/comingsoon.png';
}
}
