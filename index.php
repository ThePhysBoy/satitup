<?php
/**
 * หน้าหลักเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา
 * Demonstration School of University of Phayao
 * 
 * พัฒนาโดย: ทีมพัฒนาเว็บไซต์โรงเรียนสาธิต
 * วันที่: <?php echo date('Y-m-d'); ?>
 */

// การตั้งค่าพื้นฐาน (Basic Configuration)
error_reporting(E_ALL);       // รายงานข้อผิดพลาดทั้งหมด
ini_set('display_errors', 0); // ไม่แสดงข้อผิดพลาดบนหน้าเว็บ (ปิดการแสดงผลข้อผิดพลาด)
session_start();              // เริ่มต้นเซสชัน สำหรับเก็บข้อมูลผู้ใช้

// กำหนด timezone ให้เป็นเวลาประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// นำเข้าไฟล์ส่วนหัวของเว็บไซต์ (header.php)
// ประกอบด้วย: DOCTYPE, HTML, HEAD, META tags, CSS, JavaScript libraries
include_once 'header.php';

// นำเข้าไฟล์เมนูนำทาง (navbar.php)
// ประกอบด้วย: เมนูหลัก, โลโก้, ลิงก์ต่างๆ
include_once 'navbar.php';

// นำเข้าไฟล์สไลด์โชว์ (slideshow.php)
// ประกอบด้วย: รูปภาพสไลด์โชว์หลักพร้อมข้อความ
include_once 'includes/slideshow.php';
?>

<!-- ตัวแบ่งส่วน (Section Separator) - เส้นคั่นระหว่างส่วนสไลด์โชว์กับเนวิเกชันบาร์ถัดไป -->
<div class="section-separator"></div>

<?php
// นำเข้าไฟล์เมนูนำทางส่วนใหม่ (new_section_navbar.php)
// ประกอบด้วย: เมนูเสริม, ลิงก์ด่วน, การนำทางเฉพาะส่วน
include_once 'new_section_navbar.php';
?>

<!-- นำเข้าส่วนการจัดอันดับและความสำเร็จของมหาวิทยาลัย -->
<!-- แสดงข้อมูลการจัดอันดับ รางวัล และความสำเร็จต่างๆ ของมหาวิทยาลัย -->
<?php include_once 'rankings/index.php'; ?>

<!-- นำเข้าส่วนลิงก์ด่วน (Quick Links) -->
<!-- แสดงลิงก์ที่ใช้งานบ่อย เช่น ระบบลงทะเบียน ปฏิทินการศึกษา ฯลฯ -->
<?php include_once 'quick_links.php'; ?>

<!-- นำเข้าส่วนข่าวสารและประกาศ -->
<!-- แสดงข่าวสาร ประกาศ และกิจกรรมล่าสุดของโรงเรียน -->
<?php include_once 'news/announcements.php'; ?>

<!-- นำเข้าส่วนลิงก์วิดีโอด่วน -->
<!-- แสดงลิงก์ไปยังวิดีโอสำคัญ เช่น แนะนำโรงเรียน กิจกรรมเด่น ฯลฯ -->
<?php include_once 'video_quick_links.php'; ?>

<!-- ส่วนเนื้อหาหลัก (Main Content) -->
<main class="main-content">

    <!-- นำเข้าส่วนข่าวสารและกิจกรรม -->
    <!-- แสดงข่าวสารและกิจกรรมล่าสุดในรูปแบบการ์ด -->
    <?php include_once 'news_events.php'; ?>

    <!-- นำเข้าส่วนสถิติ -->
    <!-- แสดงตัวเลขสถิติสำคัญของโรงเรียน เช่น จำนวนนักเรียน ครู ฯลฯ -->
    <?php include_once 'statistics_section.php'; ?>

    <!-- นำเข้าส่วนหลักสูตรการศึกษา -->
    <!-- แสดงข้อมูลหลักสูตรการเรียนการสอนที่เปิดสอน -->
    <?php include_once 'academic_programs.php'; ?>

    <!-- นำเข้าส่วนพันธมิตร -->
    <!-- แสดงโลโก้และข้อมูลหน่วยงานพันธมิตรที่ร่วมมือกับโรงเรียน -->
    <?php include_once 'partners_section.php'; ?>

