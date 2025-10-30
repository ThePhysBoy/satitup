<?php
/**
 * Partners Section with Google Maps
 * แสดงแผนที่พันธมิตรพร้อม Interactive Markers
 */

// เชื่อมต่อฐานข้อมูล
if (!isset($conn)) {
    require_once 'db_connect.php';
}

// ดึง Google Maps API Key จากฐานข้อมูล
$google_maps_api_key = '';
$api_query = "SELECT api_key FROM api_keys WHERE api_name = 'google_maps' AND is_active = 1 LIMIT 1";
$api_result = $conn->query($api_query);
if ($api_result && $api_result->num_rows > 0) {
    $api_data = $api_result->fetch_assoc();
    $google_maps_api_key = $api_data['api_key'];
}

// ดึงข้อมูล partners ที่มีพิกัดและสถานะ active
$partners_result = false;
$partners_json = [];

if ($conn) {
    $table_check = $conn->query("SHOW TABLES LIKE 'partners'");
    if ($table_check && $table_check->num_rows > 0) {
        // ดึงทั้งที่มีและไม่มีพิกัด
        $partners_query = "SELECT * FROM partners WHERE status = 'active' ORDER BY order_number ASC, created_at DESC";
        $partners_result = $conn->query($partners_query);
        
        // สร้าง JSON data สำหรับ JavaScript (เฉพาะที่มีพิกัด)
        if ($partners_result && $partners_result->num_rows > 0) {
            while ($partner = $partners_result->fetch_assoc()) {
                if ($partner['latitude'] && $partner['longitude']) {
                    $partners_json[] = [
                        'id' => $partner['id'],
                        'name' => $partner['name'],
                        'description' => $partner['description'] ?? '',
                        'project_name' => $partner['project_name'] ?? '',
                        'logo_image' => $partner['logo_image'] ?? '',
                        'address' => $partner['address'] ?? '',
                        'latitude' => floatval($partner['latitude']),
                        'longitude' => floatval($partner['longitude']),
                        'zoom' => intval($partner['map_zoom_level'] ?? 15)
                    ];
                }
            }
            // Reset result pointer
            $partners_result->data_seek(0);
        }
    }
}
?>

<!-- Partners Map Section -->
<section class="partners-section py-5">
    <div class="container-fluid">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">เครือข่ายความร่วมมือ</h2>
            <p class="section-subtitle">แผนที่แสดงหน่วยงานพันธมิตรที่ทำ MOU ร่วมกัน</p>
        </div>
        
        <?php if (!empty($partners_json)): ?>
        <div class="row">
            <!-- แผนที่ -->
            <div class="col-lg-8 mb-4">
                <div id="partnersMap" class="map-container"></div>
            </div>
            
            <!-- รายการพันธมิตร -->
            <div class="col-lg-4 mb-4">
                <div class="partners-list-container">
                    <h4 class="list-title">
                        <i class="fas fa-handshake me-2"></i>รายการพันธมิตร
                    </h4>
                    <div id="partnerHoverCard" class="partner-hover-card d-none">
                        <div class="hover-card-inner">
                            <div class="hover-logo" id="hoverCardLogo"></div>
                            <div class="hover-info">
                                <h5 id="hoverCardName"></h5>
                                <p id="hoverCardProject" class="mb-1"></p>
                                <span id="hoverCardAddress" class="hover-address"></span>
                            </div>
                        </div>
                    </div>
                    <div class="partners-list">
                        <?php if ($partners_result && $partners_result->num_rows > 0): ?>
                            <?php while ($partner = $partners_result->fetch_assoc()): ?>
                            <?php if ($partner['latitude'] && $partner['longitude']): ?>
                            <div class="partner-list-item" 
                                 data-partner-id="<?php echo $partner['id']; ?>"
                                 onmouseover="highlightMarker(<?php echo $partner['id']; ?>)"
                                 onmouseout="removeHighlightMarker(<?php echo $partner['id']; ?>)"
                                 onclick="window.open('partners/view.php?id=<?php echo $partner['id']; ?>', '_blank')">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($partner['logo_image']) && file_exists($partner['logo_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($partner['logo_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                         class="partner-list-logo">
                                    <?php else: ?>
                                    <div class="partner-list-logo-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="partner-list-info">
                                        <h6 class="partner-name mb-1">
                                            <?php echo htmlspecialchars($partner['name']); ?>
                                        </h6>
                                        <?php if (!empty($partner['project_name'])): ?>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($partner['project_name']); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- ถ้าไม่มีข้อมูลพิกัด แสดงแบบ carousel เดิม -->
        <?php
        // Reset result pointer
        if ($partners_result) {
            $partners_result->data_seek(0);
        }
        ?>
        <?php if ($partners_result && $partners_result->num_rows > 0): ?>
        <div class="partners-carousel owl-carousel">
            <?php while ($partner = $partners_result->fetch_assoc()): ?>
            <div class="partner-item">
                <a href="partners/view.php?id=<?php echo $partner['id']; ?>" class="partner-link">
                    <div class="partner-logo">
                        <?php if (!empty($partner['logo_image']) && file_exists($partner['logo_image'])): ?>
                            <img src="<?php echo htmlspecialchars($partner['logo_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                 class="img-fluid partner-img">
                        <?php else: ?>
                            <div class="partner-placeholder">
                                <i class="fas fa-handshake fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="partner-name mt-3">
                            <h5><?php echo htmlspecialchars($partner['name']); ?></h5>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
            <p class="text-muted">ยังไม่มีข้อมูลหน่วยงานพันธมิตร</p>
            <p class="text-info">กรุณาเพิ่มข้อมูลพิกัดในระบบจัดการเพื่อแสดงแผนที่</p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Custom CSS for Map Section -->
<style>
/* Map Styles */
.map-container {
    height: 600px;
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    border: 3px solid white;
    background: #e0e0e0;
    position: relative;
}

.partners-list-container {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    height: 600px;
    display: flex;
    flex-direction: column;
}

.partner-hover-card {
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.95), rgba(255, 140, 0, 0.9));
    border-radius: 18px;
    padding: 1rem;
    box-shadow:
        0 10px 25px rgba(255, 215, 0, 0.6),
        0 0 35px rgba(255, 215, 0, 0.5),
        inset 0 0 20px rgba(255, 255, 255, 0.3);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.6);
}

