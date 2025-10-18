<?php
/**
 * หน้าแสดงรายละเอียดหน่วยงานพันธมิตร
 * พร้อมแกลเลอรี่รูปภาพ
 */

session_start();
require_once '../db_connect.php';

// ตรวจสอบ ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$partner_id = intval($_GET['id']);

// ดึงข้อมูล partner
$partner_query = "SELECT * FROM partners WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($partner_query);
$stmt->bind_param('i', $partner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../index.php');
    exit;
}

$partner = $result->fetch_assoc();

// ดึงรูปภาพแกลเลอรี่
$gallery_query = "SELECT * FROM partner_images WHERE partner_id = ? ORDER BY order_number ASC, created_at ASC";
$stmt_gallery = $conn->prepare($gallery_query);
$stmt_gallery->bind_param('i', $partner_id);
$stmt_gallery->execute();
$gallery_result = $stmt_gallery->get_result();

// นำเข้าส่วนหัว
include_once '../header.php';
include_once '../navbar.php';
?>

<!-- Partner Detail Section -->
<section class="partner-detail-section py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">หน้าแรก</a></li>
                <li class="breadcrumb-item active" aria-current="page">เครือข่ายความร่วมมือ</li>
            </ol>
        </nav>

        <!-- Partner Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="partner-header">
                    <h1 class="partner-title"><?php echo htmlspecialchars($partner['name']); ?></h1>
                    <?php if (!empty($partner['project_name'])): ?>
                    <p class="project-name text-primary">
                        <i class="fas fa-project-diagram me-2"></i>
                        <?php echo htmlspecialchars($partner['project_name']); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gallery Section -->
            <div class="col-lg-7 mb-4">
                <div class="gallery-container">
                    <!-- Main Image -->
                    <div class="main-image-container mb-3">
                        <?php if (!empty($partner['featured_image']) && file_exists('../' . $partner['featured_image'])): ?>
                            <img id="mainImage" 
                                 src="../<?php echo htmlspecialchars($partner['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                 class="img-fluid main-image">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image fa-5x text-muted"></i>
                                <p class="text-muted mt-3">ไม่มีรูปภาพ</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <?php if ($gallery_result->num_rows > 0): ?>
                    <div class="thumbnail-gallery">
                        <div class="row g-2">
                            <?php 
                            // เพิ่มรูปหลักเป็นรูปแรกในแกลเลอรี่
                            if (!empty($partner['featured_image']) && file_exists('../' . $partner['featured_image'])): ?>
                            <div class="col-3 col-md-2">
                                <img src="../<?php echo htmlspecialchars($partner['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                     class="img-fluid thumbnail-img active" 
                                     onclick="changeMainImage(this.src)">
                            </div>
                            <?php endif; ?>

                            <?php while ($gallery_img = $gallery_result->fetch_assoc()): ?>
                                <?php if (file_exists('../' . $gallery_img['image_path'])): ?>
                                <div class="col-3 col-md-2">
                                    <img src="../<?php echo htmlspecialchars($gallery_img['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($gallery_img['caption'] ?? $partner['name']); ?>" 
                                         class="img-fluid thumbnail-img" 
                                         onclick="changeMainImage(this.src)"
                                         title="<?php echo htmlspecialchars($gallery_img['caption'] ?? ''); ?>">
                                </div>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Section -->
            <div class="col-lg-5">
                <div class="content-container">
                    <!-- Description -->
                    <?php if (!empty($partner['description'])): ?>
                    <div class="description-section mb-4">
                        <h4 class="section-heading">
                            <i class="fas fa-info-circle me-2"></i>รายละเอียดความร่วมมือ
                        </h4>
                        <p class="text-justify"><?php echo nl2br(htmlspecialchars($partner['description'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Full Content -->
                    <?php if (!empty($partner['content'])): ?>
                    <div class="content-section mb-4">
                        <h4 class="section-heading">
                            <i class="fas fa-file-alt me-2"></i>ข้อมูลเพิ่มเติม
                        </h4>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($partner['content'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Contact Info -->
                    <div class="contact-section">
                        <?php if (!empty($partner['website_url'])): ?>
                        <div class="contact-item mb-3">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <strong>เว็บไซต์:</strong>
                            <a href="<?php echo htmlspecialchars($partner['website_url']); ?>" 
                               target="_blank" 
                               class="ms-2">
                                <?php echo htmlspecialchars($partner['website_url']); ?>
                                <i class="fas fa-external-link-alt ms-1 fa-sm"></i>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($partner['contact_info'])): ?>
                        <div class="contact-item">
                            <i class="fas fa-address-book text-primary me-2"></i>
                            <strong>ติดต่อ:</strong>
                            <div class="ms-4 mt-2">
                                <?php echo nl2br(htmlspecialchars($partner['contact_info'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-4">
                        <a href="../index.php#partners" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.partner-detail-section {
    min-height: 70vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.breadcrumb {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.partner-header {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.partner-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.project-name {
    font-size: 1.3rem;
    font-weight: 500;
    margin: 0;
}

.gallery-container {
    background: white;
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.main-image-container {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: #f8f9fa;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.main-image {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: contain;
    border-radius: 10px;
}

.no-image-placeholder {
    text-align: center;
    padding: 3rem;
}

.thumbnail-gallery {
    max-height: 150px;
    overflow-x: auto;
    overflow-y: hidden;
}

.thumbnail-img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail-img:hover {
    border-color: #4e73df;
    transform: scale(1.05);
}

.thumbnail-img.active {
    border-color: #4e73df;
}

.content-container {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.section-heading {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #4e73df;
}

.text-justify {
    text-align: justify;
    line-height: 1.8;
}

.content-text {
    line-height: 1.8;
    color: #4a5568;
}

.contact-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.contact-item a {
    color: #4e73df;
    text-decoration: none;
}

.contact-item a:hover {
    text-decoration: underline;
}
</style>

<!-- JavaScript for Image Gallery -->
<script>
function changeMainImage(src) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail-img').forEach(img => {
            img.classList.remove('active');
        });
        
        // Add active class to clicked thumbnail
        event.target.classList.add('active');
    }
}
</script>

<?php include_once '../footer.php'; ?>

