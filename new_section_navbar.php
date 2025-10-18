<!-- แถบนำทางส่วนใหม่ (New Section Navigation Bar) -->
<!-- แถบนำทางนี้แสดงเมนูเพิ่มเติมที่เกี่ยวข้องกับการสมัครเรียน, วิดีโอ, ระบบบริหาร, และบริการต่างๆ -->
<nav class="navbar navbar-expand-lg new-section-navbar" style="background: linear-gradient(135deg, #8B7AA8, #A698BC); margin-top: 0; position: relative; z-index: 100;">
    <!-- 
    สไตล์ inline ที่กำหนด:
    - background: พื้นหลังไล่ระดับสีมวง (#8B7AA8 ไป #A698BC)
    - margin-top: 0 - ไม่มีขอบด้านบน
    - position: relative - กำหนดตำแหน่งสัมพัทธ์
    - z-index: 100 - ลำดับการซ้อนทับ (ค่ายิ่งสูงยิ่งอยู่ด้านบน)
    -->
    
    <!-- container-fluid คือคอนเทนเนอร์ที่กว้างเต็มหน้าจอ -->
    <div class="container-fluid">
        <!-- ส่วนเมนูที่จะยุบเมื่อหน้าจอเล็กลง แต่ตั้งค่า show ให้แสดงตลอด -->
        <div class="collapse navbar-collapse show" id="newSectionNavbar">
            <!-- รายการเมนูหลัก - กระจายเต็มพื้นที่ -->
            <ul class="navbar-nav w-100 d-flex justify-content-between">
                <!-- เมนูแบบดรอปดาวน์สำหรับการสมัครเรียน -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navApply" role="button" data-bs-toggle="dropdown">
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
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navVideos" role="button" data-bs-toggle="dropdown">
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
                    <a class="nav-link dropdown-toggle text-white" href="https://academic.satit.up.ac.th/index#/teacher/processgradeinfo" target="_blank">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์เฟือง (การตั้งค่า) -->
                        <i class="fas fa-cogs"></i> ระบบบริหารงานวิชาการ
                    </a>
                </li>

                <!-- เมนูลิงก์เดียวสำหรับทุนการศึกษา -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าทุนการศึกษา -->
                    <a class="nav-link text-white" href="scholarships.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์มือถือเงิน -->
                        <i class="fas fa-hand-holding-usd"></i> ทุนการศึกษา
                    </a>
                </li>

                <!-- เมนูแบบดรอปดาวน์สำหรับการนำเสนอผลงาน/การแข่งขัน -->
                <li class="nav-item dropdown">
                    <!-- ลิงก์หลักที่เปิดเมนูดรอปดาวน์ -->
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navPresentations" role="button" data-bs-toggle="dropdown">
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
                    <a class="nav-link text-white" href="green-office.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์ใบไม้ -->
                        <i class="fas fa-leaf"></i> Green Office
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับ SAR (Self Assessment Report) -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้า SAR -->
                    <a class="nav-link text-white" href="sar.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์กราฟเส้น -->
                        <i class="fas fa-chart-line"></i> SAR
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับหอพัก -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าหอพัก -->
                    <a class="nav-link text-white" href="dormitory.php">
                        <!-- ไอคอน Font Awesome แสดงสัญลักษณ์อาคาร -->
                        <i class="fas fa-building"></i> หอพัก
                    </a>
                </li>
                
                <!-- เมนูลิงก์เดียวสำหรับประเมินภาระงาน -->
                <li class="nav-item">
                    <!-- ลิงก์ไปยังหน้าประเมินภาระงาน -->
                    <a class="nav-link text-white" href="workload-assessment.php">
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
