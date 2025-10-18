<?php
// นำเข้าไฟล์ฟังก์ชันสำหรับระบบวิดีโอ
// ตรวจสอบว่าไฟล์ video_functions.php มีอยู่หรือไม่ หากมี ให้นำเข้า
if (file_exists('video_system/includes/video_functions.php')) {
    require_once 'video_system/includes/video_functions.php';
    
    // ดึงวิดีโอแนะนำ (Featured Video) จากฐานข้อมูล
    $featuredVideo = getFeaturedVideo();
    
    // ดึงวิดีโอล่าสุด 20 รายการ (ไม่รวม Featured Video หากมี) จากฐานข้อมูล โดยเรียงจากใหม่ที่สุดก่อน
    $latestVideos = getLatestVideos(20, $featuredVideo ? $featuredVideo['id'] : null);
} else {
    // กรณีที่ไฟล์ video_functions.php ไม่มีอยู่ กำหนดให้ตัวแปรวิดีโอเป็นค่าว่าง
    $featuredVideo = null;
    $latestVideos = [];
}

// Debug: ตรวจสอบข้อมูล (Commented out เพื่อไม่ให้แสดงในหน้าเว็บจริง)
// echo "<!-- Debug: Featured Video = " . ($featuredVideo ? "Found" : "Not found") . " -->";
// echo "<!-- Debug: Latest Videos = " . count($latestVideos) . " items -->";

// Debug: แสดงจำนวนวิดีโอที่ดึงได้ (Commented out)
// echo "<!-- Debug: Got " . count($latestVideos) . " videos from database -->";

// ถ้าไม่มีวิดีโอทั้งในส่วนแนะนำและวิดีโอล่าสุด ให้ใช้ข้อมูลตัวอย่างเพื่อแสดงผล
if (empty($latestVideos) && empty($featuredVideo)) {
    // ชุดข้อมูลตัวอย่างสำหรับวิดีโอล่าสุด
    $latestVideos = [
        [
            'id' => 1,
            'title' => 'กิจกรรมวันวิทยาศาสตร์',
            'category_name' => 'กิจกรรมวิชาการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // URL วิดีโอ YouTube (ตัวอย่าง)
            'upload_date' => date('Y-m-d')
        ],
        [
            'id' => 2,
            'title' => 'การแข่งขันกีฬาสี',
            'category_name' => 'กีฬา',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-1 days'))
        ],
        [
            'id' => 3,
            'title' => 'กิจกรรมวันภาษาไทย',
            'category_name' => 'กิจกรรมวิชาการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-2 days'))
        ],
        [
            'id' => 4,
            'title' => 'การนำเสนอผลงานวิจัย',
            'category_name' => 'การนำเสนอผลงานทางวิชาการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-3 days'))
        ],
        [
            'id' => 5,
            'title' => 'กิจกรรมโครงการ วมว.',
            'category_name' => 'โครงการ วมว.',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-4 days'))
        ],
        [
            'id' => 6,
            'title' => 'โครงงานวิทยาศาสตร์ดีเด่น',
            'category_name' => 'ผลงานนักเรียน',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-5 days'))
        ],
        [
            'id' => 7,
            'title' => 'คอนเสิร์ตดนตรีโรงเรียน',
            'category_name' => 'ดนตรี',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-6 days'))
        ],
        [
            'id' => 8,
            'title' => 'ห้องปฏิบัติการคอมพิวเตอร์',
            'category_name' => 'ห้องปฏิบัติการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-7 days'))
        ],
        [
            'id' => 9,
            'title' => 'การเรียนภาษาอังกฤษ',
            'category_name' => 'การเรียนภาษาอังกฤษ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-8 days'))
        ],
        [
            'id' => 10,
            'title' => 'แนะนำโรงเรียนสาธิต',
            'category_name' => 'แนะนำมหาวิทยาลัย',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-9 days'))
        ],
        [
            'id' => 11,
            'title' => 'บรรยากาศการเรียนการสอน',
            'category_name' => 'บรรยากาศการเรียนการสอน',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-10 days'))
        ],
        [
            'id' => 12,
            'title' => 'กิจกรรมพัฒนานักเรียน',
            'category_name' => 'กิจกรรมวิชาการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-11 days'))
        ],
        [
            'id' => 13,
            'title' => 'การแข่งขันฟุตบอล',
            'category_name' => 'กีฬา',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-12 days'))
        ],
        [
            'id' => 14,
            'title' => 'การแสดงดนตรีคลาสสิก',
            'category_name' => 'ดนตรี',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-13 days'))
        ],
        [
            'id' => 15,
            'title' => 'ห้องปฏิบัติการเคมี',
            'category_name' => 'ห้องปฏิบัติการ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-14 days'))
        ],
        [
            'id' => 16,
            'title' => 'การนำเสนอผลงานนานาชาติ',
            'category_name' => 'การนำเสนอผลงานทางวิชาการในและต่างประเทศ',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-15 days'))
        ],
        [
            'id' => 17,
            'title' => 'โครงงานพัฒนาชุมชน',
            'category_name' => 'ผลงานนักเรียน',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-16 days'))
        ],
        [
            'id' => 18,
            'title' => 'กิจกรรมพัฒนาอัจฉริยภาพ',
            'category_name' => 'โครงการ วมว.',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-17 days'))
        ],
        [
            'id' => 19,
            'title' => 'การแข่งขันบาสเกตบอล',
            'category_name' => 'กีฬา',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-18 days'))
        ],
        [
            'id' => 20,
            'title' => 'คอนเสิร์ตประจำปี',
            'category_name' => 'ดนตรี',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-19 days'))
        ]
    ];
}

