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
        if (!mkdir($upload_dir, 0777, true)) {
            return ['success' => false, 'error' => 'ไม่สามารถสร้างโฟลเดอร์สำหรับอัพโหลดได้'];
        }
    }
    
    // Generate unique filename with timestamp to avoid cache issues
    $filename = uniqid() . '_' . time() . '_' . basename($file['name']);
    $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename); // Remove special characters
    $upload_path = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Optimize image if needed
        $image_info = getimagesize($upload_path);
        if ($image_info && $image_info[0] > 1200) {
            // Image is too large, resize it
            resizeImage($upload_path, $upload_path, 1200);
        }
        
        return [
            'success' => true,
            'path' => 'uploads/' . $subfolder . '/' . $filename,
            'filename' => $filename
        ];
    } else {
        return ['success' => false, 'error' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
}

/**
 * Resize an image
 * 
 * @param string $source_path Source image path
 * @param string $target_path Target image path
 * @param int $max_width Maximum width
 * @param int $max_height Maximum height (optional, will maintain aspect ratio if not provided)
 * @param int $quality JPEG quality (1-100)
 * @return bool True on success, false on failure
 */
function resizeImage($source_path, $target_path, $max_width, $max_height = null, $quality = 80) {
    // Get image info
    $info = getimagesize($source_path);
    if (!$info) {
        return false;
    }
    
    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];
    
    // No resize needed if image is already smaller
    if ($width <= $max_width && ($max_height === null || $height <= $max_height)) {
        if ($source_path !== $target_path) {
            return copy($source_path, $target_path);
        }
        return true;
    }
    
    // Calculate new dimensions
    if ($max_height === null) {
        // Calculate height based on width
        $max_height = floor($height * ($max_width / $width));
    } else {
        // Calculate dimensions based on aspect ratio
        $ratio = min($max_width / $width, $max_height / $height);
        $max_width = floor($width * $ratio);
        $max_height = floor($height * $ratio);
    }
    
    // Create image resource based on mime type
    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Create target image
    $target = imagecreatetruecolor($max_width, $max_height);
    
    // Preserve transparency for PNG and GIF
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $max_width, $max_height, $transparent);
    }
    
    // Resize image
    imagecopyresampled($target, $source, 0, 0, 0, 0, $max_width, $max_height, $width, $height);
    
    // Save image
    $result = false;
    switch ($mime) {
        case 'image/jpeg':
            $result = imagejpeg($target, $target_path, $quality);
            break;
        case 'image/png':
            $result = imagepng($target, $target_path, floor($quality / 10));
            break;
        case 'image/gif':
            $result = imagegif($target, $target_path);
            break;
        case 'image/webp':
            $result = imagewebp($target, $target_path, $quality);
            break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($target);
    
    return $result;
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
    $stmt = $conn->prepare("SELECT n.*, u.username, u.full_name
                           FROM news n
                           LEFT JOIN users u ON n.author_id = u.id
                           WHERE n.id = ?");
    if (!$stmt) {
        error_log("Database error in getNewsById: " . $conn->error);
        return null;
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        error_log("Execute error in getNewsById: " . $stmt->error);
        return null;
    }
    
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
    if (!$stmt) {
        error_log("Database error in getNewsImages: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param('i', $news_id);
    if (!$stmt->execute()) {
        error_log("Execute error in getNewsImages: " . $stmt->error);
        return [];
    }
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Increment view count for a news article
 * 
 * @param int $id The news ID
 * @param mysqli $conn Database connection
 * @return bool True on success, false on failure
 */
function incrementViewCount($id, $conn) {
    $stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
    if (!$stmt) {
        error_log("Database error in incrementViewCount: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        error_log("Execute error in incrementViewCount: " . $stmt->error);
        return false;
    }
    
    return true;
}

/**
 * Get latest news
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of news to get
 * @param int $category_id Category ID (optional)
 * @return array The latest news
 */
function getLatestNews($conn, $limit = 5, $category_id = null) {
    $sql = "SELECT n.*, u.username, u.full_name
            FROM news n
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.status = 'published'";
    
    if ($category_id !== null) {
        $sql .= " AND n.category_id = ?";
        $stmt = $conn->prepare($sql . " ORDER BY n.published_at DESC LIMIT ?");
        if (!$stmt) {
            error_log("Database error in getLatestNews: " . $conn->error);
            return [];
        }
        $stmt->bind_param('ii', $category_id, $limit);
    } else {
        $stmt = $conn->prepare($sql . " ORDER BY n.published_at DESC LIMIT ?");
        if (!$stmt) {
            error_log("Database error in getLatestNews: " . $conn->error);
            return [];
        }
        $stmt->bind_param('i', $limit);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute error in getLatestNews: " . $stmt->error);
        return [];
    }
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get most viewed news
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of news to get
 * @return array The most viewed news
 */
function getMostViewedNews($conn, $limit = 5) {
    $sql = "SELECT n.*, u.username, u.full_name
            FROM news n
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.status = 'published'
            ORDER BY n.views DESC, n.published_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Database error in getMostViewedNews: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param('i', $limit);
    
    if (!$stmt->execute()) {
        error_log("Execute error in getMostViewedNews: " . $stmt->error);
        return [];
    }
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get news statistics
 * 
 * @param mysqli $conn Database connection
 * @return array The statistics
 */
function getNewsStatistics($conn) {
    $stats = [
        'total' => 0,
        'published' => 0,
        'draft' => 0,
        'pending' => 0,
        'total_views' => 0
    ];
    
    // Get total news count
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(views) as total_views
            FROM news";
    
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total'] = $row['total'] ?? 0;
        $stats['published'] = $row['published'] ?? 0;
        $stats['draft'] = $row['draft'] ?? 0;
        $stats['pending'] = $row['pending'] ?? 0;
        $stats['total_views'] = $row['total_views'] ?? 0;
    }
    
    return $stats;
}

/**
 * Get all news categories
 * 
 * @param mysqli $conn Database connection
 * @param bool $active_only Get only active categories
 * @return array The categories
 */
function getAllCategories($conn, $active_only = false) {
    $sql = "SELECT c.*, COUNT(n.id) as news_count 
            FROM news_categories c
            LEFT JOIN news n ON c.id = n.category_id";
    
    if ($active_only) {
        $sql .= " WHERE c.is_active = 1";
    }
    
    $sql .= " GROUP BY c.id ORDER BY c.name ASC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        error_log("Error fetching categories: " . $conn->error);
        return [];
    }
}

/**
 * Get category by ID or slug
 * 
 * @param mixed $id_or_slug Category ID or slug
 * @param mysqli $conn Database connection
 * @return array|null The category or null if not found
 */
function getCategoryByIdOrSlug($id_or_slug, $conn) {
    if (is_numeric($id_or_slug)) {
        $stmt = $conn->prepare("SELECT * FROM news_categories WHERE id = ?");
        $stmt->bind_param('i', $id_or_slug);
    } else {
        $stmt = $conn->prepare("SELECT * FROM news_categories WHERE slug = ?");
        $stmt->bind_param('s', $id_or_slug);
    }
    
    if (!$stmt) {
        error_log("Database error in getCategoryByIdOrSlug: " . $conn->error);
        return null;
    }
    
    if (!$stmt->execute()) {
        error_log("Execute error in getCategoryByIdOrSlug: " . $stmt->error);
        return null;
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}