.partner-hover-card::before {
    content: '';
    position: absolute;
    top: -60%;
    left: -60%;
    width: 220%;
    height: 220%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, transparent 60%);
    animation: hoverCardPulse 4s ease-in-out infinite;
}

.partner-hover-card.d-none {
    display: none !important;
}

.hover-card-inner {
    position: relative;
    display: flex;
    gap: 1rem;
    align-items: center;
    z-index: 2;
}

.hover-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: rgba(255,255,255,0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.hover-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.hover-info h5 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.3rem;
    text-shadow: 0 2px 6px rgba(255,255,255,0.5);
}

.hover-info p {
    font-size: 0.9rem;
    color: #5a2e00;
    margin-bottom: 0.5rem;
}

.hover-address {
    display: inline-block;
    font-size: 0.8rem;
    color: rgba(45,55,72,0.85);
}

@keyframes hoverCardPulse {
    0%, 100% {
        transform: scale(0.95);
        opacity: 0.7;
    }
    50% {
        transform: scale(1.05);
        opacity: 1;
    }
}

@keyframes markerRipple {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.5;
    }
    70% {
        transform: translate(-50%, -50%) scale(3.8);
        opacity: 0;
    }
    100% {
        transform: translate(-50%, -50%) scale(3.8);
        opacity: 0;
    }
}

@keyframes markerFloat {
    0%, 100% {
        transform: translate(-50%, -60%);
    }
    50% {
        transform: translate(-50%, -40%);
    }
}

.list-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #667eea;
}

.partners-list {
    overflow-y: auto;
    flex: 1;
    padding-right: 10px;
}

.partners-list::-webkit-scrollbar {
    width: 8px;
}

.partners-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.partners-list::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.partner-list-item {
    padding: 1rem;
    margin-bottom: 0.8rem;
    background: #f8f9fa;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.partner-list-item:hover:not(.active) {
    background: white;
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    border-color: #667eea;
}

.partner-list-item.active:hover {
    /* รักษาเอฟเฟกต์สีทองเมื่อ active และ hover */
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #ffd700 100%) !important;
    transform: translateX(10px) scale(1.08) !important;
}

.partner-list-item.active {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #ffd700 100%) !important;
    color: #2d3748 !important;
    box-shadow: 
        0 8px 25px rgba(255, 215, 0, 0.6),
        0 0 30px rgba(255, 215, 0, 0.4),
        inset 0 0 20px rgba(255, 255, 255, 0.3) !important;
    border-color: #ffd700 !important;
    border-width: 3px !important;
    transform: translateX(8px) scale(1.05) !important;
    animation: goldenGlow 2s ease-in-out infinite !important;
    position: relative !important;
    overflow: hidden !important;
    z-index: 10 !important;
}

.partner-list-item.active::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 255, 255, 0.5) 50%,
        transparent 70%
    );
    animation: goldenShine 3s linear infinite;
    pointer-events: none;
}

