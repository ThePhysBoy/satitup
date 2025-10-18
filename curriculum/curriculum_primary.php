<?php
/**
 * หลักสูตรระดับชั้นประถมศึกษา
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

// Page title
$page_title = "หลักสูตรระดับชั้นประถมศึกษา";

// นำเข้าไฟล์ส่วนหัวของเว็บไซต์ (header.php)
// ประกอบด้วย: DOCTYPE, HTML, HEAD, META tags, CSS, JavaScript libraries
include_once '../header.php';

?>

<!-- ตัวแบ่งส่วน (Section Separator) - เส้นคั่นระหว่างส่วนสไลด์โชว์กับเนวิเกชันบาร์ถัดไป -->
<div class="section-separator"></div>

    <!-- Custom CSS for this page -->
    <style>
        .hero-section {
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 100%);
            padding: 80px 0;
            color: white;
            margin-top: -20px;
        }
        
        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero-section .lead {
            font-size: 1.2rem;
            opacity: 0.95;
        }
        
        .content-section {
            padding: 60px 0;
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 40px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .section-title {
            color: #5cb85c;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            border-left: 4px solid #a8e6cf;
            padding-left: 15px;
        }
        
        .highlight-box {
            background: linear-gradient(135deg, #f0fff4 0%, #d4fdd4 100%);
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 12px 0;
            padding-left: 35px;
            position: relative;
            font-size: 1.05rem;
        }
        
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #a8e6cf;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .pdf-viewer-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .pdf-title {
            font-size: 1.3rem;
            color: #333;
            font-weight: 600;
        }
        
        .pdf-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(168, 230, 207, 0.4);
            color: white;
        }
        
        .btn-outline-custom {
            border: 2px solid #a8e6cf;
            color: #a8e6cf;
            background: transparent;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-custom:hover {
            background: #a8e6cf;
            color: white;
            transform: translateY(-2px);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .info-card i {
            font-size: 2.5rem;
            color: #a8e6cf;
            margin-bottom: 15px;
        }
        
        .info-card h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .info-card p {
            color: #666;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Include Navigation Bar -->
    <?php include '../navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1>หลักสูตรระดับชั้นประถมศึกษา</h1>
                    <p class="lead">ระดับชั้นประถมศึกษาปีที่ 1 - 6 (ป.1 - ป.6)</p>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="../images/logo-school-white.png" alt="School Logo" style="max-width: 200px; opacity: 0.9;">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main Content Section -->
    <section class="content-section">
        <div class="container">
            <!-- About Section -->
            <div class="section-card">
                <h2 class="section-title">เกี่ยวกับหลักสูตร</h2>
                <p style="font-size: 1.1rem; line-height: 1.8;">
                    หลักสูตรระดับชั้นประถมศึกษา โรงเรียนสาธิตมหาวิทยาลัยพะเยา เป็นหลักสูตรที่บูรณาการ
                    ทั้ง 8 กลุ่มสาระการเรียนรู้ เพื่อพัฒนานักเรียนให้มีความรู้พื้นฐานที่แข็งแกร่ง มีทักษะการคิด
                    และคุณลักษณะที่พึงประสงค์ สร้างรากฐานที่มั่นคงสำหรับการเรียนรู้ในระดับที่สูงขึ้น
                </p>
                
                <div class="highlight-box mt-4">
                    <h4 style="color: #5cb85c; margin-bottom: 15px;">วัตถุประสงค์ของหลักสูตร</h4>
                    <ul class="feature-list">
                        <li>พัฒนานักเรียนให้มีความรู้พื้นฐานครบทุกด้าน ทั้ง 8 กลุ่มสาระการเรียนรู้</li>
                        <li>ส่งเสริมทักษะการคิด การอ่าน การเขียน และการสื่อสาร</li>
                        <li>เสริมสร้างคุณลักษณะอันพึงประสงค์และจิตสำนึกในความเป็นไทย</li>
                        <li>พัฒนาทักษะการใช้เทคโนโลยีและการเรียนรู้ในศตวรรษที่ 21</li>
                    </ul>
                </div>
            </div>
            
            <!-- Info Cards -->
            <div class="info-grid">
                <div class="info-card">
                    <i class="fas fa-users"></i>
                    <h4>จำนวนรับ</h4>
                    <p>ตามความเหมาะสม</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h4>ระยะเวลา</h4>
                    <p>6 ปี (ป.1-ป.6)</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-book"></i>
                    <h4>เวลาเรียนรวม</h4>
                    <p>7,440 ชั่วโมง</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-award"></i>
                    <h4>กิจกรรม</h4>
                    <p>720 ชั่วโมง</p>
                </div>
            </div>
            
            <!-- Curriculum Structure -->
            <div class="section-card">
                <h2 class="section-title">โครงสร้างหลักสูตร</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h4 style="color: #a8e6cf; margin-bottom: 20px;">รายวิชาพื้นฐาน (6,720 ชั่วโมง)</h4>
                        <ul class="feature-list">
                            <li>ภาษาไทย - 1,080 ชั่วโมง</li>
                            <li>คณิตศาสตร์ - 1,080 ชั่วโมง</li>
                            <li>วิทยาศาสตร์และเทคโนโลยี - 600 ชั่วโมง</li>
                            <li>สังคมศึกษา ศาสนาและวัฒนธรรม - 720 ชั่วโมง</li>
                            <li>สุขศึกษาและพลศึกษา - 360 ชั่วโมง</li>
                            <li>ศิลปะ - 240 ชั่วโมง</li>
                            <li>การงานอาชีพ - 240 ชั่วโมง</li>
                            <li>ภาษาต่างประเทศ - 720 ชั่วโมง</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4 style="color: #a8e6cf; margin-bottom: 20px;">รายวิชาเพิ่มเติม (1,680 ชั่วโมง)</h4>
                        <ul class="feature-list">
                            <li>วิทยาศาสตร์เชิงปฏิบัติการ (PBL)</li>
                            <li>เทคโนโลยีสารสนเทศในชีวิตประจำวัน</li>
                            <li>ทักษะกีฬาและนันทนาการ</li>
                            <li>การแสดงเพื่อสุนทรียภาพ</li>
                            <li>ดนตรี</li>
                            <li>ภาษาอังกฤษเพื่อการสื่อสาร (ECD)</li>
                            <li>สะเต็มศึกษา (STEM Education)</li>
                            <li>ภาษาต่างประเทศ (ภาษาจีน)</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Special Features -->
            <div class="section-card">
                <h2 class="section-title">จุดเด่นของหลักสูตร</h2>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="highlight-box h-100">
                            <h5 style="color: #5cb85c;"><i class="fas fa-child"></i> พัฒนาการเด็กองค์รวม</h5>
                            <p>เน้นพัฒนาเด็กทั้งด้านสติปัญญา ร่างกาย อารมณ์ และสังคม</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="highlight-box h-100">
                            <h5 style="color: #5cb85c;"><i class="fas fa-puzzle-piece"></i> การเรียนรู้แบบบูรณาการ</h5>
                            <p>เชื่อมโยงความรู้ในกลุ่มสาระต่างๆ ผ่านกิจกรรมที่หลากหลาย</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="highlight-box h-100">
                            <h5 style="color: #5cb85c;"><i class="fas fa-seedling"></i> สร้างพื้นฐานที่แข็งแกร่ง</h5>
                            <p>พร้อมสำหรับการศึกษาต่อในระดับมัธยมศึกษา</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- PDF Document Section -->
            <div class="pdf-viewer-section">
                <div class="pdf-header">
                    <h3 class="pdf-title">
                        <i class="fas fa-file-pdf" style="color: #dc3545;"></i> 
                        เอกสารหลักสูตรระดับชั้นประถมศึกษา
                    </h3>
                    <div class="pdf-actions">
                        <a href="pdf/curriculum_primary.pdf" class="btn btn-custom" target="_blank">
                            <i class="fas fa-eye"></i> ดูเอกสาร
                        </a>
                        <a href="pdf/curriculum_primary.pdf" class="btn btn-outline-custom" download>
                            <i class="fas fa-download"></i> ดาวน์โหลด
                        </a>
                    </div>
                </div>

                <!-- PDF Embed -->
                <div style="background: white; border-radius: 10px; padding: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <embed src="pdf/curriculum_primary.pdf" type="application/pdf" width="100%" height="600px" />
                    <p class="text-muted text-center mt-3">
                        <i class="fas fa-info-circle"></i> หากไม่สามารถแสดง PDF ได้ กรุณา
                        <a href="pdf/curriculum_primary.pdf" target="_blank">คลิกที่นี่เพื่อเปิดในแท็บใหม่</a>
                    </p>
                </div>
            </div>
            
            <!-- Contact Section -->
            <div class="section-card text-center">
                <h3 style="color: #5cb85c; margin-bottom: 20px;">สนใจสมัครเรียน หรือต้องการข้อมูลเพิ่มเติม</h3>
                <p style="font-size: 1.1rem;">ติดต่อฝ่ายวิชาการ โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
                <div class="mt-4">
                    <a href="../contact.php" class="btn btn-custom btn-lg">
                        <i class="fas fa-phone"></i> ติดต่อเรา
                    </a>
                    <a href="../admission-info.php" class="btn btn-outline-custom btn-lg ms-2">
                        <i class="fas fa-user-plus"></i> ข้อมูลการรับสมัคร
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Include Footer -->
    <?php include '../footer.php'; ?>
    
    <!-- Back to top button -->
    <button onclick="topFunction()" id="backToTop" class="btn btn-custom" style="display: none; position: fixed; bottom: 20px; right: 30px; z-index: 99; border-radius: 50%; width: 50px; height: 50px;">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Back to top button
        window.onscroll = function() {scrollFunction()};
        
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("backToTop").style.display = "block";
            } else {
                document.getElementById("backToTop").style.display = "none";
            }
        }
        
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>

<!-- นำเข้าส่วนท้ายของเว็บไซต์ -->
<!-- ประกอบด้วย: ข้อมูลติดต่อ, ลิงก์โซเชียลมีเดีย, ลิขสิทธิ์, JavaScript libraries -->
<?php include_once '../footer.php'; ?>
</body>
</html>