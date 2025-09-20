<!DOCTYPE html>
<!-- ประกาศประเภทเอกสารเป็น HTML5 -->
<html lang="th">
<!-- เริ่มต้น HTML โดยกำหนดภาษาหลักเป็นภาษาไทย -->
<head>
    <!-- ส่วนหัวของเอกสาร HTML ประกอบด้วยข้อมูล meta, title, และการเชื่อมโยงไฟล์ต่างๆ -->
    <meta charset="UTF-8">
    <!-- กำหนดรหัสอักขระเป็น UTF-8 เพื่อรองรับภาษาไทยและอักขระพิเศษ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- กำหนดการแสดงผลให้รองรับอุปกรณ์มือถือ (Responsive) -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- กำหนดให้ Internet Explorer ใช้เครื่องมือแสดงผลล่าสุด -->
    <title>โรงเรียนสาธิตมหาวิทยาลัยพะเยา | Demonstration School of University of Phayao</title>
    <!-- กำหนดชื่อเว็บไซต์ที่จะแสดงบนแท็บของเบราว์เซอร์ -->
    
    <!-- Meta Tags สำหรับ SEO (Search Engine Optimization) -->
    <meta name="description" content="โรงเรียนสาธิตมหาวิทยาลัยพะเยา มุ่งมั่นพัฒนาการศึกษาระดับมัธยมศึกษา สร้างเยาวชนคุณภาพสู่อนาคตที่ยั่งยืน">
    <!-- คำอธิบายเว็บไซต์สำหรับแสดงในผลการค้นหา -->
    <meta name="keywords" content="โรงเรียนสาธิต, มหาวิทยาลัยพะเยา, การศึกษา, มัธยมศึกษา, พะเยา">
    <!-- คำสำคัญที่เกี่ยวข้องกับเว็บไซต์ -->
    <meta name="author" content="โรงเรียนสาธิตมหาวิทยาลัยพะเยา">
    <!-- ผู้เขียนหรือเจ้าของเว็บไซต์ -->
    <meta name="robots" content="index, follow">
    <!-- อนุญาตให้เครื่องมือค้นหาทำดัชนีและติดตามลิงก์ -->
    <meta name="theme-color" content="#8B7AA8">
    <!-- กำหนดสีธีมสำหรับเบราว์เซอร์มือถือ (สีม่วง) -->
    
    <!-- ไอคอนเว็บไซต์ (Favicon) -->
    <link rel="icon" type="image/x-icon" href="img/logo_up32x.ico">
    <!-- ไอคอนที่แสดงบนแท็บของเบราว์เซอร์ -->
    
    <!-- Google Fonts - นำเข้าฟอนต์จาก Google -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- ฟอนต์ Sarabun สำหรับข้อความทั่วไป น้ำหนักตั้งแต่ 300-700 -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- ฟอนต์ Kanit สำหรับหัวข้อ น้ำหนักตั้งแต่ 300-700 -->
    
    <!-- CSS Libraries - นำเข้าไลบรารี CSS จากภายนอก -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5.3.0 สำหรับระบบ Grid และ UI Components -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Font Awesome 6.4.0 สำหรับไอคอนต่างๆ -->
    
    <!-- Original CSS Files - ไฟล์ CSS ดั้งเดิมของเว็บไซต์ -->
    <link href="css/owl.carousel.min.css" rel="stylesheet">
    <!-- CSS สำหรับ Owl Carousel (สไลด์โชว์) -->
    <link href="css/owl.theme.default.min.css" rel="stylesheet">
    <!-- CSS สำหรับธีมเริ่มต้นของ Owl Carousel -->
    
    <!-- Custom School Theme CSS - ไฟล์ CSS ที่กำหนดเองสำหรับธีมโรงเรียน -->
    <link href="css/style-school.css" rel="stylesheet">
    <!-- CSS หลักสำหรับธีมโรงเรียน กำหนดสี ฟอนต์ และสไตล์ทั่วไป -->
    
    <!-- Hover Dropdown CSS - CSS สำหรับเมนูดรอปดาวน์แบบ Hover -->
    <link href="css/hover-dropdown.css" rel="stylesheet">
    <!-- CSS สำหรับทำให้เมนูดรอปดาวน์แสดงเมื่อนำเมาส์ไปชี้ -->
    
    <!-- Contact Fix CSS - แก้ไขตำแหน่งของ QR code และปุ่ม CONTACT US -->
    <link href="css/contact-fix.css" rel="stylesheet">
    <!-- CSS สำหรับแก้ไขปัญหาตำแหน่งที่ซ้อนทับกัน -->
    
    <!-- Button Position Fix CSS - แก้ไขตำแหน่งของปุ่มเลื่อนเพจและไอคอนแชท AI -->
    <link href="css/button-position-fix.css" rel="stylesheet">
    <!-- CSS สำหรับแก้ไขปัญหาปุ่มที่ซ้อนทับกัน -->
    
    <!-- Chatbot Animation CSS - เพิ่มการเคลื่อนไหวให้ปุ่มแชทบอท -->
    <link href="css/chatbot-animation.css" rel="stylesheet">
    <!-- CSS สำหรับเพิ่มแอนิเมชันให้ปุ่มแชทบอท -->
    
    <!-- jQuery - ไลบรารี JavaScript พื้นฐานที่จำเป็นสำหรับปลั๊กอินหลายตัว -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- jQuery เวอร์ชัน 3.7.0 -->

    <!-- MathJax - ไลบรารีสำหรับแสดงสูตรคณิตศาสตร์และสัญลักษณ์ทางวิทยาศาสตร์ -->
    <script type="text/javascript" id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
    </script>
    <!-- โหลด MathJax แบบ async เพื่อไม่ให้ขัดขวางการโหลดหน้าเว็บ -->
    <script>
        // การตั้งค่า MathJax
        MathJax = {
            tex: {
                inlineMath: [['$','$'], ['\\(','\\)']]
                // กำหนดให้ใช้ $ หรือ \( \) เพื่อเขียนสูตรคณิตศาสตร์แบบอินไลน์
            },
            // การแสดงผลเป็น Common HTML เหมาะสำหรับประสิทธิภาพและการจัดสไตล์ด้วย CSS
            // หากต้องการการแสดงผลเฉพาะสำหรับเบราว์เซอร์ (เช่น สำหรับเบราว์เซอร์เก่า) ให้ปรับค่านี้
            chtml: {
                matchFontHeight: false
                // ไม่ต้องปรับความสูงของฟอนต์ให้ตรงกัน
            },
            options: {
                // ตัวเลือกนี้ทำให้ MathJax ประมวลผลทั้งหน้า
                ignoreHtmlClass: 'noforprocess',
                // ข้ามองค์ประกอบที่มีคลาส 'noforprocess'
                processHtmlClass: 'process-mathjax'
                // ประมวลผลเฉพาะองค์ประกอบที่มีคลาส 'process-mathjax'
            }
        };
    </script>
