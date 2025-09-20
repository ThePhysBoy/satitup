<?php
/**
 * Static Slideshow Component
 * This file is a fallback for when the database connection fails
 */
?>
<!-- CSS สำหรับปรับแต่งปุ่มควบคุมสไลด์ -->
<style>
    /* ปรับแต่งปุ่มควบคุมสไลด์ (ลูกศรซ้าย-ขวา) */
    .carousel-control-prev,
    .carousel-control-next {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    /* เมื่อ hover ที่ปุ่มควบคุม */
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background-color: rgba(255, 255, 255, 0.9);
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }
    
    /* ปรับแต่งไอคอนลูกศร */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 24px;
        height: 24px;
        filter: invert(1) grayscale(100%);
        transition: all 0.3s ease;
    }
    
    /* เมื่อ hover ที่ปุ่มควบคุม ให้ไอคอนเปลี่ยนสี */
    .carousel-control-prev:hover .carousel-control-prev-icon,
    .carousel-control-next:hover .carousel-control-next-icon {
        filter: invert(0.5) sepia(1) saturate(5) hue-rotate(175deg);
    }
    
    /* ปรับแต่งจุดบอกตำแหน่งสไลด์ด้านล่าง */
    .carousel-indicators {
        bottom: 20px;
    }
    
    /* ปรับแต่งปุ่มจุดบอกตำแหน่งสไลด์ */
    .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.7);
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    /* เมื่อ hover ที่ปุ่มจุดบอกตำแหน่งสไลด์ */
    .carousel-indicators button:hover {
        background-color: rgba(255, 255, 255, 0.9);
        transform: scale(1.2);
    }
    
    /* ปุ่มจุดบอกตำแหน่งสไลด์ที่กำลังแสดงอยู่ */
    .carousel-indicators button.active {
        width: 25px;
        border-radius: 10px;
        background-color: #fff;
        border-color: #fff;
    }
    
    /* ปรับตำแหน่งปุ่มควบคุมบนหน้าจอขนาดเล็ก */
    @media (max-width: 767px) {
        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 20px;
            height: 20px;
        }
        
        .carousel-indicators button {
            width: 10px;
            height: 10px;
            margin: 0 3px;
        }
        
        .carousel-indicators button.active {
            width: 20px;
        }
    }
</style>

