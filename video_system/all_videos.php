<?php
// เรียกใช้ไฟล์ฟังก์ชัน
require_once 'includes/video_functions.php';

// ตรวจสอบหมวดหมู่ที่เลือก
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// ดึงข้อมูลหมวดหมู่ทั้งหมด
$categories = getAllCategories();

// ดึงข้อมูลวิดีโอตามหมวดหมู่
$videos = $selectedCategory > 0 ? getVideosByCategory($selectedCategory, 12) : getLatestVideos(12);

// ชื่อหมวดหมู่ที่เลือก
$categoryName = "วิดีโอทั้งหมด";
if ($selectedCategory > 0) {
    foreach ($categories as $category) {
        if ($category['id'] == $selectedCategory) {
            $categoryName = $category['name'];
            break;
        }
    }
}

// Title ของหน้า
$pageTitle = "วิดีโอกิจกรรม - " . $categoryName;

// Include header จากเว็บหลัก
include_once '../header.php';
// Include navbar จากเว็บหลัก
include_once '../navbar.php';
?>

<!-- Custom CSS สำหรับหน้า video -->
<link href="css/video-styles.css" rel="stylesheet">
    
    <!-- Main Content -->
    <main>
        <!-- Page Header -->
        <section class="page-header bg-light py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="h2 mb-0"><?php echo $categoryName; ?></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="all_videos.php">วิดีโอกิจกรรม</a></li>
                                <?php if ($selectedCategory > 0): ?>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo $categoryName; ?></li>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6">
                        <div class="search-form">
                            <form action="search_videos.php" method="get" class="d-flex justify-content-end">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control" placeholder="ค้นหาวิดีโอ..." aria-label="ค้นหาวิดีโอ">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Categories Nav -->
        <section class="categories-nav py-3 bg-white border-bottom">
            <div class="container">
                <div class="categories-scroll">
                    <ul class="nav nav-pills categories-list">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $selectedCategory == 0 ? 'active' : ''; ?>" href="all_videos.php">ทั้งหมด</a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>" href="all_videos.php?category=<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>
        
        <!-- Videos Grid -->
        <section class="videos-grid py-5">
            <div class="container">
                <?php if ($selectedCategory > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <?php if ($category['id'] == $selectedCategory && !empty($category['description'])): ?>
                            <div class="category-description mb-4">
                                <p><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="row g-4">
                    <?php if (count($videos) > 0): ?>
                        <?php foreach ($videos as $video): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="video-card h-100">
                                    <a href="video_detail.php?id=<?php echo $video['id']; ?>" class="video-thumbnail-link">
                                        <div class="video-thumbnail">
                                            <img src="<?php echo getYoutubeThumbnail($video['youtube_url']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="img-fluid rounded">
                                            <div class="video-play-button">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            <div class="video-duration">
                                                <span class="badge bg-dark">YouTube</span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="video-details p-3">
                                        <h3 class="h5 mb-2">
                                            <a href="video_detail.php?id=<?php echo $video['id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($video['title']); ?>
                                            </a>
                                        </h3>
                                        <div class="video-meta mb-2">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($video['category_name']); ?></span>
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-calendar-alt me-1"></i> 
                                                <?php echo formatDate($video['upload_date']); ?>
                                            </small>
                                        </div>
                                        <div class="video-stats d-flex align-items-center">
                                            <span class="me-3">
                                                <i class="fas fa-eye me-1 text-muted"></i> 
                                                <?php echo formatViews($video['views']); ?>
                                            </span>
                                            <div class="share-buttons">
                                                <button class="btn btn-sm btn-outline-secondary share-btn" data-video-id="<?php echo $video['id']; ?>" data-video-title="<?php echo htmlspecialchars($video['title']); ?>">
                                                    <i class="fas fa-share-alt"></i> แชร์
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <div class="no-videos">
                                <i class="fas fa-video-slash fa-4x text-muted mb-3"></i>
                                <h3>ไม่พบวิดีโอในหมวดหมู่นี้</h3>
                                <p class="text-muted">โปรดเลือกหมวดหมู่อื่น หรือกลับไปที่หน้าวิดีโอทั้งหมด</p>
                                <a href="all_videos.php" class="btn btn-primary mt-3">ดูวิดีโอทั้งหมด</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <?php 
    // ตรวจสอบและ include footer
    if (file_exists('../footer.php')) {
        include_once '../footer.php';
    }
    ?>
    
    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">แชร์วิดีโอ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="shareVideoTitle" class="mb-3"></h6>
                    <div class="share-options d-flex justify-content-center gap-3">
                        <a href="#" id="shareFacebook" class="btn btn-outline-primary" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="#" id="shareTwitter" class="btn btn-outline-info" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="#" id="shareLineApp" class="btn btn-outline-success" target="_blank">
                            <i class="fab fa-line"></i> Line
                        </a>
                    </div>
                    <div class="mt-3">
                        <label for="shareLink" class="form-label">หรือคัดลอกลิงก์:</label>
                        <div class="input-group">
                            <input type="text" id="shareLink" class="form-control" readonly>
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
        // Hover effect for video thumbnails
        const videoThumbnails = document.querySelectorAll('.video-thumbnail');
        videoThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });
            thumbnail.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });
        
        // Share buttons functionality
        const shareButtons = document.querySelectorAll('.share-btn');
        shareButtons.forEach(button => {
            button.addEventListener('click', function() {
                const videoId = this.getAttribute('data-video-id');
                const videoTitle = this.getAttribute('data-video-title');
                const videoUrl = `${window.location.origin}/satitup/video_system/video_detail.php?id=${videoId}`;
                
                // Set modal content
                document.getElementById('shareVideoTitle').textContent = videoTitle;
                document.getElementById('shareLink').value = videoUrl;
                
                // Set social share links
                document.getElementById('shareFacebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(videoUrl)}`;
                document.getElementById('shareTwitter').href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(videoTitle)}&url=${encodeURIComponent(videoUrl)}`;
                document.getElementById('shareLineApp').href = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(videoUrl)}`;
                
                // Show modal
                const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
                shareModal.show();
            });
        });
        
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
        
        // Horizontal scroll for categories
        const categoriesScroll = document.querySelector('.categories-scroll');
        if (categoriesScroll) {
            categoriesScroll.addEventListener('wheel', function(e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    this.scrollLeft += e.deltaY;
                }
            });
        }
    });
    </script>
    
    <!-- Include footer จากเว็บหลัก -->
    <?php include_once '../footer.php'; ?>