@keyframes goldenGlow {
    0%, 100% {
        box-shadow: 
            0 8px 25px rgba(255, 215, 0, 0.6),
            0 0 30px rgba(255, 215, 0, 0.4),
            inset 0 0 20px rgba(255, 255, 255, 0.3);
    }
    50% {
        box-shadow: 
            0 12px 35px rgba(255, 215, 0, 0.8),
            0 0 45px rgba(255, 215, 0, 0.6),
            inset 0 0 25px rgba(255, 255, 255, 0.4);
    }
}

@keyframes goldenShine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }
}

.partner-list-item.active .partner-name,
.partner-list-item.active .text-muted {
    color: #2d3748 !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
}

.partner-list-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-right: 1rem;
    border-radius: 8px;
    background: white;
    padding: 5px;
}

.partner-list-logo-placeholder {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 1.5rem;
}

.partner-list-info {
    flex: 1;
}

.partner-name {
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

/* Custom Info Window Styles - Compact Design to not block markers */
.map-info-window {
    max-width: 280px;
}

/* Compact Info Window (เมื่อ hover) - โปร่งแสง ไม่บังหมุด ไม่มีกรอบ */
.info-window-compact {
    padding: 6px 10px;
    background: rgba(255, 61, 0, 0.9);
    border-radius: 8px;
    box-shadow: 0 3px 12px rgba(255, 61, 0, 0.5);
    border: none !important;
    max-width: 180px;
    text-align: center;
    backdrop-filter: blur(6px);
    position: relative;
    z-index: 999999;
}

.compact-name {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
}

/* Full Info Window (เมื่อ click) */
.info-window-content {
    padding: 12px;
    max-width: 280px;
}

/* ลบพื้นหลังและกรอบสีขาวของ Google Maps InfoWindow ทั้งหมด */
#partnersMap .gm-style .gm-style-iw-c,
#partnersMap .gm-style .gm-style-iw-c * {
    padding: 0 !important;
    border-radius: 10px !important;
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
}

#partnersMap .gm-style .gm-style-iw-d {
    overflow: visible !important;
    background: transparent !important;
    border: none !important;
}

#partnersMap .gm-style .gm-style-iw-t::after,
#partnersMap .gm-style .gm-style-iw-t::before,
#partnersMap .gm-style .gm-style-iw-t {
    background: transparent !important;
    box-shadow: none !important;
    display: none !important;
    border: none !important;
}

/* ปุ่มปิด InfoWindow */
#partnersMap .gm-style button[title="Close"],
#partnersMap .gm-style button[aria-label="Close"] {
    top: 6px !important;
    right: 6px !important;
    border-radius: 50% !important;
    background: rgba(0,0,0,0.6) !important;
    width: 20px !important;
    height: 20px !important;
    color: #fff !important;
    border: none !important;
}

/* ลบกรอบสีขาวทุกประเภท */
#partnersMap .gm-style div[style*="background"] {
    background: transparent !important;
}

#partnersMap .gm-style-iw {
    background: transparent !important;
    border: none !important;
}


.info-window-logo {
    width: 100%;
    max-width: 120px;
    height: auto;
    margin-bottom: 8px;
    border-radius: 6px;
}

.info-window-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 6px;
    line-height: 1.3;
}

.info-window-project {
    font-size: 0.85rem;
    color: #667eea;
    margin-bottom: 6px;
    line-height: 1.3;
}

.info-window-description {
    font-size: 0.8rem;
    color: #4a5568;
    margin-bottom: 8px;
    line-height: 1.3;
    max-height: 45px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.info-window-address {
    font-size: 0.8rem;
    color: #718096;
    margin-bottom: 10px;
    line-height: 1.3;
}

.info-window-btn {
    display: inline-block;
    padding: 6px 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    width: 100%;
    text-align: center;
    box-sizing: border-box;
}

.info-window-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
    text-decoration: none;
}

/* Custom top-most marker rendered via OverlayView (อยู่หน้าสุดเสมอ) */
.partner-circle-marker {
    position: absolute;
    width: 32px;
    height: 32px;
    background: #FF3D00;
    border: 4px solid #FFFFFF;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(255, 61, 0, 0.25), 0 6px 12px rgba(0,0,0,0.25);
    transform: translate(-50%, -50%);
    cursor: pointer;
    z-index: 2147483647; /* สูงสุด */
    animation: markerFloat 2.2s ease-in-out infinite;
    overflow: visible;
}

.partner-circle-marker:hover {
    box-shadow: 0 0 0 8px rgba(255, 61, 0, 0.2), 0 12px 18px rgba(0,0,0,0.3);
}

.partner-circle-marker::before,
.partner-circle-marker::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 61, 0, 0.25);
    animation: markerRipple 3.8s ease-out infinite;
    pointer-events: none;
}

