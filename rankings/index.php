<?php
/**
 * University Rankings Component
 * This file displays university rankings from the database
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
        // If database connection fails, include the static rankings
        include_once 'static_rankings.php';
        return;
    }
}

// Get active ranking items from database
$stmt = $conn->prepare("SELECT * FROM university_rankings WHERE active = 1 ORDER BY display_order ASC, id ASC");
$stmt->execute();
$result = $stmt->get_result();

// If no ranking items found, include the static rankings
if ($result->num_rows === 0) {
    include_once 'static_rankings.php';
    return;
}

// Fetch all rankings
$rankings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- ส่วนแสดงผลการจัดอันดับและความสำเร็จของมหาวิทยาลัย -->
<section class="rankings-section py-5">
    <!-- container-fluid ใช้เพื่อให้ขยายเต็มความกว้างของหน้าจอ -->
    <div class="container-fluid">
        <!-- ส่วนหัวข้อของเซคชั่น -->
        <div class="section-header text-center mb-5">
            <h2 class="section-title">ความสำเร็จและการจัดอันดับ</h2>
            <p class="section-subtitle">โรงเรียนสาธิตมหาวิทยาลัยพะเยาได้รับการยอมรับในระดับสากล</p>
        </div>
        
        <!-- สไลด์แสดงผลการจัดอันดับ - carousel-fade ทำให้เปลี่ยนสไลด์แบบจางๆ - data-bs-interval="4000" ตั้งเวลาเปลี่ยนสไลด์ทุก 4 วินาที -->
        <div id="rankingsCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php foreach ($rankings as $index => $ranking): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="row justify-content-center">
                        <!-- col-lg-10 ใช้ 10/12 ส่วนบนหน้าจอใหญ่, col-md-11 ใช้ 11/12 ส่วนบนหน้าจอกลาง, col-sm-12 ใช้เต็มหน้าจอบนมือถือ -->
                        <div class="col-lg-10 col-md-11 col-sm-12">
                            <!-- การ์ดแสดงข้อมูลการจัดอันดับ - h-100 ทำให้สูงเต็มพื้นที่ -->
                            <div class="ranking-card-single h-100">
                                <!-- ลิงก์ไปยังข่าวต้นฉบับ -->
                                <a href="<?php echo htmlspecialchars($ranking['link']); ?>" target="_blank" class="ranking-link">
                                    <!-- ส่วนแสดงรูปภาพ -->
                                    <div class="ranking-image">
                                        <img src="<?php echo htmlspecialchars($ranking['image_path']); ?>" alt="<?php echo htmlspecialchars($ranking['title']); ?>" class="img-fluid">
                                        <!-- โอเวอร์เลย์ที่แสดงไอคอนเมื่อ hover -->
                                        <div class="ranking-overlay">
                                            <i class="fas fa-award"></i>
                                        </div>
                                    </div>
                                    <!-- ส่วนแสดงเนื้อหา -->
                                    <div class="ranking-content">
                                        <h5 class="ranking-title"><?php echo htmlspecialchars($ranking['title']); ?></h5>
                                        <p class="ranking-description"><?php echo htmlspecialchars($ranking['description']); ?></p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- ปุ่มควบคุมสไลด์ (Controls) -->
            <button class="carousel-control-prev rankings-control-prev" type="button" data-bs-target="#rankingsCarousel" data-bs-slide="prev">
                <span class="control-icon"><i class="fas fa-chevron-left"></i></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next rankings-control-next" type="button" data-bs-target="#rankingsCarousel" data-bs-slide="next">
                <span class="control-icon"><i class="fas fa-chevron-right"></i></span>
                <span class="visually-hidden">Next</span>
            </button>
            
            <!-- ตัวบ่งชี้ด้านล่าง (Indicators) -->
            <div class="carousel-indicators rankings-indicators">
                <?php foreach ($rankings as $index => $ranking): ?>
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once 'styles.php'; ?>
<?php include_once 'scripts.php'; ?>
