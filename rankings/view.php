<?php
/**
 * University Rankings View Page
 * This file displays a single university ranking item in detail
 */

// Include database connection if not already included
if (!function_exists('mysqli_connect') || !isset($conn)) {
    // Create a simple database connection for frontend
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'satitup';
    
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];

// Get ranking item data
$stmt = $conn->prepare("SELECT * FROM university_rankings WHERE id = ? AND active = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// If no ranking item found, redirect to home
if ($result->num_rows === 0) {
    header("Location: ../index.php");
    exit;
}

// Fetch ranking data
$ranking = $result->fetch_assoc();

// Parse additional links if available
$additional_links = [];
if (!empty($ranking['additional_links'])) {
    try {
        $additional_links = json_decode($ranking['additional_links'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $additional_links = [];
        }
    } catch (Exception $e) {
        $additional_links = [];
    }
}

// View count functionality removed as the column doesn't exist
// If you want to track views, you need to add a 'views' column to the university_rankings table first
// ALTER TABLE university_rankings ADD COLUMN views INT DEFAULT 0;

// Get page title
$page_title = $ranking['title'] . " - การจัดอันดับมหาวิทยาลัย";

// Include header
include_once '../header.php';
?>



<!-- ส่วนแสดงรายละเอียดการจัดอันดับ -->
<section class="ranking-detail-section py-5">
    <div class="container">
        <!-- ส่วนหัวข้อ -->
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="ranking-detail-title"><?php echo htmlspecialchars($ranking['title']); ?></h1>
                
                <?php if (!empty($ranking['ranking_organization']) || !empty($ranking['ranking_year'])): ?>
                <div class="ranking-detail-meta">
                    <?php if (!empty($ranking['ranking_organization'])): ?>
                        <span class="ranking-organization-badge">
                            <?php echo htmlspecialchars($ranking['ranking_organization']); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['ranking_year'])): ?>
                        <span class="ranking-year-badge">
                            <?php echo htmlspecialchars($ranking['ranking_year']); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['publication_date'])): ?>
                        <span class="ranking-date">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo date('d/m/Y', strtotime($ranking['publication_date'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ส่วนเนื้อหาหลัก -->
        <div class="row">
            <!-- รูปภาพด้านซ้าย -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="ranking-detail-image">
                    <?php
                    $image_path = $ranking['image_path'];
                    $full_path = '../' . $image_path;
                    $image_exists = file_exists($full_path);
                    
                    if ($image_exists): ?>
                        <img src="<?php echo htmlspecialchars('../' . $image_path); ?>" alt="<?php echo htmlspecialchars($ranking['title']); ?>" class="img-fluid rounded shadow">
                    <?php else: ?>
                        <div class="no-image-placeholder">
                            <i class="fas fa-university"></i>
                            <p>ไม่พบรูปภาพ</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['logo_path'])): ?>
                    <div class="ranking-logo">
                        <img src="<?php echo htmlspecialchars('../' . $ranking['logo_path']); ?>" alt="<?php echo htmlspecialchars($ranking['ranking_organization']); ?> Logo" class="img-fluid">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- รายละเอียดด้านขวา -->
            <div class="col-lg-7">
                <div class="ranking-detail-content">
                    <?php if (!empty($ranking['ranking_position'])): ?>
                    <div class="ranking-position-highlight">
                        <div class="position-label">อันดับที่ได้รับ</div>
                        <div class="position-value"><?php echo htmlspecialchars($ranking['ranking_position']); ?></div>
                        <?php if (!empty($ranking['ranking_category'])): ?>
                            <div class="position-category"><?php echo htmlspecialchars($ranking['ranking_category']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['ranking_score'])): ?>
                    <div class="ranking-score">
                        <div class="score-label">คะแนน</div>
                        <div class="score-value"><?php echo htmlspecialchars($ranking['ranking_score']); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['description'])): ?>
                    <div class="ranking-description">
                        <h3>รายละเอียด</h3>
                        <p><?php echo nl2br(htmlspecialchars($ranking['description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['achievement_highlights'])): ?>
                    <div class="ranking-highlights">
                        <h3><i class="fas fa-award me-2"></i>จุดเด่น</h3>
                        <p><?php echo nl2br(htmlspecialchars($ranking['achievement_highlights'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking['ranking_criteria'])): ?>
                    <div class="ranking-criteria">
                        <h3>เกณฑ์การจัดอันดับ</h3>
                        <p><?php echo nl2br(htmlspecialchars($ranking['ranking_criteria'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- ลิงก์ -->
                    <div class="ranking-links">
                        <?php if (!empty($ranking['link'])): ?>
                        <a href="<?php echo htmlspecialchars($ranking['link']); ?>" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i>ดูข้อมูลเพิ่มเติม
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($additional_links) && is_array($additional_links)): ?>
                            <?php foreach ($additional_links as $label => $url): ?>
                                <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="btn btn-outline-secondary ms-2">
                                    <?php echo htmlspecialchars($label); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ปุ่มย้อนกลับ -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="../index.php#rankings-section" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการการจัดอันดับทั้งหมด
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CSS สำหรับหน้าแสดงรายละเอียด -->
<style>
/* ส่วนพื้นหลังของเซคชั่น */
.ranking-detail-section {
    background: linear-gradient(135deg, #f8f7fb 0%, #ffffff 100%);
    padding: 80px 0;
    position: relative;
}

/* หัวข้อหลัก */
.ranking-detail-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 1rem;
    position: relative;
    display: inline-block;
}

/* เส้นใต้หัวข้อ */
.ranking-detail-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
    border-radius: 2px;
}

/* ส่วนแสดงข้อมูลเมตา (องค์กรและปี) */
.ranking-detail-meta {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin: 25px 0 15px;
}

/* แบดจ์แสดงชื่อองค์กร */
.ranking-organization-badge {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 3px 10px rgba(139, 122, 168, 0.2);
}

/* แบดจ์แสดงปี */
.ranking-year-badge {
    background: var(--accent-color);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 3px 10px rgba(255, 105, 180, 0.2);
}

/* วันที่ประกาศ */
.ranking-date {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

/* ส่วนรูปภาพ */
.ranking-detail-image {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    background: white;
    padding: 10px;
}

/* รูปภาพ */
.ranking-detail-image img {
    width: 100%;
    height: auto;
    transition: all 0.5s ease;
}

/* เอฟเฟกต์รูปภาพเมื่อ hover */
.ranking-detail-image:hover img {
    transform: scale(1.03);
}

/* ส่วนแสดงโลโก้ */
.ranking-logo {
    position: absolute;
    bottom: 20px;
    right: 20px;
    max-width: 80px;
    background: white;
    padding: 5px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* กรณีไม่มีรูปภาพ */
.no-image-placeholder {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    border-radius: 10px;
}

.no-image-placeholder i {
    font-size: 5rem;
    margin-bottom: 15px;
}

.no-image-placeholder p {
    font-size: 1.2rem;
    margin: 0;
}

/* ส่วนเนื้อหา */
.ranking-detail-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 100%;
}

/* ส่วนแสดงอันดับ */
.ranking-position-highlight {
    background: linear-gradient(135deg, #f5f9ff, #e6f0ff);
    border-left: 5px solid var(--primary-color);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    text-align: center;
}

.position-label {
    font-size: 1.1rem;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.position-value {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-dark);
    line-height: 1.2;
}

.position-category {
    font-size: 1.1rem;
    color: var(--text-secondary);
    margin-top: 5px;
}

/* ส่วนแสดงคะแนน */
.ranking-score {
    background: linear-gradient(135deg, #fff9f5, #ffefe6);
    border-left: 5px solid var(--accent-color);
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
}

.score-label {
    font-size: 1.1rem;
    color: var(--accent-color);
    margin-right: 15px;
}

.score-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--accent-color);
}

/* ส่วนคำอธิบาย */
.ranking-description, .ranking-criteria, .ranking-highlights {
    margin-bottom: 25px;
}

.ranking-description h3, .ranking-criteria h3, .ranking-highlights h3 {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.ranking-description p, .ranking-criteria p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-secondary);
}

/* ส่วนจุดเด่น */
.ranking-highlights {
    background-color: rgba(255, 248, 220, 0.7);
    padding: 20px;
    border-radius: 10px;
    border-left: 5px solid #FFD700;
}

.ranking-highlights h3 {
    color: #b8860b;
    border-bottom-color: rgba(255, 215, 0, 0.3);
}

.ranking-highlights i {
    color: #FFD700;
}

.ranking-highlights p {
    color: #5a5a5a;
}

/* ส่วนลิงก์ */
.ranking-links {
    margin-top: 30px;
}

/* การปรับขนาดตามหน้าจอ */
@media (max-width: 992px) {
    .ranking-detail-title {
        font-size: 2rem;
    }
    
    .position-value {
        font-size: 2.5rem;
    }
    
    .ranking-detail-content {
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .ranking-detail-section {
        padding: 50px 0;
    }
    
    .ranking-detail-title {
        font-size: 1.8rem;
    }
    
    .ranking-organization-badge, .ranking-year-badge {
        font-size: 1rem;
        padding: 5px 12px;
    }
}

@media (max-width: 576px) {
    .ranking-detail-title {
        font-size: 1.5rem;
    }
    
    .ranking-detail-content {
        padding: 20px;
    }
    
    .position-value {
        font-size: 2rem;
    }
    
    .score-value {
        font-size: 1.5rem;
    }
}
</style>

<?php
// Include footer
include_once '../footer.php'; ?>

<!-- Initialize Bootstrap Dropdowns for navbar (including multi-level) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activate standard bootstrap dropdowns
    var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });

    // Handle submenu open/close for hover/click
    var dropdownSubmenus = document.querySelectorAll('.dropdown-submenu');
    dropdownSubmenus.forEach(function(submenu) {
        submenu.addEventListener('mouseenter', function() {
            var submenuDropdown = submenu.querySelector('.dropdown-menu');
            if (submenuDropdown) {
                submenuDropdown.classList.add('show');
            }
        });
        submenu.addEventListener('mouseleave', function() {
            var submenuDropdown = submenu.querySelector('.dropdown-menu');
            if (submenuDropdown) {
                submenuDropdown.classList.remove('show');
            }
        });
    });
});
</script>
