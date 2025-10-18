<?php
// เรียกใช้ไฟล์ฟังก์ชัน
require_once 'includes/video_functions.php';

// ตรวจสอบ ID ของวิดีโอ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: all_videos.php');
    exit;
}

$videoId = (int)$_GET['id'];

// ดึงข้อมูลวิดีโอ
$video = getVideoById($videoId);

// ถ้าไม่พบวิดีโอให้ redirect ไปหน้าแสดงวิดีโอทั้งหมด
if (!$video) {
    header('Location: all_videos.php');
    exit;
}

// เพิ่มจำนวนการดูวิดีโอ
incrementVideoViews($videoId);

// ดึงวิดีโอที่เกี่ยวข้องในหมวดหมู่เดียวกัน
$relatedVideos = getVideosByCategory($video['category_id'], 3, 0);

// Title ของหน้า
$pageTitle = $video['title'] . " - วิดีโอกิจกรรม";

// Include header จากเว็บหลัก
include_once '../header.php';
// Include navbar จากเว็บหลัก
include_once '../navbar.php';
?>

<!-- Custom CSS สำหรับหน้า video detail -->
<link href="css/video-styles.css" rel="stylesheet">
<style>
/* เพิ่ม styles เฉพาะสำหรับหน้า video detail */
.video-player-section {
    background-color: #f8f9fa;
    padding-bottom: 60px;
}

/* Fix Bootstrap conflicts */
.video-player-section .container {
    max-width: 1200px;
}

.video-info h1 {
    font-family: 'Sarabun', sans-serif;
}

.video-description {
    font-family: 'Sarabun', sans-serif;
}

/* Hide any PHP warnings */
.warning {
    display: none;
}

/* Fix related video thumbnails */
.related-video-thumbnail {
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    position: relative;
    background-color: #f0f0f0;
}

.related-video-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
    