.partner-circle-marker::after {
    animation-delay: 1.9s;
}

/* Responsive */
@media (max-width: 991px) {
    .map-container {
        height: 500px;
    }
    
    .partners-list-container {
        height: 400px;
        margin-top: 20px;
    }
}

@media (max-width: 767px) {
    .map-container {
        height: 400px;
        border-radius: 15px;
    }
    
    .partners-list-container {
        height: auto;
        max-height: 500px;
    }
}

/* Original carousel styles */
.partners-section {
    background: linear-gradient(135deg,
        #f8f9fa 0%,
        #fffef7 25%,
        #fff9e6 50%,
        #fffef7 75%,
        #f8f9fa 100%
    );
    position: relative;
    overflow: hidden;
    animation: goldenBackgroundShift 10s ease-in-out infinite;
}

@keyframes goldenBackgroundShift {
    0%, 100% {
        background: linear-gradient(135deg,
            #f8f9fa 0%,
            #fffef7 25%,
            #fff9e6 50%,
            #fffef7 75%,
            #f8f9fa 100%
        );
    }
    50% {
        background: linear-gradient(135deg,
            #fffef7 0%,
            #fff9e6 25%,
            #fffef7 50%,
            #fff9e6 75%,
            #fffef7 100%
        );
    }
}

.partners-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffd700" opacity="0.08"/><circle cx="75" cy="75" r="1.5" fill="%23ffb347" opacity="0.06"/><circle cx="50" cy="10" r="0.5" fill="%23ffd700" opacity="0.1"/><circle cx="10" cy="60" r="0.8" fill="%23ffb347" opacity="0.07"/><circle cx="90" cy="30" r="1.2" fill="%23ffd700" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
    z-index: 0;
    animation: goldenSparkle 8s ease-in-out infinite;
}

@keyframes goldenSparkle {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.6;
        transform: scale(1.05);
    }
}

/* เพิ่มเอฟเฟกต์ twinkling stars */
.partners-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><defs><pattern id="stars" width="200" height="200" patternUnits="userSpaceOnUse"><circle cx="20" cy="30" r="0.5" fill="%23ffd700" opacity="0.8" animation="twinkle 3s ease-in-out infinite"/><circle cx="150" cy="80" r="0.8" fill="%23ffb347" opacity="0.6" animation="twinkle 4s ease-in-out infinite 0.5s"/><circle cx="80" cy="160" r="0.6" fill="%23ffd700" opacity="0.9" animation="twinkle 2.5s ease-in-out infinite 1s"/></pattern></defs><rect width="200" height="200" fill="url(%23stars)"/></svg>');
    pointer-events: none;
    z-index: 0;
    opacity: 0.4;
    animation: starField 12s linear infinite;
}

@keyframes starField {
    0% {
        transform: translateY(0) translateX(0);
    }
    100% {
        transform: translateY(-20px) translateX(-10px);
    }
}

.partners-section > .container {
    position: relative;
    z-index: 1;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #ffd700, #ffb347, #ffd700, #ff8c00);
    border-radius: 2px;
    animation: goldenTitleLine 3s ease-in-out infinite;
    box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
}

@keyframes goldenTitleLine {
    0%, 100% {
        width: 60px;
        opacity: 1;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
    }
    50% {
        width: 90px;
        opacity: 0.8;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.9),
                    0 0 30px rgba(255, 179, 71, 0.6);
    }
}

.section-title {
    position: relative;
}

.section-title::before {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    background: linear-gradient(45deg,
        rgba(255, 215, 0, 0.1),
        rgba(255, 179, 71, 0.05),
        rgba(255, 215, 0, 0.1)
    );
    border-radius: 10px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: goldenTitleGlow 4s ease-in-out infinite;
}

@keyframes goldenTitleGlow {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.6;
        transform: scale(1.02);
    }
}

.section-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 400;
    animation: fadeInUp 1s ease 0.3s both;
    position: relative;
}

.section-subtitle::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #ffd700, #ffb347);
    animation: subtitleUnderline 2s ease 1.5s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes subtitleUnderline {
    0% {
        width: 0;
        opacity: 0;
    }
    100% {
        width: 100px;
        opacity: 1;
    }
}

.partner-item {
    padding: 1rem;
}

.partner-link {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: all 0.3s ease;
}

.partner-link:hover {
    transform: translateY(-10px);
}

.partner-logo {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
    margin: 0.5rem;
}

.partner-logo::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
        transparent,
        rgba(255, 215, 0, 0.2),
        rgba(255, 179, 71, 0.3),
        rgba(255, 215, 0, 0.2),
        transparent
    );
    transition: left 0.8s ease;
    z-index: 0;
}

