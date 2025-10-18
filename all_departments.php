<?php
/**
 * หน้ารวมหน่วยงานทั้งหมด
 * All Departments Page
 *
 * แสดงหน่วยงานแบ่งตามหมวดหมู่:
 * 1. สำนักงานสภามหาวิทยาลัย
 * 2. ส่วนงานบริหารมหาวิทยาลัยพะเยา
 * 3. ส่วนงานวิชาการ (คณะทั้งหมด)
 * 4. ส่วนงานอื่น
 *
 * พัฒนาโดย: ทีมพัฒนาเว็บไซต์โรงเรียนสาธิต
 * วันที่: <?php echo date('Y-m-d'); ?>
 */

// รวมไฟล์ header และ navigation จาก index.php
include_once 'header.php';
include_once 'navbar.php';
?>

<!-- Page Header -->
<section class="page-header hero-modern text-white">
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="hero-content">
                    <span class="hero-badge d-inline-flex align-items-center mb-3">
                        <i class="fas fa-sitemap me-2"></i>
                        โครงสร้างหน่วยงานมหาวิทยาลัยพะเยา
                    </span>
                    <h1 class="display-5 fw-bold mb-3">สำรวจหน่วยงานทั้งหมด</h1>
                    <p class="lead text-white-75 mb-4">เข้าถึงข้อมูลหน่วยงานทุกหมวด ทั้งหน่วยงานบริหาร วิชาการ และส่วนงานอื่นๆ ของมหาวิทยาลัยพะเยาได้ในที่เดียว</p>
                    <div class="hero-actions d-flex flex-wrap align-items-center gap-3">
                        <a href="#departmentSearchArea" class="btn btn-light btn-lg rounded-pill px-4 shadow-sm">
                            <i class="fas fa-search me-2"></i> เริ่มค้นหา
                        </a>
                        <div class="hero-meta ms-lg-3">
                            <span class="d-block text-white-50 small">อัปเดตล่าสุด <?php echo date('F Y'); ?></span>
                            <span class="d-block fw-semibold"><i class="fas fa-building me-2"></i>รวม 40 หน่วยงาน</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card shadow-lg">
                    <div class="hero-card-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <h3 class="fw-semibold mb-3">หมวดหมู่หน่วยงาน</h3>
                    <ul class="hero-list list-unstyled mb-0">
                        <li><i class="fas fa-check-circle me-2"></i>สำนักงานสภามหาวิทยาลัย</li>
                        <li><i class="fas fa-check-circle me-2"></i>ส่วนงานบริหารมหาวิทยาลัยพะเยา</li>
                        <li><i class="fas fa-check-circle me-2"></i>ส่วนงานวิชาการ (คณะทั้งหมด)</li>
                        <li><i class="fas fa-check-circle me-2"></i>ส่วนงานอื่น ๆ</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="hero-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-75">หน้าแรก</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">หน่วยงานทั้งหมด</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="hero-shape"></div>
</section>