<!-- Main Content -->
<main>
        <!-- Page Header -->
        <section class="page-header bg-light py-3">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="all_videos.php">วิดีโอกิจกรรม</a></li>
                        <li class="breadcrumb-item"><a href="all_videos.php?category=<?php echo $video['category_id']; ?>"><?php echo htmlspecialchars($video['category_name']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($video['title']); ?></li>
                    </ol>
                </nav>
            </div>
        </section>
        
        <!-- Video Player Section -->
        <section class="video-player-section py-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Video Player -->
                        <div class="video-player-container mb-4">
                            <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                                <?php 
                                $youtube_id = '';
                                if (isset($video['youtube_id']) && !empty($video['youtube_id'])) {
                                    $youtube_id = $video['youtube_id'];
                                } elseif (isset($video['youtube_url']) && !empty($video['youtube_url'])) {
                                    $youtube_id = getYoutubeVideoId($video['youtube_url']);
                                }
                                ?>
                                <iframe 
                                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtube_id); ?>?autoplay=0&rel=0" 
                                    title="<?php echo htmlspecialchars($video['title']); ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        
                        <!-- Video Info -->
                        <div class="video-info mb-4">
                            <h1 class="h3 mb-2"><?php echo htmlspecialchars($video['title']); ?></h1>
                            <div class="video-meta d-flex flex-wrap align-items-center mb-3">
                                <span class="badge bg-primary me-2"><?php echo htmlspecialchars($video['category_name']); ?></span>
                                <span class="text-muted me-3">
                                    <i class="fas fa-calendar-alt me-1"></i> 
                                    <?php 
                                    $date = isset($video['event_date']) ? $video['event_date'] : (isset($video['created_at']) ? $video['created_at'] : date('Y-m-d'));
                                    echo formatDate($date); 
                                    ?>
                                </span>
                                <span class="text-muted me-3">
                                    <i class="fas fa-eye me-1"></i> 
                                    <?php echo formatViews(isset($video['views']) ? $video['views'] : 0); ?> views
                                </span>
                                <div class="share-buttons ms-auto">
                                    <button class="btn btn-sm btn-outline-secondary share-btn" data-video-id="<?php echo $video['id']; ?>" data-video-title="<?php echo htmlspecialchars($video['title']); ?>">
                                        <i class="fas fa-share-alt"></i> แชร์
                                    </button>
                                </div>
                            </div>
                            <div class="video-description">
                                <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Related Videos -->
                        <div class="related-videos">
                            <h3 class="h5 mb-3">วิดีโอที่เกี่ยวข้อง</h3>
                            <?php if (count($relatedVideos) > 0): ?>
                                <?php foreach ($relatedVideos as $relatedVideo): ?>
                                    <?php if ($relatedVideo['id'] != $videoId): ?>
                                        <div class="related-video-item mb-3">
                                            <a href="video_detail.php?id=<?php echo $relatedVideo['id']; ?>" class="related-video-link">
                                                <div class="row g-0">
                                                    <div class="col-4">
                                                        <div class="related-video-thumbnail position-relative">
                                                            <?php 
                                                            $rel_youtube_id = '';
                                                            if (isset($relatedVideo['youtube_id']) && !empty($relatedVideo['youtube_id'])) {
                                                                $rel_youtube_id = $relatedVideo['youtube_id'];
                                                            } elseif (isset($relatedVideo['youtube_url']) && !empty($relatedVideo['youtube_url'])) {
                                                                $rel_youtube_id = getYoutubeVideoId($relatedVideo['youtube_url']);
                                                            }
                                                            $thumbnail_url = getYoutubeThumbnail($rel_youtube_id);
                                                            ?>
                                                            <img src="<?php echo $thumbnail_url; ?>" 
                                                                 alt="<?php echo htmlspecialchars($relatedVideo['title']); ?>" 
                                                                 class="img-fluid rounded"
                                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'320\' height=\'180\' viewBox=\'0 0 320 180\'%3E%3Crect width=\'320\' height=\'180\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-family=\'sans-serif\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                                            <div class="video-play-icon">
                                                                <i class="fas fa-play"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="related-video-info p-2">
                                                            <h4 class="h6 mb-1"><?php echo htmlspecialchars($relatedVideo['title']); ?></h4>
                                                            <div class="small text-muted">
                                                                <i class="fas fa-calendar-alt me-1"></i> 
                                                                <?php 
                                                                $rel_date = isset($relatedVideo['event_date']) ? $relatedVideo['event_date'] : (isset($relatedVideo['created_at']) ? $relatedVideo['created_at'] : date('Y-m-d'));
                                                                echo formatDate($rel_date); 
                                                                ?>
                                                            </div>
                                                            <div class="small text-muted">
                                                                <i class="fas fa-eye me-1"></i> 
                                                                <?php echo formatViews(isset($relatedVideo['views']) ? $relatedVideo['views'] : 0); ?> views
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <div class="text-center mt-3">
                                    <a href="all_videos.php?category=<?php echo $video['category_id']; ?>" class="btn btn-outline-primary btn-sm">
                                        ดูวิดีโอทั้งหมดในหมวด <?php echo htmlspecialchars($video['category_name']); ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">ไม่พบวิดีโอที่เกี่ยวข้อง</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Categories -->
                        <div class="video-categories mt-4">
                            <h3 class="h5 mb-3">หมวดหมู่วิดีโอ</h3>
                            <div class="list-group">
                                <?php 
                                $categories = getAllCategories();
                                foreach ($categories as $category): 
                                ?>
                                    <a href="all_videos.php?category=<?php echo $category['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category['id'] == $video['category_id'] ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <span class="badge bg-primary rounded-pill">
                                            <?php 
                                            // ดึงจำนวนวิดีโอในหมวดหมู่
                                            $categoryVideos = getVideosByCategory($category['id'], 1000);
                                            echo count($categoryVideos);
                                            ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <?php include_once '../footer.php'; ?>
    
    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">แชร์วิดีโอ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="shareVideoTitle" class="mb-3"><?php echo htmlspecialchars($video['title']); ?></h6>
                    <div class="share-options d-flex justify-content-center gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($video['title']); ?>&url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" class="btn btn-outline-info" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" class="btn btn-outline-success" target="_blank">
                            <i class="fab fa-line"></i> Line
                        </a>
                    </div>
                    <div class="mt-3">
                        <label for="shareLink" class="form-label">หรือคัดลอกลิงก์:</label>
                        <div class="input-group">
                            <input type="text" id="shareLink" class="form-control" value="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" readonly>
                            <button class="btn btn-outline-secondary copy-link-btn" type="button">
                                <i class="fas fa-copy"></i> คัดลอก
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hover effect for related video thumbnails
        const relatedThumbnails = document.querySelectorAll('.related-video-thumbnail');
        relatedThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });
            thumbnail.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });
        
        // Share button functionality
        const shareBtn = document.querySelector('.share-btn');
        if (shareBtn) {
            shareBtn.addEventListener('click', function() {
                const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
                shareModal.show();
            });
        }
        
        // Copy link button
        document.querySelector('.copy-link-btn').addEventListener('click', function() {
            const shareLink = document.getElementById('shareLink');
            shareLink.select();
            document.execCommand('copy');
            
            // Change button text temporarily
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> คัดลอกแล้ว';
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    });
    </script>
    
    <!-- Include footer จากเว็บหลัก -->
    <?php include_once '../footer.php'; ?>
