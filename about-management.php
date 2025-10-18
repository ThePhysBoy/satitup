<?php
// Basic PHP setup (keep consistent with other pages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in as admin
$isAdminLoggedIn = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'pr_officer') {
        $isAdminLoggedIn = true;
    }
}
?>
<?php include_once 'header.php'; ?>
<?php include_once 'navbar.php'; ?>
<?php require_once 'db_connect.php'; ?>


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Sarabun', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .main-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        padding: 40px;
        margin: 20px auto;
        max-width: 1400px;
    }
    
    .header-section {
        text-align: center;
        margin-bottom: 50px;
        position: relative;
    }
    
    .header-title {
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .header-subtitle {
        color: #6c757d;
        font-size: 1.2rem;
        font-weight: 400;
    }
    
    .director-section {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .director-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 50px;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        transform: translateY(0);
        transition: all 0.3s ease;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .director-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.4);
    }
    
    .director-photo {
            width: 220px;
            height: 250px;
            border-radius: 10px;
            border: 3px solid rgba(255, 255, 255, 0.5);
        margin: 0 auto 20px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: #6c757d;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .director-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
    }
    
    .director-name {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .director-position {
        font-size: 1.3rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    
    .section-title {
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 1.8rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 2px;
    }
    
    /* Special style for Chairman */
    .chairman-label {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    
    .admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    
    .admin-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .admin-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }
    
    .admin-photo {
            width: 150px;
            height: 180px;
            border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        margin-bottom: 15px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .admin-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
    }
    
    .admin-name {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .admin-position {
        color: #6c757d;
        font-size: 1.1rem;
        line-height: 1.5;
    }
    
    .committee-section {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 40px;
    }
    
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .search-box {
        max-width: 400px;
        margin: 0 auto 40px;
        position: relative;
    }
    
    .search-input {
        border-radius: 25px;
        border: 2px solid rgba(79, 172, 254, 0.3);
        padding: 12px 50px 12px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #4facfe;
        box-shadow: 0 0 0 0.2rem rgba(79, 172, 254, 0.25);
    }
    
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .header-title {
            font-size: 2rem;
        }
        
        .admin-grid {
            grid-template-columns: 1fr;
        }
        
        .main-container {
            padding: 20px;
            margin: 10px;
        }
        
        .director-photo {
            width: 180px;
            height: 200px;
        }
        
        .admin-photo {
            width: 120px;
            height: 150px;
        }
        
        .director-card {
            padding: 30px;
            max-width: 100%;
        }
        
        .admin-card {
            padding: 20px;
        }
    }
    
    <?php if ($isAdminLoggedIn): ?>
    /* Styles for admin users */
    .admin-card:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        border-color: #4facfe;
    }
    
    .director-card:hover {
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.5);
        border: 3px solid rgba(79, 172, 254, 0.5);
    }
    
    .admin-card::after,
    .director-card::after {
        content: '\f06e';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        color: #4facfe;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .admin-card:hover::after,
    .director-card:hover::after {
        opacity: 0.6;
    }
    <?php endif; ?>
    
    /* Links for Google Scholar and CV */
    .admin-links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(52, 73, 94, 0.1);
    }
    
    .btn-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 18px;
    }
    
    .btn-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .scholar-link {
        background: linear-gradient(135deg, #4285f4, #1a73e8);
        color: white;
    }
    
    .scholar-link:hover {
        background: linear-gradient(135deg, #1a73e8, #0d5ec8);
        color: white;
    }
    
    .cv-link {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .cv-link:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        color: white;
    }
    
    .email-link {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }
    
    .email-link:hover {
        background: linear-gradient(135deg, #138496, #117a8b);
        color: white;
    }
    
    .director-card .admin-links {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid rgba(52, 73, 94, 0.1);
    }
</style>

<div class="main-container">
    <div class="header-section">
        <h1 class="header-title">คณะกรรมการบริหาร</h1>
        <div class="header-subtitle">รวมรายชื่อผู้บริหารและตำแหน่งบริหารของโรงเรียน</div>
    </div>

    <!-- Search Box -->
    <div class="search-box fade-in">
        <input type="text" class="form-control search-input" id="searchInput" placeholder="ค้นหาชื่อหรือตำแหน่ง...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <?php
    $management_list = [];
    if (isset($conn) && $conn) {
        // ดึงข้อมูลตามลำดับ order_number ที่กำหนดในฐานข้อมูล
        $res = $conn->query("SELECT * FROM management WHERE status='active' ORDER BY order_number ASC");
        if ($res) { $management_list = $res->fetch_all(MYSQLI_ASSOC); }
    }
    
    function getIconClass($position) {
        $pos = mb_strtolower($position);
        if (strpos($pos, 'ผู้อำนวยการ') !== false && strpos($pos, 'รอง') === false && strpos($pos, 'ผู้ช่วย') === false) return 'fa-user-tie';
        if (strpos($pos, 'ผู้ทรงคุณวุฒิ') !== false) return 'fa-user-graduate';
        if (strpos($pos, 'รองผู้อำนวยการ') !== false) return 'fa-user-cog';
        if (strpos($pos, 'ผู้ช่วยผู้อำนวยการ') !== false) return 'fa-user-edit';
        if (strpos($pos, 'ประธานผู้รับผิดชอบหลักสูตร') !== false) return 'fa-book-reader';
        if (strpos($pos, 'ตัวแทนอาจารย์') !== false) return 'fa-chalkboard-teacher';
        if (strpos($pos, 'หัวหน้าสำนักงาน') !== false) return 'fa-building';
        if (strpos($pos, 'หัวหน้างาน') !== false) return 'fa-tasks';
        return 'fa-user';
    }
    
    function isDirector($position) {
        $pos = mb_strtolower($position);
        return (strpos($pos, 'ผู้อำนวยการ') !== false && strpos($pos, 'รอง') === false && strpos($pos, 'ผู้ช่วย') === false);
    }
    
    function isChairman($first_name, $last_name) {
        // ตรวจสอบว่าเป็นประธาน รองศาสตราจารย์ ดร. ชยันต์ บุณยรักษ์ หรือไม่
        return ($first_name === 'ชยันต์' && $last_name === 'บุณยรักษ์');
    }
    ?>

    <?php if (!empty($management_list)): ?>
    
    <?php 
    // แยกประธานออกมาแสดงก่อน
    $chairman = null;
    $others = [];
    foreach ($management_list as $m) {
        if (isChairman($m['first_name'], $m['last_name'])) {
            $chairman = $m;
        } else {
            $others[] = $m;
        }
    }
    ?>
    
    <?php if ($chairman): ?>
    <!-- Chairman Section - แสดงด้านบนสุด -->
    <div class="director-section fade-in mb-5">
        <div class="director-card" 
             data-name="<?php echo htmlspecialchars($chairman['title'].' '.$chairman['first_name'].' '.$chairman['last_name']); ?>" 
             data-position="<?php echo htmlspecialchars($chairman['management_position']); ?>"
             <?php if ($isAdminLoggedIn): ?>
             onclick="window.location.href='admin/management/view.php?id=<?php echo $chairman['id']; ?>'"
             style="cursor: pointer; background: linear-gradient(135deg,rgb(188, 107, 255) 0%,rgb(152, 78, 236) 100%);"
             title="คลิกเพื่อดูรายละเอียด"
             <?php else: ?>
             style="background: linear-gradient(135deg,rgb(151, 107, 255) 0%,rgb(76, 42, 201) 100%);"
             <?php endif; ?>>
            <span class="chairman-label"><i class="fas fa-crown me-1"></i> ประธาน</span>
            <div class="director-photo">
                <?php if (!empty($chairman['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($chairman['image_path']); ?>" alt="<?php echo htmlspecialchars($chairman['title'].' '.$chairman['first_name'].' '.$chairman['last_name']); ?>">
                <?php else: ?>
                    <i class="fas fa-crown"></i>
                <?php endif; ?>
            </div>
            <div class="director-name"><?php echo htmlspecialchars($chairman['title'].' '.$chairman['first_name'].' '.$chairman['last_name']); ?></div>
            <div class="director-position"><?php echo nl2br(htmlspecialchars($chairman['management_position'])); ?></div>
            <div class="admin-links">
                <?php if (!empty($chairman['email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($chairman['email']); ?>" class="btn-link email-link" title="Send Email" onclick="event.stopPropagation();">
                    <i class="fas fa-envelope"></i>
                </a>
                <?php endif; ?>
                <?php if (!empty($chairman['google_scholar_link'])): ?>
                <a href="<?php echo htmlspecialchars($chairman['google_scholar_link']); ?>" target="_blank" class="btn-link scholar-link" title="Google Scholar" onclick="event.stopPropagation();">
                    <i class="fas fa-graduation-cap"></i>
                </a>
                <?php endif; ?>
                <?php if (!empty($chairman['cv_path'])): ?>
                <a href="<?php echo htmlspecialchars($chairman['cv_path']); ?>" target="_blank" class="btn-link cv-link" title="Download CV" onclick="event.stopPropagation();">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Management List Section - แสดงคนอื่นๆ -->
    <div class="committee-section fade-in">
        <div class="admin-grid" id="managementGrid">
            <?php foreach ($others as $m): ?>
                <?php if (isDirector($m['management_position'])): ?>
                <!-- ผู้อำนวยการแสดงแบบพิเศษ -->
                <div class="col-12 mb-4">
                    <div class="director-card mx-auto" 
                         data-name="<?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?>" 
                         data-position="<?php echo htmlspecialchars($m['management_position']); ?>"
                         <?php if ($isAdminLoggedIn): ?>
                         onclick="window.location.href='admin/management/view.php?id=<?php echo $m['id']; ?>'"
                         style="cursor: pointer;"
                         title="คลิกเพื่อดูรายละเอียด"
                         <?php endif; ?>>
                        <div class="director-photo">
                            <?php if (!empty($m['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($m['image_path']); ?>" alt="<?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-user-tie"></i>
                            <?php endif; ?>
                        </div>
                        <div class="director-name"><?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?></div>
                        <div class="director-position"><?php echo nl2br(htmlspecialchars($m['management_position'])); ?></div>
                        <div class="admin-links">
                            <?php if (!empty($m['email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" class="btn-link email-link" title="Send Email" onclick="event.stopPropagation();">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($m['google_scholar_link'])): ?>
                            <a href="<?php echo htmlspecialchars($m['google_scholar_link']); ?>" target="_blank" class="btn-link scholar-link" title="Google Scholar" onclick="event.stopPropagation();">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($m['cv_path'])): ?>
                            <a href="<?php echo htmlspecialchars($m['cv_path']); ?>" target="_blank" class="btn-link cv-link" title="Download CV" onclick="event.stopPropagation();">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- บุคคลอื่นๆ แสดงแบบปกติ -->
                <div class="admin-card" 
                     data-name="<?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?>" 
                     data-position="<?php echo htmlspecialchars($m['management_position']); ?>"
                     <?php if ($isAdminLoggedIn): ?>
                     onclick="window.location.href='admin/management/view.php?id=<?php echo $m['id']; ?>'"
                     style="cursor: pointer;"
                     title="คลิกเพื่อดูรายละเอียด"
                     <?php endif; ?>>
                    <div class="admin-photo">
                        <?php if (!empty($m['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($m['image_path']); ?>" alt="<?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?>">
                        <?php else: ?>
                            <i class="fas <?php echo getIconClass($m['management_position']); ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="admin-name"><?php echo htmlspecialchars($m['title'].' '.$m['first_name'].' '.$m['last_name']); ?></div>
                    <div class="admin-position"><?php echo nl2br(htmlspecialchars($m['management_position'])); ?></div>
                    <div class="admin-links">
                        <?php if (!empty($m['email'])): ?>
                        <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" class="btn-link email-link" title="Send Email" onclick="event.stopPropagation();">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($m['google_scholar_link'])): ?>
                        <a href="<?php echo htmlspecialchars($m['google_scholar_link']); ?>" target="_blank" class="btn-link scholar-link" title="Google Scholar" onclick="event.stopPropagation();">
                            <i class="fas fa-graduation-cap"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($m['cv_path'])): ?>
                        <a href="<?php echo htmlspecialchars($m['cv_path']); ?>" target="_blank" class="btn-link cv-link" title="Download CV" onclick="event.stopPropagation();">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php else: ?>
        <div class="alert alert-info text-center">ยังไม่มีข้อมูลผู้บริหาร กรุณาเพิ่มจากระบบผู้ดูแล</div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>

<!-- Custom JS for Search and Animation -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const allCards = document.querySelectorAll('.admin-card, .director-card');
    const managementSection = document.querySelector('.committee-section');
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            allCards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const position = card.getAttribute('data-position') || '';
                const searchText = (name + ' ' + position).toLowerCase();
                
                // สำหรับ director card ที่อยู่ใน col-12
                const parentCol = card.closest('.col-12');
                
                if (searchText.includes(term) || term === '') {
                    if (parentCol) {
                        parentCol.style.display = 'block';
                    } else {
                        card.style.display = 'block';
                    }
                    card.classList.add('search-visible');
                    visibleCount++;
                } else {
                    if (parentCol) {
                        parentCol.style.display = 'none';
                    } else {
                        card.style.display = 'none';
                    }
                    card.classList.remove('search-visible');
                }
            });
            
            // Show search results
            updateSearchResults(visibleCount, term);
        });
    }
    
    function updateSearchResults(count, term) {
        let indicator = document.getElementById('searchResults');
        if (indicator) indicator.remove();
        
        if (term !== '') {
            indicator = document.createElement('div');
            indicator.id = 'searchResults';
            indicator.className = 'alert alert-info text-center';
            indicator.style.cssText = `margin: 20px auto; max-width: 400px; border-radius: 25px; border: none; background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%); color: #2c3e50; font-weight: 500;`;
            indicator.innerHTML = `พบผลการค้นหา ${count} รายการ สำหรับ "${term}"`;
            document.querySelector('.search-box').appendChild(indicator);
        }
    }
    
    // Fade-in animation on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
    
    // Trigger entrance animations
    setTimeout(() => {
        document.querySelectorAll('.fade-in').forEach((el, index) => {
            setTimeout(() => el.classList.add('visible'), index * 150);
        });
    }, 100);
    
    // Card hover animation
    allCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px) scale(1.02)';
            card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            card.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '';
            card.style.zIndex = '';
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.blur();
        }
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
        }
    });
    
    // Smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
});
</script>