<!-- Search and Filter Section -->
<section id="departmentSearchArea" class="search-filter-section py-4" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="search-box mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="departmentSearch" placeholder="ค้นหาหน่วยงาน... เช่น กองคลัง, คณะแพทยศาสตร์" aria-label="ค้นหาหน่วยงาน">
                        <div class="input-group-append">
                            <span class="input-group-text search-icon">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-buttons text-center">
                    <button class="btn btn-outline-primary filter-btn active mx-1" data-filter="ทั้งหมด">
                        <i class="fas fa-th-large"></i> ทั้งหมด
                    </button>
                    <button class="btn btn-outline-primary filter-btn mx-1" data-filter="ส่วนงานบริหาร">
                        <i class="fas fa-building"></i> ส่วนงานบริหาร
                    </button>
                    <button class="btn btn-outline-primary filter-btn mx-1" data-filter="ส่วนงานวิชาการ">
                        <i class="fas fa-graduation-cap"></i> ส่วนงานวิชาการ
                    </button>
                    <button class="btn btn-outline-primary filter-btn mx-1" data-filter="ส่วนงานอื่น">
                        <i class="fas fa-cogs"></i> ส่วนงานอื่น ๆ
                    </button>
                </div>
                <div id="departmentNoResults" class="text-center text-muted d-none mt-4">
                    <i class="fas fa-info-circle me-2"></i> ไม่พบหน่วยงานที่ตรงกับคำค้นหา
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content py-5">
    <div class="container">

        <!-- University Council Office -->
        <section class="department-section mb-5" data-category="ส่วนงานอื่น">
            <div class="section-header mb-4">
                <h2 class="section-title h3">🏛️ สำนักงานสภามหาวิทยาลัย</h2>
                <div class="section-divider" style="height: 3px; background: linear-gradient(90deg, var(--primary-color, #8B7AA8), transparent); margin: 10px 0 20px 0; border-radius: 2px;"></div>
            </div>

            <div class="row">
                <!-- สำนักงานสภามหาวิทยาลัย -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image">
                            <img src="images/faculties/สำนักงานสภา-1.jpg" alt="สำนักงานสภามหาวิทยาลัย">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">สำนักงานสภามหาวิทยาลัย</h5>
                            <p class="card-text text-muted small">University Council Office</p>
                            <a href="https://council-new.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- Administrative Departments -->
        <section class="department-section mb-5" data-category="ส่วนงานบริหาร">
            <div class="section-header mb-4">
                <h2 class="section-title h3">ส่วนงานบริหารมหาวิทยาลัยพะเยา</h2>
                <div class="section-divider" style="height: 3px; background: linear-gradient(90deg, var(--primary-color, #8B7AA8), transparent); margin: 10px 0 20px 0; border-radius: 2px;"></div>
            </div>

            <div class="row">
            <!-- สำนักงานอธิการบดี -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="department-card card h-100 shadow-sm">
                    <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                        <img src="images/faculties/สำนักงานอธิการบดี-1-500x270.jpg" alt="สำนักงานอธิการบดี">
                    </div>
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">สำนักงานอธิการบดี</h5>
                        <p class="card-text text-muted small">President Office</p>
                        <a href="http://www.op.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                        </a>
                    </div>
                </div>
            </div>

            <!-- กองกฎหมาย -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="department-card card h-100 shadow-sm">
                    <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                        <img src="images/faculties/กองกฏหมาย-500x270.jpg" alt="กองกฎหมาย">
                    </div>
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">กองกฎหมาย</h5>
                        <p class="card-text text-muted small">Legal Affairs Division</p>
                        <a href="https://www.lp.up.ac.th/th/main/default" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                        </a>
                    </div>
                </div>
            </div>

                <!-- กองกลาง -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/LINE_ALBUM_2024.4.3_240409_1-768.jpg" alt="กองกลาง">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองกลาง</h5>
                            <p class="card-text text-muted small">Central Division</p>
                            <a href="https://doga.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองการเจ้าหน้าที่ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/hru.jpg" alt="กองการเจ้าหน้าที่">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองการเจ้าหน้าที่</h5>
                            <p class="card-text text-muted small">Personnel Division</p>
                            <a href="https://personnel.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองกิจการนิสิต -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/dsa.jpg" alt="กองกิจการนิสิต">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองกิจการนิสิต</h5>
                            <p class="card-text text-muted small">Student Affairs Division</p>
                            <a href="https://dsa.up.ac.th/v4/main.php" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองคลัง -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/finance.jpg" alt="กองคลัง">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองคลัง</h5>
                            <p class="card-text text-muted small">Finance Division</p>
                            <a href="https://doga.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองทรัพย์สิน -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/กองทรัพย์สิน-500x270.jpg" alt="กองทรัพย์สิน">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองทรัพย์สิน</h5>
                            <p class="card-text text-muted small">Property Division</p>
                            <a href="https://pd.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองบริการการศึกษา -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/does-1.jpg" alt="กองบริการการศึกษา">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองบริการการศึกษา</h5>
                            <p class="card-text text-muted small">Educational Services Division</p>
                            <a href="https://itsc.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองบริหารงานวิจัย -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/dra.jpg" alt="กองบริหารงานวิจัย">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองบริหารงานวิจัย</h5>
                            <p class="card-text text-muted small">Research Administration Division</p>
                            <a href="http://dra.up.ac.th/updra/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองแผนงาน -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/LINE_ALBUM_2024.4.3_240409_11.jpg" alt="กองแผนงาน">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองแผนงาน</h5>
                            <p class="card-text text-muted small">Planning Division</p>
                            <a href="https://plan.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองพัฒนาคุณภาพนิสิตและนิสิตพิการ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/โลโก้-สีม่วง-ทอง-500x313.jpg" alt="กองพัฒนาคุณภาพนิสิตและนิสิตพิการ">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองพัฒนาคุณภาพนิสิตและนิสิตพิการ</h5>
                            <p class="card-text text-muted small">Student Development Division</p>
                            <a href="https://dsq.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- กองอาคารสถานที่ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/กองอาคาร-500x270.jpg" alt="กองอาคารสถานที่">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">กองอาคารสถานที่</h5>
                            <p class="card-text text-muted small">Building and Facilities Division</p>
                            <a href="https://building.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ศูนย์บริการเทคโนโลยีสารสนเทศและการสื่อสาร -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/Citcom-500x270.jpg" alt="ศูนย์บริการเทคโนโลยีสารสนเทศและการสื่อสาร">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">ศูนย์บริการเทคโนโลยีสารสนเทศและการสื่อสาร</h5>
                            <p class="card-text text-muted small">IT Service Center</p>
                            <a href="https://citcoms.up.ac.th/#gsc.tab=0" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ศูนย์สิ่งแวดล้อมและการจัดการที่ยั่งยืน -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/ดกดก.jpg" alt="ศูนย์สิ่งแวดล้อมและการจัดการที่ยั่งยืน">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">ศูนย์สิ่งแวดล้อมและการจัดการที่ยั่งยืน</h5>
                            <p class="card-text text-muted small">Environmental Center</p>
                            <a href="https://cesm.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- หน่วยตรวจสอบภายใน -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/ตรวจสอบภายใน.jpg" alt="หน่วยตรวจสอบภายใน">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">หน่วยตรวจสอบภายใน</h5>
                            <p class="card-text text-muted small">Internal Audit Unit</p>
                            <a href="https://audit.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Academic Departments (Faculties) -->
        <section class="department-section mb-5" data-category="ส่วนงานวิชาการ">
            <div class="section-header mb-4">
                <h2 class="section-title h3">🎓 ส่วนงานวิชาการ (คณะทั้งหมด)</h2>
                <div class="section-divider" style="height: 3px; background: linear-gradient(90deg, var(--primary-color, #8B7AA8), transparent); margin: 10px 0 20px 0; border-radius: 2px;"></div>
                <p class="text-muted">คณะวิชาต่างๆ ของมหาวิทยาลัยพะเยา</p>
            </div>

            <div class="row">
            <!-- คณะเกษตรศาสตร์และทรัพยากรธรรมชาติ -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="department-card card h-100 shadow-sm">
                    <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                        <img src="images/faculties/faculty-agri.jpg" alt="คณะเกษตรศาสตร์และทรัพยากรธรรมชาติ">
                    </div>
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">คณะเกษตรศาสตร์และทรัพยากรธรรมชาติ</h5>
                        <p class="card-text text-muted small">Faculty of Agriculture</p>
                        <a href="https://www.agri.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                        </a>
                    </div>
                </div>
            </div>

                <!-- คณะทันตแพทยศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-dent.jpg" alt="คณะทันตแพทยศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะทันตแพทยศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Dentistry</p>
                            <a href="https://dentistry.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะเทคโนโลยีสารสนเทศและการสื่อสาร -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-ict.jpg" alt="คณะเทคโนโลยีสารสนเทศและการสื่อสาร">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะเทคโนโลยีสารสนเทศและการสื่อสาร</h5>
                            <p class="card-text text-muted small">Faculty of ICT</p>
                            <a href="https://ict.up.ac.th/home" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะนิติศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-law.jpg" alt="คณะนิติศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะนิติศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Law</p>
                            <a href="https://law.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะพยาบาลศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-nurse.jpg" alt="คณะพยาบาลศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะพยาบาลศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Nursing</p>
                            <a href="https://www.nurse.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะแพทยศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-med.jpg" alt="คณะแพทยศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะแพทยศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Medicine</p>
                            <a href="https://medicine.up.ac.th/th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะเภสัชศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-pharm.jpg" alt="คณะเภสัชศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะเภสัชศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Pharmacy</p>
                            <a href="https://www.pharmacy.up.ac.th/th/main/default" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะรัฐศาสตร์และสังคมศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-polsci.jpg" alt="คณะรัฐศาสตร์และสังคมศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะรัฐศาสตร์และสังคมศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Political Science</p>
                            <a href="https://www.spss.up.ac.th/default" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะวิทยาศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-science.jpg" alt="คณะวิทยาศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะวิทยาศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Science</p>
                            <a href="http://www.science.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะวิทยาศาสตร์การแพทย์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-medsci.jpg" alt="คณะวิทยาศาสตร์การแพทย์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะวิทยาศาสตร์การแพทย์</h5>
                            <p class="card-text text-muted small">Faculty of Medical Science</p>
                            <a href="https://www.medsci.up.ac.th/v4/index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะวิศวกรรมศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-eng.jpg" alt="คณะวิศวกรรมศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะวิศวกรรมศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Engineering</p>
                            <a href="https://www.up.ac.th/th/faculty_eng" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะศิลปศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-libarts.jpg" alt="คณะศิลปศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะศิลปศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Liberal Arts</p>
                            <a href="https://www.libarts.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะสถาปัตยกรรมศาสตร์และศิลปกรรมศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-arch.jpg" alt="คณะสถาปัตยกรรมศาสตร์และศิลปกรรมศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะสถาปัตยกรรมศาสตร์และศิลปกรรมศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Architecture</p>
                            <a href="https://safa.up.ac.th/main/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะสหเวชศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-ams.jpg" alt="คณะสหเวชศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะสหเวชศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Allied Health Sciences</p>
                            <a href="https://www.ahs.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- คณะสาธารณสุขศาสตร์ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/ph.jpg" alt="คณะสาธารณสุขศาสตร์">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">คณะสาธารณสุขศาสตร์</h5>
                            <p class="card-text text-muted small">Faculty of Public Health</p>
                            <a href="https://www.ph.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- วิทยาลัยการศึกษา -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/School-of-Education.jpg" alt="วิทยาลัยการศึกษา">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">วิทยาลัยการศึกษา</h5>
                            <p class="card-text text-muted small">College of Education</p>
                            <a href="https://www.se.up.ac.th/se/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- วิทยาลัยการจัดการ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/faculty-scm.jpg" alt="วิทยาลัยการจัดการ">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">วิทยาลัยการจัดการ</h5>
                            <p class="card-text text-muted small">College of Management</p>
                            <a href="http://www.upcm.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Other Departments -->
        <section class="department-section mb-5" data-category="ส่วนงานอื่น">
            <div class="section-header mb-4">
                <h2 class="section-title h3">🔗 ส่วนงานอื่น ๆ</h2>
                <div class="section-divider" style="height: 3px; background: linear-gradient(90deg, var(--primary-color, #8B7AA8), transparent); margin: 10px 0 20px 0; border-radius: 2px;"></div>
            </div>

            <div class="row">

                <!-- วิทยาเขตเชียงราย -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/schoolchangrai-1-500x270.jpg" alt="วิทยาเขตเชียงราย">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">วิทยาเขตเชียงราย</h5>
                            <p class="card-text text-muted small">Chiang Rai Campus</p>
                            <a href="https://www.crc.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- สถาบันนวัตกรรมและถ่ายทอดเทคโนโลยี -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/iti.jpg" alt="สถาบันนวัตกรรมและถ่ายทอดเทคโนโลยี">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">สถาบันนวัตกรรมและถ่ายทอดเทคโนโลยี</h5>
                            <p class="card-text text-muted small">Innovation and Technology Transfer Institute</p>
                            <a href="https://iti.up.ac.th/th/main/default" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

                <!-- สถาบันนวัตกรรมการเรียนรู้ -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="department-card card h-100 shadow-sm">
                        <div class="faculty-image" style="height: 140px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img src="images/faculties/image2.jpg" alt="สถาบันนวัตกรรมการเรียนรู้">
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title">สถาบันนวัตกรรมการเรียนรู้</h5>
                            <p class="card-text text-muted small">Learning Innovation Institute</p>
                            <a href="https://upili.up.ac.th/" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> เข้าชมเว็บไซต์
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</main>

