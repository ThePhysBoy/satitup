<?php
/**
 * Static University Rankings Component
 * This file is a fallback for when the database connection fails
 */
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
                <!-- สไลด์ที่ 1 - THE Impact Rankings 2025 -->
                <div class="carousel-item active">
                    <div class="row justify-content-center">
                        <!-- col-lg-10 ใช้ 10/12 ส่วนบนหน้าจอใหญ่, col-md-11 ใช้ 11/12 ส่วนบนหน้าจอกลาง, col-sm-12 ใช้เต็มหน้าจอบนมือถือ -->
                        <div class="col-lg-10 col-md-11 col-sm-12">
                            <!-- การ์ดแสดงข้อมูลการจัดอันดับ - h-100 ทำให้สูงเต็มพื้นที่ -->
                            <div class="ranking-card-single h-100">
                                <!-- ลิงก์ไปยังข่าวต้นฉบับ -->
                                <a href="https://www.up.ac.th/NewsRead.aspx?itemID=34799" target="_blank" class="ranking-link">
                                    <!-- ส่วนแสดงรูปภาพ -->
                                    <div class="ranking-image">
                                        <img src="slide/2568/UP-ImpactRnaking-2025.jpg" alt="THE Impact Rankings 2025" class="img-fluid">
                                        <!-- โอเวอร์เลย์ที่แสดงไอคอนเมื่อ hover -->
                                        <div class="ranking-overlay">
                                            <i class="fas fa-award"></i>
                                        </div>
                                    </div>
                                    <!-- ส่วนแสดงเนื้อหา -->
                                    <div class="ranking-content">
                                        <h5 class="ranking-title">THE Impact Rankings 2025</h5>
                                        <p class="ranking-description">การจัดอันดับผลกระทบเพื่อการพัฒนาที่ยั่งยืน</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2 - THE Asia University Rankings 2025 -->
                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-md-11 col-sm-12">
                            <div class="ranking-card-single h-100">
                                <a href="https://www.up.ac.th/NewsRead.aspx?itemID=34342" target="_blank" class="ranking-link">
                                    <div class="ranking-image">
                                        <img src="slide/2567/Banner-WUR-2025-01.jpg" alt="THE Asia University Rankings 2025" class="img-fluid">
                                        <div class="ranking-overlay">
                                            <i class="fas fa-award"></i>
                                        </div>
                                    </div>
                                    <div class="ranking-content">
                                        <h5 class="ranking-title">THE Asia University Rankings 2025</h5>
                                        <p class="ranking-description">การจัดอันดับมหาวิทยาลัยในเอเชีย</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3 - QS World University Rankings 2025 -->
                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-md-11 col-sm-12">
                            <div class="ranking-card-single h-100">
                                <a href="https://www.up.ac.th/NewsRead.aspx?itemID=34567" target="_blank" class="ranking-link">
                                    <div class="ranking-image">
                                        <img src="slide/2567/qs.jpg" alt="QS World University Rankings 2025" class="img-fluid">
                                        <div class="ranking-overlay">
                                            <i class="fas fa-award"></i>
                                        </div>
                                    </div>
                                    <div class="ranking-content">
                                        <h5 class="ranking-title">QS World University Rankings 2025</h5>
                                        <p class="ranking-description">การจัดอันดับมหาวิทยาลัยโลก</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 4 - UI GreenMetric World University Rankings 2024 -->
                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-md-11 col-sm-12">
                            <div class="ranking-card-single h-100">
                                <a href="https://www.up.ac.th/NewsRead.aspx?itemID=34123" target="_blank" class="ranking-link">
                                    <div class="ranking-image">
                                        <img src="slide/2567/UI_Green.jpg" alt="UI GreenMetric World University Rankings 2024" class="img-fluid">
                                        <div class="ranking-overlay">
                                            <i class="fas fa-award"></i>
                                        </div>
                                    </div>
                                    <div class="ranking-content">
                                        <h5 class="ranking-title">UI GreenMetric World University Rankings 2024</h5>
                                        <p class="ranking-description">การจัดอันดับมหาวิทยาลัยสีเขียวโลก</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#rankingsCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/styles.php'; ?>
<?php include_once __DIR__ . '/scripts.php'; ?>
