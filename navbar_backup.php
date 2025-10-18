    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top main-navbar">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="images/logo@2x.png" alt="โรงเรียนสาธิตมหาวิทยาลัยพะเยา" height="60">
                <div class="brand-text">
                    <span class="brand-title">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</span>
                    <span class="brand-subtitle">Demonstration School of University of Phayao</span>
                </div>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> หน้าหลัก
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navAbout" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-school"></i> เกี่ยวกับเรา
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navAbout">
                            <li><a class="dropdown-item" href="about-history.php">ประวัติโรงเรียน</a></li>
                            <li><a class="dropdown-item" href="about-vision.php">วิสัยทัศน์ / พันธกิจ</a></li>
                            <li><a class="dropdown-item" href="about-director.php">ผู้อำนวยการ</a></li>
                            <li><a class="dropdown-item" href="about-structure.php">โครงสร้างองค์กร</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="staff/index.php">บุคลากร</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navAcademic" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-graduation-cap"></i> วิชาการ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navAcademic">
                            <li><a class="dropdown-item" href="academic-curriculum.php">หลักสูตร</a></li>
                            <li><a class="dropdown-item" href="academic-calendar.php">ปฏิทินการศึกษา</a></li>
                            <li><a class="dropdown-item" href="academic-schedule.php">ตารางเรียน</a></li>
                            <li><a class="dropdown-item" href="academic-exam.php">ตารางสอบ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="academic-results.php">ผลการเรียน</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navStudent" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users"></i> นักเรียน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navStudent">
                            <li><a class="dropdown-item" href="student-activities.php">กิจกรรมนักเรียน</a></li>
                            <li><a class="dropdown-item" href="student-council.php">สภานักเรียน</a></li>
                            <li><a class="dropdown-item" href="student-clubs.php">ชมรม</a></li>
                            <li><a class="dropdown-item" href="student-awards.php">ผลงานนักเรียน</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navAdmission" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-plus"></i> รับสมัคร
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navAdmission">
                            <li><a class="dropdown-item" href="admission-info.php">ข้อมูลการรับสมัคร</a></li>
                            <li><a class="dropdown-item" href="admission-schedule.php">กำหนดการ</a></li>
                            <li><a class="dropdown-item" href="admission-documents.php">เอกสารที่ใช้</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="admission-apply.php">
                                <strong>สมัครออนไลน์</strong>
                            </a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">
                            <i class="fas fa-newspaper"></i> ข่าวสาร
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-envelope"></i> ติดต่อ
                        </a>
                    </li>
                    
                    <!-- Search Button -->
                    <li class="nav-item">
                        <button class="btn btn-outline-primary btn-sm rounded-pill ms-2" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search"></i>
                        </button>
                    </li>
                    
                    <!-- Login Button -->
                    <li class="nav-item">
                        <a href="admin/login.php" class="btn btn-primary btn-sm rounded-pill ms-2">
                            <i class="fas fa-lock"></i> เข้าสู่ระบบ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Secondary Navigation Bar -->
    <nav class="navbar navbar-expand-lg secondary-navbar">
        <div class="container-fluid">
            <div class="navbar-collapse" id="secondaryNavbar">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="secondaryNavHome" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            หน้าหลัก
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="secondaryNavHome">
                            <li><a class="dropdown-item" href="#">ภาพพระบรมรูป</a></li>
                            <li><a class="dropdown-item" href="#">ภาพมหาวิทยาลัย</a></li>
                            <li><a class="dropdown-item" href="#">ภาพพระพุทธคชารักษ์</a></li>
                            <li><a class="dropdown-item" href="#">ภาพผู้บริหาร</a></li>
                            <li><a class="dropdown-item" href="#">ภาพนักเรียนประถมศึกษาตอนต้น</a></li>
                            <li><a class="dropdown-item" href="#">ภาพนักเรียนประถมศึกษาตอนปลาย</a></li>
                            <li><a class="dropdown-item" href="#">ภาพนักเรียนมัธยมศึกษาตอนต้น</a></li>
                            <li><a class="dropdown-item" href="#">ภาพนักเรียนมัธยมศึกษาตอนปลาย</a></li>
                            <li><a class="dropdown-item" href="#">ภาพนักเรียนโครงการ วมว.</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="secondaryNavAbout" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            เกี่ยวกับโรงเรียน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="secondaryNavAbout">
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">ข้อมูลทั่วไป</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">แนะนำโรงเรียน/ประวัติโรงเรียน</a></li>
                                    <li><a class="dropdown-item" href="#">วิสัยทัศน์ / พันธกิจ</a></li>
                                    <li><a class="dropdown-item" href="#">นโยบาย-เอกลักษณ์</a></li>
                                    <li><a class="dropdown-item" href="#">รายงานประจำปี</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">ข้อมูลผู้บริหาร</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">คณะกรรมการของโรงเรียน</a></li>
                                    <li><a class="dropdown-item" href="#">คณะกรรมการบริหาร</a></li>
                                    <li><a class="dropdown-item" href="#">โครงสร้างของโรงเรียน</a></li>
                                    <li><a class="dropdown-item" href="#">อำนาจหน้าที่</a></li>
                                    <li><a class="dropdown-item" href="#">แผนยุทธศาสตร์</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">ข้อมูลอื่น ๆ</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">ข้อมูลอื่น ๆ</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="secondaryNavCurriculum" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            หลักสูตร
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="secondaryNavCurriculum">
                            <li><a class="dropdown-item" href="#">ประถมศึกษาตอนต้น</a></li>
                            <li><a class="dropdown-item" href="#">ประถมศึกษาตอนปลาย</a></li>
                            <li><a class="dropdown-item" href="#">มัธยมศึกษาตอนต้น</a></li>
                            <li><a class="dropdown-item" href="#">มัธยมศึกษาตอนปลาย</a></li>
                            <li><a class="dropdown-item" href="#">โครงการ วมว.</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="secondaryNavPersonnel" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            บุคลากร
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="secondaryNavPersonnel">
                            <li><a class="dropdown-item" href="staff/index.php">บุคลากรทั้งหมด</a></li>
                            <li><a class="dropdown-item" href="staff/academic.php">บุคลากรสายวิชาการ</a></li>
                            <li><a class="dropdown-item" href="staff/primary.php">บุคลากรประถมศึกษา</a></li>
                            <li><a class="dropdown-item" href="staff/service.php">บุคลากรสายสนับสนุน</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="secondaryNavLab" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ห้องปฏิบัติการ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="secondaryNavLab">
                            <li><a class="dropdown-item" href="#">ภาพห้องปฏิบัติการ</a></li>
                            <li><a class="dropdown-item" href="#">เครื่องมือ</a></li>
                            <li><a class="dropdown-item" href="#">มาตรฐานห้องปฏิบัติการ</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">สมาคมศิษย์เก่าและผู้ปกครอง</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">ติดต่อร้องเรียน/ติชม</a>
                    </li>
                    
                    <!-- Teacher Section Placeholder -->
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link teacher-placeholder" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="ยังไม่เปิดให้บริการ">
                            <i class="fas fa-chalkboard-teacher me-2"></i> ส่วนของครู
                            <span class="badge bg-warning ms-2">Soon</span>
                            <span class="cross-out"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">ค้นหาข้อมูล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="search.php" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="q" placeholder="พิมพ์คำค้นหา..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>