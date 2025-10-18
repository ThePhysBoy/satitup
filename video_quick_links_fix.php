<?php
// นำเข้าไฟล์ฟังก์ชันสำหรับระบบวิดีโอ
if (file_exists('video_system/includes/video_functions.php')) {
    require_once 'video_system/includes/video_functions.php';
    
    // ดึงวิดีโอแนะนำ (Featured Video)
    $featuredVideo = getFeaturedVideo();
    
    // ดึงวิดีโอทั้งหมด (ไม่รวม Featured) เรียงจากใหม่ที่สุดก่อน สำหรับแสดงในสไลด์
    $latestVideos = getLatestVideos(50, $featuredVideo ? $featuredVideo['id'] : null);
} else {
    // กรณีไม่มีไฟล์ video_functions.php
    $featuredVideo = null;
    $latestVideos = [];
}

// ถ้าไม่มีวิดีโอในฐานข้อมูลเลย ให้ใช้ข้อมูลตัวอย่าง
if (empty($latestVideos) && empty($featuredVideo)) {
    $latestVideos = [];
    $sampleTitles = [
        'กิจกรรมวันวิทยาศาสตร์', 'การแข่งขันกีฬาสี', 'กิจกรรมวันภาษาไทย', 
        'การนำเสนอผลงานวิจัย', 'กิจกรรมโครงการ วมว.', 'โครงงานวิทยาศาสตร์ดีเด่น',
        'คอนเสิร์ตดนตรีโรงเรียน', 'ห้องปฏิบัติการคอมพิวเตอร์', 'การเรียนภาษาอังกฤษ',
        'แนะนำโรงเรียนสาธิต', 'บรรยากาศการเรียนการสอน', 'กิจกรรมพัฒนานักเรียน',
        'การแข่งขันฟุตบอล', 'การแสดงดนตรีคลาสสิก', 'ห้องปฏิบัติการเคมี'
    ];
    $categories = [
        ['name' => 'กิจกรรมวิชาการ', 'id' => 1],
        ['name' => 'กีฬา', 'id' => 2],
        ['name' => 'การนำเสนอผลงานทางวิชาการ', 'id' => 3],
        ['name' => 'โครงการ วมว.', 'id' => 4],
        ['name' => 'ผลงานนักเรียน', 'id' => 5],
        ['name' => 'ดนตรี', 'id' => 6],
        ['name' => 'ห้องปฏิบัติการ', 'id' => 7],
        ['name' => 'การเรียนภาษาอังกฤษ', 'id' => 8]
    ];
    
    // สร้างวิดีโอตัวอย่าง 15 รายการ
    for ($i = 0; $i < 15; $i++) {
        $category = $categories[$i % count($categories)];
        $latestVideos[] = [
            'id' => $i + 1,
            'title' => $sampleTitles[$i % count($sampleTitles)],
            'category_name' => $category['name'],
            'category_id' => $category['id'],
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d', strtotime('-' . $i . ' days'))
        ];
    }
}
    
    // ถ้าไม่มี featured video ให้ใช้วิดีโอแรกเป็น featured
    if (empty($featuredVideo)) {
        $featuredVideo = [
            'id' => 0,
            'title' => 'แนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา',
            'category_name' => 'แนะนำโรงเรียน',
            'description' => 'วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรียนรู้เกี่ยวกับประวัติความเป็นมา หลักสูตร และกิจกรรมต่างๆ ของโรงเรียน',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'upload_date' => date('Y-m-d'),
            'featured' => 1
        ];
    }

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
if (!function_exists('formatThaiDate')) {
    function formatThaiDate($dateString) {
        $timestamp = strtotime($dateString);
        $thaiMonths = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];
        
        $day = date('j', $timestamp);
        $month = $thaiMonths[date('n', $timestamp) - 1];
        $year = date('Y', $timestamp) + 543;
        
        return "$day $month $year";
    }
}