.partner-logo::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(
        circle at center,
        rgba(255, 215, 0, 0.1) 0%,
        rgba(255, 179, 71, 0.05) 30%,
        transparent 70%
    );
    opacity: 0;
    transition: opacity 0.6s ease;
    z-index: -1;
    animation: goldenGlow 4s ease-in-out infinite;
}

@keyframes goldenGlow {
    0%, 100% {
        transform: rotate(0deg) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: rotate(180deg) scale(1.1);
        opacity: 0.6;
    }
}

.partner-link:hover .partner-logo::before {
    left: 100%;
}

.partner-link:hover .partner-logo::after {
    opacity: 1;
}

.partner-link:hover .partner-logo {
    box-shadow:
        0 15px 35px rgba(255, 215, 0, 0.4),
        0 25px 50px rgba(255, 179, 71, 0.2),
        0 5px 15px rgba(0,0,0,0.1),
        inset 0 0 20px rgba(255, 215, 0, 0.1);
    transform: translateY(-8px) scale(1.03);
    border-color: rgba(255, 215, 0, 0.5);
    background: linear-gradient(145deg, #ffffff, #fffef7);
}

.partner-img {
    max-height: 160px;
    width: auto;
    object-fit: contain;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    filter: grayscale(20%) brightness(1.1);
    position: relative;
    z-index: 1;
    margin-bottom: 1rem;
}

.partner-link:hover .partner-img {
    filter: grayscale(0%) brightness(1.3) contrast(1.2)
            drop-shadow(0 0 15px rgba(255, 215, 0, 0.8))
            drop-shadow(0 0 30px rgba(255, 179, 71, 0.6));
    transform: scale(1.2) rotateY(10deg);
    animation: goldenFloat 2s ease-in-out infinite;
}

@keyframes goldenFloat {
    0%, 100% {
        transform: scale(1.2) rotateY(10deg) translateY(0px);
    }
    50% {
        transform: scale(1.2) rotateY(10deg) translateY(-5px);
    }
}

.partner-placeholder {
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
    margin-bottom: 1rem;
}

.partner-link:hover .partner-placeholder {
    transform: scale(1.2) rotateY(10deg);
    color: #ffd700;
    text-shadow: 0 0 15px rgba(255, 215, 0, 0.8),
                 0 0 25px rgba(255, 179, 71, 0.5);
    animation: goldenHandshake 1.5s ease-in-out infinite;
}

@keyframes goldenHandshake {
    0%, 100% {
        transform: scale(1.2) rotateY(10deg);
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.6));
    }
    50% {
        transform: scale(1.25) rotateY(15deg);
        filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.9));
    }
}

.partner-name {
    position: relative;
    z-index: 1;
}

.partner-name h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    transition: all 0.3s ease;
    position: relative;
    line-height: 1.3;
    max-width: 100%;
    word-wrap: break-word;
}

.partner-link:hover .partner-name h5 {
    background: linear-gradient(90deg, #ffd700, #ffb347, #ffd700, #ff8c00);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 100%;
    animation: goldenTextShine 2s ease-in-out infinite;
    transform: translateY(-3px) scale(1.08);
    text-shadow: 0 0 20px rgba(255, 215, 0, 1),
                 0 0 35px rgba(255, 179, 71, 0.7);
    filter: drop-shadow(0 3px 12px rgba(255, 140, 0, 0.8));
}

@keyframes goldenTextShine {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Owl Carousel custom styles */
.partners-carousel .owl-dots {
    text-align: center;
    margin-top: 2rem;
    position: relative;
}

.partners-carousel .owl-dots::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #4e73df, transparent);
    z-index: -1;
}

.partners-carousel .owl-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    margin: 0 8px;
    background: linear-gradient(45deg, #e9ecef, #dee2e6);
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.partners-carousel .owl-dot::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 0;
    height: 0;
    background: #4e73df;
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: -1;
}

.partners-carousel .owl-dot.active {
    background: linear-gradient(45deg, #ffd700, #ffb347, #ffd700);
    transform: scale(1.4);
    box-shadow:
        0 0 20px rgba(255, 215, 0, 0.8),
        0 0 30px rgba(255, 179, 71, 0.5),
        0 4px 15px rgba(255, 140, 0, 0.6);
    animation: goldenDotPulse 2s ease-in-out infinite;
}

@keyframes goldenDotPulse {
    0%, 100% {
        box-shadow:
            0 0 20px rgba(255, 215, 0, 0.8),
            0 0 30px rgba(255, 179, 71, 0.5),
            0 4px 15px rgba(255, 140, 0, 0.6);
    }
    50% {
        box-shadow:
            0 0 30px rgba(255, 215, 0, 1),
            0 0 45px rgba(255, 179, 71, 0.8),
            0 6px 20px rgba(255, 140, 0, 0.9);
    }
}

.partners-carousel .owl-dot.active::after {
    width: 28px;
    height: 28px;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.3), rgba(255, 179, 71, 0.1));
    animation: goldenRipple 2s ease-in-out infinite;
}

