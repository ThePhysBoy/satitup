<!-- Main Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light main-navbar" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1030;">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>index.php">
            <!-- <img src="images/logo@2x.png" alt="โรงเรียนสาธิตมหาวิทยาลัยพะเยา" height="60"> -->
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
                <!-- สาธิตประถม - วมว. -->
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>academic_programs.php">
                        <i class="fas fa-graduation-cap"></i> สาธิตประถม
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>academic_programs.php">
                        <i class="fas fa-graduation-cap"></i>โครงการ วมว.
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>academic_programs.php">
                        <i class="fas fa-graduation-cap"></i>องค์การนักเรียน
                    </a>
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
<nav class="navbar navbar-expand-lg secondary-navbar" style="background: linear-gradient(135deg, #8B7AA8, #A698BC); margin-top: 0; position: relative; z-index: 100;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse show" id="secondaryNavbar">
            <ul class="navbar-nav w-100 d-flex justify-content-between">
                <!-- สาธิตประถม - วมว. -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>index.php">
                      หน้าหลัก
                    </a>
                </li>
                
                <!-- เกี่ยวกับโรงเรียน Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavAbout" role="button" data-bs-toggle="dropdown">
                        เกี่ยวกับโรงเรียน
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="secondaryNavAbout">
                        <!-- กลุ่ม: ข้อมูลทั่วไป -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลทั่วไป</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>about-history.php">แนะนำโรงเรียน/ประวัติโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>about-vision.php">วิสัยทัศน์ / พันธกิจ</a></li>
                                <li><a class="dropdown-item" href="#">นโยบาย-เอกลักษณ์</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>org-structure.php" target="_blank">โครงสร้างของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">รายงานประจำปี</a></li>
                            </ul>
                        </li>
                        
                        <!-- กลุ่ม: ข้อมูลผู้บริหาร -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลผู้บริหาร</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>steering_committee.php">คณะกรรมการอำนวยการ</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>about-management.php">คณะกรรมการบริหาร</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>org-structure.php" target="_blank">โครงสร้างของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">อำนาจหน้าที่</a></li>
                                <li><a class="dropdown-item" href="#">แผนยุทธศาสตร์</a></li>
                            </ul>
                        </li>
                        
                        <!-- กลุ่ม: ข้อมูลอื่น ๆ -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">ข้อมูลอื่น ๆ</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">คณะกรรมการของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>about-management.php">คณะกรรมการบริหาร</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>steering_committee.php">คณะกรรมการอำนวยการ</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '../' : ''; ?>org-structure.php" target="_blank">โครงสร้างของโรงเรียน</a></li>
                                <li><a class="dropdown-item" href="#">อำนาจหน้าที่</a></li>
                                <li><a class="dropdown-item" href="#">แผนยุทธศาสตร์</a></li>
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
                        <li><a class="dropdown-item" href="curriculum/pdf/curriculum_primary.pdf" target="_blank">หลักสูตรประถมศึกษา</a></li>
                        <li><a class="dropdown-item" href="curriculum/pdf/curriculum_arts_science_lower.pdf" target="_blank">ศิลปวิทยาศาสตร์ ม.ต้น</a></li>
                        <li><a class="dropdown-item" href="curriculum/pdf/curriculum_arts_science_upper.pdf" target="_blank">ศิลปวิทยาศาสตร์ ม.ปลาย</a></li>
                        <li><a class="dropdown-item" href="curriculum/pdf/curriculum_scius.pdf" target="_blank">โครงการ วมว.มพ.</a></li>
                    </ul>
                </li>
                
                <!-- บุคลากร Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="secondaryNavPersonnel" role="button" data-bs-toggle="dropdown">
                        บุคลากร
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="secondaryNavPersonnel">
                        <!-- บุคลากรสายวิชาการ -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">บุคลากรสายวิชาการ</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_science.php" target="_blank">วิทยาศาสตร์และเทคโนโลยี</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_math.php" target="_blank">คณิตศาสตร์</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_foreign.php" target="_blank">ภาษาต่างประเทศ</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_thai.php" target="_blank">ภาษาไทย</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_social.php" target="_blank">สังคมศึกษา ศาสนาและวัฒนธรรม</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_pe.php" target="_blank">สุขศึกษาและพลศึกษา</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_arts.php" target="_blank">ศิลปะ</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_counseling.php" target="_blank">แนะแนว</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_occupation.php" target="_blank">การงานอาชีพ</a></li>
                            </ul>
                        </li>
                        <!-- บุคลากรสายสนับสนุน -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">บุคลากรสายสนับสนุน</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>administration.php" target="_blank">งานบริหารทั่วไป</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>academic_support.php" target="_blank">งานวิชาการ</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>student_affairs.php" target="_blank">งานกิจการนักเรียน</a></li>
                                <li><a class="dropdown-item" href="<?php echo (basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'staff') ? '' : ((basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'curriculum' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) == 'admin') ? '../staff/' : 'staff/'); ?>planning.php" target="_blank">งานแผนงาน</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <!-- ห้องปฏิบัติการ -->
                <li class="nav-item">
                <a class="nav-link text-white" href="https://sites.google.com/view/labsatitup" id="secondaryNavLab" role="button" target="_blank">
                        ห้องปฏิบัติการ
                    </a>
                </li>
                
                <!-- สมาคมศิษย์เก่า -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">สมาคมศิษย์เก่าและผู้ปกครอง</a>
                </li>
                
                <!-- ติดต่อร้องเรียน -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">ติดต่อเรา</a>
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