</head>
<body>
<!-- เริ่มต้นส่วนเนื้อหาของเว็บไซต์ -->
    <!-- แถบด้านบน (Top Bar) - แสดงข้อมูลติดต่อและลิงก์โซเชียลมีเดีย -->
    <div class="top-bar">
        <!-- container-fluid ทำให้แถบกว้างเต็มหน้าจอ -->
        <div class="container-fluid">
            <!-- แถวที่จัดให้เนื้อหาอยู่ตรงกลางในแนวตั้ง -->
            <div class="row align-items-center">
                <!-- คอลัมน์ซ้าย ขนาด 6/12 สำหรับหน้าจอขนาดกลางขึ้นไป -->
                <div class="col-md-6">
                    <!-- ข้อมูลที่อยู่โรงเรียน -->
                    <div class="top-info">
                        <!-- ไอคอนหมุดปักตำแหน่ง -->
                        <i class="fas fa-map-marker-alt"></i>
                        <!-- ข้อความที่อยู่ -->
                        <span>โรงเรียนสาธิตมหาวิทยาลัยพะเยา จ.พะเยา</span>
                    </div>
                </div>
                <!-- คอลัมน์ขวา ขนาด 6/12 สำหรับหน้าจอขนาดกลางขึ้นไป จัดข้อความชิดขวา -->
                <div class="col-md-6 text-end">
                    <!-- ลิงก์ต่างๆ บนแถบด้านบน -->
                    <div class="top-links">
                        <!-- ลิงก์ไปยัง TikTok -->
                        <a href="https://www.tiktok.com/@desup_satitphayao?_t=ZS-8znTHBxQaDS&_r=1" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <!-- ไอคอน TikTok -->
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <!-- ลิงก์ไปยัง Facebook -->
                        <a href="https://www.facebook.com/desup.official" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <!-- ไอคอน Facebook -->
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <!-- ลิงก์ไปยัง YouTube -->
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <!-- ไอคอน YouTube -->
                            <i class="fab fa-youtube"></i>
                        </a>
                        <!-- ลิงก์ไปยัง Instagram -->
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <!-- ไอคอน Instagram -->
                            <i class="fab fa-instagram"></i>
                        </a>
                        <!-- ลิงก์โทรศัพท์ -->
                        <a href="tel:054466666" class="btn btn-sm btn-outline-light rounded-pill me-2">
                            <!-- ไอคอนโทรศัพท์ (ไม่ต้องเปิดหน้าใหม่เพราะเป็นการโทร) -->
                            <i class="fas fa-phone"></i> 054-466666
                        </a>
                        <!-- ลิงก์ไปยังระบบนักเรียน -->
                        <a href="https://academic.satit.up.ac.th/index#/teacher/processgradeinfo" class="btn btn-sm btn-primary rounded-pill" target="_blank" rel="noopener noreferrer">
                            <!-- ไอคอนนักเรียน -->
                            <i class="fas fa-user-graduate"></i> ระบบนักเรียน
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- จบแถบด้านบน -->