<style>
/* All Departments Page Styles */
.department-section {
    margin-bottom: 3rem;
}

.section-title {
    color: var(--text-dark, #333);
    font-weight: 600;
    position: relative;
    display: inline-block;
}

.department-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.department-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139,122,168,0.15);
    border-color: var(--primary-color, #8B7AA8);
}

.department-icon {
    margin-bottom: 1rem;
}

.department-card .card-title {
    color: var(--text-dark, #333);
    font-weight: 600;
    font-size: 1.1rem;
}

.department-card .card-text {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.hero-modern .hero-actions .btn {
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    transition: all 0.35s ease;
}

.hero-modern .hero-actions .btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 22px 40px rgba(0,0,0,0.18);
}

.hero-modern .hero-actions .hero-meta span {
    color: rgba(255,255,255,0.8);
    letter-spacing: 0.3px;
}

.hero-modern .hero-actions .btn i {
    color: #5b4a90;
}
.search-box .input-group {
    background: #fff;
    border-radius: 32px;
    border: 2px solid rgba(139,122,168,0.18);
    box-shadow: 0 15px 30px rgba(139,122,168,0.08);
    overflow: hidden;
    transition: all 0.35s ease;
}

.search-box .input-group:focus-within {
    border-color: rgba(139,122,168,0.45);
    box-shadow: 0 18px 40px rgba(139,122,168,0.15);
    transform: translateY(-2px);
}

.department-card .faculty-image {
    height: 150px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 10px;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
}

.department-card .faculty-image img {
    width: auto !important;
    height: auto !important;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain !important;
    display: block;
    transition: transform 0.3s ease;
}

.department-card:hover .faculty-image img {
    transform: scale(1.03);
}

/* Page Header */

.hero-modern {
    position: relative;
    overflow: hidden;
    background: linear-gradient(125deg, #5b4a90 0%, #8b7aa8 45%, #a698bc 100%);
    padding: 90px 0 110px 0;
}

.hero-modern::before,
.hero-modern::after {
    content: '';
    position: absolute;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    z-index: 0;
}

.hero-modern::before {
    top: -120px;
    left: -80px;
}

.hero-modern::after {
    bottom: -140px;
    right: -60px;
}

.hero-modern .hero-content,
.hero-modern .hero-card,
.hero-modern .hero-actions {
    position: relative;
    z-index: 1;
}

.hero-modern .hero-badge {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 999px;
    padding: 8px 18px;
    letter-spacing: 0.5px;
    font-size: 0.95rem;
    backdrop-filter: blur(6px);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2);
}

.hero-modern .hero-actions .btn {
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    transition: all 0.35s ease;
}

.hero-modern .hero-actions .btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 22px 40px rgba(0,0,0,0.18);
}

