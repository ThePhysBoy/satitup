<!-- เริ่มต้นส่วนข่าวสารและประกาศ -->
<section class="news-announcements-section py-5">
    <!-- พื้นที่ส่วนข่าวสารและประกาศ ระยะห่างด้านบนและล่าง 5 หน่วย (py-5) -->
    <div class="container">
        <!-- กำหนดความกว้างของเนื้อหาให้อยู่ภายใน container ตามมาตรฐาน Bootstrap -->
        <div class="section-header text-center mb-5">
            <!-- ส่วนหัวของเซคชั่น จัดข้อความตรงกลาง และมีระยะห่างด้านล่าง 5 หน่วย -->
            <h2 class="section-title">ข่าวประชาสัมพันธ์</h2>
            <!-- หัวข้อหลักของเซคชั่น -->
            <p class="section-subtitle">ติดตามข่าวสารและประกาศสำคัญจากโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            <!-- คำอธิบายเพิ่มเติมใต้หัวข้อหลัก -->
        </div>
        
        <!-- สไตล์สำหรับรายการประกาศ -->
        <style>
        /* สไตล์สำหรับเมนูย่อย */
        .announcement-submenu .nav-link {
            color: #333;
            font-weight: 500;
            border-radius: 20px;
            padding: 8px 15px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .announcement-submenu .nav-link.active {
            background-color: #7b3b95;
            color: #fff;
        }
        
        .announcement-submenu .nav-link:hover:not(.active) {
            background-color: #f0f0f0;
        }
        
        /* สไตล์สำหรับรายการประกาศ */
        .announcement-list .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .announcement-list .list-group-item {
            padding: 15px 20px;
            border-left: none;
            border-right: none;
            transition: all 0.3s ease;
        }
        
        .announcement-list .list-group-item:hover {
            background-color: #f8f9fa;
        }
        
        .announcement-list .list-group-item:first-child {
            border-top: none;
        }
        
        .announcement-list .list-group-item:last-child {
            border-bottom: none;
        }
        
        .announcement-title {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
            transition: color 0.3s ease;
        }
        
        .announcement-title:hover {
            color: #7b3b95;
        }
        
        .announcement-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .announcement-author {
            color: #888;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        /* แอนิเมชันสำหรับรายการ */
        .list-group-item.animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ปรับการแสดงผลบนอุปกรณ์มือถือ */
        @media (max-width: 767px) {
            .announcement-submenu .nav-link {
                margin: 3px;
                padding: 6px 10px;
                font-size: 0.9rem;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .announcement-author {
                margin-top: 5px;
            }
        }
        </style>
        
        <!-- แถบเมนู Tab สำหรับเลือกประเภทข่าวสาร -->
        <div class="news-tabs-wrapper mb-4">
            <!-- กล่องครอบแถบเมนู Tab มีระยะห่างด้านล่าง 4 หน่วย -->
            <ul class="nav nav-tabs news-tabs" id="newsTab" role="tablist">
                <!-- รายการแถบเมนู Tab ใช้คลาส Bootstrap nav และ nav-tabs -->
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูแรก: ภาพข่าวกิจกรรม -->
                    <button class="nav-link active" id="news-activities-tab" data-bs-toggle="tab" data-bs-target="#news-activities" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab แรก กำหนดให้เป็น active (แสดงเริ่มต้น) -->
                        <i class="fas fa-camera"></i>
                        <!-- ไอคอนรูปกล้อง จาก Font Awesome -->
                        <span>ภาพข่าวกิจกรรม</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่สอง: คำสั่งและประกาศ -->
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับคำสั่งและประกาศ -->
                        <i class="fas fa-file-signature"></i>
                        <!-- ไอคอนรูปเอกสารและปากกา จาก Font Awesome -->
                        <span>คำสั่งและประกาศ</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่สอง: การจัดซื้อจัดจ้าง -->
                    <button class="nav-link" id="procurement-tab" data-bs-toggle="tab" data-bs-target="#procurement" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับการจัดซื้อจัดจ้าง -->
                        <i class="fas fa-shopping-cart"></i>
                        <!-- ไอคอนรูปตะกร้าสินค้า จาก Font Awesome -->
                        <span>การจัดซื้อจัดจ้าง</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่สาม: การรับสมัครงาน -->
                    <button class="nav-link" id="recruitment-tab" data-bs-toggle="tab" data-bs-target="#recruitment" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับการรับสมัครงาน -->
                        <i class="fas fa-user-tie"></i>
                        <!-- ไอคอนรูปคนใส่สูท จาก Font Awesome -->
                        <span>การรับสมัครงาน</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่สี่: การอบรม -->
                    <button class="nav-link" id="training-tab" data-bs-toggle="tab" data-bs-target="#training" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับการอบรม -->
                        <i class="fas fa-chalkboard-teacher"></i>
                        <!-- ไอคอนรูปครูสอนหน้ากระดาน จาก Font Awesome -->
                        <span>การอบรม</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่ห้า: การประกวดแข่งขัน -->
                    <button class="nav-link" id="competition-tab" data-bs-toggle="tab" data-bs-target="#competition" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับการประกวดแข่งขัน -->
                        <i class="fas fa-trophy"></i>
                        <!-- ไอคอนรูปถ้วยรางวัล จาก Font Awesome -->
                        <span>การประกวดแข่งขัน</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <!-- รายการเมนูที่หก: การไปต่างประเทศ -->
                    <button class="nav-link" id="international-tab" data-bs-toggle="tab" data-bs-target="#international" type="button" role="tab">
                        <!-- ปุ่มเมนู Tab สำหรับการไปต่างประเทศ -->
                        <i class="fas fa-globe"></i>
                        <!-- ไอคอนรูปโลก จาก Font Awesome -->
                        <span>การไปต่างประเทศ</span>
                        <!-- ข้อความบนปุ่มเมนู -->
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- สไตล์สำหรับภาพข่าวกิจกรรม -->
        <style>
            /* สไตล์สำหรับการ์ดข่าวกิจกรรม */
            .news-activity-card {
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                margin-bottom: 30px;
                transition: all 0.3s ease;
                background-color: #fff;
            }
            
            .news-activity-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(123, 59, 149, 0.2);
            }
            
            .news-activity-image {
                position: relative;
                overflow: hidden;
                height: 240px;
            }
            
            .news-activity-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            
            .news-activity-card:hover .news-activity-image img {
                transform: scale(1.05);
            }
            
            .news-activity-date {
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(123, 59, 149, 0.85);
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                font-weight: 500;
                font-size: 0.9rem;
                backdrop-filter: blur(5px);
            }
            
            .news-activity-content {
                padding: 20px;
            }
            
            .news-activity-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 10px;
                color: #333;
                line-height: 1.4;
            }
            
            .news-activity-title a {
                color: #333;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .news-activity-title a:hover {
                color: #7b3b95;
            }
            
            .news-activity-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #666;
                font-size: 0.9rem;
                margin-top: 15px;
            }
            
            .news-activity-author {
                display: flex;
                align-items: center;
            }
            
            .news-activity-author i {
                margin-right: 5px;
                color: #7b3b95;
            }
            
            /* แอนิเมชันสำหรับการ์ดข่าวกิจกรรม */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-card {
                animation: fadeInUp 0.6s ease forwards;
            }
            
            /* ปรับการแสดงผลบนอุปกรณ์มือถือ */
            @media (max-width: 767px) {
                .news-activity-image {
                    height: 180px;
                }
                
                .news-activity-content {
                    padding: 15px;
                }
                
                .news-activity-title {
                    font-size: 1.1rem;
                }
            }
        </style>
        
        <!-- เนื้อหาของแต่ละแท็บ -->
        <div class="tab-content" id="newsTabContent">
            <!-- เนื้อหาแท็บ: ภาพข่าวกิจกรรม (แสดงเริ่มต้น) -->
            <div class="tab-pane fade show active" id="news-activities" role="tabpanel">
                <!-- แท็บแรกถูกกำหนดให้แสดงเริ่มต้นด้วย class active และ show -->
                <div class="row g-4">
<?php
// ดึง 5 ข่าวล่าสุดจากฐานข้อมูลสำหรับภาพข่าวกิจกรรม
if (!isset($conn) || !($conn instanceof mysqli)) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'satitup';
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
}