</main>

<!-- CSS เฉพาะสำหรับหน้าหลัก (Custom CSS for Index Page) -->
<style>
/* ส่วนต้อนรับ (Welcome Section) */
.welcome-section {
    /* พื้นหลังไล่ระดับสีจากสีอ่อนไปเข้ม (จากซ้ายบนไปขวาล่าง) */
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

/* สไตล์หัวข้อส่วน (Section Title) */
.section-title {
    /* ขนาดตัวอักษร 2.5 เท่าของขนาดปกติ */
    font-size: 2.5rem;
    /* ตัวหนา */
    font-weight: 700;
    /* สีข้อความเข้ม (ตามตัวแปร CSS) */
    color: var(--text-dark);
}

/* รายการคุณสมบัติ (Feature Item) */
.feature-item {
    /* แสดงเป็น flex เพื่อจัดวางไอคอนและข้อความในแนวนอน */
    display: flex;
    /* จัดให้องค์ประกอบอยู่ตรงกลางในแนวตั้ง */
    align-items: center;
    /* ขนาดตัวอักษร 1.1 เท่าของขนาดปกติ */
    font-size: 1.1rem;
}

/* การ์ดข่าว (News Cards) */
.news-card {
    /* พื้นหลังสีขาว */
    background: white;
    /* มุมโค้ง 15px */
    border-radius: 15px;
    /* ซ่อนเนื้อหาที่ล้นออกไป */
    overflow: hidden;
    /* การเปลี่ยนแปลงทุกคุณสมบัติใช้เวลา 0.3 วินาที */
    transition: all 0.3s ease;
    /* เงาของการ์ด */
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* เอฟเฟกต์เมื่อชี้เมาส์ที่การ์ดข่าว */
.news-card:hover {
    /* ยกขึ้น 5px */
    transform: translateY(-5px);
    /* เพิ่มเงาให้ชัดขึ้น */
    box-shadow: 0 10px 30px rgba(139,122,168,0.2);
}

/* ส่วนรูปภาพข่าว */
.news-image {
    /* กำหนดตำแหน่งเป็น relative เพื่อให้สามารถวางองค์ประกอบอื่นๆ ทับได้ */
    position: relative;
    /* ความสูงคงที่ 200px */
    height: 200px;
    /* ซ่อนส่วนของรูปภาพที่ล้นออกไป */
    overflow: hidden;
}

/* รูปภาพในส่วนรูปภาพข่าว */
.news-image img {
    /* ความกว้าง 100% ของพื้นที่ */
    width: 100%;
    /* ความสูง 100% ของพื้นที่ */
    height: 100%;
    /* ปรับขนาดรูปภาพให้พอดีกับพื้นที่ โดยอาจตัดบางส่วนออก */
    object-fit: cover;
}

/* วันที่ของข่าว */
.news-date {
    /* กำหนดตำแหน่งแบบ absolute เพื่อวางทับบนรูปภาพ */
    position: absolute;
    /* ห่างจากขอบบน 15px */
    top: 15px;
    /* ห่างจากขอบขวา 15px */
    right: 15px;
    /* พื้นหลังสีหลัก (ตามตัวแปร CSS) */
    background: var(--primary-color);
    /* ข้อความสีขาว */
    color: white;
    /* ระยะห่างภายใน 10px */
    padding: 10px;
    /* มุมโค้ง 10px */
    border-radius: 10px;
    /* จัดข้อความให้อยู่ตรงกลาง */
    text-align: center;
}

/* วันที่ (ตัวเลข) */
.news-date .day {
    /* ขนาดตัวอักษร 1.5 เท่าของขนาดปกติ */
    font-size: 1.5rem;
    /* ตัวหนา */
    font-weight: 700;
    /* แสดงเป็นบล็อก (ขึ้นบรรทัดใหม่) */
    display: block;
}

/* เดือน */
.news-date .month {
    /* ขนาดตัวอักษร 0.9 เท่าของขนาดปกติ */
    font-size: 0.9rem;
}

/* ส่วนสถิติ (Statistics Section) */
.statistics-section {
    /* พื้นหลังไล่ระดับสีจากสีหลักอ่อนไปสีรอง (จากซ้ายบนไปขวาล่าง) */
    background: linear-gradient(135deg, var(--primary-light), var(--secondary-color));
    /* ข้อความสีขาว */
    color: white;
}

/* การ์ดสถิติ */
.stat-card {
    /* ระยะห่างภายใน 30px */
    padding: 30px;
}

/* ไอคอนสถิติ */
.stat-icon {
    /* ขนาดไอคอน 3 เท่าของขนาดปกติ */
    font-size: 3rem;
}
</style>

<!-- JavaScript เฉพาะสำหรับหน้าหลัก (Custom JavaScript for Index Page) -->
<script>
// เมื่อเอกสาร HTML โหลดเสร็จสมบูรณ์
$(document).ready(function() {
    // เริ่มต้นการทำงานของ Owl Carousel สำหรับส่วนพันธมิตร
    $('.partners-carousel').owlCarousel({
        loop: true,              // วนซ้ำไปเรื่อยๆ
        margin: 30,              // ระยะห่างระหว่างรายการ 30px
        nav: false,              // ไม่แสดงปุ่มนำทาง
        dots: true,              // แสดงจุดบ่งชี้ด้านล่าง
        autoplay: true,          // เล่นอัตโนมัติ
        autoplayTimeout: 3000,   // เปลี่ยนทุก 3 วินาที
        // การปรับตัวตามขนาดหน้าจอ (Responsive)
        responsive: {
            0: {                 // หน้าจอขนาดเล็ก (น้อยกว่า 600px)
                items: 2         // แสดง 2 รายการต่อแถว
            },
            600: {               // หน้าจอขนาดกลาง (600px ขึ้นไป)
                items: 3         // แสดง 3 รายการต่อแถว
            },
            1000: {              // หน้าจอขนาดใหญ่ (1000px ขึ้นไป)
                items: 5         // แสดง 5 รายการต่อแถว
            }
        }
    });
    
    // แอนิเมชันตัวเลขสถิติ (Counter Animation)
    $('.stat-number').each(function() {
        var $this = $(this);                // อ้างอิงถึงองค์ประกอบปัจจุบัน
        var countTo = $this.attr('data-count'); // ดึงค่าเป้าหมายจากแอตทริบิวต์ data-count
        
        // สร้างออบเจ็กต์สำหรับแอนิเมชัน
        $({ countNum: $this.text() }).animate({
            countNum: countTo    // เพิ่มค่าไปจนถึงเป้าหมาย
        }, {
            duration: 2000,      // ระยะเวลาแอนิเมชัน 2 วินาที
            easing: 'linear',    // รูปแบบการเคลื่อนไหวแบบเส้นตรง
            // ฟังก์ชันที่ทำงานในแต่ละขั้นตอนของแอนิเมชัน
            step: function() {
                // แสดงค่าปัจจุบัน (ปัดเศษลง)
                $this.text(Math.floor(this.countNum));
            },
            // ฟังก์ชันที่ทำงานเมื่อแอนิเมชันเสร็จสิ้น
            complete: function() {
                // แสดงค่าสุดท้าย
                $this.text(this.countNum);
                // ถ้าข้อความใน stat-label มีเครื่องหมาย % ให้เพิ่ม % ต่อท้ายตัวเลข
                if ($this.parent().find('.stat-label').text().includes('%')) {
                    $this.append('%');
                }
            }
        });
    });
});
</script>

<?php
// นำเข้าส่วนท้ายของเว็บไซต์ (Footer)

// นำเข้าส่วนแสดงหน่วยงานของมหาวิทยาลัย
// แสดงข้อมูลคณะ/หน่วยงานต่างๆ ของมหาวิทยาลัย
include_once 'university_departments.php';

// นำเข้าส่วนลิงก์ภายนอก
// แสดงลิงก์ไปยังเว็บไซต์ภายนอกที่เกี่ยวข้อง
include_once 'external_links.php';

// นำเข้าส่วนท้ายของเว็บไซต์
// ประกอบด้วย: ข้อมูลติดต่อ, ลิงก์โซเชียลมีเดีย, ลิขสิทธิ์, JavaScript libraries
include_once 'footer.php';
?>