@keyframes goldenRipple {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.8;
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}

/* Animation for partner items */
.partners-carousel .owl-item {
    opacity: 0.6;
    transform: scale(0.9);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.partners-carousel .owl-item.active {
    opacity: 1;
    transform: scale(1);
}

.partners-carousel .owl-item.center {
    opacity: 1;
    transform: scale(1.05);
}

/* Enhanced partner card animations */
.partner-item {
    animation: partnerSlideIn 0.8s ease forwards;
    opacity: 0;
}

@keyframes partnerSlideIn {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.85);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Stagger animation for multiple items */
.partner-item:nth-child(1) { animation-delay: 0.1s; }
.partner-item:nth-child(2) { animation-delay: 0.2s; }
.partner-item:nth-child(3) { animation-delay: 0.3s; }
.partner-item:nth-child(4) { animation-delay: 0.4s; }
.partner-item:nth-child(5) { animation-delay: 0.5s; }

/* Responsive adjustments for larger cards */
@media (max-width: 1200px) {
    .partner-logo {
        min-height: 260px;
        padding: 2.2rem;
    }
    .partner-img {
        max-height: 150px;
    }
    .partner-placeholder {
        min-height: 150px;
    }
}

@media (max-width: 991px) {
    .partners-section {
        padding: 60px 0;
    }
    .section-title {
        font-size: 2.2rem;
    }
    .partner-logo {
        min-height: 240px;
        padding: 2rem;
        margin: 0.3rem;
    }
    .partner-img {
        max-height: 130px;
    }
    .partner-placeholder {
        min-height: 130px;
    }
    .partner-name h5 {
        font-size: 1rem;
    }
}

@media (max-width: 767px) {
    .partners-section {
        padding: 50px 0;
    }
    .section-title {
        font-size: 2rem;
    }
    .section-subtitle {
        font-size: 1rem;
    }
    .partner-logo {
        min-height: 220px;
        padding: 1.8rem;
        margin: 0.2rem;
    }
    .partner-img {
        max-height: 110px;
    }
    .partner-placeholder {
        min-height: 110px;
    }
    .partner-name h5 {
        font-size: 0.95rem;
    }
}

@media (max-width: 575px) {
    .partners-section {
        padding: 40px 0;
    }
    .section-title {
        font-size: 1.8rem;
    }
    .section-subtitle {
        font-size: 0.95rem;
    }
    .partner-logo {
        min-height: 200px;
        padding: 1.5rem;
        margin: 0.2rem;
    }
    .partner-img {
        max-height: 90px;
    }
    .partner-placeholder {
        min-height: 90px;
    }
    .partner-name h5 {
        font-size: 0.9rem;
    }
}

/* ขจัดพื้นหลังสีขาวของ InfoWindow ดั้งเดิม (Google) ให้โปร่งใสจริง ๆ */
.gm-style-iw-c,
.gm-style-iw-d {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
}

.gm-style-iw-t,
.gm-style-iw-t::after,
.gm-style-iw-t::before { 
    display: none !important;
}
</style>

<!-- Google Maps JavaScript -->
<script>
// ข้อมูลพันธมิตร
const partnersData = <?php echo json_encode($partners_json); ?>;

// Hover card elements
const hoverCardEl = document.getElementById('partnerHoverCard');
const hoverCardLogoEl = document.getElementById('hoverCardLogo');
const hoverCardNameEl = document.getElementById('hoverCardName');
const hoverCardProjectEl = document.getElementById('hoverCardProject');
const hoverCardAddressEl = document.getElementById('hoverCardAddress');
let hoverCardCurrentId = null;

// Variables for map and markers
let map;
let markers = [];
let infoWindow;
let activeMarkerId = null;

// Initialize Map
function initMap() {
    // กำหนดจุดกึ่งกลางเริ่มต้น (จังหวัดสตูล)
    const centerLocation = { lat: 6.6238, lng: 100.0676 };
    
    // สร้างแผนที่
    map = new google.maps.Map(document.getElementById('partnersMap'), {
        zoom: 10,
        center: centerLocation,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        scaleControl: true,
        streetViewControl: true,
        streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_TOP
        },
        fullscreenControl: true,
        styles: [
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{"color": "#e9e9e9"}, {"lightness": 17}]
            },
            {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}, {"lightness": 20}]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}, {"lightness": 17}]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{"color": "#ffffff"}, {"lightness": 29}, {"weight": 0.2}]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}, {"lightness": 21}]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{"color": "#dedede"}, {"lightness": 21}]
            }
        ]
    });
    
    // สร้าง InfoWindow
    infoWindow = new google.maps.InfoWindow();
    
    // สร้าง Markers จากข้อมูล
    partnersData.forEach(partner => {
        createMarker(partner);
    });
    
    // ปรับ Bounds ให้แสดงทุก Markers
    if (markers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        map.fitBounds(bounds);
        
        // ถ้ามี marker เดียว ให้ zoom เข้าไปหน่อย
        if (markers.length === 1) {
            map.setZoom(15);
        }
    }
}

