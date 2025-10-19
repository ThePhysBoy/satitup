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

// Group rankings into sets of 2
$grouped_rankings = array_chunk($rankings, 2);
?>

<!-- ส่วนแสดงผลการจัดอันดับและความสำเร็จของมหาวิทยาลัย -->
<section id="rankings-section" class="rankings-section">
    <div class="container-fluid p-0">
        <!-- สไลด์แสดงผลการจัดอันดับ - carousel-fade ทำให้เปลี่ยนสไลด์แบบจางๆ - data-bs-interval="4000" ตั้งเวลาเปลี่ยนสไลด์ทุก 4 วินาที -->
        <div id="rankingsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php foreach ($grouped_rankings as $index => $group): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="row g-0">
                        <?php foreach ($group as $ranking): ?>
                        <div class="col-12 col-md-6">
                            <div class="ranking-block">
                                <a href="rankings/view.php?id=<?php echo $ranking['id']; ?>" class="ranking-link" target="_blank" rel="noopener noreferrer">
                                    <div class="ranking-image-container">
                                        <img src="<?php echo htmlspecialchars($ranking['image_path']); ?>" alt="<?php echo htmlspecialchars($ranking['title']); ?>" class="img-fluid">
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php 
                        // Fill empty slots if group has less than 2 items
                        $empty_slots = 2 - count($group);
                        for ($i = 0; $i < $empty_slots; $i++): ?>
                        <div class="col-12 col-md-6">
                            <div class="ranking-block empty"></div>
                        </div>
                        <?php endfor; ?>
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
                <?php foreach ($grouped_rankings as $index => $group): ?>
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* ส่วนพื้นหลังของเซคชั่นการจัดอันดับ */
.rankings-section {
    width: 100%;
    padding: 0 !important;
    margin: 0;
    overflow: hidden;
    background-color: transparent;
}

/* คอนเทนเนอร์สำหรับสไลด์ */
#rankingsCarousel {
    width: 100%;
    margin: 0;
    padding: 0;
}

.carousel-inner {
    width: 100%;
}

.carousel-item {
    width: 100%;
}

/* แถวสำหรับรูปภาพ */
.carousel-item .row {
    margin: 0;
    padding: 0;
    width: 100%;
}

/* คอลัมน์สำหรับรูปภาพ */
.carousel-item .col-6 {
    padding: 0;
    margin: 0;
}

/* บล็อกการจัดอันดับ */
.ranking-block {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    margin: 0;
    background-color: #000;
}

/* ลิงก์ */
.ranking-link {
    display: block;
    width: 100%;
    height: 100%;
}

/* คอนเทนเนอร์สำหรับรูปภาพ */
.ranking-image-container {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #000;
}

/* รูปภาพ */
.ranking-image-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    transition: transform 0.5s ease;
    margin: 0;
    padding: 0;
    display: block;
}

/* ปุ่มควบคุมสไลด์ซ้าย-ขวา */
.rankings-control-prev,
.rankings-control-next {
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    opacity: 0.7;
    transition: all 0.3s ease;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}

/* เอฟเฟกต์เมื่อ hover ปุ่มควบคุม */
.rankings-control-prev:hover,
.rankings-control-next:hover {
    background: rgba(0, 0, 0, 0.5);
    opacity: 1;
}

/* ไอคอนในปุ่มควบคุม */
.control-icon {
    color: white;
    font-size: 16px;
}

/* จุดบอกตำแหน่งสไลด์ด้านล่าง */
.rankings-indicators {
    bottom: 0;
    margin: 0;
}

/* ปุ่มจุดบอกตำแหน่งสไลด์ */
.rankings-indicators button {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    border: none;
    margin: 0 3px;
    transition: all 0.3s ease;
    padding: 0;
}

/* ปุ่มจุดบอกตำแหน่งสไลด์ที่กำลังแสดงอยู่ */
.rankings-indicators button.active {
    width: 16px;
    border-radius: 8px;
    background: white;
}

/* การปรับขนาดตามหน้าจอ */
@media (min-width: 992px) {
    .rankings-control-prev,
    .rankings-control-next {
        width: 50px;
        height: 50px;
    }
    
    .control-icon {
        font-size: 20px;
    }
}

@media (max-width: 768px) {
    .rankings-control-prev,
    .rankings-control-next {
        width: 30px;
        height: 30px;
    }
    
    .control-icon {
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .rankings-control-prev,
    .rankings-control-next {
        width: 25px;
        height: 25px;
    }
    
    .control-icon {
        font-size: 12px;
    }
    
    .rankings-indicators button {
        width: 6px;
        height: 6px;
        margin: 0 2px;
    }
    
    .rankings-indicators button.active {
        width: 12px;
    }
}
</style>