// ถ้าไม่มีวิดีโอแนะนำ (featuredVideo) และมีวิดีโอล่าสุด (latestVideos) ให้ใช้วิดีโอแรกจาก latestVideos เป็น featuredVideo
if (empty($featuredVideo) && !empty($latestVideos)) {
    $featuredVideo = $latestVideos[0];
    // ลบวิดีโอแรกออกจากรายการวิดีโอล่าสุด เพื่อไม่ให้ซ้ำกันเมื่อแสดงผลในสไลเดอร์ด้านล่าง
    array_shift($latestVideos);
}

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย (ใช้ if (!function_exists) เพื่อป้องกันการประกาศซ้ำหากมีฟังก์ชันชื่อเดียวกันในไฟล์อื่น)
if (!function_exists('formatThaiDate')) {
    function formatThaiDate($date_str) {
        $timestamp = strtotime($date_str); // แปลง string วันที่ให้เป็น timestamp
        // อาร์เรย์ชื่อเดือนภาษาไทย
        $thai_month_arr = array(
            "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
        );
        
        $day = date('j', $timestamp); // วันที่ (ไม่มี 0 นำหน้า)
        $month = $thai_month_arr[date('n', $timestamp) - 1]; // เดือนภาษาไทย
        $year = date('Y', $timestamp) + 543; // ปี พ.ศ.
        
        return "$day $month $year"; // คืนค่าวันที่ในรูปแบบ "วัน เดือน ปีพ.ศ."
    }
}