// สร้าง Marker แบบ OverlayView เพื่อให้อยู่หน้าสุดเสมอ
function createMarker(partner) {
    class PartnerMarker extends google.maps.OverlayView {
        constructor(partner) {
            super();
            this.partner = partner;
            this.position = new google.maps.LatLng(partner.latitude, partner.longitude);
            this.div = null;
        }
        onAdd() {
            this.div = document.createElement('div');
            this.div.className = 'partner-circle-marker';
            this.div.title = this.partner.name;
            this.div.dataset.partnerId = this.partner.id;
            const panes = this.getPanes();
            panes.floatPane.appendChild(this.div); // อยู่ชั้นบนสุด
            // events
            this.div.addEventListener('mouseover', () => {
                highlightListItem(this.partner.id);
                // แสดงการ์ดแบบย่อเมื่อ hover
                showCompactInfoWindow({ getPosition: () => this.position }, this.partner);
                showHoverCard(this.partner);
            });
            this.div.addEventListener('mouseout', () => {
                removeHighlightListItem();
                // ปิดการ์ดเมื่อเอาเมาส์ออก (แต่ถ้าเป็น activeMarkerId ไม่ปิด)
                setTimeout(() => {
                    if (activeMarkerId !== this.partner.id) {
                        infoWindow.close();
                    }
                    hideHoverCard(this.partner.id);
                }, 100);
            });
            this.div.addEventListener('click', () => {
                // เปิดหน้าใหม่ของ MOU ทันที
                window.open('partners/view.php?id=' + this.partner.id, '_blank');
            });
        }
        draw() {
            const projection = this.getProjection();
            const point = projection.fromLatLngToDivPixel(this.position);
            if (this.div) {
                this.div.style.left = `${point.x}px`;
                this.div.style.top = `${point.y}px`;
            }
        }
        onRemove() {
            if (this.div && this.div.parentNode) this.div.parentNode.removeChild(this.div);
            this.div = null;
        }
        getPosition() { return this.position; }
    }

    const overlay = new PartnerMarker(partner);
    overlay.partnerId = partner.id; // เพิ่ม property เพื่อหาได้ง่าย
    overlay.setMap(map);
    markers.push(overlay);
}

// แสดง InfoWindow แบบย่อ (เมื่อ hover)
function showCompactInfoWindow(marker, partner) {
    const content = `
        <div class="info-window-compact">
            <div class="compact-name">${partner.name}</div>
        </div>
    `;
    
    infoWindow.setContent(content);
    
    // แสดงเหนือหมุดมากขึ้น ไม่บังหมุด
    const pixelOffset = new google.maps.Size(0, -110);
    infoWindow.setOptions({
        pixelOffset: pixelOffset,
        maxWidth: 180
    });
    
    infoWindow.open(map, marker);
}

// แสดง InfoWindow แบบเต็ม (เมื่อ click)
function showFullInfoWindow(marker, partner) {
    let logoHtml = '';
    if (partner.logo_image && partner.logo_image !== '') {
        logoHtml = `<img src="${partner.logo_image}" alt="${partner.name}" class="info-window-logo">`;
    }
    
    let projectHtml = '';
    if (partner.project_name) {
        projectHtml = `<div class="info-window-project">
            <i class="fas fa-project-diagram me-1"></i>${partner.project_name}
        </div>`;
    }
    
    let descriptionHtml = '';
    if (partner.description) {
        // ตัดข้อความให้สั้นลง
        const shortDesc = partner.description.length > 100 ? 
            partner.description.substring(0, 100) + '...' : 
            partner.description;
        descriptionHtml = `<div class="info-window-description">${shortDesc}</div>`;
    }
    
    let addressHtml = '';
    if (partner.address) {
        addressHtml = `<div class="info-window-address">
            <i class="fas fa-map-marker-alt me-1"></i>${partner.address}
        </div>`;
    }
    
    const content = `
        <div class="info-window-content">
            ${logoHtml}
            <div class="info-window-title">${partner.name}</div>
            ${projectHtml}
            ${descriptionHtml}
            ${addressHtml}
            <a href="partners/view.php?id=${partner.id}" 
               target="_blank" 
               class="info-window-btn">
                <i class="fas fa-info-circle me-1"></i>ดูรายละเอียด
            </a>
        </div>
    `;
    
    infoWindow.setContent(content);
    
    // ตั้งค่าให้ InfoWindow แสดงเหนือหมุดไม่บังหมุด
    const pixelOffset = new google.maps.Size(0, -100);
    infoWindow.setOptions({
        pixelOffset: pixelOffset,
        maxWidth: 280
    });
    
    infoWindow.open(map, marker);
}