$latest = [];
if ($conn && !$conn->connect_error) {
    $stmt = $conn->prepare("SELECT n.title, n.slug, n.published_at, n.created_at, n.featured_image, n.views, u.full_name, u.username
                            FROM news n
                            LEFT JOIN users u ON u.id = n.author_id
                            WHERE n.status = 'published'
                            ORDER BY COALESCE(n.published_at, n.created_at) DESC
                            LIMIT 5");
    $stmt->execute();
    $res = $stmt->get_result();
    $latest = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<?php if (!empty($latest)): ?>
<?php foreach ($latest as $index => $item): ?>
<?php $colClass = ($index < 2) ? 'col-lg-6' : 'col-lg-4'; ?>
                    <div class="<?php echo $colClass; ?>">
                        <div class="news-activity-card">
                            <div class="news-activity-image">
                                <img src="<?php echo htmlspecialchars(!empty($item['featured_image']) ? $item['featured_image'] : 'images/comingsoon.png'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <div class="news-activity-date"><?php echo date('d/m/Y', strtotime($item['published_at'] ?: $item['created_at'])); ?></div>
                                </div>
                            <div class="news-activity-content">
                                <h3 class="news-activity-title">
                                    <a href="news/detail.php?slug=<?php echo urlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                </h3>
                                <div class="news-activity-meta">
                                    <span class="news-activity-author">
                                        <i class="fas fa-user"></i> โดย <?php echo htmlspecialchars($item['full_name'] ?? 'ระบบ'); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-eye"></i> <?php echo number_format((int)$item['views']); ?>
                                    </span>
                            </div>
                                </div>
                            </div>
                    </div>
<?php endforeach; ?>
<?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">ยังไม่มีข่าวเผยแพร่</div>
                    </div>
<?php endif; ?>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <a href="news/index.php" class="btn btn-view-all">
                        ข่าวทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: คำสั่งและประกาศ -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <!-- แท็บนี้ถูกกำหนดให้ไม่แสดงเริ่มต้น (ไม่มี class active และ show) -->
                <!-- เมนูย่อยและเนื้อหาสำหรับคำสั่งและประกาศต่างๆ -->
                
                <!-- เมนูย่อยสำหรับคำสั่งและประกาศ -->
                <div class="announcement-submenu mb-4">
                    <!-- เมนูย่อยมีระยะห่างด้านล่าง 4 หน่วย -->
                    <ul class="nav nav-pills justify-content-center">
                        <!-- เมนูย่อยแบบปุ่ม (pills) จัดให้อยู่ตรงกลาง -->
                        <li class="nav-item">
                            <a class="nav-link active" href="#announcement" data-bs-toggle="pill">
                                <i class="fas fa-bullhorn"></i> ประกาศ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#order" data-bs-toggle="pill">
                                <i class="fas fa-file-alt"></i> คำสั่ง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#regulation" data-bs-toggle="pill">
                                <i class="fas fa-clipboard-list"></i> ระเบียบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rule" data-bs-toggle="pill">
                                <i class="fas fa-gavel"></i> ข้อบังคับ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#act" data-bs-toggle="pill">
                                <i class="fas fa-landmark"></i> พระราชบัญญัติ
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- เนื้อหาของแต่ละเมนูย่อย -->
                <div class="tab-content">
                    <!-- สไตล์สำหรับประกาศและคำสั่ง -->
                    <style>
                        .order-announcement-list {
                            background-color: #fff;
                            border-radius: 12px;
                            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
                            overflow: hidden;
                        }
                        
                        .order-announcement-item {
                            padding: 18px 20px;
                            border-bottom: 1px solid #eee;
                            transition: all 0.3s ease;
                        }
                        
                        .order-announcement-item:last-child {
                            border-bottom: none;
                        }
                        
                        .order-announcement-item:hover {
                            background-color: #f8f5ff;
                        }
                        
                        .order-announcement-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: flex-start;
                            margin-bottom: 10px;
                        }
                        
                        .order-announcement-title {
                            font-size: 1.1rem;
                            font-weight: 600;
                            color: #333;
                            margin: 0;
                            padding-right: 15px;
                            margin-bottom: 8px;
                        }
                        
                        .order-announcement-title a {
                            color: #333;
                            text-decoration: none;
                            transition: color 0.2s ease;
                        }
                        
                        .order-announcement-title a:hover {
                            color: #7b3b95;
                        }
                        
                        .order-announcement-date {
                            background-color: #7b3b95;
                            color: white;
                            padding: 5px 12px;
                            border-radius: 20px;
                            font-size: 0.85rem;
                            font-weight: 500;
                            white-space: nowrap;
                            min-width: 130px;
                            text-align: center;
                        }
                        
                        .order-announcement-info {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            font-size: 0.9rem;
                            color: #666;
                        }
                        
                        .order-announcement-budget {
                            font-weight: 500;
                        }
                        
                        .order-announcement-budget span {
                            color: #28a745;
                        }
                        
                        .order-announcement-status {
                            display: flex;
                            align-items: center;
                        }
                        
                        .order-announcement-badge {
                            padding: 3px 10px;
                            border-radius: 15px;
                            font-size: 0.8rem;
                            font-weight: 500;
                            margin-right: 10px;
                        }
                        
                        .status-open {
                            background-color: #e3f5ff;
                            color: #0088cc;
                        }
                        
                        .status-closed {
                            background-color: #ffe3e3;
                            color: #dc3545;
                        }
                        
                        .order-announcement-department {
                            color: #666;
                        }
                        
                        .order-announcement-department i {
                            color: #7b3b95;
                            margin-right: 5px;
                        }
                        
                        .order-announcement-search {
                            position: relative;
                            max-width: 400px;
                            margin: 0 auto 20px;
                        }
                        
                        .order-announcement-search input {
                            width: 100%;
                            padding: 10px 15px 10px 40px;
                            border: 1px solid #ddd;
                            border-radius: 25px;
                            font-size: 0.95rem;
                        }
                        
                        .order-announcement-search i {
                            position: absolute;
                            left: 15px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #7b3b95;
                        }
                        
                        @media (max-width: 767px) {
                            .order-announcement-header {
                                flex-direction: column;
                            }
                            
                            .order-announcement-date {
                                margin-top: 10px;
                                align-self: flex-start;
                            }
                            
                            .order-announcement-info {
                                flex-direction: column;
                                align-items: flex-start;
                            }
                            
                            .order-announcement-budget {
                                margin-bottom: 10px;
                            }
                        }
                    </style>
                    
                    <!-- เนื้อหาประกาศ (แสดงเริ่มต้น) -->
                    <div class="tab-pane fade show active" id="announcement">
                        <!-- ช่องค้นหาประกาศ -->
                        <div class="order-announcement-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="ค้นหาประกาศ..." id="announcement-search-input">
                        </div>
                        
                        <!-- รายการประกาศ -->
                        <div class="order-announcement-list">
                            <!-- ประกาศที่ 1 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาซื้อครุภัณฑ์ห้องปฏิบัติการวิทยาศาสตร์ จำนวน 1 ชุด</a>
                                    </h3>
                                    <div class="order-announcement-date">20 กันยายน 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">งบประมาณ: <span>2,500,000 บาท</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-badge status-open">เปิดรับ</div>
                                        <div class="order-announcement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ประกาศที่ 2 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาจ้างปรับปรุงห้องสมุด จำนวน 1 งาน</a>
                                    </h3>
                                    <div class="order-announcement-date">15 กันยายน 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">งบประมาณ: <span>1,800,000 บาท</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-badge status-open">เปิดรับ</div>
                                        <div class="order-announcement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ประกาศที่ 3 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาซื้อครุภัณฑ์คอมพิวเตอร์ จำนวน 50 เครื่อง</a>
                                    </h3>
                                    <div class="order-announcement-date">10 กันยายน 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">งบประมาณ: <span>1,250,000 บาท</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-badge status-closed">ประกาศผล</div>
                                        <div class="order-announcement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ประกาศที่ 4 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง จัดซื้อหนังสือเรียนและแบบฝึกหัด ประจำปีการศึกษา 2568</a>
                                    </h3>
                                    <div class="order-announcement-date">5 กันยายน 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">งบประมาณ: <span>850,000 บาท</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-badge status-closed">ประกาศผล</div>
                                        <div class="order-announcement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ประกาศที่ 5 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง จัดซื้อวัสดุสำนักงาน ประจำปีงบประมาณ 2568</a>
                                    </h3>
                                    <div class="order-announcement-date">1 กันยายน 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">งบประมาณ: <span>350,000 บาท</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-badge status-closed">ปิดรับ</div>
                                        <div class="order-announcement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เนื้อหาคำสั่ง -->
                    <div class="tab-pane fade" id="order">
                        <!-- ช่องค้นหาคำสั่ง -->
                        <div class="order-announcement-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="ค้นหาคำสั่ง..." id="order-search-input">
                        </div>
                        
                        <!-- รายการคำสั่ง -->
                        <div class="order-announcement-list">
                            <!-- คำสั่งที่ 1 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">คำสั่งโรงเรียนสาธิตมหาวิทยาลัยพะเยา ที่ 023/2568 เรื่อง แต่งตั้งคณะกรรมการดำเนินงานพิธีไหว้ครู ประจำปีการศึกษา 2568</a>
                                    </h3>
                                    <div class="order-announcement-date">15 พฤษภาคม 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">ประเภท: <span>คำสั่งแต่งตั้ง</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-department"><i class="fas fa-user-tie"></i> ฝ่ายบริหาร / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- คำสั่งที่ 2 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">คำสั่งโรงเรียนสาธิตมหาวิทยาลัยพะเยา ที่ 022/2568 เรื่อง แต่งตั้งคณะกรรมการจัดทำแผนยุทธศาสตร์โรงเรียน ประจำปี 2568-2572</a>
                                    </h3>
                                    <div class="order-announcement-date">10 พฤษภาคม 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">ประเภท: <span>คำสั่งแต่งตั้ง</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-department"><i class="fas fa-user-tie"></i> ฝ่ายบริหาร / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- คำสั่งที่ 3 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">คำสั่งโรงเรียนสาธิตมหาวิทยาลัยพะเยา ที่ 021/2568 เรื่อง แต่งตั้งคณะกรรมการดำเนินงานวันสถาปนาโรงเรียน ประจำปี 2568</a>
                                    </h3>
                                    <div class="order-announcement-date">5 พฤษภาคม 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">ประเภท: <span>คำสั่งแต่งตั้ง</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-department"><i class="fas fa-user-tie"></i> ฝ่ายบริหาร / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- คำสั่งที่ 4 -->
                            <div class="order-announcement-item">
                                <div class="order-announcement-header">
                                    <h3 class="order-announcement-title">
                                        <a href="#">คำสั่งโรงเรียนสาธิตมหาวิทยาลัยพะเยา ที่ 020/2568 เรื่อง มอบหมายหน้าที่และความรับผิดชอบของบุคลากรฝ่ายวิชาการ ประจำปีการศึกษา 2568</a>
                                    </h3>
                                    <div class="order-announcement-date">1 พฤษภาคม 2568</div>
                                </div>
                                <div class="order-announcement-info">
                                    <div class="order-announcement-budget">ประเภท: <span>คำสั่งมอบหมายงาน</span></div>
                                    <div class="order-announcement-status">
                                        <div class="order-announcement-department"><i class="fas fa-user-tie"></i> ฝ่ายวิชาการ / โดย adminsatit</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เนื้อหาระเบียบ -->
                    <div class="tab-pane fade" id="regulation">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <!-- รายการระเบียบที่ 1 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">ระเบียบโรงเรียนสาธิตมหาวิทยาลัยพะเยา ว่าด้วยการแต่งกายของนักเรียน พ.ศ. 2568</a>
                                                    <div class="announcement-date">5 เมษายน 2568</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                        
                                        <!-- รายการระเบียบที่ 2 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">ระเบียบโรงเรียนสาธิตมหาวิทยาลัยพะเยา ว่าด้วยการวัดและประเมินผลการเรียน พ.ศ. 2567</a>
                                                    <div class="announcement-date">15 ธันวาคม 2567</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เนื้อหาข้อบังคับ -->
                    <div class="tab-pane fade" id="rule">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <!-- รายการข้อบังคับที่ 1 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">ข้อบังคับมหาวิทยาลัยพะเยา ว่าด้วยการบริหารงานโรงเรียนสาธิตมหาวิทยาลัยพะเยา พ.ศ. 2568</a>
                                                    <div class="announcement-date">20 มีนาคม 2568</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                        
                                        <!-- รายการข้อบังคับที่ 2 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">ข้อบังคับมหาวิทยาลัยพะเยา ว่าด้วยการจัดการศึกษาในโรงเรียนสาธิตมหาวิทยาลัยพะเยา พ.ศ. 2567</a>
                                                    <div class="announcement-date">10 พฤศจิกายน 2567</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เนื้อหาพระราชบัญญัติ -->
                    <div class="tab-pane fade" id="act">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <!-- รายการพระราชบัญญัติที่ 1 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">พระราชบัญญัติการศึกษาแห่งชาติ พ.ศ. 2566</a>
                                                    <div class="announcement-date">15 มกราคม 2567</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                        
                                        <!-- รายการพระราชบัญญัติที่ 2 -->
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="#" class="announcement-title">พระราชบัญญัติการพัฒนาเด็กปฐมวัย พ.ศ. 2562</a>
                                                    <div class="announcement-date">30 เมษายน 2562</div>
                                                </div>
                                                <div>
                                                    <span class="announcement-author">/ โดย adminsatit</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมด -->
                        ข่าวทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: การจัดซื้อจัดจ้าง -->
            <div class="tab-pane fade" id="procurement" role="tabpanel">
                <!-- แท็บนี้จะถูกซ่อนไว้เริ่มต้น (ไม่มี class active) จะแสดงเมื่อคลิกที่แท็บ -->
                
                <!-- สไตล์สำหรับการจัดซื้อจัดจ้าง -->
                <style>
                    .procurement-list {
                        background-color: #fff;
                        border-radius: 12px;
                        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
                        overflow: hidden;
                    }
                    
                    .procurement-item {
                        padding: 18px 20px;
                        border-bottom: 1px solid #eee;
                        transition: all 0.3s ease;
                    }
                    
                    .procurement-item:last-child {
                        border-bottom: none;
                    }
                    
                    .procurement-item:hover {
                        background-color: #f8f5ff;
                    }
                    
                    .procurement-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 10px;
                    }
                    
                    .procurement-title {
                        font-size: 1.1rem;
                        font-weight: 600;
                        color: #333;
                        margin: 0;
                        padding-right: 15px;
                    }
                    
                    .procurement-title a {
                        color: #333;
                        text-decoration: none;
                        transition: color 0.2s ease;
                    }
                    
                    .procurement-title a:hover {
                        color: #7b3b95;
                    }
                    
                    .procurement-date {
                        background-color: #7b3b95;
                        color: white;
                        padding: 5px 12px;
                        border-radius: 20px;
                        font-size: 0.85rem;
                        font-weight: 500;
                        white-space: nowrap;
                    }
                    
                    .procurement-info {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        font-size: 0.9rem;
                        color: #666;
                    }
                    
                    .procurement-budget {
                        font-weight: 500;
                    }
                    
                    .procurement-budget span {
                        color: #28a745;
                    }
                    
                    .procurement-status {
                        display: flex;
                        align-items: center;
                    }
                    
                    .status-badge {
                        padding: 3px 10px;
                        border-radius: 15px;
                        font-size: 0.8rem;
                        font-weight: 500;
                        margin-right: 10px;
                    }
                    
                    .status-open {
                        background-color: #e3f5ff;
                        color: #0088cc;
                    }
                    
                    .status-closed {
                        background-color: #ffe3e3;
                        color: #dc3545;
                    }
                    
                    .status-awarded {
                        background-color: #e3ffe3;
                        color: #28a745;
                    }
                    
                    .procurement-department {
                        color: #666;
                    }
                    
                    .procurement-department i {
                        color: #7b3b95;
                        margin-right: 5px;
                    }
                    
                    .procurement-filter {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 20px;
                    }
                    
                    .procurement-search {
                        position: relative;
                        flex: 1;
                        max-width: 400px;
                    }
                    
                    .procurement-search input {
                        width: 100%;
                        padding: 10px 15px 10px 40px;
                        border: 1px solid #ddd;
                        border-radius: 25px;
                        font-size: 0.95rem;
                    }
                    
                    .procurement-search i {
                        position: absolute;
                        left: 15px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #7b3b95;
                    }
                    
                    .procurement-sort {
                        display: flex;
                        align-items: center;
                    }
                    
                    .procurement-sort label {
                        margin-right: 10px;
                        color: #666;
                        font-size: 0.95rem;
                    }
                    
                    .procurement-sort select {
                        padding: 8px 30px 8px 15px;
                        border: 1px solid #ddd;
                        border-radius: 20px;
                        font-size: 0.9rem;
                        background-color: #f8f8f8;
                        appearance: none;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%237b3b95' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
                        background-repeat: no-repeat;
                        background-position: calc(100% - 10px) center;
                    }
                    
                    @media (max-width: 767px) {
                        .procurement-filter {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .procurement-search {
                            width: 100%;
                            max-width: none;
                            margin-bottom: 15px;
                        }
                        
                        .procurement-header {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .procurement-date {
                            margin-top: 10px;
                        }
                        
                        .procurement-info {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .procurement-budget {
                            margin-bottom: 10px;
                        }
                    }
                </style>
                
                <!-- ตัวกรองและการค้นหา -->
                <div class="procurement-filter">
                    <div class="procurement-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="ค้นหาการจัดซื้อจัดจ้าง...">
                                </div>
                    <div class="procurement-sort">
                        <label for="procurement-sort-select">เรียงตาม:</label>
                        <select id="procurement-sort-select">
                            <option value="date-desc" selected>วันที่: ล่าสุด - เก่าสุด</option>
                            <option value="date-asc">วันที่: เก่าสุด - ล่าสุด</option>
                            <option value="budget-desc">งบประมาณ: สูง - ต่ำ</option>
                            <option value="budget-asc">งบประมาณ: ต่ำ - สูง</option>
                        </select>
                            </div>
                                </div>
                
                <!-- รายการจัดซื้อจัดจ้าง -->
                <div class="procurement-list">
                    <!-- รายการที่ 1 -->
                    <div class="procurement-item">
                        <div class="procurement-header">
                            <h3 class="procurement-title">
                                <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาซื้อครุภัณฑ์ห้องปฏิบัติการวิทยาศาสตร์ จำนวน 1 ชุด</a>
                            </h3>
                            <div class="procurement-date">20 กันยายน 2568</div>
                            </div>
                        <div class="procurement-info">
                            <div class="procurement-budget">งบประมาณ: <span>2,500,000 บาท</span></div>
                            <div class="procurement-status">
                                <div class="status-badge status-open">เปิดรับ</div>
                                <div class="procurement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                            </div>
                    </div>
                </div>
                
                    <!-- รายการที่ 2 -->
                    <div class="procurement-item">
                        <div class="procurement-header">
                            <h3 class="procurement-title">
                                <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาจ้างปรับปรุงห้องสมุด จำนวน 1 งาน</a>
                            </h3>
                            <div class="procurement-date">15 กันยายน 2568</div>
                        </div>
                        <div class="procurement-info">
                            <div class="procurement-budget">งบประมาณ: <span>1,800,000 บาท</span></div>
                            <div class="procurement-status">
                                <div class="status-badge status-open">เปิดรับ</div>
                                <div class="procurement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 3 -->
                    <div class="procurement-item">
                        <div class="procurement-header">
                            <h3 class="procurement-title">
                                <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง ประกวดราคาซื้อครุภัณฑ์คอมพิวเตอร์ จำนวน 50 เครื่อง</a>
                            </h3>
                            <div class="procurement-date">10 กันยายน 2568</div>
                        </div>
                        <div class="procurement-info">
                            <div class="procurement-budget">งบประมาณ: <span>1,250,000 บาท</span></div>
                            <div class="procurement-status">
                                <div class="status-badge status-awarded">ประกาศผล</div>
                                <div class="procurement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 4 -->
                    <div class="procurement-item">
                        <div class="procurement-header">
                            <h3 class="procurement-title">
                                <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง จัดซื้อหนังสือเรียนและแบบฝึกหัด ประจำปีการศึกษา 2568</a>
                            </h3>
                            <div class="procurement-date">5 กันยายน 2568</div>
                        </div>
                        <div class="procurement-info">
                            <div class="procurement-budget">งบประมาณ: <span>850,000 บาท</span></div>
                            <div class="procurement-status">
                                <div class="status-badge status-awarded">ประกาศผล</div>
                                <div class="procurement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 5 -->
                    <div class="procurement-item">
                        <div class="procurement-header">
                            <h3 class="procurement-title">
                                <a href="#">ประกาศโรงเรียนสาธิตมหาวิทยาลัยพะเยา เรื่อง จัดซื้อวัสดุสำนักงาน ประจำปีงบประมาณ 2568</a>
                            </h3>
                            <div class="procurement-date">1 กันยายน 2568</div>
                        </div>
                        <div class="procurement-info">
                            <div class="procurement-budget">งบประมาณ: <span>350,000 บาท</span></div>
                            <div class="procurement-status">
                                <div class="status-badge status-closed">ปิดรับ</div>
                                <div class="procurement-department"><i class="fas fa-building"></i> ฝ่ายพัสดุ / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมดในหมวดการจัดซื้อจัดจ้าง -->
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: การรับสมัครงาน -->
            <div class="tab-pane fade" id="recruitment" role="tabpanel">
                <!-- แท็บนี้จะถูกซ่อนไว้เริ่มต้น จะแสดงเมื่อคลิกที่แท็บ -->
                
                <!-- สไตล์สำหรับการรับสมัครงาน -->
                <style>
                    .job-list {
                        background-color: #fff;
                        border-radius: 12px;
                        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
                        overflow: hidden;
                    }
                    
                    .job-item {
                        padding: 18px 20px;
                        border-bottom: 1px solid #eee;
                        transition: all 0.3s ease;
                    }
                    
                    .job-item:last-child {
                        border-bottom: none;
                    }
                    
                    .job-item:hover {
                        background-color: #f8f5ff;
                    }
                    
                    .job-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 10px;
                    }
                    
                    .job-title {
                        font-size: 1.1rem;
                        font-weight: 600;
                        color: #333;
                        margin: 0;
                        padding-right: 15px;
                    }
                    
                    .job-title a {
                        color: #333;
                        text-decoration: none;
                        transition: color 0.2s ease;
                    }
                    
                    .job-title a:hover {
                        color: #7b3b95;
                    }
                    
                    .job-date {
                        background-color: #7b3b95;
                        color: white;
                        padding: 5px 12px;
                        border-radius: 20px;
                        font-size: 0.85rem;
                        font-weight: 500;
                        white-space: nowrap;
                    }
                    
                    .job-info {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        font-size: 0.9rem;
                        color: #666;
                    }
                    
                    .job-details {
                        display: flex;
                        gap: 15px;
                    }
                    
                    .job-detail {
                        display: flex;
                        align-items: center;
                    }
                    
                    .job-detail i {
                        margin-right: 5px;
                        color: #7b3b95;
                    }
                    
                    .job-salary {
                        font-weight: 500;
                    }
                    
                    .job-salary span {
                        color: #28a745;
                    }
                    
                    .job-status {
                        display: flex;
                        align-items: center;
                    }
                    
                    .job-badge {
                        padding: 3px 10px;
                        border-radius: 15px;
                        font-size: 0.8rem;
                        font-weight: 500;
                        margin-right: 10px;
                    }
                    
                    .job-open {
                        background-color: #e3f5ff;
                        color: #0088cc;
                    }
                    
                    .job-closed {
                        background-color: #ffe3e3;
                        color: #dc3545;
                    }
                    
                    .job-urgent {
                        background-color: #fff3cd;
                        color: #856404;
                    }
                    
                    .job-department {
                        color: #666;
                    }
                    
                    .job-department i {
                        color: #7b3b95;
                        margin-right: 5px;
                    }
                    
                    .job-filter {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 20px;
                    }
                    
                    .job-search {
                        position: relative;
                        flex: 1;
                        max-width: 400px;
                    }
                    
                    .job-search input {
                        width: 100%;
                        padding: 10px 15px 10px 40px;
                        border: 1px solid #ddd;
                        border-radius: 25px;
                        font-size: 0.95rem;
                    }
                    
                    .job-search i {
                        position: absolute;
                        left: 15px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #7b3b95;
                    }
                    
                    .job-filter-options {
                        display: flex;
                        gap: 10px;
                    }
                    
                    .job-filter-select {
                        padding: 8px 30px 8px 15px;
                        border: 1px solid #ddd;
                        border-radius: 20px;
                        font-size: 0.9rem;
                        background-color: #f8f8f8;
                        appearance: none;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%237b3b95' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
                        background-repeat: no-repeat;
                        background-position: calc(100% - 10px) center;
                    }
                    
                    @media (max-width: 767px) {
                        .job-filter {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .job-search {
                            width: 100%;
                            max-width: none;
                            margin-bottom: 15px;
                        }
                        
                        .job-filter-options {
                            width: 100%;
                            justify-content: space-between;
                        }
                        
                        .job-filter-select {
                            flex: 1;
                        }
                        
                        .job-header {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .job-date {
                            margin-top: 10px;
                        }
                        
                        .job-info {
                            flex-direction: column;
                            align-items: flex-start;
                        }
                        
                        .job-details {
                            flex-direction: column;
                            gap: 5px;
                            margin-bottom: 10px;
                        }
                    }
                </style>
                
                <!-- ตัวกรองและการค้นหา -->
                <div class="job-filter">
                    <div class="job-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="ค้นหาตำแหน่งงาน...">
                    </div>
                    <div class="job-filter-options">
                        <select id="job-type-select" class="job-filter-select">
                            <option value="all" selected>ทุกประเภท</option>
                            <option value="full-time">เต็มเวลา</option>
                            <option value="part-time">นอกเวลา</option>
                            <option value="contract">สัญญาจ้าง</option>
                        </select>
                        <select id="job-sort-select" class="job-filter-select">
                            <option value="date-desc" selected>วันที่: ล่าสุด - เก่าสุด</option>
                            <option value="date-asc">วันที่: เก่าสุด - ล่าสุด</option>
                            <option value="salary-desc">เงินเดือน: สูง - ต่ำ</option>
                            <option value="salary-asc">เงินเดือน: ต่ำ - สูง</option>
                        </select>
                    </div>
                </div>
                
                <!-- รายการรับสมัครงาน -->
                <div class="job-list">
                    <!-- รายการที่ 1 -->
                    <div class="job-item">
                        <div class="job-header">
                            <h3 class="job-title">
                                <a href="#">รับสมัครครูผู้สอนวิชาคณิตศาสตร์ ระดับมัธยมศึกษา</a>
                            </h3>
                            <div class="job-date">20 กันยายน 2568</div>
                        </div>
                        <div class="job-info">
                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-briefcase"></i> เต็มเวลา
                                </div>
                                <div class="job-detail job-salary">
                                    <i class="fas fa-money-bill-wave"></i> เงินเดือน: <span>20,000 - 25,000 บาท</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-graduate"></i> วุฒิปริญญาโท
                                </div>
                            </div>
                            <div class="job-status">
                                <div class="job-badge job-open">เปิดรับสมัคร</div>
                                <div class="job-department"><i class="fas fa-building"></i> ฝ่ายบุคคล / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 2 -->
                    <div class="job-item">
                        <div class="job-header">
                            <h3 class="job-title">
                                <a href="#">รับสมัครครูผู้สอนวิชาวิทยาศาสตร์ ระดับประถมศึกษา</a>
                            </h3>
                            <div class="job-date">15 กันยายน 2568</div>
                        </div>
                        <div class="job-info">
                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-briefcase"></i> เต็มเวลา
                                </div>
                                <div class="job-detail job-salary">
                                    <i class="fas fa-money-bill-wave"></i> เงินเดือน: <span>18,000 - 22,000 บาท</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-graduate"></i> วุฒิปริญญาตรี
                                </div>
                            </div>
                            <div class="job-status">
                                <div class="job-badge job-urgent">ด่วน</div>
                                <div class="job-department"><i class="fas fa-building"></i> ฝ่ายบุคคล / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 3 -->
                    <div class="job-item">
                        <div class="job-header">
                            <h3 class="job-title">
                                <a href="#">รับสมัครเจ้าหน้าที่ธุรการ</a>
                            </h3>
                            <div class="job-date">10 กันยายน 2568</div>
                        </div>
                        <div class="job-info">
                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-briefcase"></i> เต็มเวลา
                                </div>
                                <div class="job-detail job-salary">
                                    <i class="fas fa-money-bill-wave"></i> เงินเดือน: <span>15,000 - 18,000 บาท</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-graduate"></i> วุฒิปริญญาตรี
                                </div>
                            </div>
                            <div class="job-status">
                                <div class="job-badge job-open">เปิดรับสมัคร</div>
                                <div class="job-department"><i class="fas fa-building"></i> ฝ่ายบุคคล / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 4 -->
                    <div class="job-item">
                        <div class="job-header">
                            <h3 class="job-title">
                                <a href="#">รับสมัครนักการภารโรง</a>
                            </h3>
                            <div class="job-date">5 กันยายน 2568</div>
                        </div>
                        <div class="job-info">
                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-briefcase"></i> เต็มเวลา
                                </div>
                                <div class="job-detail job-salary">
                                    <i class="fas fa-money-bill-wave"></i> เงินเดือน: <span>9,000 - 12,000 บาท</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-graduate"></i> วุฒิ ม.3 ขึ้นไป
                                </div>
                            </div>
                            <div class="job-status">
                                <div class="job-badge job-closed">ปิดรับสมัคร</div>
                                <div class="job-department"><i class="fas fa-building"></i> ฝ่ายบุคคล / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รายการที่ 5 -->
                    <div class="job-item">
                        <div class="job-header">
                            <h3 class="job-title">
                                <a href="#">รับสมัครครูผู้สอนวิชาภาษาอังกฤษ (นอกเวลา)</a>
                            </h3>
                            <div class="job-date">1 กันยายน 2568</div>
                        </div>
                        <div class="job-info">
                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-briefcase"></i> นอกเวลา
                                </div>
                                <div class="job-detail job-salary">
                                    <i class="fas fa-money-bill-wave"></i> ค่าตอบแทน: <span>300 บาท/ชั่วโมง</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-graduate"></i> วุฒิปริญญาตรี
                                </div>
                            </div>
                            <div class="job-status">
                                <div class="job-badge job-open">เปิดรับสมัคร</div>
                                <div class="job-department"><i class="fas fa-building"></i> ฝ่ายบุคคล / โดย adminsatit</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมดในหมวดการรับสมัครงาน -->
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: การอบรม -->
            <div class="tab-pane fade" id="training" role="tabpanel">
                <!-- แท็บนี้จะถูกซ่อนไว้เริ่มต้น จะแสดงเมื่อคลิกที่แท็บ -->
                <div class="row g-4">
                    <!-- จัดเรียงการ์ดข่าวเป็นแถว มีระยะห่างระหว่างการ์ด 4 หน่วย -->
                    <div class="col-lg-4 col-md-6">
                        <!-- การ์ดข่าวกว้าง 4 คอลัมน์บนจอใหญ่, 6 คอลัมน์บนจอกลาง -->
                        <article class="news-card">
                            <!-- บทความข่าวเกี่ยวกับการอบรม -->
                            <div class="news-card-image">
                                <!-- ส่วนรูปภาพของการ์ดข่าว -->
                                <img src="images/comingsoon.png" alt="อบรมครู">
                                <!-- รูปภาพข่าว พร้อมข้อความอธิบายภาพ -->
                                <div class="news-category">อบรม</div>
                                <!-- ป้ายหมวดหมู่ข่าว -->
                                <div class="news-date">
                                    <!-- ส่วนแสดงวันที่ของข่าว -->
                                    <span class="day">11</span>
                                    <!-- วันที่ -->
                                    <span class="month">ม.ค.</span>
                                    <!-- เดือน -->
                                </div>
                            </div>
                            <div class="news-card-content">
                                <!-- ส่วนเนื้อหาของการ์ดข่าว -->
                                <h5 class="news-title">
                                    <!-- หัวข้อข่าว -->
                                    <a href="#">อบรมเชิงปฏิบัติการ Active Learning</a>
                                    <!-- ลิงก์ไปยังหน้ารายละเอียดข่าว -->
                                </h5>
                                <p class="news-excerpt">จัดอบรมเชิงปฏิบัติการการจัดการเรียนรู้แบบ Active Learning สำหรับครูผู้สอน...</p>
                                <!-- เนื้อหาย่อของข่าว -->
                                <div class="news-meta">
                                    <!-- ข้อมูลเพิ่มเติมของข่าว -->
                                    <span><i class="fas fa-user"></i> ฝ่ายวิชาการ</span>
                                    <!-- ผู้เขียนหรือแหล่งที่มา พร้อมไอคอนรูปคน -->
                                    <span><i class="fas fa-eye"></i> 567</span>
                                    <!-- จำนวนผู้เข้าชม พร้อมไอคอนรูปตา -->
                                </div>
                                <a href="#" class="btn-read-more">อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i></a>
                                <!-- ปุ่มอ่านเพิ่มเติม พร้อมไอคอนลูกศรขวา -->
                            </div>
                        </article>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมดในหมวดการอบรม -->
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: การประกวดแข่งขัน -->
            <div class="tab-pane fade" id="competition" role="tabpanel">
                <!-- แท็บนี้จะถูกซ่อนไว้เริ่มต้น จะแสดงเมื่อคลิกที่แท็บ -->
                <div class="row g-4">
                    <!-- จัดเรียงการ์ดข่าวเป็นแถว มีระยะห่างระหว่างการ์ด 4 หน่วย -->
                    <div class="col-lg-4 col-md-6">
                        <!-- การ์ดข่าวกว้าง 4 คอลัมน์บนจอใหญ่, 6 คอลัมน์บนจอกลาง -->
                        <article class="news-card">
                            <!-- บทความข่าวเกี่ยวกับการประกวดแข่งขัน -->
                            <div class="news-card-image">
                                <!-- ส่วนรูปภาพของการ์ดข่าว -->
                                <img src="images/comingsoon.png" alt="การแข่งขัน">
                                <!-- รูปภาพข่าว พร้อมข้อความอธิบายภาพ -->
                                <div class="news-category">แข่งขัน</div>
                                <!-- ป้ายหมวดหมู่ข่าว -->
                                <div class="news-date">
                                    <!-- ส่วนแสดงวันที่ของข่าว -->
                                    <span class="day">09</span>
                                    <!-- วันที่ -->
                                    <span class="month">ม.ค.</span>
                                    <!-- เดือน -->
                                </div>
                            </div>
                            <div class="news-card-content">
                                <!-- ส่วนเนื้อหาของการ์ดข่าว -->
                                <h5 class="news-title">
                                    <!-- หัวข้อข่าว -->
                                    <a href="#">การแข่งขันโครงงานวิทยาศาสตร์ระดับภาค</a>
                                    <!-- ลิงก์ไปยังหน้ารายละเอียดข่าว -->
                                </h5>
                                <p class="news-excerpt">นักเรียนได้รับรางวัลชนะเลิศการแข่งขันโครงงานวิทยาศาสตร์ระดับภาคเหนือ...</p>
                                <!-- เนื้อหาย่อของข่าว -->
                                <div class="news-meta">
                                    <!-- ข้อมูลเพิ่มเติมของข่าว -->
                                    <span><i class="fas fa-user"></i> ฝ่ายวิชาการ</span>
                                    <!-- ผู้เขียนหรือแหล่งที่มา พร้อมไอคอนรูปคน -->
                                    <span><i class="fas fa-eye"></i> 892</span>
                                    <!-- จำนวนผู้เข้าชม พร้อมไอคอนรูปตา -->
                                </div>
                                <a href="#" class="btn-read-more">อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i></a>
                                <!-- ปุ่มอ่านเพิ่มเติม พร้อมไอคอนลูกศรขวา -->
                            </div>
                        </article>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมดในหมวดการประกวดแข่งขัน -->
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: การไปต่างประเทศ -->
            <div class="tab-pane fade" id="international" role="tabpanel">
                <!-- แท็บนี้จะถูกซ่อนไว้เริ่มต้น จะแสดงเมื่อคลิกที่แท็บ -->
                <div class="row g-4">
                    <!-- จัดเรียงการ์ดข่าวเป็นแถว มีระยะห่างระหว่างการ์ด 4 หน่วย -->
                    <div class="col-lg-4 col-md-6">
                        <!-- การ์ดข่าวกว้าง 4 คอลัมน์บนจอใหญ่, 6 คอลัมน์บนจอกลาง -->
                        <article class="news-card">
                            <!-- บทความข่าวเกี่ยวกับการไปต่างประเทศ -->
                            <div class="news-card-image">
                                <!-- ส่วนรูปภาพของการ์ดข่าว -->
                                <img src="images/comingsoon.png" alt="ต่างประเทศ">
                                <!-- รูปภาพข่าว พร้อมข้อความอธิบายภาพ -->
                                <div class="news-category">ต่างประเทศ</div>
                                <!-- ป้ายหมวดหมู่ข่าว -->
                                <div class="news-date">
                                    <!-- ส่วนแสดงวันที่ของข่าว -->
                                    <span class="day">08</span>
                                    <!-- วันที่ -->
                                    <span class="month">ม.ค.</span>
                                    <!-- เดือน -->
                                </div>
                            </div>
                            <div class="news-card-content">
                                <!-- ส่วนเนื้อหาของการ์ดข่าว -->
                                <h5 class="news-title">
                                    <!-- หัวข้อข่าว -->
                                    <a href="#">โครงการแลกเปลี่ยนนักเรียนญี่ปุ่น</a>
                                    <!-- ลิงก์ไปยังหน้ารายละเอียดข่าว -->
                                </h5>
                                <p class="news-excerpt">เปิดรับสมัครนักเรียนเข้าร่วมโครงการแลกเปลี่ยนวัฒนธรรมกับประเทศญี่ปุ่น...</p>
                                <!-- เนื้อหาย่อของข่าว -->
                                <div class="news-meta">
                                    <!-- ข้อมูลเพิ่มเติมของข่าว -->
                                    <span><i class="fas fa-user"></i> ฝ่ายต่างประเทศ</span>
                                    <!-- ผู้เขียนหรือแหล่งที่มา พร้อมไอคอนรูปคน -->
                                    <span><i class="fas fa-eye"></i> 1,567</span>
                                    <!-- จำนวนผู้เข้าชม พร้อมไอคอนรูปตา -->
                                </div>
                                <a href="#" class="btn-read-more">อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i></a>
                                <!-- ปุ่มอ่านเพิ่มเติม พร้อมไอคอนลูกศรขวา -->
                            </div>
                        </article>
                    </div>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <!-- จัดตำแหน่งปุ่มให้อยู่ตรงกลาง มีระยะห่างด้านบน 4 หน่วย -->
                    <a href="#" class="btn btn-view-all">
                        <!-- ปุ่มลิงก์ไปยังหน้าแสดงข่าวทั้งหมดในหมวดการไปต่างประเทศ -->
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                        <!-- ข้อความบนปุ่ม พร้อมไอคอนลูกศรขวา มีระยะห่างด้านซ้าย 2 หน่วย -->
                    </a>
                </div>
            </div>
        </div>
        <!-- จบส่วนเนื้อหาของแต่ละแท็บ -->
    </div>
    <!-- จบส่วน container -->
</section>
<!-- จบส่วนข่าวสารและประกาศ -->

<!-- ส่วน JavaScript สำหรับการทำงานของแท็บข่าวสาร -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // เพิ่มแอนิเมชันให้กับรายการประกาศเมื่อมีการเปลี่ยนแท็บ
    // รอให้เอกสาร HTML โหลดเสร็จสมบูรณ์ก่อนทำงาน
    
    // เพิ่มการทำงานของช่องค้นหาในแท็บประกาศ
    const announcementSearchInput = document.getElementById('announcement-search-input');
    if (announcementSearchInput) {
        announcementSearchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const announcementItems = document.querySelectorAll('#announcement .order-announcement-item');
            
            announcementItems.forEach(item => {
                const title = item.querySelector('.order-announcement-title').textContent.toLowerCase();
                const budget = item.querySelector('.order-announcement-budget').textContent.toLowerCase();
                const department = item.querySelector('.order-announcement-department').textContent.toLowerCase();
                const status = item.querySelector('.order-announcement-badge')?.textContent.toLowerCase() || '';
                
                if (title.includes(searchValue) || 
                    budget.includes(searchValue) || 
                    department.includes(searchValue) || 
                    status.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // เพิ่มการทำงานของช่องค้นหาในแท็บคำสั่ง
    const orderSearchInput = document.getElementById('order-search-input');
    if (orderSearchInput) {
        orderSearchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const orderItems = document.querySelectorAll('#order .order-announcement-item');
            
            orderItems.forEach(item => {
                const title = item.querySelector('.order-announcement-title').textContent.toLowerCase();
                const type = item.querySelector('.order-announcement-budget').textContent.toLowerCase();
                const department = item.querySelector('.order-announcement-department').textContent.toLowerCase();
                
                if (title.includes(searchValue) || 
                    type.includes(searchValue) || 
                    department.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // ค้นหาปุ่มแท็บทั้งหมด
    const tabButtons = document.querySelectorAll('#newsTab button[data-bs-toggle="tab"]');
    
    // วนลูปเพื่อเพิ่ม event listener ให้กับทุกปุ่มแท็บ
    tabButtons.forEach(button => {
        // เมื่อแท็บถูกแสดง (หลังจากคลิก)
        button.addEventListener('shown.bs.tab', function (e) {
            // ค้นหาพื้นที่เนื้อหาของแท็บที่ถูกเลือก
            const targetPane = document.querySelector(e.target.getAttribute('data-bs-target'));
            
            // ตรวจสอบว่ามีรายการประกาศหรือไม่
            const announcementItems = targetPane.querySelectorAll('.list-group-item');
            if (announcementItems.length > 0) {
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละรายการประกาศ
                announcementItems.forEach((item, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนรายการและเลื่อนลงด้านล่าง
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(10px)';
                    
                    // ตั้งเวลาเพื่อแสดงรายการด้วยแอนิเมชัน
                    setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                        item.style.transition = 'all 0.3s ease';
                        // แสดงรายการ
                        item.style.opacity = '1';
                        // เลื่อนรายการกลับมาตำแหน่งปกติ
                        item.style.transform = 'translateY(0)';
                    }, index * 50); // หน่วงเวลา 50ms คูณด้วยลำดับของรายการ ทำให้รายการทยอยปรากฏ
                });
            }
            
            // ตรวจสอบว่ามีรายการจัดซื้อจัดจ้างหรือไม่
            const procurementItems = targetPane.querySelectorAll('.procurement-item');
            if (procurementItems.length > 0) {
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละรายการจัดซื้อจัดจ้าง
                procurementItems.forEach((item, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนรายการและเลื่อนลงด้านล่าง
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(10px)';
                    
                    // ตั้งเวลาเพื่อแสดงรายการด้วยแอนิเมชัน
                    setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                        item.style.transition = 'all 0.3s ease';
                        // แสดงรายการ
                        item.style.opacity = '1';
                        // เลื่อนรายการกลับมาตำแหน่งปกติ
                        item.style.transform = 'translateY(0)';
                    }, index * 100); // หน่วงเวลา 100ms คูณด้วยลำดับของรายการ ทำให้รายการทยอยปรากฏ
                });
                
                // เพิ่มแอนิเมชันให้กับตัวกรองและการค้นหา
                const filter = targetPane.querySelector('.procurement-filter');
                if (filter) {
                    filter.style.opacity = '0';
                    filter.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        filter.style.transition = 'all 0.5s ease';
                        filter.style.opacity = '1';
                        filter.style.transform = 'translateY(0)';
                    }, 100);
                }
            }
            
            // ตรวจสอบว่ามีรายการรับสมัครงานหรือไม่
            const jobItems = targetPane.querySelectorAll('.job-item');
            if (jobItems.length > 0) {
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละรายการรับสมัครงาน
                jobItems.forEach((item, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนรายการและเลื่อนลงด้านล่าง
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(10px)';
                    
                    // ตั้งเวลาเพื่อแสดงรายการด้วยแอนิเมชัน
                    setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                        item.style.transition = 'all 0.3s ease';
                        // แสดงรายการ
                        item.style.opacity = '1';
                        // เลื่อนรายการกลับมาตำแหน่งปกติ
                        item.style.transform = 'translateY(0)';
                    }, index * 100); // หน่วงเวลา 100ms คูณด้วยลำดับของรายการ ทำให้รายการทยอยปรากฏ
                });
                
                // เพิ่มแอนิเมชันให้กับตัวกรองและการค้นหา
                const jobFilter = targetPane.querySelector('.job-filter');
                if (jobFilter) {
                    jobFilter.style.opacity = '0';
                    jobFilter.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        jobFilter.style.transition = 'all 0.5s ease';
                        jobFilter.style.opacity = '1';
                        jobFilter.style.transform = 'translateY(0)';
                    }, 100);
                }
            }
            
            // ตรวจสอบว่ามีการ์ดข่าวกิจกรรมหรือไม่
            const activityCards = targetPane.querySelectorAll('.news-activity-card');
            if (activityCards.length > 0) {
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละการ์ดข่าวกิจกรรม
                activityCards.forEach((card, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนการ์ดและเลื่อนลงด้านล่าง
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    
                    // ตั้งเวลาเพื่อแสดงการ์ดด้วยแอนิเมชัน
                    setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                        card.style.transition = 'all 0.5s ease';
                        // แสดงการ์ด
                        card.style.opacity = '1';
                        // เลื่อนการ์ดกลับมาตำแหน่งปกติ
                        card.style.transform = 'translateY(0)';
                    }, index * 150); // หน่วงเวลา 150ms คูณด้วยลำดับของการ์ด ทำให้การ์ดทยอยปรากฏ
                });
            }
            
            // ตรวจสอบว่ามีการ์ดข่าวหรือไม่ (สำหรับแท็บที่ยังใช้การแสดงแบบการ์ด)
            const newsCards = targetPane.querySelectorAll('.news-card');
            if (newsCards.length > 0) {
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละการ์ดข่าว
            newsCards.forEach((card, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนการ์ดและเลื่อนลงด้านล่าง
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                    // ตั้งเวลาเพื่อแสดงการ์ดด้วยแอนิเมชัน
                setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                    card.style.transition = 'all 0.5s ease';
                        // แสดงการ์ด
                    card.style.opacity = '1';
                        // เลื่อนการ์ดกลับมาตำแหน่งปกติ
                    card.style.transform = 'translateY(0)';
                    }, index * 100); // หน่วงเวลา 100ms คูณด้วยลำดับของการ์ด ทำให้การ์ดทยอยปรากฏ
            });
            }
        });
    });
    
    // เพิ่ม event listener สำหรับเมนูย่อย (pills) ในแท็บคำสั่งและประกาศ
    const pillButtons = document.querySelectorAll('.announcement-submenu .nav-link');
    
    // วนลูปเพื่อเพิ่ม event listener ให้กับทุกปุ่มเมนูย่อย
    pillButtons.forEach(pill => {
        // เมื่อเมนูย่อยถูกแสดง (หลังจากคลิก)
        pill.addEventListener('shown.bs.tab', function (e) {
            // ค้นหา ID ของเป้าหมาย (จาก href attribute)
            const targetId = this.getAttribute('href');
            // ค้นหาพื้นที่เนื้อหาของเมนูย่อยที่ถูกเลือก
            const targetContent = document.querySelector(targetId);
            
            if (targetContent) {
                // ค้นหารายการในเนื้อหาที่ถูกเลือก
                const items = targetContent.querySelectorAll('.list-group-item');
                
                // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละรายการ
                items.forEach((item, index) => {
                    // ตั้งค่าเริ่มต้น: ซ่อนรายการและเลื่อนลงด้านล่าง
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(10px)';
                    
                    // ตั้งเวลาเพื่อแสดงรายการด้วยแอนิเมชัน
        setTimeout(() => {
                        // กำหนดการเปลี่ยนแปลงแบบนุ่มนวล
                        item.style.transition = 'all 0.3s ease';
                        // แสดงรายการ
                        item.style.opacity = '1';
                        // เลื่อนรายการกลับมาตำแหน่งปกติ
                        item.style.transform = 'translateY(0)';
                    }, index * 50); // หน่วงเวลา 50ms คูณด้วยลำดับของรายการ
                });
            }
        });
    });
    
    // เริ่มต้นแอนิเมชันสำหรับการ์ดข่าวกิจกรรมที่แสดงเริ่มต้น
    const activityCards = document.querySelectorAll('.tab-pane.active .news-activity-card');
    activityCards.forEach((card, index) => {
        // เพิ่มคลาส animate-card ให้กับแต่ละการ์ด
        setTimeout(() => {
            card.classList.add('animate-card');
        }, index * 150);
    });
    
    // เริ่มต้นแอนิเมชันสำหรับรายการประกาศในแท็บแรกที่แสดงเมื่อโหลดหน้า
    const activeItems = document.querySelectorAll('.tab-pane.active .list-group-item');
    // วนลูปเพื่อเพิ่มแอนิเมชันให้กับแต่ละรายการในแท็บแรก
    activeItems.forEach((item, index) => {
        // ตั้งเวลาเพื่อทยอยแสดงรายการ
        setTimeout(() => {
            // เพิ่มคลาส animate-in ซึ่งจะมีการกำหนด CSS แอนิเมชันไว้
            item.classList.add('animate-in');
        }, index * 50); // หน่วงเวลา 50ms คูณด้วยลำดับของรายการ
    });
    
    // เพิ่มการทำงานของตัวกรองและการเรียงลำดับในแท็บการจัดซื้อจัดจ้าง
    const procurementSortSelect = document.getElementById('procurement-sort-select');
    if (procurementSortSelect) {
        procurementSortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const procurementList = document.querySelector('.procurement-list');
            const procurementItems = Array.from(document.querySelectorAll('.procurement-item'));
            
            // เรียงลำดับรายการตามที่เลือก
            procurementItems.sort((a, b) => {
                if (sortValue === 'date-desc') {
                    // เรียงตามวันที่ล่าสุด - เก่าสุด
                    const dateA = a.querySelector('.procurement-date').textContent;
                    const dateB = b.querySelector('.procurement-date').textContent;
                    return new Date(dateB.split(' ').reverse().join(' ')) - new Date(dateA.split(' ').reverse().join(' '));
                } else if (sortValue === 'date-asc') {
                    // เรียงตามวันที่เก่าสุด - ล่าสุด
                    const dateA = a.querySelector('.procurement-date').textContent;
                    const dateB = b.querySelector('.procurement-date').textContent;
                    return new Date(dateA.split(' ').reverse().join(' ')) - new Date(dateB.split(' ').reverse().join(' '));
                } else if (sortValue === 'budget-desc') {
                    // เรียงตามงบประมาณสูง - ต่ำ
                    const budgetA = parseInt(a.querySelector('.procurement-budget span').textContent.replace(/[^\d]/g, ''));
                    const budgetB = parseInt(b.querySelector('.procurement-budget span').textContent.replace(/[^\d]/g, ''));
                    return budgetB - budgetA;
                } else if (sortValue === 'budget-asc') {
                    // เรียงตามงบประมาณต่ำ - สูง
                    const budgetA = parseInt(a.querySelector('.procurement-budget span').textContent.replace(/[^\d]/g, ''));
                    const budgetB = parseInt(b.querySelector('.procurement-budget span').textContent.replace(/[^\d]/g, ''));
                    return budgetA - budgetB;
                }
                return 0;
            });
            
            // ลบรายการทั้งหมดออกจาก DOM
            procurementItems.forEach(item => {
                item.remove();
            });
            
            // เพิ่มรายการที่เรียงลำดับแล้วกลับเข้าไป
            procurementItems.forEach((item, index) => {
                procurementList.appendChild(item);
                
                // เพิ่มแอนิเมชัน
                item.style.opacity = '0';
                item.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    }
    
    // เพิ่มการทำงานของช่องค้นหาในแท็บการจัดซื้อจัดจ้าง
    const procurementSearchInput = document.querySelector('.procurement-search input');
    if (procurementSearchInput) {
        procurementSearchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const procurementItems = document.querySelectorAll('.procurement-item');
            
            procurementItems.forEach(item => {
                const title = item.querySelector('.procurement-title').textContent.toLowerCase();
                const department = item.querySelector('.procurement-department').textContent.toLowerCase();
                const budget = item.querySelector('.procurement-budget').textContent.toLowerCase();
                const status = item.querySelector('.status-badge').textContent.toLowerCase();
                
                // ตรวจสอบว่ามีข้อความที่ค้นหาอยู่ในส่วนใดส่วนหนึ่งของรายการหรือไม่
                if (title.includes(searchValue) || 
                    department.includes(searchValue) || 
                    budget.includes(searchValue) || 
                    status.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // เพิ่มการทำงานของตัวกรองและการเรียงลำดับในแท็บการรับสมัครงาน
    const jobSortSelect = document.getElementById('job-sort-select');
    if (jobSortSelect) {
        jobSortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const jobList = document.querySelector('.job-list');
            const jobItems = Array.from(document.querySelectorAll('.job-item'));
            
            // เรียงลำดับรายการตามที่เลือก
            jobItems.sort((a, b) => {
                if (sortValue === 'date-desc') {
                    // เรียงตามวันที่ล่าสุด - เก่าสุด
                    const dateA = a.querySelector('.job-date').textContent;
                    const dateB = b.querySelector('.job-date').textContent;
                    return new Date(dateB.split(' ').reverse().join(' ')) - new Date(dateA.split(' ').reverse().join(' '));
                } else if (sortValue === 'date-asc') {
                    // เรียงตามวันที่เก่าสุด - ล่าสุด
                    const dateA = a.querySelector('.job-date').textContent;
                    const dateB = b.querySelector('.job-date').textContent;
                    return new Date(dateA.split(' ').reverse().join(' ')) - new Date(dateB.split(' ').reverse().join(' '));
                } else if (sortValue === 'salary-desc') {
                    // เรียงตามเงินเดือนสูง - ต่ำ
                    const salaryA = a.querySelector('.job-salary span').textContent;
                    const salaryB = b.querySelector('.job-salary span').textContent;
                    const numA = parseInt(salaryA.match(/\d+/g)[0]) || 0;
                    const numB = parseInt(salaryB.match(/\d+/g)[0]) || 0;
                    return numB - numA;
                } else if (sortValue === 'salary-asc') {
                    // เรียงตามเงินเดือนต่ำ - สูง
                    const salaryA = a.querySelector('.job-salary span').textContent;
                    const salaryB = b.querySelector('.job-salary span').textContent;
                    const numA = parseInt(salaryA.match(/\d+/g)[0]) || 0;
                    const numB = parseInt(salaryB.match(/\d+/g)[0]) || 0;
                    return numA - numB;
                }
                return 0;
            });
            
            // ลบรายการทั้งหมดออกจาก DOM
            jobItems.forEach(item => {
                item.remove();
            });
            
            // เพิ่มรายการที่เรียงลำดับแล้วกลับเข้าไป
            jobItems.forEach((item, index) => {
                jobList.appendChild(item);
                
                // เพิ่มแอนิเมชัน
                item.style.opacity = '0';
                item.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    }
    
    // เพิ่มการทำงานของช่องค้นหาในแท็บการรับสมัครงาน
    const jobSearchInput = document.querySelector('.job-search input');
    if (jobSearchInput) {
        jobSearchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const jobItems = document.querySelectorAll('.job-item');
            
            jobItems.forEach(item => {
                const title = item.querySelector('.job-title').textContent.toLowerCase();
                const department = item.querySelector('.job-department').textContent.toLowerCase();
                const salary = item.querySelector('.job-salary').textContent.toLowerCase();
                const status = item.querySelector('.job-badge').textContent.toLowerCase();
                const details = item.querySelectorAll('.job-detail');
                let detailsText = '';
                details.forEach(detail => {
                    detailsText += detail.textContent.toLowerCase() + ' ';
                });
                
                // ตรวจสอบว่ามีข้อความที่ค้นหาอยู่ในส่วนใดส่วนหนึ่งของรายการหรือไม่
                if (title.includes(searchValue) || 
                    department.includes(searchValue) || 
                    salary.includes(searchValue) || 
                    status.includes(searchValue) ||
                    detailsText.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // เพิ่มการทำงานของตัวกรองประเภทงานในแท็บการรับสมัครงาน
    const jobTypeSelect = document.getElementById('job-type-select');
    if (jobTypeSelect) {
        jobTypeSelect.addEventListener('change', function() {
            const typeValue = this.value;
            const jobItems = document.querySelectorAll('.job-item');
            
            jobItems.forEach(item => {
                if (typeValue === 'all') {
                    item.style.display = '';
                } else {
                    const jobType = item.querySelector('.job-detail:first-child').textContent.toLowerCase();
                    if ((typeValue === 'full-time' && jobType.includes('เต็มเวลา')) ||
                        (typeValue === 'part-time' && jobType.includes('นอกเวลา')) ||
                        (typeValue === 'contract' && jobType.includes('สัญญาจ้าง'))) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    }
});
</script>
