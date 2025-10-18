<?php
// ไฟล์ฟังก์ชันสำหรับระบบวิดีโอ
require_once 'db_config.php';

/**
 * แปลง YouTube URL เป็น Embed URL
 * @param string $url URL ของวิดีโอ YouTube
 * @return string URL สำหรับ embed วิดีโอ
 */
function getYoutubeEmbedUrl($url) {
    $videoId = getYoutubeVideoId($url);
    if ($videoId) {
        return "https://www.youtube.com/embed/{$videoId}";
    }
    return '';
}

/**
 * ดึง Video ID จาก YouTube URL
 * @param string $url URL ของวิดีโอ YouTube
 * @return string|null Video ID หรือ null ถ้าไม่พบ
 */
function getYoutubeVideoId($url) {
    $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([^&?#]+)/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

/**
 * ดึง URL ของรูปภาพตัวอย่างจาก YouTube URL
 * @param string $url URL ของวิดีโอ YouTube
 * @return string URL ของรูปภาพตัวอย่าง
 */
function getYoutubeThumbnail($urlOrId) {
    // ถ้าเป็น video ID โดยตรง (ไม่มี / หรือ .)
    if (!empty($urlOrId) && strpos($urlOrId, '/') === false && strpos($urlOrId, '.') === false) {
        return "https://img.youtube.com/vi/{$urlOrId}/mqdefault.jpg";
    }
    
    // ถ้าเป็น URL ให้แปลงเป็น video ID ก่อน
    $videoId = getYoutubeVideoId($urlOrId);
    if ($videoId) {
        return "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg";
    }
    
    // Return placeholder image if no valid ID
    return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect width="320" height="180" fill="%23ddd"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-family="sans-serif" font-size="16"%3ENo Video%3C/text%3E%3C/svg%3E';
}

/**
 * ดึงข้อมูลวิดีโอที่เป็น Featured
 * @return array ข้อมูลวิดีโอที่เป็น Featured
 */
function getFeaturedVideo() {
    global $video_conn;
    
    if (!$video_conn) {
        return null;
    }
    
    // ตรวจสอบว่าตารางมีอยู่หรือไม่
    $check_table = mysqli_query($video_conn, "SHOW TABLES LIKE 'videos'");
    if (!$check_table || mysqli_num_rows($check_table) == 0) {
        return null;
    }
    
    $sql = "SELECT v.*, c.name as category_name 
            FROM videos v 
            LEFT JOIN video_categories c ON v.category_id = c.id 
            WHERE v.featured = 1 AND v.active = 1 
            ORDER BY v.created_at DESC 
            LIMIT 1";
    
    $result = mysqli_query($video_conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        // ถ้าไม่มีวิดีโอที่เป็น Featured ให้ดึงวิดีโอล่าสุด
        $sql = "SELECT v.*, c.name as category_name 
                FROM videos v 
                LEFT JOIN video_categories c ON v.category_id = c.id 
                WHERE v.active = 1
                ORDER BY v.created_at DESC 
                LIMIT 1";
        
        $result = mysqli_query($video_conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    
    return null;
}

/**
 * ดึงข้อมูลวิดีโอล่าสุด
 * @param int $limit จำนวนวิดีโอที่ต้องการดึง
 * @param int $excludeId ID ของวิดีโอที่ต้องการข้าม (เช่น วิดีโอที่เป็น Featured)
 * @return array ข้อมูลวิดีโอล่าสุด
 */
function getLatestVideos($limit = 3, $excludeId = null) {
    global $video_conn;
    
    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!$video_conn) {
        return [];
    }
    
    // ตรวจสอบว่าตารางมีอยู่หรือไม่
    $check_table = mysqli_query($video_conn, "SHOW TABLES LIKE 'videos'");
    if (!$check_table || mysqli_num_rows($check_table) == 0) {
        return [];
    }
    
    $excludeClause = $excludeId ? "AND v.id != " . intval($excludeId) : "";
    
    $sql = "SELECT v.*, c.name as category_name 
            FROM videos v 
            LEFT JOIN video_categories c ON v.category_id = c.id 
            WHERE v.active = 1 $excludeClause 
            ORDER BY v.created_at DESC 
            LIMIT " . intval($limit);
    
    $result = mysqli_query($video_conn, $sql);
    
    if (!$result) {
        return [];
    }
    
    $videos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $videos[] = $row;
    }
    
    return $videos;
}

/**
 * ดึงข้อมูลวิดีโอตามหมวดหมู่
 * @param int $categoryId ID ของหมวดหมู่
 * @param int $limit จำนวนวิดีโอที่ต้องการดึง
 * @param int $offset ตำแหน่งเริ่มต้นในการดึงข้อมูล
 * @return array ข้อมูลวิดีโอตามหมวดหมู่
 */
function getVideosByCategory($categoryId, $limit = 10, $offset = 0) {
    global $video_conn;
    
    if (!$video_conn) {
        // Return sample data if no database connection
        $sampleVideos = [];
        for ($i = 1; $i <= min($limit, 3); $i++) {
            $sampleVideos[] = [
                'id' => $i + 100,
                'title' => "วิดีโอตัวอย่าง $i",
                'description' => "รายละเอียดวิดีโอตัวอย่าง $i",
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'youtube_id' => 'dQw4w9WgXcQ',
                'category_id' => $categoryId,
                'category_name' => 'หมวดหมู่ตัวอย่าง',
                'views' => rand(100, 5000),
                'event_date' => date('Y-m-d', strtotime("-$i days")),
                'created_at' => date('Y-m-d', strtotime("-$i days"))
            ];
        }
        return $sampleVideos;
    }
    
    $sql = "SELECT v.*, c.name as category_name 
            FROM videos v 
            LEFT JOIN video_categories c ON v.category_id = c.id 
            WHERE v.category_id = " . intval($categoryId) . " AND v.active = 1
            ORDER BY v.created_at DESC 
            LIMIT " . intval($offset) . ", " . intval($limit);
    
    $result = mysqli_query($video_conn, $sql);
    
    if (!$result) {
        return [];
    }
    
    $videos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $videos[] = $row;
    }
    
    return $videos;
}

/**
 * ดึงข้อมูลหมวดหมู่วิดีโอทั้งหมด
 * @return array ข้อมูลหมวดหมู่วิดีโอทั้งหมด
 */
function getAllCategories() {
    global $video_conn;
    
    if (!$video_conn) {
        // Return sample categories if no database connection
        return [
            ['id' => 1, 'name' => 'แนะนำโรงเรียน'],
            ['id' => 2, 'name' => 'กิจกรรมวิชาการ'],
            ['id' => 3, 'name' => 'กีฬา'],
            ['id' => 4, 'name' => 'ศิลปะและดนตรี'],
            ['id' => 5, 'name' => 'โครงการพิเศษ']
        ];
    }
    
    // ตรวจสอบว่าตารางมีอยู่หรือไม่
    $check_table = mysqli_query($video_conn, "SHOW TABLES LIKE 'video_categories'");
    if (!$check_table || mysqli_num_rows($check_table) == 0) {
        return [];
    }
    
    $sql = "SELECT * FROM video_categories ORDER BY name ASC";
    
    $result = mysqli_query($video_conn, $sql);
    
    if (!$result) {
        return [];
    }
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * ดึงข้อมูลวิดีโอตาม ID
 * @param int $videoId ID ของวิดีโอ
 * @return array|null ข้อมูลวิดีโอ
 */
function getVideoById($videoId) {
    global $video_conn;
    
    if (!$video_conn) {
        // Return sample data if no database connection
        return [
            'id' => $videoId,
            'title' => 'แนะนำโรงเรียนสาธิต โครงการ วมว. มหาวิทยาลัยพะเยา 2566 - เปิดบ้าน วมว.มพ.',
            'description' => 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา ในงานเปิดบ้าน วมว.มพ. ประจำปี 2566 นำเสนอหลักสูตรการเรียนการสอน กิจกรรมพัฒนานักเรียน และสิ่งอำนวยความสะดวกต่างๆ ของโรงเรียน',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'youtube_id' => 'dQw4w9WgXcQ',
            'category_id' => 1,
            'category_name' => 'แนะนำโรงเรียน',
            'views' => 1234,
            'event_date' => '2023-08-15',
            'created_at' => '2023-08-10',
            'featured' => 0
        ];
    }
    
    $sql = "SELECT v.*, c.name as category_name 
            FROM videos v 
            LEFT JOIN video_categories c ON v.category_id = c.id 
            WHERE v.id = " . intval($videoId);
    
    $result = mysqli_query($video_conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $video = mysqli_fetch_assoc($result);
        // Ensure all fields have default values
        $video['views'] = isset($video['views']) ? $video['views'] : 0;
        $video['youtube_id'] = isset($video['youtube_id']) ? $video['youtube_id'] : '';
        $video['event_date'] = isset($video['event_date']) ? $video['event_date'] : date('Y-m-d');
        $video['created_at'] = isset($video['created_at']) ? $video['created_at'] : date('Y-m-d');
        return $video;
    }
    
    // Return sample data if video not found
    return [
        'id' => $videoId,
        'title' => 'แนะนำโรงเรียนสาธิต โครงการ วมว. มหาวิทยาลัยพะเยา 2566 - เปิดบ้าน วมว.มพ.',
        'description' => 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา ในงานเปิดบ้าน วมว.มพ. ประจำปี 2566 นำเสนอหลักสูตรการเรียนการสอน กิจกรรมพัฒนานักเรียน และสิ่งอำนวยความสะดวกต่างๆ ของโรงเรียน',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'youtube_id' => 'dQw4w9WgXcQ',
        'category_id' => 1,
        'category_name' => 'แนะนำโรงเรียน',
        'views' => 1234,
        'event_date' => '2023-08-15',
        'created_at' => '2023-08-10',
        'featured' => 0
    ];
}

/**
 * เพิ่มจำนวนการดูวิดีโอ
 * @param int $videoId ID ของวิดีโอ
 * @return bool สถานะการอัปเดต
 */
function incrementVideoViews($videoId) {
    global $video_conn;
    
    if (!$video_conn) {
        return false;
    }
    
    $sql = "UPDATE videos SET views = views + 1 WHERE id = $videoId";
    
    return mysqli_query($video_conn, $sql);
}

/**
 * บันทึกข้อมูลวิดีโอใหม่
 * @param array $data ข้อมูลวิดีโอ
 * @return int|bool ID ของวิดีโอที่บันทึก หรือ false หากเกิดข้อผิดพลาด
 */
function saveVideo($data) {
    global $video_conn;
    
    $title = mysqli_real_escape_string($video_conn, $data['title']);
    $description = mysqli_real_escape_string($video_conn, $data['description']);
    $youtube_id = mysqli_real_escape_string($video_conn, $data['youtube_id']);
    $category_id = (int)$data['category_id'];
    $featured = isset($data['featured']) ? 1 : 0;
    $event_date = !empty($data['event_date']) ? "'" . mysqli_real_escape_string($video_conn, $data['event_date']) . "'" : "NULL";
    
    $sql = "INSERT INTO videos (title, description, youtube_id, category_id, featured, event_date) 
            VALUES ('$title', '$description', '$youtube_id', $category_id, $featured, $event_date)";
    
    if (mysqli_query($video_conn, $sql)) {
        return mysqli_insert_id($video_conn);
    }
    
    return false;
}

/**
 * อัปเดตข้อมูลวิดีโอ
 * @param int $videoId ID ของวิดีโอ
 * @param array $data ข้อมูลวิดีโอ
 * @return bool สถานะการอัปเดต
 */
function updateVideo($videoId, $data) {
    global $video_conn;
    
    $title = mysqli_real_escape_string($video_conn, $data['title']);
    $description = mysqli_real_escape_string($video_conn, $data['description']);
    $youtube_id = mysqli_real_escape_string($video_conn, $data['youtube_id']);
    $category_id = (int)$data['category_id'];
    $featured = isset($data['featured']) ? 1 : 0;
    $event_date = !empty($data['event_date']) ? "'" . mysqli_real_escape_string($video_conn, $data['event_date']) . "'" : "NULL";
    
    // ถ้าเป็น featured ให้ยกเลิก featured ของวิดีโออื่น
    if ($featured) {
        $sql = "UPDATE videos SET featured = 0 WHERE id != $videoId";
        mysqli_query($video_conn, $sql);
    }
    
    $sql = "UPDATE videos SET 
            title = '$title', 
            description = '$description', 
            youtube_id = '$youtube_id', 
            category_id = $category_id, 
            featured = $featured,
            event_date = $event_date
            WHERE id = $videoId";
    
    return mysqli_query($video_conn, $sql);
}

/**
 * ลบวิดีโอ
 * @param int $videoId ID ของวิดีโอ
 * @return bool สถานะการลบ
 */
function deleteVideo($videoId) {
    global $video_conn;
    
    $sql = "DELETE FROM videos WHERE id = $videoId";
    
    return mysqli_query($video_conn, $sql);
}

/**
 * ตั้งค่าวิดีโอเป็น Featured
 * @param int $videoId ID ของวิดีโอ
 * @return bool สถานะการอัปเดต
 */
function setFeaturedVideo($videoId) {
    global $video_conn;
    
    // ยกเลิก featured ของวิดีโอทั้งหมด
    $sql = "UPDATE videos SET featured = 0";
    mysqli_query($video_conn, $sql);
    
    // ตั้งค่า featured ให้กับวิดีโอที่ต้องการ
    $sql = "UPDATE videos SET featured = 1 WHERE id = $videoId";
    
    return mysqli_query($video_conn, $sql);
}

/**
 * ดึง YouTube ID จาก URL
 * @param string $url YouTube URL
 * @return string|null YouTube ID
 */
function extractYoutubeId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }
    
    return null;
}


/**
 * แปลงวันที่เป็นรูปแบบที่อ่านง่าย
 * @param string $date วันที่ในรูปแบบ Y-m-d หรือ timestamp
 * @return string วันที่ในรูปแบบที่อ่านง่าย
 */
function formatDate($date) {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    return date('d F Y', $timestamp);
}

/**
 * แปลงจำนวนการดูเป็นรูปแบบที่อ่านง่าย
 * @param int $views จำนวนการดู
 * @return string จำนวนการดูในรูปแบบที่อ่านง่าย
 */
function formatViews($views) {
    if ($views < 1000) {
        return $views;
    } elseif ($views < 1000000) {
        return round($views / 1000, 1) . 'K';
    } else {
        return round($views / 1000000, 1) . 'M';
    }
}
