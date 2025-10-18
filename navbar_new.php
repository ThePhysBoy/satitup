<!-- Main Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light main-navbar" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1030;">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo@2x.png" alt="โรงเรียนสาธิตมหาวิทยาลัยพะเยา" height="60">
            <div class="ms-3">
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--primary-color, #8B7AA8); line-height: 1.2;">
                    โรงเรียนสาธิตมหาวิทยาลัยพะเยา
                </div>
                <div style="font-size: 0.8rem; color: #666; line-height: 1.2;">
                    Demonstration School of University of Phayao
                </div>
            </div>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <!-- หน้าหลัก -->
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-home"></i> หน้าหลัก
                    </a>
                </li>
                
                <!-- เกี่ยวกับเรา Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navAbout" role="button" data-bs-toggle="dropdown">
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
                
                <!-- วิชาการ Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navAcademic" role="button" data-bs-toggle="dropdown">
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
                
                <!-- นักเรียน Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navStudent" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-users"></i> นักเรียน
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navStudent">
                        <li><a class="dropdown-item" href="student-activities.php">กิจกรรมนักเรียน</a></li>
                        <li><a class="dropdown-item" href="student-council.php">สภานักเรียน</a></li>
                        <li><a class="dropdown-item" href="student-clubs.php">ชมรม</a></li>
                        <li><a class="dropdown-item" href="student-awards.php">ผลงานนักเรียน</a></li>
                    </ul>
                </li>
                
                <!-- รับสมัคร Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navAdmission" role="button" data-bs-toggle="dropdown">
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
                
                <!-- ข่าวสาร -->
                <li class="nav-item">
                    <a class="nav-link" href="news.php">
                        <i class="fas fa-newspaper"></i> ข่าวสาร
                    </a>
                </li>
                
                <!-- ติดต่อ -->
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">
                        <i class="fas fa-envelope"></i> ติดต่อ
                    </a>
                </li>
                
                <!-- Search Button -->
                <li class="nav-item ms-2">
                    <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i>
                    </button>
                </li>
                
                <!-- Login Button -->
                <li class="nav-item ms-2">
                    <a href="admin/login.php" class="btn btn-primary btn-sm rounded-pill">
                        <i class="fas fa-lock"></i> เข้าสู่ระบบ
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Secondary Navigation Bar -->
<nav class="navbar navbar-expand-lg secondary-navbar" style="background: linear-gradient(135deg, var(--primary-color, #8B7AA8), var(--primary-light, #A698BC)); margin-top: 0; position: relative; z-index: 100;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse show" id="secondaryNavbar">
            <ul class="navbar-nav w-100 d-flex justify-content-between">
                <!-- หน้าหลัก Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavHome" role="button" data-bs-toggle="dropdown">
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
                
                <!-- เกี่ยวกับโรงเรียน Dropdown with Submenu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavAbout" role="button" data-bs-toggle="dropdown">
                        เกี่ยวกับโรงเรียน
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="secondaryNavAbout">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลทั่วไป</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">แนะนำโรงเรียน/ประวัติโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">วิสัยทัศน์ / พันธกิจ</a></li>
                                <li><a class="dropdown-item" href="#">นโยบาย-เอกลักษณ์</a></li>
                                <li><a class="dropdown-item" href="#">รายงานประจำปี</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลผู้บริหาร</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">คณะกรรมการของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">คณะกรรมการบริหาร</a></li>
                                <li><a class="dropdown-item" href="#">โครงสร้างของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">อำนาจหน้าที่</a></li>
                                <li><a class="dropdown-item" href="#">แผนยุทธศาสตร์</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลอื่น ๆ</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">ข้อมูลอื่น ๆ</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <!-- หลักสูตร Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavCurriculum" role="button" data-bs-toggle="dropdown">
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
                
                <!-- บุคลากร Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavPersonnel" role="button" data-bs-toggle="dropdown">
                        บุคลากร
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="secondaryNavPersonnel">
                        <li><a class="dropdown-item" href="staff/index.php">บุคลากรทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="staff/academic.php">บุคลากรสายวิชาการ</a></li>
                        <li><a class="dropdown-item" href="staff/primary.php">บุคลากรประถมศึกษา</a></li>
                        <li><a class="dropdown-item" href="staff/service.php">บุคลากรสายสนับสนุน</a></li>
                    </ul>
                </li>
                
                <!-- ห้องปฏิบัติการ Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavLab" role="button" data-bs-toggle="dropdown">
                        ห้องปฏิบัติการ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="secondaryNavLab">
                        <li><a class="dropdown-item" href="#">ภาพห้องปฏิบัติการ</a></li>
                        <li><a class="dropdown-item" href="#">เครื่องมือ</a></li>
                        <li><a class="dropdown-item" href="#">มาตรฐานห้องปฏิบัติการ</a></li>
                    </ul>
                </li>
                
                <!-- สมาคมศิษย์เก่า -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">สมาคมศิษย์เก่าและผู้ปกครอง</a>
                </li>
                
                <!-- ติดต่อร้องเรียน -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">ติดต่อร้องเรียน/ติชม</a>
                </li>
                
                <!-- ส่วนของครู -->
                <li class="nav-item ms-lg-3">
                    <a class="nav-link text-white" href="#" style="opacity: 0.7; text-decoration: line-through;">
                        <i class="fas fa-chalkboard-teacher me-2"></i> ส่วนของครู
                        <span class="badge bg-warning ms-2">Soon</span>
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

<!-- CSS สำหรับ Navbar -->
<style>
/* Main Navbar Styles */
.main-navbar .nav-link {
    color: #333;
    font-weight: 500;
    padding: 8px 15px;
    transition: all 0.3s ease;
}

.main-navbar .nav-link:hover {
    color: var(--primary-color, #8B7AA8);
}

.main-navbar .nav-link.active {
    color: var(--primary-color, #8B7AA8);
    font-weight: 600;
}

/* Secondary Navbar Styles */
.secondary-navbar .nav-link {
    font-weight: 500;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.secondary-navbar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

/* Dropdown Styles */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.dropdown-item {
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: rgba(139, 122, 168, 0.1);
    color: var(--primary-color, #8B7AA8);
}

/* Submenu Styles */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -1px;
}

.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}

/* Mobile Responsive */
@media (max-width: 991px) {
    .secondary-navbar .navbar-nav {
        flex-direction: column;
    }
    
    .dropdown-submenu .dropdown-menu {
        left: 0;
        margin-left: 20px;
    }
}
</style>
