<!-- JavaScript สำหรับการสไลด์อัตโนมัติ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // เริ่มต้นสไลด์อัตโนมัติ
    var rankingsCarousel = document.getElementById('rankingsCarousel');
    if (rankingsCarousel) {
        var carousel = new bootstrap.Carousel(rankingsCarousel, {
            interval: 2000,  // เปลี่ยนสไลด์ทุก 2 วินาที
            ride: 'carousel', // เริ่มสไลด์อัตโนมัติ
            pause: 'hover',   // หยุดเมื่อเมาส์ชี้
            wrap: true,       // วนกลับไปสไลด์แรกเมื่อถึงสไลด์สุดท้าย
            keyboard: true,   // รองรับการกดปุ่มลูกศรบนคีย์บอร์ด
            touch: true       // รองรับการสัมผัสบนอุปกรณ์มือถือ
        });
        
        // เพิ่มเอฟเฟกต์การเปลี่ยนสไลด์แบบนุ่มนวล
        rankingsCarousel.addEventListener('slide.bs.carousel', function (e) {
            // สไลด์ที่กำลังจะแสดง
            var nextSlide = e.relatedTarget;
            // เพิ่มคลาส animated เพื่อเริ่มแอนิเมชัน
            nextSlide.classList.add('animated');
            
            // ลบคลาส animated หลังจากแอนิเมชันเสร็จสิ้น
            setTimeout(function() {
                nextSlide.classList.remove('animated');
            }, 1000);
        });
        
        // หยุดสไลด์ชั่วคราวเมื่อเมาส์ชี้ที่สไลด์
        rankingsCarousel.addEventListener('mouseenter', function() {
            carousel.pause();
        });
        
        // เริ่มสไลด์อีกครั้งเมื่อเมาส์ออกจากสไลด์
        rankingsCarousel.addEventListener('mouseleave', function() {
            carousel.cycle();
        });
        
        // รองรับการนำทางด้วยคีย์บอร์ด
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                // ไปสไลด์ก่อนหน้า
                carousel.prev();
            } else if (e.key === 'ArrowRight') {
                // ไปสไลด์ถัดไป
                carousel.next();
            }
        });
        
        // เพิ่มการเปลี่ยนสไลด์เมื่อชี้เมาส์ที่ปุ่มควบคุม
        const prevControl = document.querySelector('.rankings-control-prev');
        const nextControl = document.querySelector('.rankings-control-next');
        const indicators = document.querySelectorAll('.rankings-indicators button');
        
        // เมื่อชี้เมาส์ที่ปุ่มย้อนกลับ
        if (prevControl) {
            prevControl.addEventListener('mouseenter', function() {
                carousel.prev(); // เลื่อนไปสไลด์ก่อนหน้า
            });
        }
        
        // เมื่อชี้เมาส์ที่ปุ่มถัดไป
        if (nextControl) {
            nextControl.addEventListener('mouseenter', function() {
                carousel.next(); // เลื่อนไปสไลด์ถัดไป
            });
        }
        
        // เมื่อชี้เมาส์ที่ตัวบ่งชี้สไลด์ (จุดด้านล่าง)
        if (indicators && indicators.length > 0) {
            indicators.forEach(function(indicator) {
                indicator.addEventListener('mouseenter', function() {
                    // ดึงหมายเลขสไลด์จาก data-bs-slide-to
                    const slideIndex = this.getAttribute('data-bs-slide-to');
                    if (slideIndex !== null) {
                        // เลื่อนไปยังสไลด์ที่ระบุ
                        carousel.to(parseInt(slideIndex));
                    }
                });
            });
        }
    }
});
</script>
