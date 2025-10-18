<?php
// Basic PHP setup
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
        background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
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
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
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
    
    .president-section {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .president-card {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border-radius: 20px;
        padding: 50px;
        color: white;
        box-shadow: 0 15px 35px rgba(106, 17, 203, 0.3);
        transform: translateY(0);
        transition: all 0.3s ease;
        max-width: 500px;
        margin: 0 auto;
        position: relative;
    }
    
    .president-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(106, 17, 203, 0.4);
    }
    
    .president-photo {
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
    
    .president-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .president-name {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .president-position {
        font-size: 1.3rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    
    .president-label {
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
    
    .section-title {
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
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
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
        border-radius: 2px;
    }
    
    .committee-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    
    .committee-card {
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
    
    .committee-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .committee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }
    
    .committee-photo {
        width: 150px;
        height: 180px;
        border-radius: 8px;
        background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        margin-bottom: 15px;
        border: 2px solid rgba(102, 166, 255, 0.2);
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .committee-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .committee-name {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .committee-position {
        color: #6c757d;
        font-size: 1.1rem;
        line-height: 1.5;
        margin-bottom: 5px;
    }
    
    .committee-role {
        color: #2575fc;
        font-weight: 500;
        font-size: 1rem;
    }
    
    .committee-section {
        background: linear-gradient(135deg, rgba(106, 17, 203, 0.05) 0%, rgba(37, 117, 252, 0.05) 100%);
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
        border: 2px solid rgba(37, 117, 252, 0.3);
        padding: 12px 50px 12px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #2575fc;
        box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.25);
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
        
        .committee-grid {
            grid-template-columns: 1fr;
        }
        
        .main-container {
            padding: 20px;
            margin: 10px;
        }
        
        .president-photo {
            width: 180px;
            height: 200px;
        }
        
        .committee-photo {
            width: 120px;
            height: 150px;
        }
        
        .president-card {
            padding: 30px;
            max-width: 100%;
        }
        
        .committee-card {
            padding: 20px;
        }
    }
    
    <?php if ($isAdminLoggedIn): ?>
    /* Styles for admin users */
    .committee-card:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        border-color: #2575fc;
    }
    
    .president-card:hover {
        box-shadow: 0 25px 50px rgba(106, 17, 203, 0.5);
        border: 3px solid rgba(37, 117, 252, 0.5);
    }
    
    .committee-card::after,
    .president-card::after {
        content: '\f06e';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        color: #2575fc;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .committee-card:hover::after,
    .president-card:hover::after {
        opacity: 0.6;
    }
    <?php endif; ?>
</style>

<div class="main-container">
    <div class="header-section">
        <h1 class="header-title">คณะกรรมการอำนวยการ</h1>
        <div class="header-subtitle">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</div>
    </div>

    <!-- Search Box -->
    <div class="search-box fade-in">
        <input type="text" class="form-control search-input" id="searchInput" placeholder="ค้นหาชื่อหรือตำแหน่ง...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <?php
    $committee_list = [];
    if (isset($conn) && $conn) {
        // ดึงข้อมูลตามลำดับ order_number ที่กำหนดในฐานข้อมูล
        $res = $conn->query("SELECT * FROM steering_committee WHERE status='active' ORDER BY order_number ASC");
        if ($res) { $committee_list = $res->fetch_all(MYSQLI_ASSOC); }
    }
    
    function getCommitteeIcon($category, $position = '') {
        $pos = mb_strtolower($position);
        switch($category) {
            case 'president':
                return 'fa-university';
            case 'vp_dean':
                if (strpos($pos, 'รองอธิการบดี') !== false) return 'fa-user-tie';
                if (strpos($pos, 'คณบดี') !== false) return 'fa-graduation-cap';
                return 'fa-user-tie';
            case 'expert':
                return 'fa-user-graduate';
            case 'school_rep':
                if (strpos($pos, 'ผู้อำนวยการ') !== false) return 'fa-school';
                if (strpos($pos, 'รองผู้อำนวยการ') !== false) return 'fa-user-edit';
                return 'fa-user';
            default:
                return 'fa-user';
        }
    }
    
    // จัดกลุ่มข้อมูลตามหมวดหมู่
    $categorized = [
        'president' => [],
        'vp_dean' => [],
        'expert' => [],
        'school_rep' => []
    ];
    
    foreach ($committee_list as $c) {
        $categorized[$c['category']][] = $c;
    }
    ?>

    <?php if (!empty($committee_list)): ?>
    
    <?php if (!empty($categorized['president'])): ?>
    <!-- President Section -->
    <div class="president-section fade-in">
        <?php foreach ($categorized['president'] as $c): ?>
        <div class="president-card" 
             data-name="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>" 
             data-position="<?php echo htmlspecialchars($c['position']); ?>"
             data-role="<?php echo htmlspecialchars($c['role']); ?>"
             <?php if ($isAdminLoggedIn): ?>
             onclick="window.location.href='admin/steering/view.php?id=<?php echo $c['id']; ?>'"
             style="cursor: pointer;"
             title="คลิกเพื่อดูรายละเอียด"
             <?php endif; ?>>
            <span class="president-label"><i class="fas fa-crown me-1"></i> ประธานกรรมการ</span>
            <div class="president-photo">
                <?php if (!empty($c['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($c['image_path']); ?>" alt="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>">
                <?php else: ?>
                    <i class="fas fa-university"></i>
                <?php endif; ?>
            </div>
            <div class="president-name"><?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?></div>
            <div class="president-position"><?php echo htmlspecialchars($c['position']); ?></div>
            <div class="president-position"><?php echo htmlspecialchars($c['role']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($categorized['vp_dean'])): ?>
    <!-- Vice Presidents & Deans Section -->
    <div class="committee-section fade-in">
        <h2 class="section-title">รองอธิการบดีและคณบดี</h2>
        <div class="committee-grid">
            <?php foreach ($categorized['vp_dean'] as $c): ?>
            <div class="committee-card" 
                 data-name="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>" 
                 data-position="<?php echo htmlspecialchars($c['position']); ?>"
                 data-role="<?php echo htmlspecialchars($c['role']); ?>"
                 <?php if ($isAdminLoggedIn): ?>
                 onclick="window.location.href='admin/steering/view.php?id=<?php echo $c['id']; ?>'"
                 style="cursor: pointer;"
                 title="คลิกเพื่อดูรายละเอียด"
                 <?php endif; ?>>
                <div class="committee-photo">
                    <?php if (!empty($c['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($c['image_path']); ?>" alt="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>">
                    <?php else: ?>
                        <i class="fas <?php echo getCommitteeIcon($c['category'], $c['position']); ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="committee-name"><?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?></div>
                <div class="committee-position"><?php echo htmlspecialchars($c['position']); ?></div>
                <div class="committee-role"><?php echo htmlspecialchars($c['role']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($categorized['expert'])): ?>
    <!-- Expert Committee Section -->
    <div class="committee-section fade-in">
        <h2 class="section-title">กรรมการผู้ทรงคุณวุฒิ</h2>
        <div class="committee-grid">
            <?php foreach ($categorized['expert'] as $c): ?>
            <div class="committee-card" 
                 data-name="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>" 
                 data-position="<?php echo htmlspecialchars($c['position']); ?>"
                 data-role="<?php echo htmlspecialchars($c['role']); ?>"
                 <?php if ($isAdminLoggedIn): ?>
                 onclick="window.location.href='admin/steering/view.php?id=<?php echo $c['id']; ?>'"
                 style="cursor: pointer;"
                 title="คลิกเพื่อดูรายละเอียด"
                 <?php endif; ?>>
                <div class="committee-photo">
                    <?php if (!empty($c['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($c['image_path']); ?>" alt="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>">
                    <?php else: ?>
                        <i class="fas <?php echo getCommitteeIcon($c['category'], $c['position']); ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="committee-name"><?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?></div>
                <div class="committee-role"><?php echo htmlspecialchars($c['role']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($categorized['school_rep'])): ?>
    <!-- School Representatives & Secretariat Section -->
    <div class="committee-section fade-in">
        <h2 class="section-title">ผู้แทนโรงเรียนและฝ่ายเลขานุการ</h2>
        <div class="committee-grid">
            <?php foreach ($categorized['school_rep'] as $c): ?>
            <div class="committee-card" 
                 data-name="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>" 
                 data-position="<?php echo htmlspecialchars($c['position']); ?>"
                 data-role="<?php echo htmlspecialchars($c['role']); ?>"
                 <?php if ($isAdminLoggedIn): ?>
                 onclick="window.location.href='admin/steering/view.php?id=<?php echo $c['id']; ?>'"
                 style="cursor: pointer;"
                 title="คลิกเพื่อดูรายละเอียด"
                 <?php endif; ?>>
                <div class="committee-photo">
                    <?php if (!empty($c['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($c['image_path']); ?>" alt="<?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?>">
                    <?php else: ?>
                        <i class="fas <?php echo getCommitteeIcon($c['category'], $c['position']); ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="committee-name"><?php echo htmlspecialchars($c['title'].' '.$c['first_name'].' '.$c['last_name']); ?></div>
                <div class="committee-position"><?php echo htmlspecialchars($c['position']); ?></div>
                <div class="committee-role"><?php echo htmlspecialchars($c['role']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
        <div class="alert alert-info text-center">ยังไม่มีข้อมูลคณะกรรมการอำนวยการ กรุณาเพิ่มจากระบบผู้ดูแล</div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>

<!-- Custom JS for Search and Animation -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const allCards = document.querySelectorAll('.committee-card, .president-card');
    const sections = document.querySelectorAll('.committee-section, .president-section');
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            allCards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const position = card.getAttribute('data-position') || '';
                const role = card.getAttribute('data-role') || '';
                const searchText = (name + ' ' + position + ' ' + role).toLowerCase();
                
                if (searchText.includes(term) || term === '') {
                    card.style.display = 'block';
                    card.classList.add('search-visible');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.remove('search-visible');
                }
            });
            
            // Hide/show sections based on visible cards
            sections.forEach(section => {
                const visibleCards = section.querySelectorAll('.committee-card.search-visible, .president-card.search-visible');
                if (term !== '') {
                    section.style.display = (visibleCards.length === 0) ? 'none' : 'block';
                } else {
                    section.style.display = 'block';
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
            indicator.style.cssText = `margin: 20px auto; max-width: 400px; border-radius: 25px; border: none; background: linear-gradient(135deg, rgba(106, 17, 203, 0.1) 0%, rgba(37, 117, 252, 0.1) 100%); color: #2c3e50; font-weight: 500;`;
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