<!-- ส่วนสไลด์โชว์หลัก (Main Slideshow) -->
<div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    <!-- ตัวบ่งชี้สไลด์ (Indicators) - จุดกลมด้านล่างสำหรับเลือกสไลด์ -->
    <div class="carousel-indicators">
        <!-- ปุ่มสำหรับสไลด์ที่ 1 (active = แสดงอยู่) -->
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <!-- ปุ่มสำหรับสไลด์ที่ 2 -->
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <!-- ปุ่มสำหรับสไลด์ที่ 3 -->
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    
    <!-- เนื้อหาสไลด์ (Slides) -->
    <div class="carousel-inner">
        <!-- สไลด์ที่ 1 (active = แสดงเป็นสไลด์แรก) -->
        <div class="carousel-item active">
            <!-- รูปภาพสไลด์ -->
            <img src="images/slideshow/slideshow1.jpg" class="d-block w-100" alt="ยินดีต้อนรับสู่โรงเรียนสาธิตมหาวิทยาลัยพะเยา">
            
            <!-- ข้อความบนสไลด์ (Caption) -->
            <div class="carousel-caption">
                <!-- เนื้อหาข้อความ -->
                <div class="caption-content">
                    <!-- หัวข้อใหญ่พร้อมแอนิเมชัน -->
                    <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">
                        ยินดีต้อนรับ
                    </h1>
                    <!-- หัวข้อรองพร้อมแอนิเมชันและหน่วงเวลา 1 วินาที -->
                    <h2 class="h3 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        สู่โรงเรียนสาธิตมหาวิทยาลัยพะเยา
                    </h2>
                    <!-- ข้อความพร้อมแอนิเมชันและหน่วงเวลา 2 วินาที -->
                    <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                        สร้างคนดี มีความรู้ สู่สังคมอย่างมีคุณภาพ
                    </p>
                    <!-- ปุ่มพร้อมแอนิเมชันและหน่วงเวลา 3 วินาที -->
                    <a href="about-history.php" class="btn btn-primary btn-lg rounded-pill px-4 animate__animated animate__fadeInUp animate__delay-3s">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- สไลด์ที่ 2 -->
        <div class="carousel-item">
            <!-- รูปภาพสไลด์ที่ 2 -->
            <img src="images/slideshow/slideshow2.jpg" class="d-block w-100" alt="การเรียนการสอนที่ทันสมัย">
            <!-- ข้อความบนสไลด์ที่ 2 -->
            <div class="carousel-caption">
                <!-- เนื้อหาข้อความ -->
                <div class="caption-content">
                    <!-- หัวข้อใหญ่ -->
                    <h1 class="display-4 fw-bold mb-3">
                        การศึกษาที่มีคุณภาพ
                    </h1>
                    <!-- หัวข้อรอง -->
                    <h2 class="h3 mb-4">
                        ด้วยเทคโนโลยีและนวัตกรรมที่ทันสมัย
                    </h2>
                    <!-- ข้อความ -->
                    <p class="lead mb-4">
                        พัฒนาศักยภาพนักเรียนสู่ความเป็นเลิศ
                    </p>
                    <!-- ปุ่มลิงก์ไปยังหน้าหลักสูตร -->
                    <a href="academic-curriculum.php" class="btn btn-primary btn-lg rounded-pill px-4">
                        ดูหลักสูตร <i class="fas fa-graduation-cap ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- สไลด์ที่ 3 -->
        <div class="carousel-item">
            <!-- รูปภาพสไลด์ที่ 3 -->
            <img src="images/slideshow/slideshow3.jpg" class="d-block w-100" alt="กิจกรรมพัฒนานักเรียน">
            <!-- ข้อความบนสไลด์ที่ 3 -->
            <div class="carousel-caption">
                <!-- เนื้อหาข้อความ -->
                <div class="caption-content">
                    <!-- หัวข้อใหญ่ -->
                    <h1 class="display-4 fw-bold mb-3">
                        กิจกรรมหลากหลาย
                    </h1>
                    <!-- หัวข้อรอง -->
                    <h2 class="h3 mb-4">
                        ส่งเสริมการเรียนรู้ในทุกมิติ
                    </h2>
                    <!-- ข้อความ -->
                    <p class="lead mb-4">
                        พัฒนาทักษะชีวิต สร้างประสบการณ์ที่มีคุณค่า
                    </p>
                    <!-- ปุ่มลิงก์ไปยังหน้ากิจกรรมนักเรียน -->
                    <a href="student-activities.php" class="btn btn-primary btn-lg rounded-pill px-4">
                        ดูกิจกรรม <i class="fas fa-users ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ปุ่มควบคุมสไลด์ (Controls) -->
    <!-- ปุ่มย้อนกลับไปสไลด์ก่อนหน้า -->
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <!-- ไอคอนลูกศรซ้าย -->
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <!-- ข้อความสำหรับผู้อ่านหน้าจอ (Screen Reader) -->
        <span class="visually-hidden">Previous</span>
    </button>
    <!-- ปุ่มไปยังสไลด์ถัดไป -->
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <!-- ไอคอนลูกศรขวา -->
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <!-- ข้อความสำหรับผู้อ่านหน้าจอ (Screen Reader) -->
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- JavaScript สำหรับเริ่มต้นการทำงานของสไลด์โชว์ -->
<script>
// รอให้เอกสาร HTML โหลดเสร็จสมบูรณ์ก่อนทำงาน
document.addEventListener('DOMContentLoaded', function() {
    // เริ่มต้นการทำงานของ Bootstrap Carousel
    var carousel = new bootstrap.Carousel(document.getElementById('mainCarousel'), {
        interval: 4000,  // ระยะเวลาในการเปลี่ยนสไลด์ (4 วินาที)
        ride: 'carousel', // เริ่มทำงานอัตโนมัติ
        pause: 'hover',   // หยุดเมื่อเมาส์ชี้ (hover)
        wrap: true        // วนกลับไปสไลด์แรกเมื่อถึงสไลด์สุดท้าย
    });
    
    // ทำให้แน่ใจว่าการเล่นอัตโนมัติยังคงทำงาน
    setInterval(function() {
        // ตรวจสอบว่าเมาส์ไม่ได้ชี้อยู่บนสไลด์
        if (!document.querySelector('#mainCarousel:hover')) {
            // เลื่อนไปสไลด์ถัดไป
            carousel.next();
        }
    }, 4000); // ทุก 4 วินาที
    
    // เพิ่มการเปลี่ยนสไลด์เมื่อชี้เมาส์ที่ปุ่มควบคุม
    const prevButton = document.querySelector('.carousel-control-prev');
    const nextButton = document.querySelector('.carousel-control-next');
    const indicators = document.querySelectorAll('.carousel-indicators button');
    
    // เมื่อชี้เมาส์ที่ปุ่มย้อนกลับ
    prevButton.addEventListener('mouseenter', function() {
        carousel.prev(); // เลื่อนไปสไลด์ก่อนหน้า
    });
    
    // เมื่อชี้เมาส์ที่ปุ่มถัดไป
    nextButton.addEventListener('mouseenter', function() {
        carousel.next(); // เลื่อนไปสไลด์ถัดไป
    });
    
    // เมื่อชี้เมาส์ที่ตัวบ่งชี้สไลด์ (จุดด้านล่าง)
    indicators.forEach(function(indicator) {
        indicator.addEventListener('mouseenter', function() {
            // ดึงหมายเลขสไลด์จาก data-bs-slide-to
            const slideIndex = this.getAttribute('data-bs-slide-to');
            // เลื่อนไปยังสไลด์ที่ระบุ
            carousel.to(parseInt(slideIndex));
        });
    });
});
</script>
