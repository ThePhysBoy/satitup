<?php
/**
 * Partners Section - เครือข่ายความร่วมมือ
 * แสดงโลโก้พันธมิตรแบบสไลด์ทีละ 3 รายการ
 */

// เชื่อมต่อฐานข้อมูล (ถ้ายังไม่ได้เชื่อมต่อ)
if (!isset($conn)) {
    require_once 'db_connect.php';
}

// ดึงข้อมูล partners ที่มีสถานะ active เรียงตาม order_number
$partners_result = false;
if ($conn) {
    // ตรวจสอบว่าตาราง partners มีอยู่หรือไม่
    $table_check = $conn->query("SHOW TABLES LIKE 'partners'");
    if ($table_check && $table_check->num_rows > 0) {
        $partners_query = "SELECT * FROM partners WHERE status = 'active' ORDER BY order_number ASC, created_at DESC";
        $partners_result = $conn->query($partners_query);
    }
}
?>

<!-- Partners Section -->
<section class="partners-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">เครือข่ายความร่วมมือ</h2>
            <p class="section-subtitle text-muted">หน่วยงานพันธมิตรที่ร่วมพัฒนาการศึกษา</p>
        </div>
        
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
                            <!-- รูป placeholder ถ้าไม่มีรูป -->
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
        <!-- ถ้าไม่มีข้อมูล partners ให้แสดงข้อความ -->
        <div class="text-center py-5">
            <i class="fas fa-handshake fa-4x text-muted mb-3"></i>
            <p class="text-muted">ยังไม่มีข้อมูลหน่วยงานพันธมิตร</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Custom CSS for Partners Section -->
<style>
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
</style>