/* ============================================= */
/* GOLDEN EFFECT STYLES FOR NAVBAR LINKS */
/* ============================================= */

/* Golden effect for navbar links */
.navbar-nav .nav-link {
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Golden animated border - placed outside link */
.navbar-nav .nav-link::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 8px;
    background: linear-gradient(45deg,
        #FFB74D, #FF9800, #FFC107, #FFD700,
        #FFEB3B, #FFC107, #FFB74D
    );
    background-size: 300% 300%;
    opacity: 0;
    transition: opacity 0.3s ease;
    animation: nav-gradient-animation 3s ease infinite;
    z-index: -1;
    pointer-events: none;
}

@keyframes nav-gradient-animation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Show golden border on hover */
.navbar-nav .nav-link:hover::before {
    opacity: 1;
}

/* Shine sweep effect for navbar */
.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 215, 0, 0.4) 50%,
        transparent 70%
    );
    transform: rotate(45deg) translateX(-100%);
    transition: transform 0.5s;
    opacity: 0;
    pointer-events: none;
}

.navbar-nav .nav-link:hover::after {
    transform: rotate(45deg) translateX(100%);
    opacity: 1;
}

/* Main hover transformation for navbar links */
.navbar-nav .nav-link:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow:
        0 10px 25px rgba(255, 152, 0, 0.2),
        0 0 30px rgba(255, 193, 7, 0.15),
        0 0 50px rgba(255, 235, 59, 0.1);
    text-shadow:
        0 0 10px rgba(255, 235, 59, 0.4),
        0 0 20px rgba(255, 193, 7, 0.25);
    animation: nav-glow-pulse 2s ease-in-out infinite;
}

/* Text golden gradient on hover */
.navbar-nav .nav-link:hover {
    background: linear-gradient(45deg, #FFB74D, #FF9800, #FFC107);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
}

/* Icon glow effect */
.navbar-nav .nav-link:hover i {
    color: #FFC107;
    text-shadow:
        0 0 10px rgba(255, 235, 59, 0.6),
        0 0 20px rgba(255, 193, 7, 0.4);
    animation: nav-icon-pulse 1.5s ease-in-out infinite;
}

@keyframes nav-icon-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Glow pulse animation for navbar */
@keyframes nav-glow-pulse {
    0%, 100% {
        box-shadow:
            0 10px 25px rgba(255, 152, 0, 0.2),
            0 0 30px rgba(255, 193, 7, 0.15),
            0 0 50px rgba(255, 235, 59, 0.1);
    }
    50% {
        box-shadow:
            0 15px 35px rgba(255, 152, 0, 0.35),
            0 0 50px rgba(255, 193, 7, 0.25),
            0 0 70px rgba(255, 235, 59, 0.2);
    }
}

/* Special effect for active nav links */
.navbar-nav .nav-link.active {
    position: relative;
    color: var(--primary-color, #8B7AA8);
    font-weight: 600;
}

/* ============================================= */
/* GOLDEN EFFECT FOR NAVBAR BUTTONS */
/* ============================================= */

/* Search and Login buttons */
.navbar .btn {
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Golden border for buttons */
.navbar .btn::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: calc(0.375rem + 2px);
    background: linear-gradient(45deg,
        #FFB74D, #FF9800, #FFC107, #FFD700,
        #FFEB3B, #FFC107, #FFB74D
    );
    background-size: 300% 300%;
    opacity: 0;
    transition: opacity 0.3s ease;
    animation: btn-gradient-animation 3s ease infinite;
    z-index: -1;
}

@keyframes btn-gradient-animation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Show golden border on button hover */
.navbar .btn:hover::before {
    opacity: 1;
}

/* Shine effect for buttons */
.navbar .btn::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 215, 0, 0.4) 50%,
        transparent 70%
    );
    transform: rotate(45deg) translateX(-100%);
    transition: transform 0.5s;
    opacity: 0;
    pointer-events: none;
}

