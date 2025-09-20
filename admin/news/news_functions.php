<?php
/**
 * News Functions
 * Helper functions for news management
 */

/**
 * Create a URL-friendly slug from a string
 * 
 * @param string $string The string to convert
 * @return string The slug
 */
function createSlug($string) {
    // Replace Thai characters with English characters or remove them
    $thai_map = array(
        'ก' => 'k', 'ข' => 'kh', 'ฃ' => 'kh', 'ค' => 'kh', 'ฅ' => 'kh', 'ฆ' => 'kh',
        'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch', 'ช' => 'ch', 'ซ' => 's', 'ฌ' => 'ch',
        'ญ' => 'y', 'ฎ' => 'd', 'ฏ' => 't', 'ฐ' => 'th', 'ฑ' => 'th', 'ฒ' => 'th',
        'ณ' => 'n', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th', 'ท' => 'th', 'ธ' => 'th',
        'น' => 'n', 'บ' => 'b', 'ป' => 'p', 'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph',
        'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm', 'ย' => 'y', 'ร' => 'r', 'ล' => 'l',
        'ว' => 'w', 'ศ' => 's', 'ษ' => 's', 'ส' => 's', 'ห' => 'h', 'ฬ' => 'l',
        'อ' => '', 'ฮ' => 'h',
        'ะ' => 'a', 'ั' => 'a', 'า' => 'a', 'ำ' => 'am', 'ิ' => 'i', 'ี' => 'i',
        'ึ' => 'ue', 'ื' => 'ue', 'ุ' => 'u', 'ู' => 'u', 'เ' => 'e', 'แ' => 'ae',
        'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai', '่' => '', '้' => '', '๊' => '', '๋' => '',
        'ั' => 'a', '็' => '', '์' => '', 'ๆ' => '', 'ฯ' => ''
    );
    
    $string = strtr($string, $thai_map);
    
    // Convert to lowercase
    $string = mb_strtolower($string, 'UTF-8');
    
    // Replace spaces with dashes
    $string = str_replace(' ', '-', $string);
    
    // Remove all non-alphanumeric characters except dashes
    $string = preg_replace('/[^a-z0-9\-]/', '', $string);
    
    // Replace multiple dashes with single dash
    $string = preg_replace('/-+/', '-', $string);
    
    // Remove leading and trailing dashes
    $string = trim($string, '-');
    
    return $string;
}

/**
 * Upload an image file
 * 
 * @param array $file The uploaded file array ($_FILES['field'])
 * @param string $subfolder The subfolder to upload to
 * @return array Result array with success status, path and error message
 */
function uploadImage($file, $subfolder = '') {
    // Define allowed file types and max file size
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check if file is valid
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'ไม่พบไฟล์ที่อัพโหลด'];
    }
    
    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'ประเภทไฟล์ไม่ถูกต้อง กรุณาอัพโหลดไฟล์รูปภาพเท่านั้น (JPEG, PNG, GIF, WEBP)'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'ไฟล์มีขนาดใหญ่เกินไป กรุณาอัพโหลดไฟล์ขนาดไม่เกิน 5MB'];
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../../uploads/' . $subfolder;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename); // Remove special characters
    $upload_path = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return [
            'success' => true,
            'path' => 'uploads/' . $subfolder . '/' . $filename
        ];
    } else {
        return ['success' => false, 'error' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
}

/**
 * Re-arrange $_FILES array for multiple file uploads
 * 
 * @param array $file_post The $_FILES array
 * @return array Re-arranged array
 */
function reArrayFiles($file_post) {
    $file_array = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    
    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_array[$i][$key] = $file_post[$key][$i];
        }
    }
    
    return $file_array;
}

/**
 * Get news by ID
 * 
 * @param int $id The news ID
 * @param mysqli $conn Database connection
 * @return array|null The news data or null if not found
 */
function getNewsById($id, $conn) {
    $stmt = $conn->prepare("SELECT n.*, c.name as category_name, u.username, u.full_name 
                           FROM news n 
                           LEFT JOIN news_categories c ON n.category_id = c.id 
                           LEFT JOIN users u ON n.author_id = u.id 
                           WHERE n.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get news images by news ID
 * 
 * @param int $news_id The news ID
 * @param mysqli $conn Database connection
 * @return array The news images
 */
function getNewsImages($news_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM news_images WHERE news_id = ? ORDER BY display_order");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Increment view count for a news article
 * 
 * @param int $id The news ID
 * @param mysqli $conn Database connection
 * @return void
 */
function incrementViewCount($id, $conn) {
    $stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

/**
 * Get latest news
 * 
 * @param int $limit Number of news to get
 * @param int $category_id Category ID (optional)
 * @param mysqli $conn Database connection
 * @return array The latest news
 */
function getLatestNews($limit = 5, $category_id = null, $conn) {
    $sql = "SELECT n.*, c.name as category_name, u.username, u.full_name 
            FROM news n 
            LEFT JOIN news_categories c ON n.category_id = c.id 
            LEFT JOIN users u ON n.author_id = u.id 
            WHERE n.status = 'published'";
    
    if ($category_id !== null) {
        $sql .= " AND n.category_id = ?";
        $stmt = $conn->prepare($sql . " ORDER BY n.published_at DESC LIMIT ?");
        $stmt->bind_param('ii', $category_id, $limit);
    } else {
        $stmt = $conn->prepare($sql . " ORDER BY n.published_at DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}