// ฟังก์ชันดึง YouTube thumbnail จาก URL
if (!function_exists('getYouTubeThumbnail')) {
    function getYouTubeThumbnail($youtubeUrl) {
        $videoId = '';
        
        if (strpos($youtubeUrl, 'youtube.com/watch?v=') !== false) {
            $queryString = parse_url($youtubeUrl, PHP_URL_QUERY);
            parse_str($queryString, $params);
            $videoId = isset($params['v']) ? $params['v'] : '';
        } elseif (strpos($youtubeUrl, 'youtu.be/') !== false) {
            $path = parse_url($youtubeUrl, PHP_URL_PATH);
            $videoId = trim($path, '/');
        }
        
        if (!empty($videoId)) {
            return "https://img.youtube.com/vi/$videoId/mqdefault.jpg";
        }
        
        return "images/video-placeholder.jpg";
    }
}
?>

<!-- Video Quick Links Section -->
<section class="video-quick-links-section py-5">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">วิดีโอกิจกรรม</h2>
            <p class="section-subtitle">รับชมวิดีโอกิจกรรมต่างๆ ของโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="featured-video-container mb-4">
                    <?php 
                    // ใช้วิดีโอ featured หรือวิดีโอแรกเป็นวิดีโอหลัก
                    $mainVideo = $featuredVideo ? $featuredVideo : (!empty($latestVideos) ? $latestVideos[0] : null);
                    if ($mainVideo) {
                        $videoId = '';
                        if (strpos($mainVideo['youtube_url'], 'youtube.com/watch?v=') !== false) {
                            $queryString = parse_url($mainVideo['youtube_url'], PHP_URL_QUERY);
                            parse_str($queryString, $params);
                            $videoId = isset($params['v']) ? $params['v'] : '';
                        } elseif (strpos($mainVideo['youtube_url'], 'youtu.be/') !== false) {
                            $path = parse_url($mainVideo['youtube_url'], PHP_URL_PATH);
                            $videoId = trim($path, '/');
                        }
                        
                        if ($videoId) {
                            // แสดงวิดีโอ YouTube ที่เล่นอัตโนมัติแบบไม่มีเสียง
                            echo '<div class="ratio ratio-16x9">';
                            echo '<iframe id="featuredVideo" src="https://www.youtube.com/embed/' . $videoId . '?autoplay=1&mute=1&loop=1&playlist=' . $videoId . '&controls=1&showinfo=0&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                            echo '</div>';
                            echo '<div class="featured-video-info mt-3">';
                            echo '<h4>' . htmlspecialchars($mainVideo['title']) . '</h4>';
                            echo '<p class="text-muted">' . htmlspecialchars($mainVideo['category_name']) . '</p>';
                            echo '</div>';
                        } else {
                            // ถ้าไม่สามารถดึง video ID ได้ แสดง placeholder
                            echo '<div class="featured-video-placeholder">';
                            echo '<p class="py-5">วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>';
                            echo '</div>';
                        }
                    } else {
                        // ถ้าไม่มีวิดีโอเลย แสดง placeholder
                        echo '<div class="featured-video-placeholder">';
                        echo '<p class="py-5">วิดีโอแนะนำโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="latest-videos-slider">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">วิดีโอล่าสุด</h4>
                        <div class="d-flex align-items-center gap-3">
                            <a href="video_system/all_videos.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-video me-1"></i> ดูวิดีโอทั้งหมด
                            </a>
                            <div class="slider-controls d-flex gap-2">
                                <button id="prevVideo" class="btn btn-sm rounded-circle slider-control-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="nextVideo" class="btn btn-sm rounded-circle slider-control-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="video-slider-container">
                        <div class="video-slider" id="videoSlider">
                            <?php
                            if (!empty($latestVideos)) {
                                $videoIndex = 1;
                                foreach ($latestVideos as $video) {
                                    $thumbnail = getYouTubeThumbnail($video['youtube_url']);
                                    $isNew = (strtotime($video['upload_date']) > strtotime('-7 days'));
                                    
                                    // กำหนด category_id จากข้อมูลวิดีโอ (ถ้ามี)
                                    $categoryId = isset($video['category_id']) ? $video['category_id'] : 0;
                                    
                                    echo '<div class="video-item" data-video-number="' . $videoIndex . '">';
                                    echo '<div class="video-card">';
                                    echo '<a href="video_system/all_videos.php?category=' . $categoryId . '#video-' . $video['id'] . '" class="video-thumbnail-link" data-video-id="' . $video['id'] . '" data-video-title="' . htmlspecialchars($video['title']) . '" data-video-category="' . htmlspecialchars($video['category_name']) . '" data-youtube-url="' . htmlspecialchars($video['youtube_url']) . '">';
                                    echo '<div class="video-thumbnail">';
                                    if ($isNew) {
                                        echo '<div class="video-new-badge"><span class="badge bg-danger">ใหม่</span></div>';
                                    }
                                    echo '<img src="' . $thumbnail . '" alt="' . htmlspecialchars($video['title']) . '" loading="lazy">';
                                    echo '<div class="video-play-button"><i class="fas fa-play"></i></div>';
                                    echo '<div class="video-info-overlay">';
                                    echo '<h5>' . htmlspecialchars($video['title']) . '</h5>';
                                    echo '<p class="category">' . htmlspecialchars($video['category_name']) . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                    echo '</div>';
                                    $videoIndex++;
                                }
                            } else {
                                echo '<div class="col-12 text-center py-4">';
                                echo '<p class="text-muted">ยังไม่มีวิดีโอในระบบ</p>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS สำหรับ Video Quick Links -->
<link rel="stylesheet" href="css/video_links_custom.css">

<!-- JavaScript สำหรับ Video Quick Links -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ตัวแปรสำหรับ slider
    const slider = document.getElementById('videoSlider');
    const prevBtn = document.getElementById('prevVideo');
    const nextBtn = document.getElementById('nextVideo');
    const videoItems = document.querySelectorAll('.video-item');
    
    // ตัวแปรสำหรับ tooltip
    const tooltip = document.createElement('div');
    tooltip.className = 'video-tooltip';
    document.body.appendChild(tooltip);
    
    // ตัวแปรสำหรับการเลื่อน slider
    let currentIndex = 0;
    let itemWidth = 0;
    let visibleItems = 0;
    
    // คำนวณจำนวนรายการที่แสดงได้ในหน้าจอตามขนาดหน้าจอ
    function calculateVisibleItems() {
        const containerWidth = slider.parentElement.clientWidth;
        const screenWidth = window.innerWidth;
        let targetItems = 5; // default สำหรับหน้าจอใหญ่
        
        // กำหนดจำนวนรายการตามขนาดหน้าจอ
        if (screenWidth <= 576) {
            targetItems = 1; // มือถือเล็ก
        } else if (screenWidth <= 767) {
            targetItems = 2; // มือถือ
        } else if (screenWidth <= 991) {
            targetItems = 3; // แท็บเล็ต
        } else if (screenWidth <= 1200) {
            targetItems = 4; // หน้าจอขนาดกลาง
        } else {
            targetItems = 5; // หน้าจอใหญ่
        }
        
        const gap = screenWidth <= 767 ? 10 : 15;
        itemWidth = Math.floor((containerWidth - (gap * (targetItems - 1))) / targetItems) + gap;
        
        // ปรับขนาดของแต่ละ video item
        videoItems.forEach(item => {
            item.style.minWidth = (itemWidth - gap) + 'px';
            item.style.maxWidth = (itemWidth - gap) + 'px';
        });
        
        visibleItems = targetItems;
        return visibleItems;
    }
    
    // อัพเดทการแสดงปุ่มเลื่อน
    function updateButtons() {
        // ถ้าวิดีโอน้อยกว่าหรือเท่ากับจำนวนที่แสดงได้ ให้ซ่อนปุ่มทั้งหมด
        if (videoItems.length <= visibleItems) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = currentIndex > 0 ? 'flex' : 'none';
            nextBtn.style.display = currentIndex < videoItems.length - visibleItems ? 'flex' : 'none';
        }
    }
    
    // เลื่อน slider
    function moveSlider() {
        slider.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        updateButtons();
    }
    
    // ปุ่มเลื่อนไปทางซ้าย
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            moveSlider();
        }
    });
    
    // ปุ่มเลื่อนไปทางขวา
    nextBtn.addEventListener('click', function() {
        if (currentIndex < videoItems.length - visibleItems) {
            currentIndex++;
            moveSlider();
        }
    });
    
    // คำนวณจำนวนรายการที่แสดงได้เมื่อโหลดหน้าและเมื่อ resize
    window.addEventListener('resize', function() {
        calculateVisibleItems();
        
        // ปรับ currentIndex ถ้าเกินขอบเขต
        if (currentIndex > videoItems.length - visibleItems) {
            currentIndex = Math.max(0, videoItems.length - visibleItems);
        }
        
        moveSlider();
    });
    
    // เริ่มต้น
    calculateVisibleItems();
    updateButtons();
    
    // Auto-slide วิดีโอ
    let autoSlideInterval;
    let isPaused = false;
    
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            if (!isPaused && videoItems.length > visibleItems) {
                // เลื่อนไปข้างหน้า
                if (currentIndex >= videoItems.length - visibleItems) {
                    // ถ้าถึงท้ายแล้ว ให้วนกลับไปเริ่มต้น
                    currentIndex = 0;
                } else {
                    currentIndex++;
                }
                moveSlider();
            }
        }, 3000); // เลื่อนทุก 3 วินาที
    }
    
    // หยุด auto-slide เมื่อ hover
    slider.addEventListener('mouseenter', () => {
        isPaused = true;
    });
    
    // เริ่ม auto-slide อีกครั้งเมื่อเอาเมาส์ออก
    slider.addEventListener('mouseleave', () => {
        isPaused = false;
    });
    
    // เริ่ม auto-slide
    if (videoItems.length > visibleItems) {
        startAutoSlide();
    }
    
    // เพิ่ม tooltip เมื่อ hover บนวิดีโอ
    videoItems.forEach((item, index) => {
        const link = item.querySelector('.video-thumbnail-link');
        const title = link.dataset.videoTitle || item.querySelector('.video-info-overlay h5').textContent;
        const category = link.dataset.videoCategory || item.querySelector('.video-info-overlay .category').textContent;
        const videoNumber = item.dataset.videoNumber || (index + 1);
        
        link.addEventListener('mouseenter', function(e) {
            // แสดง tooltip
            tooltip.innerHTML = `
                <div class="text-center">
                    <small class="text-warning">วิดีโอลำดับที่ ${videoNumber}</small><br>
                    <strong>${title}</strong><br>
                    <span class="text-info">${category}</span>
                </div>
            `;
            tooltip.style.opacity = '1';
            
            // คำนวณตำแหน่ง tooltip
            const rect = this.getBoundingClientRect();
            tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10 + window.scrollY}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
        });
        
        link.addEventListener('mouseleave', function() {
            // ซ่อน tooltip
            tooltip.style.opacity = '0';
        });
    });
    
    // สร้าง popup เมื่อคลิกที่วิดีโอ
    videoItems.forEach(item => {
        const link = item.querySelector('.video-thumbnail-link');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const videoUrl = this.getAttribute('href');
            
            // ดึง video ID จาก YouTube URL
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
                window.open(videoUrl, '_blank');
                return;
            }
            
            // สร้าง popup
            const popup = document.createElement('div');
            popup.className = 'video-popup';
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
            
            document.body.appendChild(popup);
            
            // แสดง popup
            setTimeout(() => {
                popup.classList.add('active');
            }, 10);
            
            // ปิด popup เมื่อคลิกที่ปุ่มปิด
            const closeBtn = popup.querySelector('.video-popup-close');
            closeBtn.addEventListener('click', function() {
                popup.classList.remove('active');
                setTimeout(() => {
                    popup.remove();
                }, 300);
            });
            
            // ปิด popup เมื่อคลิกที่พื้นหลัง
            popup.addEventListener('click', function(e) {
                if (e.target === popup) {
                    popup.classList.remove('active');
                    setTimeout(() => {
                        popup.remove();
                    }, 300);
                }
            });
        });
    });
});
</script>