.hero-modern .hero-actions .hero-meta span {
    color: rgba(255,255,255,0.8);
    letter-spacing: 0.3px;
}

.hero-modern .hero-actions .btn i {
    color: #5b4a90;
}

.hero-modern .hero-card {
    background: rgba(255,255,255,0.12);
    border-radius: 24px;
    padding: 32px;
    backdrop-filter: blur(18px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
}

.hero-modern .hero-card-icon {
    width: 72px;
    height: 72px;
    border-radius: 24px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 20px;
}

.hero-modern .hero-card-icon i,
.hero-modern .hero-list li i {
    color: #fff;
}

.hero-modern .hero-list li {
    margin-bottom: 12px;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.hero-modern .hero-breadcrumb {
    position: absolute;
    bottom: 20px;
    left: 0;
}

.hero-modern .breadcrumb {
    margin-bottom: 0;
}

.hero-modern .breadcrumb a {
    color: rgba(255,255,255,0.75);
    text-decoration: none;
}

.hero-modern .breadcrumb a:hover {
    color: white;
}

.hero-modern .hero-shape {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.18), transparent 45%),
                radial-gradient(circle at 80% 30%, rgba(255,255,255,0.12), transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(255,255,255,0.08), transparent 50%);
    z-index: 0;
}

/* Search and Filter Section */
.search-filter-section {
    border-bottom: 1px solid #e9ecef;
}

