<!-- แถบนำทางส่วนใหม่ (New Section Navigation Bar) -->
<!-- แถบนำทางนี้แสดงเมนูเพิ่มเติมที่เกี่ยวข้องกับการสมัครเรียน, วิดีโอ, ระบบบริหาร, และบริการต่างๆ -->
<nav class="navbar navbar-expand-lg new-section-navbar" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); margin-top: 0; position: relative; z-index: 100;">
    <!-- 
    สไตล์ inline ที่กำหนด:
    - background: พื้นหลังไล่ระดับสีจากสีหลักไปยังสีหลักอ่อน (จากซ้ายบนไปขวาล่าง)
    - margin-top: 0 - ไม่มีขอบด้านบน
    - position: relative - กำหนดตำแหน่งสัมพัทธ์
    - z-index: 100 - ลำดับการซ้อนทับ (ค่ายิ่งสูงยิ่งอยู่ด้านบน)
    -->
    
    <!-- container-fluid คือคอนเทนเนอร์ที่กว้างเต็มหน้าจอ, justify-content-center จัดให้เนื้อหาอยู่ตรงกลาง -->
    <div class="container-fluid justify-content-center">
        <!-- ส่วนเมนูที่จะยุบเมื่อหน้าจอเล็กลง แต่ตั้งค่า show ให้แสดงตลอด -->
        <div class="collapse navbar-collapse show" id="newSectionNavbar">
            <!-- รายการเมนูหลัก -->
            <ul class="navbar-nav">
                <!-- เมนูแบบดรอปดาวน์สำหรับการสมัครเรียน -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle" href="#" id="navApply" role="button" data-bs-toggle="dropdown">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์การลงนาม -->
                        <i class="fas fa-file-signature"></i> สมัครเรียน
                    </a>
                    <!-- รายการเมนูย่อยในดรอปดาวน์ -->
                    <ul class="dropdown-menu" aria-labelledby="navApply">
                        <!-- ลิงก์ไปยังหน้าสมัครเรียนระดับชั้นต่างๆ -->
                        <li><a class="dropdown-item" href="apply-primary.php">ชั้นประถมศึกษา</a></li>
                        <li><a class="dropdown-item" href="apply-secondary-early.php">ชั้นมัธยมศึกษาตอนต้น</a></li>
                        <li><a class="dropdown-item" href="apply-secondary-late.php">ชั้นมัธยมศึกษาตอนปลาย</a></li>
                        <li><a class="dropdown-item" href="apply-wmw.php">โครงการวมว</a></li>
                    </ul>
                </li>
                
                <!-- เมนูแบบดรอปดาวน์สำหรับวีดิทัศน์แนะนำ -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle" href="#" id="navVideos" role="button" data-bs-toggle="dropdown">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์วิดีโอ -->
                        <i class="fas fa-video"></i> วีดิทัศน์แนะนำ
                    </a>
                    <!-- รายการเมนูย่อยในดรอปดาวน์ -->
                    <ul class="dropdown-menu" aria-labelledby="navVideos">
                        <!-- ลิงก์ไปยังหน้าวีดิทัศน์แนะนำระดับชั้นต่างๆ -->
                        <li><a class="dropdown-item" href="video-primary-early.php">ประถมศึกษาตอนต้น</a></li>
                        <li><a class="dropdown-item" href="video-primary-late.php">ประถมศึกษาตอนปลาย</a></li>
                        <li><a class="dropdown-item" href="video-secondary-early.php">มัธยมศึกษาตอนต้น</a></li>
                        <li><a class="dropdown-item" href="video-secondary-late.php">มัธยมศึกษาตอนปลาย</a></li>
                        <li><a class="dropdown-item" href="video-wmw.php">โครงการวมว</a></li>
                    </ul>
                </li>

                <!-- เมนูแบบดรอปดาวน์สำหรับระบบบริหารงานวิชาการ -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle" href="#" id="navAcademicManagement" role="button" data-bs-toggle="dropdown">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์เฟือง (การตั้งค่า) -->
                        <i class="fas fa-cogs"></i> ระบบบริหารงานวิชาการ
                    </a>
                    <!-- รายการเมนูย่อยในดรอปดาวน์ -->
                    <ul class="dropdown-menu" aria-labelledby="navAcademicManagement">
                        <!-- ลิงก์ไปยังหน้าต่างๆ ของระบบบริหารงานวิชาการ -->
                        <li><a class="dropdown-item" href="academic-grade-calculation.php">การตัดเกรด</a></li>
                        <li><a class="dropdown-item" href="academic-grade-submission.php">การส่งเกรด</a></li>
                        <li><a class="dropdown-item" href="academic-grade-correction.php">การแก้ผลการเรียน</a></li>
                        <li><a class="dropdown-item" href="academic-status-0-r-ms.php">0 ร มส</a></li>
                        <li><a class="dropdown-item" href="academic-timetable.php">ตารางสอน</a></li>
                    </ul>
                </li>

                <!-- เมนูลิงก์เดียวสำหรับทุนการศึกษา -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าทุนการศึกษา -->
                    <a class="nav-link" href="scholarships.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์มือถือเงิน -->
                        <i class="fas fa-hand-holding-usd"></i> ทุนการศึกษา
                    </a>
                </li>

                <!-- เมนูแบบดรอปดาวน์สำหรับการนำเสนอผลงาน/การแข่งขัน -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle" href="#" id="navPresentations" role="button" data-bs-toggle="dropdown">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์ถ้วยรางวัล -->
                        <i class="fas fa-trophy"></i> การนำเสนอผลงาน/การแข่งขัน
                    </a>
                    <!-- รายการเมนูย่อยในดรอปดาวน์ -->
                    <ul class="dropdown-menu" aria-labelledby="navPresentations">
                        <!-- ลิงก์ไปยังหน้าต่างๆ ของการนำเสนอผลงานและการแข่งขัน -->
                        <li><a class="dropdown-item" href="presentation-ysc.php">YSC</a></li>
                        <li><a class="dropdown-item" href="presentation-robot.php">การแข่งหุ่นยนต์</a></li>
                        <li><a class="dropdown-item" href="presentation-drone.php">การแข่งโดรน</a></li>
                        <li><a class="dropdown-item" href="presentation-wmw-japan.php">นำเสนอวมว ไปประเทศญี่ปุ่น</a></li>
                        <li><a class="dropdown-item" href="presentation-wmw.php">นำเสนอ วมว</a></li>
                    </ul>
                </li>
                    
                <!-- เมนูลิงก์เดียวสำหรับ Green Office -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้า Green Office -->
                    <a class="nav-link" href="green-office.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์ใบไม้ -->
                        <i class="fas fa-leaf"></i> Green Office
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับ SAR (Self Assessment Report) -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้า SAR -->
                    <a class="nav-link" href="sar.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์กราฟเส้น -->
                        <i class="fas fa-chart-line"></i> SAR
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับหอพัก -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าหอพัก -->
                    <a class="nav-link" href="dormitory.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์อาคาร -->
                        <i class="fas fa-building"></i> หอพัก
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับประเมินภาระงาน -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าประเมินภาระงาน -->
                    <a class="nav-link" href="workload-assessment.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์รายการงาน -->
                        <i class="fas fa-tasks"></i> ประเมินภาระงาน
                    </a>
                </li>
            </ul>
            <!-- จบรายการเมนู -->
        </div>
        <!-- จบส่วน navbar-collapse -->
    </div>
    <!-- จบส่วน container -->
</nav>
<!-- จบแถบนำทางส่วนใหม่ -->