// แสดง InfoWindow (ใช้แบบย่อเมื่อ hover, แบบเต็มเมื่อ click)
function showInfoWindow(marker, partner, isFull = false) {
    if (isFull) {
        showFullInfoWindow(marker, partner);
    } else {
        showCompactInfoWindow(marker, partner);
    }
}

// Remove highlight marker เมื่อเอาเมาส์ออกจากรายการ
function removeHighlightMarker(partnerId) {
    removeHighlightListItem();
    // ปิด compact info window
    setTimeout(() => {
        if (activeMarkerId !== partnerId) {
            infoWindow.close();
        }
    }, 100);
    hideHoverCard(partnerId);
}

// Highlight marker เมื่อชี้ที่รายการ
function highlightMarker(partnerId) {
    const marker = markers.find(m => m.partnerId === partnerId);
    if (marker) {
        const partner = partnersData.find(p => p.id === partnerId);
        
        // Highlight list item
        highlightListItem(partnerId);
        
        // แสดง compact info window บน marker
        const position = marker.getPosition ? marker.getPosition() : marker.position;
        showCompactInfoWindow({ getPosition: () => position }, partner);
        showHoverCard(partner);
        
        // Focus ไปที่ marker (ไม่ต้อง zoom ถ้าไม่ต้องการ)
        map.setCenter(position);
    }
}

// Focus on specific marker
function focusMarker(partnerId) {
    const marker = markers.find(m => m.partnerId === partnerId);
    if (marker) {
        const partner = partnersData.find(p => p.id === partnerId);
        map.setCenter(marker.getPosition());
        map.setZoom(partner.zoom || 15);
        
        // Show full info window
        activeMarkerId = partnerId;
        showFullInfoWindow(marker, partner);
        
        // Highlight list item
        highlightListItem(partnerId);
        
        // Animate marker
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => {
            marker.setAnimation(null);
        }, 2000);
    }
}

// Highlight list item
function highlightListItem(partnerId) {
    // Remove all active classes
    document.querySelectorAll('.partner-list-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to selected item
    const item = document.querySelector(`[data-partner-id="${partnerId}"]`);
    if (item) {
        item.classList.add('active');
        // Scroll into view
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    const partner = partnersData.find(p => p.id === partnerId);
    if (partner) {
        showHoverCard(partner);
    }
}

// Remove highlight from list items
function removeHighlightListItem() {
    document.querySelectorAll('.partner-list-item').forEach(item => {
        item.classList.remove('active');
    });
    hideHoverCard();
}

function showHoverCard(partner) {
    if (!hoverCardEl) return;
    hoverCardCurrentId = partner.id;
    hoverCardNameEl.textContent = partner.name || '';
    hoverCardProjectEl.textContent = partner.project_name || '';
    hoverCardAddressEl.textContent = partner.address || '';
    if (partner.logo_image) {
        hoverCardLogoEl.innerHTML = `<img src="${partner.logo_image}" alt="${partner.name}">`;
    } else {
        hoverCardLogoEl.innerHTML = '<i class="fas fa-handshake fa-2x" style="color:#ff6f00;"></i>';
    }
    hoverCardEl.classList.remove('d-none');
}

function hideHoverCard(partnerId) {
    if (!hoverCardEl) return;
    if (partnerId && hoverCardCurrentId && hoverCardCurrentId !== partnerId) {
        return;
    }
    hoverCardCurrentId = null;
    hoverCardEl.classList.add('d-none');
}

// Load Google Maps API with Key from Database
function loadGoogleMapsAPI() {
    const apiKey = '<?php echo $google_maps_api_key; ?>';
    if (!apiKey) {
        console.error('Google Maps API Key not found! Please add it in Admin > API Keys Management');
        document.getElementById('partnersMap').innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                    <h4 class="text-muted">ไม่พบ Google Maps API Key</h4>
                    <p class="text-muted">กรุณาเพิ่ม API Key ในระบบจัดการ Admin</p>
                </div>
            </div>
        `;
        return;
    }
    
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap&language=th`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (partnersData && partnersData.length > 0) {
        loadGoogleMapsAPI();
    }
});
</script>