.search-box .form-control {
    border: none;
    font-size: 1rem;
    padding: 14px 20px;
}

.search-box .form-control:focus {
    box-shadow: none;
}

.filter-buttons .btn {
    border-radius: 25px;
    padding: 8px 20px;
    margin: 4px;
    transition: all 0.3s ease;
}

.filter-buttons .btn:hover,
.filter-buttons .btn.active {
    background-color: var(--primary-color, #8B7AA8);
    border-color: var(--primary-color, #8B7AA8);
    color: white;
    transform: translateY(-1px);
}

/* Animation for cards */
.department-card {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease;
}

.department-card.animate-in {
    opacity: 1;
    transform: translateY(0);
}

/* Staggered animation delay */
.department-section:nth-child(1) .department-card { transition-delay: 0.1s; }
.department-section:nth-child(2) .department-card { transition-delay: 0.2s; }
.department-section:nth-child(3) .department-card { transition-delay: 0.3s; }
.department-section:nth-child(4) .department-card { transition-delay: 0.4s; }

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 60px 0;
        text-align: center;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .department-card {
        margin-bottom: 1rem;
    }

    .department-card .card-body {
        padding: 1.5rem;
    }

    .search-box .form-control {
        font-size: 0.9rem;
        padding: 10px 16px;
    }

    .filter-buttons .btn {
        padding: 6px 16px;
        margin: 2px;
        font-size: 0.85rem;
    }

    .filter-buttons {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 40px 0;
    }

    .page-header h1 {
        font-size: 1.8rem;
    }

    .department-card .card-body {
        padding: 1rem;
    }

    .department-icon {
        margin-bottom: 0.5rem;
    }

    .department-icon i {
        font-size: 2rem;
    }

    .search-box .form-control {
        font-size: 0.85rem;
        padding: 8px 12px;
    }

    .filter-buttons .btn {
        padding: 4px 12px;
        margin: 1px;
        font-size: 0.8rem;
        border-radius: 20px;
    }

    .filter-buttons {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}
</style>

<!-- JavaScript for Interactive Features -->
<script>
$(document).ready(function() {
    // Add smooth scroll behavior for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $($(this).attr('href'));
        if(target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });

    // Add search functionality
    $('#departmentSearch').on('input', function() {
        var value = $(this).val().toLowerCase().trim();
        var hasResults = false;

        $('.department-section').each(function() {
            var section = $(this);
            var cards = section.find('.department-card');
            var visibleCount = 0;

            cards.each(function() {
                var card = $(this);
                var text = card.text().toLowerCase();
                var matched = value === '' || text.indexOf(value) > -1;
                card.toggle(matched);
                if (matched) {
                    visibleCount++;
                }
            });

            section.toggle(visibleCount > 0);
            hasResults = hasResults || visibleCount > 0;
        });

        $('#departmentNoResults').toggleClass('d-none', hasResults);
    });

    // Add animation on scroll
    function animateOnScroll() {
        $('.department-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-in');
            }
        });
    }

    // Trigger animation on scroll and load
    $(window).on('scroll load', animateOnScroll);

    // Add click tracking for analytics
    $('.department-card a').on('click', function() {
        var departmentName = $(this).closest('.department-card').find('.card-title').text();
        // You can add analytics tracking here
        console.log('Clicked department:', departmentName);
    });

    // Add filter buttons functionality
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');

        if (filter === 'ทั้งหมด') {
            $('.department-section').show();
        } else {
            $('.department-section').hide();
            $('.department-section[data-category="' + filter + '"]').show();
        }

        // Reset search
        $('#departmentSearch').val('');
        $('.department-card').show();
        $('#departmentNoResults').addClass('d-none');

        // Update active button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Add animation to newly shown sections
        $('.department-section').each(function(index) {
            if ($(this).is(':visible')) {
                $(this).find('.department-card').css('animation-delay', (index * 0.1) + 's');
                $(this).find('.department-card').addClass('animate-in');
            }
        });
    });
});
</script>

<?php
// รวม footer จาก index.php
include_once 'footer.php';
?>
