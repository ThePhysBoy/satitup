<script>
// ตั้งค่าเพิ่มเติมสำหรับสไลด์การจัดอันดับ
document.addEventListener('DOMContentLoaded', function() {
    // ตั้งค่า carousel ให้เลื่อนทุก 4 วินาที
    var rankingsCarousel = document.getElementById('rankingsCarousel');
    if (rankingsCarousel) {
        var carousel = new bootstrap.Carousel(rankingsCarousel, {
            interval: 4000,
            pause: false,
            wrap: true,
            touch: true
        });
        
        // ปรับการแสดงผลให้แสดงทีละอัน และวนไปเรื่อยๆ
        rankingsCarousel.addEventListener('slide.bs.carousel', function (event) {
            // ดึงข้อมูลสไลด์ปัจจุบันและสไลด์ถัดไป
            var currentSlide = event.from;
            var nextSlide = event.to;
            
            // แสดงข้อมูลในคอนโซล (สำหรับการดีบัก)
            console.log('กำลังเปลี่ยนจากสไลด์ที่ ' + currentSlide + ' ไปยังสไลด์ที่ ' + nextSlide);
        });
    }
});
</script>