.navbar .btn:hover::after {
    transform: rotate(45deg) translateX(100%);
    opacity: 1;
}

/* Button hover effects */
.navbar .btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow:
        0 8px 20px rgba(255, 152, 0, 0.3),
        0 0 30px rgba(255, 193, 7, 0.25),
        0 0 50px rgba(255, 235, 59, 0.15);
    animation: btn-glow-pulse 2s ease-in-out infinite;
}

/* Button glow pulse animation */
@keyframes btn-glow-pulse {
    0%, 100% {
        box-shadow:
            0 8px 20px rgba(255, 152, 0, 0.3),
            0 0 30px rgba(255, 193, 7, 0.25),
            0 0 50px rgba(255, 235, 59, 0.15);
    }
    50% {
        box-shadow:
            0 12px 30px rgba(255, 152, 0, 0.45),
            0 0 50px rgba(255, 193, 7, 0.35),
            0 0 70px rgba(255, 235, 59, 0.25);
    }
}

/* Icon glow in buttons */
.navbar .btn:hover i {
    color: #FFC107;
    text-shadow:
        0 0 10px rgba(255, 235, 59, 0.6),
        0 0 20px rgba(255, 193, 7, 0.4);
    animation: btn-icon-pulse 1.5s ease-in-out infinite;
}

@keyframes btn-icon-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Special styles for different button types */
.navbar .btn-outline-primary:hover {
    color: #fff;
    background: linear-gradient(45deg, #FFB74D, #FF9800, #FFC107);
    border-color: transparent;
}

.navbar .btn-primary:hover {
    background: linear-gradient(45deg, #FFB74D, #FF9800, #FFC107);
    border-color: transparent;
    box-shadow:
        0 8px 20px rgba(255, 152, 0, 0.4),
        0 0 30px rgba(255, 193, 7, 0.3),
        0 0 50px rgba(255, 235, 59, 0.2);
}

/* Bootstrap 5 Dropdown Submenu Fix */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu > .dropdown-menu {
    position: absolute;
    top: 0;
    left: 100%;
    margin-top: -1px;
    margin-left: 0;
    display: none;
    min-width: 220px;
    z-index: 1030;
    background: white;
    border: 1px solid rgba(0,0,0,.15);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}

.dropdown-submenu > .dropdown-toggle::after {
    content: "▸";
    margin-left: 0.5rem;
    border: none;
    float: right;
    font-size: 0.75rem;
}

/* Mobile behavior */
@media (max-width: 991px) {
    .dropdown-submenu > .dropdown-menu {
        position: static;
        margin-left: 1rem;
        width: 100%;
    }

    .dropdown-submenu > .dropdown-toggle::after {
        content: "▾";
    }

    /* Disable golden effects on mobile for performance */
    .navbar-nav .nav-link::before,
    .navbar-nav .nav-link::after,
    .navbar .btn::before,
    .navbar .btn::after {
        display: none !important;
    }

    .navbar-nav .nav-link:hover {
        transform: none;
        box-shadow: none;
        background: rgba(139, 122, 168, 0.1);
        border-radius: 4px;
    }
}
</style>

<!-- JavaScript สำหรับ Dropdown Submenu (Simple Hover) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ไม่ต้องใช้ JavaScript สำหรับ Desktop ให้ CSS hover จัดการ
    // ลิงค์ภายใน submenu สามารถคลิกได้ตามปกติ
});
</script>