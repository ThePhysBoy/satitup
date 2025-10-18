<?php
/**
 * Database Connection Configuration
 * การตั้งค่าการเชื่อมต่อฐานข้อมูลสำหรับโรงเรียนสาธิตมหาวิทยาลัยพะเยา
 */

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    //'database' => 'school_satitup',
    'database' => 'satitup',
    'charset' => 'utf8mb4'
];

try {
    // สร้างการเชื่อมต่อ MySQLi
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    // ตั้งค่า charset
    $conn->set_charset($db_config['charset']);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // ตั้งค่า timezone
    $conn->query("SET time_zone = '+07:00'");

    // สร้างตาราง management หากยังไม่มี (ใช้บนหน้า about-management.php)
    $createManagementTableSql = "CREATE TABLE IF NOT EXISTS `management` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(50) NOT NULL,
        `first_name` VARCHAR(255) NOT NULL,
        `last_name` VARCHAR(255) NOT NULL,
        `management_position` VARCHAR(255) NOT NULL,
        `image_path` VARCHAR(255) DEFAULT NULL,
        `email` VARCHAR(255) DEFAULT NULL,
        `phone` VARCHAR(50) DEFAULT NULL,
        `bio` TEXT DEFAULT NULL,
        `order_number` INT DEFAULT 0,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!$conn->query($createManagementTableSql)) {
        throw new Exception("Failed to ensure management table exists: " . $conn->error);
    }

} catch (Exception $e) {
    // จัดการข้อผิดพลาด
    error_log("Database connection error: " . $e->getMessage());
    
    // ในกรณีที่เชื่อมต่อไม่ได้ ให้ใช้ข้อมูลตัวอย่าง
    $conn = false;
}

/**
 * ฟังก์ชันสำหรับทำความสะอาดข้อมูล input
 */
function clean_input($data) {
    global $conn;
    if ($conn) {
        return mysqli_real_escape_string($conn, trim($data));
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * ฟังก์ชันสำหรับดึงข่าวสาร
 */
function get_news($limit = 6, $offset = 0, $category = null) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    $sql = "SELECT n.*, c.name as category_name 
            FROM news n 
            LEFT JOIN news_categories c ON n.category_id = c.id 
            WHERE n.status = 'published'";
    
    if ($category && $category !== 'all') {
        $sql .= " AND c.slug = '" . clean_input($category) . "'";
    }
    
    $sql .= " ORDER BY n.created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
    
    return $conn->query($sql);
}

/**
 * ฟังก์ชันสำหรับดึงประกาศ
 */
function get_announcements($limit = 10, $offset = 0, $type = null) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    $sql = "SELECT * FROM announcements WHERE status = 'active'";
    
    if ($type && $type !== 'all') {
        $sql .= " AND type = '" . clean_input($type) . "'";
    }
    
    $sql .= " ORDER BY priority DESC, created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
    
    return $conn->query($sql);
}

/**
 * ฟังก์ชันสำหรับดึงข้อมูลข่าวเดี่ยว
 */
function get_single_news($id) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    $sql = "SELECT n.*, c.name as category_name 
            FROM news n 
            LEFT JOIN news_categories c ON n.category_id = c.id 
            WHERE n.id = " . intval($id) . " AND n.status = 'published'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // เพิ่มจำนวนการอ่าน
        $conn->query("UPDATE news SET views = views + 1 WHERE id = " . intval($id));
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * ฟังก์ชันสำหรับค้นหาข่าว
 */
function search_news($keyword, $limit = 10) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    $keyword = clean_input($keyword);
    
    $sql = "SELECT n.*, c.name as category_name 
            FROM news n 
            LEFT JOIN news_categories c ON n.category_id = c.id 
            WHERE n.status = 'published' 
            AND (n.title LIKE '%$keyword%' OR n.content LIKE '%$keyword%' OR n.excerpt LIKE '%$keyword%')
            ORDER BY n.created_at DESC 
            LIMIT " . intval($limit);
    
    return $conn->query($sql);
}

/**
 * ฟังก์ชันสำหรับปิดการเชื่อมต่อฐานข้อมูล
 */
function close_connection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// ปิดการเชื่อมต่อเมื่อสคริปต์จบ
register_shutdown_function('close_connection');

/**
 * SQL สำหรับสร้างตารางข้อมูล (สำหรับการติดตั้งครั้งแรก)
 */
$create_tables_sql = "
-- ตารางหมวดหมู่ข่าว (ใช้ชื่อ news_categories ตามที่มีอยู่จริง)
CREATE TABLE IF NOT EXISTS `news_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตารางข่าวสาร (ใช้ author_id แทน user_id และ view_count แทน views)
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext NOT NULL,
  `featured_image` varchar(255),
  `category_id` int(11),
  `author_id` int(11),
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `published_date` timestamp NULL DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตารางประกาศ
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `content` longtext,
  `type` varchar(100) DEFAULT 'ประกาศทั่วไป',
  `priority` enum('ปกติ','สูง','ด่วน') DEFAULT 'ปกติ',
  `status` enum('draft','active','expired') DEFAULT 'draft',
  `start_date` date,
  `end_date` date,
  `file_attachment` varchar(500),
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `priority` (`priority`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

/**
 * ฟังก์ชันสำหรับสร้างตารางข้อมูล
 */
function create_tables() {
    global $conn, $create_tables_sql;
    
    if (!$conn) {
        return false;
    }
    
    // แยกคำสั่ง SQL
    $queries = explode(';', $create_tables_sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if (!$conn->query($query)) {
                error_log("Error creating table: " . $conn->error);
                return false;
            }
        }
    }
    
    return true;
}

// เรียกใช้ฟังก์ชันสร้างตารางถ้ายังไม่มี (สำหรับการติดตั้งครั้งแรก)
// create_tables();

?>