// ฟังก์ชัน fallback สำหรับกรณีที่ไม่มีฟังก์ชัน getYoutubeEmbedUrl (ป้องกันประกาศซ้ำ)
if (!function_exists('getYoutubeEmbedUrl')) {
    function getYoutubeEmbedUrl($url) {
        // Regular expression เพื่อดึง ID วิดีโอจาก YouTube URL ทุกรูปแบบ
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([^&?#]+)/';
        preg_match($pattern, $url, $matches); // ค้นหารูปแบบที่ตรงกัน
        $videoId = isset($matches[1]) ? $matches[1] : ''; // ดึง Video ID
        return "https://www.youtube.com/embed/{$videoId}"; // คืนค่า URL สำหรับ embed
    }
}

// ฟังก์ชัน fallback สำหรับกรณีที่ไม่มีฟังก์ชัน getYoutubeThumbnail (ป้องกันประกาศซ้ำ)
if (!function_exists('getYoutubeThumbnail')) {
    function getYoutubeThumbnail($url) {
        // Regular expression เพื่อดึง ID วิดีโอจาก YouTube URL ทุกรูปแบบ
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([^&?#]+)/';
        preg_match($pattern, $url, $matches); // ค้นหารูปแบบที่ตรงกัน
        $videoId = isset($matches[1]) ? $matches[1] : ''; // ดึง Video ID
        return "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg"; // คืนค่า URL ของ thumbnail
    }
}
?>

<!-- ส่วนแสดงวิดีโอกิจกรรมหลักของโรงเรียน -->
<section class="video-quick-links-section py-5">
    <div class="container">
        <!-- ส่วนหัวข้อของเซคชั่น -->
        <div class="section-header text-center mb-4">
            <h2 class="section-title">วิดีโอกิจกรรม</h2>
            <p class="section-subtitle">รวมวิดีโอกิจกรรมต่างๆ ของโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        </div>
        
        <?php if ($featuredVideo): // ตรวจสอบว่ามี Featured Video หรือไม่ ถ้ามี ให้แสดงผล ?>
        <div class="row">
            <!-- Featured Video (วิดีโอแนะนำ) -->
            <div class="col-12 mb-4">
                <div class="featured-video-container">
                    <!-- อัตราส่วน 16:9 สำหรับวิดีโอ YouTube -->
                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                        <iframe 
                            src="<?php echo getYoutubeEmbedUrl($featuredVideo['youtube_url']); ?>?autoplay=1&mute=1&controls=1&loop=1&rel=0&showinfo=0" 
                            title="<?php echo htmlspecialchars($featuredVideo['title']); ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <!-- ข้อมูลของ Featured Video -->
                    <div class="featured-video-info mt-3">
                        <h4><?php echo htmlspecialchars($featuredVideo['title']); ?></h4>
                        <p class="text-muted">
                            <!-- แสดงหมวดหมู่เป็น Badge -->
                            <span class="badge bg-primary"><?php echo htmlspecialchars($featuredVideo['category_name']); ?></span>
                            <small class="ms-2">
                                <i class="fas fa-calendar-alt me-1"></i> 
                                <?php echo formatThaiDate($featuredVideo['upload_date']); // แสดงวันที่อัปโหลด ?>
                            </small>
                        </p>
                    </div>
                    </div>
            </div>
        </div>
        <?php endif; // สิ้นสุดการแสดง Featured Video ?>
        
        <!-- Latest Videos (วิดีโอล่าสุด) -->
        <div class="row">
            <div class="col-12">
                <!-- ส่วนหัวของแถบวิดีโอล่าสุด พร้อมปุ่มควบคุม -->
                <div class="latest-videos-header d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5 mb-0">วิดีโอล่าสุด 20 รายการ</h3>
                    <div>
                        <span class="text-muted me-3">
                            <i class="fas fa-info-circle"></i> 
                            แสดง <?php echo count($latestVideos); ?> วิดีโอ
                        </span>
                        <a href="video_system/all_videos.php" class="btn btn-sm btn-outline-primary">ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
            </div>
            
                <!-- คอนเทนเนอร์สำหรับสไลเดอร์วิดีโอ (แถบเลื่อนวิดีโอด้านล่าง) -->
                <div class="position-relative latest-videos-slider">
                    <!-- ปุ่มควบคุมการเลื่อน (ซ้าย) -->
                    <button class="carousel-control-prev videos-control-prev" type="button" id="prevVideo">
                        <i class="fas fa-chevron-left"></i>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <!-- ปุ่มควบคุมการเลื่อน (ขวา) -->
                    <button class="carousel-control-next videos-control-next" type="button" id="nextVideo">
                        <i class="fas fa-chevron-right"></i>
                        <span class="visually-hidden">Next</span>
                    </button>
                    
                    <!-- คอนเทนเนอร์ที่เก็บรายการวิดีโอที่จะสไลด์ -->
                    <div class="video-slider-container">
                        <div class="video-slider" id="videoSlider">
                            <?php
                            // ตรวจสอบว่ามีวิดีโอล่าสุดหรือไม่ ถ้ามี ให้วนลูปแสดงผลแต่ละวิดีโอ
                            if (!empty($latestVideos)) {
                                $videoIndex = 1; // เริ่มต้นดัชนีวิดีโอ
                                foreach ($latestVideos as $video) {
                                    // ดึงข้อมูล thumbnail จาก YouTube URL
                                    $thumbnail = getYoutubeThumbnail($video['youtube_url']);
                                    
                                    echo '<div class="video-item" data-video-number="' . $videoIndex . '">';
                                    echo '<div class="video-card">';
                                    echo '<a href="' . $video['youtube_url'] . '" class="video-thumbnail-link" target="_blank" data-video-id="' . $video['id'] . '" data-video-title="' . htmlspecialchars($video['title']) . '" data-video-category="' . htmlspecialchars($video['category_name']) . '">';
                                    echo '<div class="video-thumbnail">';
                                    echo '<img src="' . $thumbnail . '" alt="' . htmlspecialchars($video['title']) . '" class="img-fluid rounded">';
                                    
                                    // แสดง badge "ใหม่" ถ้าอัปโหลดภายใน 7 วันที่ผ่านมา
                                    $uploadDate = strtotime($video['upload_date']);
                                    $daysDiff = (time() - $uploadDate) / (60 * 60 * 24);
                                    if ($daysDiff <= 7) {
                                        echo '<div class="video-new-badge"><span class="badge bg-danger">ใหม่</span></div>';
                                    }
                                    
                                    // ปุ่มเล่นวิดีโอ
                                    echo '<div class="video-play-button"><i class="fas fa-play"></i></div>';
                                    // โอเวอร์เลย์ข้อมูลวิดีโอเมื่อ hover
                                    echo '<div class="video-info-overlay">';
                                    echo '<h5>' . htmlspecialchars($video['title']) . '</h5>';
                                    echo '<p class="category">' . htmlspecialchars($video['category_name']) . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</a>';
                                    // เมตาข้อมูลวิดีโอ (วันที่, จำนวนการดู)
                                    echo '<div class="video-meta mt-2">';
                                    echo '<small class="text-muted">';
                                    echo '<i class="fas fa-calendar-alt me-1"></i> ' . formatThaiDate($video['upload_date']);
                                    if (isset($video['views']) && $video['views'] > 0) {
                                        echo ' <span class="ms-2"><i class="fas fa-eye me-1"></i>' . number_format($video['views']) . ' ครั้ง</span>';
                                    }
                                    echo '</small>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    $videoIndex++;
                                }
                            } else {
                                // กรณีไม่มีวิดีโอในฐานข้อมูล ให้แสดงข้อความแจ้งเตือน
                                echo '<div class="col-12 text-center py-4">';
                                echo '<p class="text-muted">ยังไม่มีวิดีโอในระบบ</p>';
                                echo '</div>';
                            }
                            ?>
                    </div>
            </div>
            
                    <!-- Slide Indicators (ตัวบ่งชี้สไลด์ด้านล่าง) -->
                    <div class="slide-indicators" id="slideIndicators"></div>
                    </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS สำหรับ Video Quick Links -->
<style>
/* Video Quick Links Section */
.video-quick-links-section {
    background: linear-gradient(135deg, #e0e7ff 0%, #f0f4ff 100%); /* พื้นหลังไล่ระดับสี */
    padding: 60px 0; /* ระยะห่างด้านบน-ล่าง */
}

.video-quick-links-section .section-title {
    color: #333; /* สีตัวอักษร */
    font-size: 2.2rem; /* ขนาดตัวอักษร */
    font-weight: 700; /* ความหนาตัวอักษร */
    margin-bottom: 0.8rem; /* ระยะห่างด้านล่าง */
    position: relative; /* กำหนดตำแหน่งสัมพันธ์ */
    display: inline-block; /* แสดงผลแบบ inline-block */
}

.video-quick-links-section .section-title::after {
    content: ''; /* สร้างเส้นใต้ */
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    bottom: -10px; /* ระยะห่างจากด้านล่าง */
    left: 50%; /* ตำแหน่งกึ่งกลาง */
    transform: translateX(-50%); /* จัดให้อยู่กึ่งกลางแนวนอน */
    width: 80px; /* ความกว้างของเส้น */
    height: 3px; /* ความหนาของเส้น */
    background: linear-gradient(90deg, #8B7AA8, #9B8AB8); /* พื้นหลังไล่ระดับสี */
    border-radius: 2px; /* ความโค้งมนของขอบ */
}

.video-quick-links-section .section-subtitle {
    color: #5a6268; /* สีตัวอักษร */
    font-size: 1.1rem; /* ขนาดตัวอักษร */
}

/* Featured Video (วิดีโอแนะนำ) */
.featured-video-container {
    border-radius: 15px; /* ความโค้งมนของขอบ */
    overflow: hidden; /* ซ่อนส่วนที่ล้นออก */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* เงาของคอนเทนเนอร์ */
    background: #fff; /* สีพื้นหลัง */
    padding: 15px; /* ระยะห่างภายใน */
}

.featured-video-info {
    padding: 10px; /* ระยะห่างภายในข้อมูลวิดีโอ */
}

.featured-video-info h4 {
    font-weight: 600; /* ความหนาตัวอักษร */
    color: #333; /* สีตัวอักษร */
    font-size: 1.3rem; /* ขนาดตัวอักษร */
    margin-bottom: 5px; /* ระยะห่างด้านล่าง */
}

.featured-video-placeholder {
    background-color: #f8f9fa; /* สีพื้นหลัง placeholder */
    border: 1px solid #ddd; /* ขอบ placeholder */
    padding: 20px; /* ระยะห่างภายใน */
    border-radius: 10px; /* ความโค้งมนของขอบ */
    color: #666; /* สีตัวอักษร */
}

/* Video Slider (แถบเลื่อนวิดีโอ) */
.latest-videos-slider {
    margin: 20px 0; /* ระยะห่างด้านบน-ล่าง */
    position: relative; /* กำหนดตำแหน่งสัมพันธ์เพื่อจัดปุ่ม */
}

.video-slider-container {
    width: 100%; /* ความกว้างเต็ม */
    overflow: hidden; /* ซ่อนส่วนที่ล้นออก */
    padding: 10px 0; /* ระยะห่างด้านบน-ล่าง */
}

.video-slider {
    display: flex; /* แสดงผลแบบ flexbox */
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); /* แอนิเมชันการเลื่อน */
    gap: 15px; /* ระยะห่างระหว่างรายการวิดีโอ */
    width: max-content; /* ปรับความกว้างตามเนื้อหา */
}

.video-item {
    min-width: 160px;  /* ขนาดขั้นต่ำของแต่ละรายการ (ปรับให้เล็กลง) */
    max-width: 160px; /* ขนาดสูงสุดของแต่ละรายการ */
    flex: 0 0 auto; /* ไม่ให้ย่อขยาย */
    transition: transform 0.3s ease; /* แอนิเมชันการ hover */
    margin: 0 5px; /* ระยะห่างซ้าย-ขวา */
}

/* Video Cards (การ์ดวิดีโอแต่ละรายการ) */
.video-card {
    border-radius: 8px; /* ความโค้งมนของขอบ (ปรับให้เล็กลง) */
    overflow: hidden; /* ซ่อนส่วนที่ล้นออก */
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); /* เงาของการ์ด (ปรับให้เล็กลง) */
    background: #fff; /* สีพื้นหลัง */
    transition: all 0.3s ease; /* แอนิเมชัน */
    height: 100%; /* ความสูงเต็ม */
}

.video-card:hover {
    transform: translateY(-3px) scale(1.02); /* ยกขึ้นเล็กน้อยและขยายเมื่อ hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* เงาเมื่อ hover */
}

.video-thumbnail {
    position: relative; /* กำหนดตำแหน่งสัมพันธ์ */
    overflow: hidden; /* ซ่อนส่วนที่ล้นออก */
    aspect-ratio: 16 / 9; /* กำหนดอัตราส่วนภาพ 16:9 */
}

.video-thumbnail img {
    transition: transform 0.5s ease; /* แอนิเมชันการขยายรูปภาพ */
    width: 100%; /* ความกว้างเต็ม */
    height: 100%; /* ความสูงเต็ม */
    object-fit: cover; /* ครอบคลุมพื้นที่โดยไม่เสียสัดส่วน */
}

.video-thumbnail:hover img {
    transform: scale(1.05); /* ขยายรูปภาพเล็กน้อยเมื่อ hover */
}

.video-play-button {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    top: 50%; /* กึ่งกลางแนวตั้ง */
    left: 50%; /* กึ่งกลางแนวนอน */
    transform: translate(-50%, -50%); /* จัดให้อยู่กึ่งกลางจริง */
    width: 40px; /* ความกว้างปุ่ม */
    height: 40px; /* ความสูงปุ่ม */
    background: rgba(0, 0, 0, 0.6); /* สีพื้นหลังปุ่ม */
    border-radius: 50%; /* ทำให้ปุ่มเป็นวงกลม */
    display: flex; /* แสดงผลแบบ flexbox */
    align-items: center; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
    justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
    color: #fff; /* สีไอคอน */
    font-size: 1rem; /* ขนาดไอคอน */
    opacity: 0; /* เริ่มต้นซ่อนปุ่ม */
    transition: all 0.3s ease; /* แอนิเมชันการแสดงผล */
}

.video-thumbnail:hover .video-play-button {
    opacity: 1; /* แสดงปุ่มเมื่อ hover */
    background: rgba(139, 122, 168, 0.8); /* สีพื้นหลังปุ่มเมื่อ hover */
}

.video-new-badge {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    top: 10px; /* ระยะห่างจากด้านบน */
    right: 10px; /* ระยะห่างจากด้านขวา */
    z-index: 5; /* ลำดับการแสดงผล */
}

.video-new-badge .badge {
    font-size: 0.75rem; /* ขนาดตัวอักษร badge */
    padding: 5px 10px; /* ระยะห่างภายใน badge */
    animation: pulse 2s infinite; /* แอนิเมชันกระพริบ */
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 10px 5px rgba(220, 53, 69, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.video-info-overlay {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    bottom: 0; /* ชิดด้านล่าง */
    left: 0; /* ชิดซ้าย */
    width: 100%; /* ความกว้างเต็ม */
    padding: 15px; /* ระยะห่างภายใน */
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0)); /* พื้นหลังไล่ระดับสี */
    color: #fff; /* สีตัวอักษร */
    opacity: 0; /* เริ่มต้นซ่อน */
    transform: translateY(20px); /* เลื่อนลงด้านล่าง */
    transition: all 0.3s ease; /* แอนิเมชัน */
}

.video-thumbnail:hover .video-info-overlay {
    opacity: 1; /* แสดงผลเมื่อ hover */
    transform: translateY(0); /* กลับสู่ตำแหน่งปกติ */
}

.video-info-overlay h5 {
    font-size: 1rem; /* ขนาดตัวอักษร */
    margin-bottom: 5px; /* ระยะห่างด้านล่าง */
    font-weight: 600; /* ความหนาตัวอักษร */
}

.video-info-overlay .category {
    font-size: 0.8rem; /* ขนาดตัวอักษร */
    opacity: 0.8; /* ความโปร่งใส */
}

.video-meta {
    font-size: 0.85rem; /* ขนาดตัวอักษร */
    padding: 10px; /* ระยะห่างภายใน */
}

/* Slide Indicators (จุดบ่งชี้สไลด์) */
.slide-indicators {
    display: flex; /* แสดงผลแบบ flexbox */
    justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
    gap: 8px; /* ระยะห่างระหว่างจุด */
    margin-top: 20px; /* ระยะห่างด้านบน */
}

.slide-indicator {
    width: 10px; /* ความกว้างจุด */
    height: 10px; /* ความสูงจุด */
    border-radius: 50%; /* ทำให้เป็นวงกลม */
    background: #ddd; /* สีพื้นหลัง */
    cursor: pointer; /* เปลี่ยน cursor เมื่อชี้ */
    transition: all 0.3s ease; /* แอนิเมชัน */
}

.slide-indicator.active {
    background: #8b7aa8; /* สีเมื่อ active */
    transform: scale(1.2); /* ขยายขนาดเมื่อ active */
}

.slide-indicator:hover {
    background: #a594c1; /* สีเมื่อ hover */
}

/* Carousel Controls (ปุ่มควบคุมการเลื่อน) */
.videos-control-prev,
.videos-control-next {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    width: 40px; /* ความกว้างปุ่ม */
    height: 40px; /* ความสูงปุ่ม */
    background: rgba(139, 122, 168, 0.7); /* สีพื้นหลังปุ่ม */
    border-radius: 50%; /* ทำให้ปุ่มเป็นวงกลม */
    top: 50%; /* กึ่งกลางแนวตั้ง */
    transform: translateY(-50%); /* จัดให้อยู่กึ่งกลางจริง */
    z-index: 10; /* ลำดับการแสดงผล */
    border: none; /* ไม่มีขอบ */
    display: flex; /* แสดงผลแบบ flexbox */
    align-items: center; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
    justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
    color: white; /* สีไอคอน */
    font-size: 1rem; /* ขนาดไอคอน */
    cursor: pointer; /* เปลี่ยน cursor เมื่อชี้ */
    transition: all 0.3s ease; /* แอนิเมชัน */
}

.videos-control-prev {
    left: -20px; /* ตำแหน่งปุ่มซ้าย */
}

.videos-control-next {
    right: -20px; /* ตำแหน่งปุ่มขวา */
}

.videos-control-prev:hover,
.videos-control-next:hover {
    background: rgba(139, 122, 168, 0.9); /* สีพื้นหลังเมื่อ hover */
    color: white; /* สีไอคอนเมื่อ hover */
}

/* Video Popup (ป๊อปอัปวิดีโอ) */
.video-popup {
    position: fixed; /* กำหนดตำแหน่งคงที่ */
    top: 0; /* ชิดด้านบน */
    left: 0; /* ชิดซ้าย */
    width: 100%; /* ความกว้างเต็มจอ */
    height: 100%; /* ความสูงเต็มจอ */
    background: rgba(0, 0, 0, 0.8); /* พื้นหลังสีดำโปร่งใส */
    display: flex; /* แสดงผลแบบ flexbox */
    align-items: center; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
    justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
    z-index: 1000; /* ลำดับการแสดงผล (อยู่บนสุด) */
    opacity: 0; /* เริ่มต้นซ่อน */
    visibility: hidden; /* ซ่อนองค์ประกอบ */
    transition: all 0.3s ease; /* แอนิเมชัน */
}

.video-popup.active {
    opacity: 1; /* แสดงผลเมื่อ active */
    visibility: visible; /* แสดงองค์ประกอบ */
}

.video-popup-content {
    width: 80%; /* ความกว้างป๊อปอัป */
    max-width: 900px; /* ความกว้างสูงสุด */
    background: white; /* สีพื้นหลัง */
    border-radius: 10px; /* ความโค้งมนของขอบ */
    overflow: hidden; /* ซ่อนส่วนที่ล้นออก */
    position: relative; /* กำหนดตำแหน่งสัมพันธ์ */
}

.video-popup-close {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    top: 10px; /* ระยะห่างจากด้านบน */
    right: 10px; /* ระยะห่างจากด้านขวา */
    background: rgba(0, 0, 0, 0.5); /* สีพื้นหลังปุ่มปิด */
    color: white; /* สีไอคอน */
    border: none; /* ไม่มีขอบ */
    width: 30px; /* ความกว้างปุ่ม */
    height: 30px; /* ความสูงปุ่ม */
    border-radius: 50%; /* ทำให้เป็นวงกลม */
    display: flex; /* แสดงผลแบบ flexbox */
    align-items: center; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
    justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
    cursor: pointer; /* เปลี่ยน cursor เมื่อชี้ */
    z-index: 10; /* ลำดับการแสดงผล */
}

/* Tooltip (ข้อความช่วยเหลือ) */
.video-tooltip {
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    background: rgba(0, 0, 0, 0.8); /* สีพื้นหลังโปร่งใส */
    color: white; /* สีตัวอักษร */
    padding: 10px 15px; /* ระยะห่างภายใน */
    border-radius: 5px; /* ความโค้งมนของขอบ */
    font-size: 0.9rem; /* ขนาดตัวอักษร */
    z-index: 100; /* ลำดับการแสดงผล */
    pointer-events: none; /* ไม่รับการคลิก/ชี้เมาส์ */
    opacity: 0; /* เริ่มต้นซ่อน */
    transition: opacity 0.3s ease; /* แอนิเมชัน */
    max-width: 300px; /* ความกว้างสูงสุด */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* เงา */
}

.video-tooltip::after {
    content: ''; /* สร้างสามเหลี่ยมชี้ลง */
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    bottom: -10px; /* ระยะห่างจากด้านล่าง */
    left: 50%; /* กึ่งกลางแนวนอน */
    transform: translateX(-50%); /* จัดให้อยู่กึ่งกลางจริง */
    border-width: 10px 10px 0; /* ขนาดสามเหลี่ยม */
    border-style: solid; /* รูปแบบเส้นขอบ */
    border-color: rgba(0, 0, 0, 0.8) transparent transparent; /* สีสามเหลี่ยม */
}

/* Responsive Design (ปรับให้เข้ากับขนาดหน้าจอต่างๆ) */
@media (max-width: 992px) {
    .video-quick-links-section {
        padding: 40px 0; /* ลด padding บนหน้าจอขนาดกลาง */
    }
    
    .video-quick-links-section .section-title {
        font-size: 1.8rem; /* ลดขนาดหัวข้อ */
    }
    
    .video-play-button {
        width: 50px; /* ปรับขนาดปุ่ม play */
        height: 50px;
        font-size: 1.2rem;
    }
    
    .video-item {
        min-width: calc(50% - 10px); /* แสดง 2 รายการต่อแถวบนหน้าจอแท็บเล็ต */
    }
}

@media (max-width: 768px) {
    .video-quick-links-section .section-title {
        font-size: 1.5rem; /* ลดขนาดหัวข้อ */
    }
    
    .featured-video-info h4 {
        font-size: 1.2rem; /* ลดขนาดหัวข้อ featured video */
    }
    
    .videos-control-prev,
    .videos-control-next {
        width: 30px; /* ลดขนาดปุ่มควบคุมสไลด์ */
        height: 30px;
    }
    
    .videos-control-prev {
        left: -15px; /* ปรับตำแหน่งปุ่มควบคุม */
    }
    
    .videos-control-next {
        right: -15px; /* ปรับตำแหน่งปุ่มควบคุม */
    }
    
    .video-item {
        min-width: 100%; /* แสดง 1 รายการต่อแถวบนมือถือ */
    }
}

@media (max-width: 576px) {
    .video-play-button {
        width: 40px; /* ปรับขนาดปุ่ม play เล็กสุด */
        height: 40px;
        font-size: 1rem;
    }
    
    .video-info-overlay {
        padding: 10px; /* ลด padding overlay */
    }
    
    .video-info-overlay h5 {
        font-size: 0.9rem; /* ลดขนาดหัวข้อ overlay */
        margin-bottom: 2px;
    }
}
</style>

<!-- JavaScript สำหรับควบคุมการทำงานของวิดีโอและสไลด์ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // สร้าง element สำหรับ tooltip (ข้อความช่วยเหลือเมื่อ hover)
    const tooltip = document.createElement('div');
    tooltip.className = 'video-tooltip';
    document.body.appendChild(tooltip); // เพิ่ม tooltip เข้าไปใน body
    
    // ตัวแปรสำหรับควบคุมสไลเดอร์วิดีโอ
    const slider = document.getElementById('videoSlider'); // คอนเทนเนอร์วิดีโอที่เลื่อนได้
    const prevBtn = document.getElementById('prevVideo'); // ปุ่มเลื่อนไปก่อนหน้า
    const nextBtn = document.getElementById('nextVideo'); // ปุ่มเลื่อนไปถัดไป
    const videoItems = document.querySelectorAll('.video-item'); // รายการวิดีโอแต่ละชิ้น
    const indicatorsContainer = document.getElementById('slideIndicators'); // คอนเทนเนอร์สำหรับจุดบ่งชี้สไลด์

    // ตรวจสอบว่ามีสไลเดอร์หรือวิดีโออยู่หรือไม่
    if (!slider || !videoItems || videoItems.length === 0) {
        console.log('No slider or videos found'); // แสดงข้อความใน console หากไม่พบ
        return; // หยุดการทำงานของ script
    }

    const visibleItems = 3; // จำนวนวิดีโอที่มองเห็นได้พร้อมกันในแถบสไลเดอร์ (กำหนดเอง)
    let currentIndex = 0; // ดัชนีเริ่มต้นของวิดีโอที่แสดง
    let autoSlideInterval; // ตัวแปรสำหรับเก็บ interval ของ auto-slide
    let isPaused = false; // สถานะการหยุดชั่วคราวของ auto-slide

    console.log('Total videos:', videoItems.length); // แสดงจำนวนวิดีโอทั้งหมด
    console.log('Visible items:', visibleItems); // แสดงจำนวนรายการที่มองเห็นได้

    // ถ้ามีวิดีโอน้อยกว่าหรือเท่ากับจำนวนที่แสดงได้ ไม่ต้องมีการสไลด์
    if (videoItems.length <= 3) {
        console.log('Not enough videos for sliding, showing all'); // แสดงข้อความใน console
        prevBtn.style.display = 'none'; // ซ่อนปุ่มเลื่อนก่อนหน้า
        nextBtn.style.display = 'none'; // ซ่อนปุ่มเลื่อนถัดไป
        return; // หยุดการทำงานของ script
    }
    
    // สร้างจุดบ่งชี้ (indicators) สำหรับแต่ละกลุ่มวิดีโอที่สามารถสไลด์ได้
    const totalGroups = Math.ceil(videoItems.length / visibleItems); // คำนวณจำนวนกลุ่ม
    for (let i = 0; i < totalGroups; i++) {
        const indicator = document.createElement('div'); // สร้าง div สำหรับ indicator
        indicator.className = 'slide-indicator' + (i === 0 ? ' active' : ''); // กำหนด class และ active สำหรับอันแรก
        indicator.dataset.index = i; // เก็บ index ของกลุ่ม
        indicator.addEventListener('click', () => { // เพิ่ม event listener เมื่อคลิก
            clearInterval(autoSlideInterval); // หยุด auto-slide ชั่วคราว
            currentIndex = i * visibleItems; // ตั้งค่า index ตามกลุ่มที่คลิก
            updateSlider(); // อัพเดตตำแหน่งสไลเดอร์
            startAutoSlide(); // เริ่ม auto-slide ใหม่
        });
        indicatorsContainer.appendChild(indicator); // เพิ่ม indicator เข้าไปในคอนเทนเนอร์
    }
    
    // อัพเดตสถานะของจุดบ่งชี้สไลด์
    function updateIndicators() {
        const currentGroup = Math.floor(currentIndex / visibleItems); // คำนวณกลุ่มปัจจุบัน
        document.querySelectorAll('.slide-indicator').forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentGroup); // เพิ่ม/ลบ class active
        });
    }
    
    // ฟังก์ชันสำหรับเลื่อนสไลเดอร์
    function moveSlider(direction) {
        const maxIndex = Math.max(0, videoItems.length - visibleItems); // ดัชนีสูงสุดที่สามารถเลื่อนไปได้
        const oldIndex = currentIndex; // เก็บดัชนีเก่าเพื่อ debug

        console.log('Moving slider:', direction, 'Current index:', currentIndex, 'Max index:', maxIndex); // แสดงสถานะใน console

        if (direction === 'prev') { // ถ้าเลื่อนไปก่อนหน้า
            currentIndex = Math.max(0, currentIndex - visibleItems); // ลด index แต่ไม่ให้ติดลบ
        } else { // ถ้าเลื่อนไปถัดไป
            // ถ้าถึงสุดท้ายแล้ว ให้วนกลับไปเริ่มต้น
            if (currentIndex >= maxIndex) {
                currentIndex = 0; // ตั้งค่ากลับไปที่ 0
            } else {
                currentIndex = Math.min(maxIndex, currentIndex + visibleItems); // เพิ่ม index แต่ไม่ให้เกิน maxIndex
            }
        }

        console.log('Index changed from', oldIndex, 'to', currentIndex); // แสดงการเปลี่ยนแปลง index
        updateSlider(); // อัพเดตการแสดงผลสไลเดอร์
    }

    // ฟังก์ชันสำหรับอัพเดตตำแหน่งของสไลเดอร์และสถานะปุ่ม
    function updateSlider() {
        const translateX = -(currentIndex * (100 / visibleItems)); // คำนวณระยะที่ต้องเลื่อน
        slider.style.transform = `translateX(${translateX}%)`; // เลื่อนสไลเดอร์

        // อัพเดตการแสดงผลของปุ่มเลื่อน
        const maxIndex = Math.max(0, videoItems.length - visibleItems);
        prevBtn.style.display = currentIndex === 0 ? 'none' : 'flex'; // ซ่อนปุ่ม Prev ถ้าอยู่ที่จุดเริ่มต้น
        nextBtn.style.display = currentIndex >= maxIndex ? 'none' : 'flex'; // ซ่อนปุ่ม Next ถ้าอยู่ที่จุดสุดท้าย

        updateIndicators(); // อัพเดตจุดบ่งชี้สไลด์
        console.log('Slider updated - Index:', currentIndex, 'TranslateX:', translateX + '%'); // แสดงสถานะใน console
    }
    
    // ฟังก์ชันสำหรับเริ่ม Auto-slide
    function startAutoSlide() {
        // ถ้ามีวิดีโอน้อยกว่าหรือเท่ากับจำนวนที่แสดงได้ ไม่ต้อง auto-slide
        if (videoItems.length <= visibleItems) {
            console.log('Not enough videos for sliding, hiding controls');
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }

        console.log('Starting auto-slide with', videoItems.length, 'videos');
        let slideCount = 0; // ตัวนับรอบการสไลด์ (สำหรับ debug)

        autoSlideInterval = setInterval(() => { // ตั้งค่า interval สำหรับ auto-slide
            if (!isPaused) { // ถ้าไม่ได้หยุดชั่วคราว
                slideCount++;
                console.log('Auto-slide tick', slideCount, '- current index:', currentIndex);

                // ถ้าถึงสุดแล้วให้วนกลับไปเริ่มต้น
                if (currentIndex >= videoItems.length - visibleItems) {
                    currentIndex = 0; // ตั้งค่ากลับไปที่ index แรก
                    console.log('Reached end, resetting to index 0');
                    updateSlider(); // อัพเดตสไลเดอร์
                } else {
                    moveSlider('next'); // เลื่อนไปวิดีโอถัดไป
                }
            } else {
                console.log('Auto-slide paused'); // แสดงสถานะหยุดชั่วคราว
            }
        }, 3000); // เลื่อนทุก 3 วินาที
    }
    
    // หยุด auto-slide ชั่วคราวเมื่อเมาส์ชี้บนสไลเดอร์
    slider.addEventListener('mouseenter', () => {
        isPaused = true;
    });
    
    // กลับมาเริ่ม auto-slide เมื่อเมาส์ออกจากสไลเดอร์
    slider.addEventListener('mouseleave', () => {
        isPaused = false;
    });
    
    // เพิ่ม event listener สำหรับปุ่มเลื่อน (Prev)
    prevBtn.addEventListener('click', () => {
        clearInterval(autoSlideInterval); // หยุด auto-slide ชั่วคราว
        moveSlider('prev'); // เลื่อนไปก่อนหน้า
        startAutoSlide(); // เริ่ม auto-slide ใหม่
    });
    
    // เพิ่ม event listener สำหรับปุ่มเลื่อน (Next)
    nextBtn.addEventListener('click', () => {
        clearInterval(autoSlideInterval); // หยุด auto-slide ชั่วคราว
        moveSlider('next'); // เลื่อนไปถัดไป
        startAutoSlide(); // เริ่ม auto-slide ใหม่
    });
    
    // ตั้งค่าสถานะเริ่มต้นของปุ่มและสไลเดอร์เมื่อโหลดหน้า
    console.log('Setting up initial button states...');
    if (videoItems.length > visibleItems) { // ถ้ามีวิดีโอมากกว่าที่แสดงได้
        prevBtn.style.display = 'none'; // ซ่อนปุ่มย้อนกลับตอนเริ่มต้น
        nextBtn.style.display = 'flex'; // แสดงปุ่มถัดไป
    } else { // ถ้ามีวิดีโอน้อยหรือเท่ากับที่แสดงได้
        prevBtn.style.display = 'none'; // ซ่อนทั้งสองปุ่ม
        nextBtn.style.display = 'none';
    }

    // เริ่ม auto-slide ทันทีที่โหลดหน้า
    console.log('Initializing slider...');
    startAutoSlide();
    
    // ปรับขนาดและตำแหน่งสไลเดอร์เมื่อหน้าจอเปลี่ยนขนาด (responsive)
    window.addEventListener('resize', function() {
        // รีเซ็ตตำแหน่งสไลเดอร์และสถานะปุ่ม
        currentIndex = 0; // กลับไปที่เริ่มต้น
        slider.style.transform = 'translateX(0)'; // เลื่อนกลับไปตำแหน่ง 0
        prevBtn.style.display = 'none'; // ซ่อนปุ่ม Prev
        nextBtn.style.display = videoItems.length > visibleItems ? 'flex' : 'none'; // แสดงปุ่ม Next ถ้าจำเป็น
    });
    
    // เพิ่ม tooltip เมื่อเมาส์ชี้บนวิดีโอแต่ละรายการ
    videoItems.forEach((item, index) => {
        const link = item.querySelector('.video-thumbnail-link'); // ลิงก์ thumbnail
        const title = link.dataset.videoTitle || item.querySelector('.video-info-overlay h5').textContent; // ชื่อวิดีโอ
        const category = link.dataset.videoCategory || item.querySelector('.video-info-overlay .category').textContent; // หมวดหมู่วิดีโอ
        const videoNumber = item.dataset.videoNumber || (index + 1); // ลำดับวิดีโอ
        
        link.addEventListener('mouseenter', function(e) { // เมื่อเมาส์ชี้เข้า
            // แสดงเนื้อหาใน tooltip
            tooltip.innerHTML = `
                <div class="text-center">
                    <small class="text-warning">วิดีโอลำดับที่ ${videoNumber}</small><br>
                    <strong>${title}</strong><br>
                    <span class="text-info">${category}</span>
                </div>
            `;
            tooltip.style.opacity = '1'; // ทำให้ tooltip แสดงผล
            
            // คำนวณตำแหน่งของ tooltip ให้อยู่เหนือวิดีโอ
            const rect = this.getBoundingClientRect(); // ขนาดและตำแหน่งของ element ปัจจุบัน
            tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10 + window.scrollY}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
        });
        
        link.addEventListener('mouseleave', function() { // เมื่อเมาส์ออกจาก
            tooltip.style.opacity = '0'; // ซ่อน tooltip
        });
    });
    
    // สร้าง popup วิดีโอเมื่อคลิกที่ thumbnail (ฟังก์ชันนี้ไม่ได้ใช้แล้ว เนื่องจากเปลี่ยนไปเปิดลิงก์ YouTube ตรง)
    videoItems.forEach(item => {
        const link = item.querySelector('.video-thumbnail-link');
        
        link.addEventListener('click', function(e) {
            e.preventDefault(); // ป้องกันการกระทำ default ของลิงก์
            const videoUrl = this.getAttribute('href'); // ดึง URL วิดีโอ
            
            // ดึง video ID จาก YouTube URL (รองรับทุกรูปแบบ)
            let videoId = '';
            if (videoUrl.includes('youtube.com/watch?v=')) {
                videoId = videoUrl.split('v=')[1];
                if (videoId.includes('&')) {
                    videoId = videoId.split('&')[0];
                }
            } else if (videoUrl.includes('youtu.be/')) {
                videoId = videoUrl.split('youtu.be/')[1];
                if (videoId.includes('?')) {
                    videoId = videoId.split('?')[0];
                }
            }
            
            if (!videoId) {
                console.error('Cannot extract video ID from:', videoUrl);
                window.open(videoUrl, '_blank'); // ถ้าหา ID ไม่เจอ ให้เปิดในแท็บใหม่
                return;
            }
            
            // สร้าง popup สำหรับแสดงวิดีโอ
            const popup = document.createElement('div');
            popup.className = 'video-popup';
            // โครงสร้าง HTML ของ popup
            popup.innerHTML = `
                <div class="video-popup-content">
                    <button class="video-popup-close"><i class="fas fa-times"></i></button>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            `;
            
            document.body.appendChild(popup); // เพิ่ม popup เข้าไปใน body
            
            // แสดง popup ด้วยการเพิ่ม class 'active'
            setTimeout(() => {
                popup.classList.add('active');
            }, 10);
            
            // ปิด popup เมื่อคลิกที่ปุ่มปิด
            const closeBtn = popup.querySelector('.video-popup-close');
            closeBtn.addEventListener('click', function() {
                popup.classList.remove('active'); // ซ่อน popup
                setTimeout(() => {
                    popup.remove(); // ลบ popup ออกจาก DOM
                }, 300);
            });
            
            // ปิด popup เมื่อคลิกที่พื้นหลัง
            popup.addEventListener('click', function(e) {
                if (e.target === popup) { // ตรวจสอบว่าคลิกที่พื้นหลังจริงๆ
                    popup.classList.remove('active');
        setTimeout(() => {
                        popup.remove();
                    }, 300);
                }
            });
        });
    });
    
    // ปรับ responsive สำหรับมือถือ (ฟังก์ชันนี้อาจไม่จำเป็นต้องเรียกใช้โดยตรง)
    function adjustForMobile() {
        const isMobile = window.innerWidth < 768;
        if (isMobile) {
            videoItems.forEach(item => {
                item.style.minWidth = '100%';
            });
        } else {
            const isTablet = window.innerWidth < 992;
            videoItems.forEach(item => {
                item.style.minWidth = isTablet ? 'calc(50% - 10px)' : 'calc(33.333% - 14px)';
            });
        }
    }
    
    // เรียกใช้ฟังก์ชันปรับ responsive (และเพิ่ม event listener สำหรับ resize)
    adjustForMobile();
    window.addEventListener('resize', adjustForMobile);
});
</script>

<!-- เพิ่ม HTML สำหรับ popup (ไม่ได้ใช้งานใน script ปัจจุบัน เนื่องจากสร้าง popup ด้วย JavaScript โดยตรง) -->
<div id="videoPopupTemplate" style="display: none;">
    <div class="video-popup">
        <div class="video-popup-content">
            <button class="video-popup-close"><i class="fas fa-times"></i></button>
            <div class="ratio ratio-16x9">
                <iframe src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>