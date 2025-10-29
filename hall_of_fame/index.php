<?php
// เชื่อมต่อฐานข้อมูล
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'satitup';
$db_port = 3306;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// รับพารามิเตอร์การกรอง
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// สร้าง query
$where_conditions = ["status = 'active'"];

if ($category != 'all') {
    $where_conditions[] = "category = '" . $conn->real_escape_string($category) . "'";
}

if ($year_filter) {
    $where_conditions[] = "year = '" . $conn->real_escape_string($year_filter) . "'";
}

if ($search) {
    $search_safe = $conn->real_escape_string($search);
    $where_conditions[] = "(student_name LIKE '%$search_safe%' OR title LIKE '%$search_safe%' OR achievement LIKE '%$search_safe%')";
}

$where_clause = " WHERE " . implode(" AND ", $where_conditions);

// นับจำนวนทั้งหมด
$count_query = "SELECT COUNT(*) as total FROM hall_of_fame" . $where_clause;
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// ดึงข้อมูล
$sql = "SELECT * FROM hall_of_fame" . $where_clause . 
       " ORDER BY featured DESC, date_achieved DESC, created_at DESC
        LIMIT $offset, $per_page";

$result = $conn->query($sql);
$achievements = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $achievements[] = $row;
    }
}

// ดึงสถิติแต่ละหมวดหมู่
$stats = [
    'academic' => 0,
    'sports' => 0,
    'music' => 0,
    'scholarship' => 0,
    'outstanding' => 0
];

$stats_sql = "SELECT category, COUNT(*) as count 
              FROM hall_of_fame 
              WHERE status = 'active' 
              GROUP BY category";
$stats_result = $conn->query($stats_sql);
if ($stats_result) {
    while ($row = $stats_result->fetch_assoc()) {
        $stats[$row['category']] = $row['count'];
    }
}

// ดึงปีที่มีข้อมูล
$years_sql = "SELECT DISTINCT year FROM hall_of_fame WHERE status = 'active' ORDER BY year DESC";
$years_result = $conn->query($years_sql);
$years = [];
if ($years_result) {
    while ($row = $years_result->fetch_assoc()) {
        $years[] = $row['year'];
    }
}

$sdg_meta = [
    1 => ['color' => '#E5243B', 'name' => 'SDG 1: ขจัดความยากจน'],
    2 => ['color' => '#DDA63A', 'name' => 'SDG 2: ขจัดความหิวโหย'],
    3 => ['color' => '#4C9F38', 'name' => 'SDG 3: สุขภาพและความเป็นอยู่ที่ดี'],
    4 => ['color' => '#C5192D', 'name' => 'SDG 4: การศึกษาที่มีคุณภาพ'],
    5 => ['color' => '#FF3A21', 'name' => 'SDG 5: ความเท่าเทียมทางเพศ'],
    6 => ['color' => '#26BDE2', 'name' => 'SDG 6: น้ำสะอาดและสุขาภิบาล'],
    7 => ['color' => '#FCC30B', 'name' => 'SDG 7: พลังงานสะอาดที่เข้าถึงได้'],
    8 => ['color' => '#A21942', 'name' => 'SDG 8: งานที่มีคุณค่าและการเติบโตทางเศรษฐกิจ'],
    9 => ['color' => '#FD6925', 'name' => 'SDG 9: อุตสาหกรรม นวัตกรรม และโครงสร้างพื้นฐาน'],
    10 => ['color' => '#DD1367', 'name' => 'SDG 10: ลดความเหลื่อมล้ำ'],
    11 => ['color' => '#FD9D24', 'name' => 'SDG 11: เมืองและชุมชนที่ยั่งยืน'],
    12 => ['color' => '#BF8B2E', 'name' => 'SDG 12: การบริโภคและการผลิตที่ยั่งยืน'],
    13 => ['color' => '#3F7E44', 'name' => 'SDG 13: การดำเนินการด้านสภาพภูมิอากาศ'],
    14 => ['color' => '#0A97D9', 'name' => 'SDG 14: ชีวิตใต้น้ำ'],
    15 => ['color' => '#56C02B', 'name' => 'SDG 15: ชีวิตบนบก'],
    16 => ['color' => '#00689D', 'name' => 'SDG 16: สันติภาพ ความยุติธรรม และสถาบันที่เข้มแข็ง'],
    17 => ['color' => '#19486A', 'name' => 'SDG 17: ความร่วมมือเพื่อบรรลุเป้าหมาย'],
];

// ฟังก์ชันสำหรับหมวดหมู่
function getCategoryInfo($category) {
    $categories = [
        'academic' => ['name' => 'วิชาการ', 'icon' => 'fa-graduation-cap', 'color' => '#3498db'],
        'sports' => ['name' => 'กีฬา', 'icon' => 'fa-trophy', 'color' => '#e74c3c'],
        'music' => ['name' => 'ดนตรี', 'icon' => 'fa-music', 'color' => '#9b59b6'],
        'scholarship' => ['name' => 'ทุนการศึกษา', 'icon' => 'fa-award', 'color' => '#f39c12'],
        'outstanding' => ['name' => 'ความโดดเด่น', 'icon' => 'fa-star', 'color' => '#27ae60']
    ];
    return isset($categories[$category]) ? $categories[$category] : ['name' => $category, 'icon' => 'fa-medal', 'color' => '#666'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หอเกียรติยศ - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ============================================= */
        /* GOLDEN EFFECT STYLES - INLINE CSS */
        /* ============================================= */

        /* Reset card styles for golden effect */
        .achievement-card {
            background: #ffffff;
            border-radius: 15px;
            position: relative;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            overflow: hidden;
        }

        /* Golden animated border - placed outside card */
        .achievement-card::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 18px;
            background: linear-gradient(45deg,
                #FF6B35, #F7931E, #FDC830, #FFD700,
                #FFA500, #FF6347, #FF6B35
            );
            background-size: 400% 400%;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: gradient-animation 3s ease infinite;
            z-index: -1;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Show golden border on hover */
        .achievement-card:hover::before {
            opacity: 1;
        }

        /* Shine sweep effect */
        .achievement-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 215, 0, 0.5) 50%,
                transparent 70%
            );
            transform: rotate(45deg) translateX(-100%);
            transition: transform 0.6s;
            opacity: 0;
            pointer-events: none;
        }

        .achievement-card:hover::after {
            transform: rotate(45deg) translateX(100%);
            opacity: 1;
        }

        /* Main hover transformation */
        .achievement-card:hover {
            transform: translateY(-20px) scale(1.05);
            box-shadow:
                0 30px 60px rgba(255, 107, 53, 0.4),
                0 0 100px rgba(255, 165, 0, 0.3),
                0 0 140px rgba(255, 215, 0, 0.2);
            animation: glow-pulse 2s ease-in-out infinite;
        }

        /* Glow pulse animation */
        @keyframes glow-pulse {
            0%, 100% {
                box-shadow:
                    0 30px 60px rgba(255, 107, 53, 0.4),
                    0 0 100px rgba(255, 165, 0, 0.3),
                    0 0 140px rgba(255, 215, 0, 0.2);
            }
            50% {
                box-shadow:
                    0 35px 70px rgba(255, 107, 53, 0.6),
                    0 0 120px rgba(255, 165, 0, 0.5),
                    0 0 160px rgba(255, 215, 0, 0.3);
            }
        }

        /* Image effects */
        .achievement-card .achievement-image {
            transition: all 0.5s ease;
            filter: brightness(0.95);
        }

        .achievement-card:hover .achievement-image {
            filter: brightness(1.2) saturate(1.3);
            transform: scale(1.05);
        }

        /* Text golden gradient on hover */
        .achievement-card .achievement-title {
            transition: all 0.5s ease;
        }

        .achievement-card:hover .achievement-title {
            background: linear-gradient(45deg, #FF6B35, #FFA500, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transform: scale(1.05);
            filter: drop-shadow(0 0 10px rgba(255, 165, 0, 0.5));
        }

        .achievement-card:hover .achievement-name {
            background: linear-gradient(45deg, #FF6B35, #FFA500, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Category badge glow */
        .achievement-card:hover .achievement-category {
            box-shadow: 0 0 20px rgba(255, 165, 0, 0.5);
            transform: scale(1.1);
        }

        /* Override any conflicting styles */
        .achievement-grid {
            position: relative;
            z-index: 1;
        }

        .achievement-body {
            position: relative;
            z-index: 10;
            background: transparent;
        }

        /* Fix for featured cards */
        .achievement-card.featured {
            overflow: visible;
        }

        .featured-star {
            z-index: 100;
        }
    </style>
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .category-tabs {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .category-tabs .nav-pills .nav-link {
            border-radius: 25px;
            padding: 10px 25px;
            margin: 0 5px;
            color: #666;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .category-tabs .nav-pills .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .category-tabs .nav-pills .nav-link.active {
            color: white;
        }
        
        .category-tabs .nav-pills .nav-link i {
            font-size: 1.2rem;
        }
        
        .stat-badge {
            background: rgba(255,255,255,0.3);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-left: 5px;
        }
        
        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 40px;
            perspective: 1000px;
        }
        
        /* Fade in animation on page load */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Initial state for fade in animation */
        .achievement-grid .achievement-card {
            animation: fadeInUp 0.8s ease forwards;
        }

        /* Stagger animation delay for each card */
        .achievement-grid .achievement-card:nth-child(1) { animation-delay: 0.05s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(2) { animation-delay: 0.1s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(3) { animation-delay: 0.15s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(4) { animation-delay: 0.2s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(5) { animation-delay: 0.25s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(6) { animation-delay: 0.3s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(7) { animation-delay: 0.35s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(8) { animation-delay: 0.4s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(9) { animation-delay: 0.45s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(10) { animation-delay: 0.5s; opacity: 0; }
        .achievement-grid .achievement-card:nth-child(n+11) { animation-delay: 0.6s; opacity: 0; }
        
        @media (max-width: 1200px) {
            .achievement-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .achievement-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .achievement-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .achievement-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .achievement-card.featured {
            border: 2px solid gold;
        }
        
        /* Star icon for featured cards */
        .featured-star {
            position: absolute;
            top: 10px;
            right: 10px;
            color: gold;
            font-size: 1.5rem;
            z-index: 10;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
        }
        
        .achievement-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            filter: grayscale(50%) brightness(1.05);
            opacity: 0.9;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }
        

        @keyframes imageFloat {
            0%, 100% {
                transform: scale(1.05) translateY(0);
            }
            50% {
                transform: scale(1.05) translateY(-3px);
            }
        }
        
        .achievement-body {
            padding: 15px;
            position: relative;
            z-index: 3;
        }
        
        .achievement-category {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            margin-bottom: 8px;
        }
        
        .achievement-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }
        

        @keyframes textGlow {
            0%, 100% {
                filter: drop-shadow(0 2px 8px rgba(255, 107, 53, 0.4));
            }
            50% {
                filter: drop-shadow(0 2px 15px rgba(255, 165, 0, 0.8));
            }
        }
        
        .achievement-name {
            font-size: 0.9rem;
            color: #34495e;
            margin-bottom: 5px;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }
        
        
        .achievement-meta {
            color: #7f8c8d;
            font-size: 0.8rem;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .achievement-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .achievement-sdg-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 10px;
            margin-top: 4px;
        }

        .achievement-sdg-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            color: #fff;
            font-weight: 600;
            font-size: 11px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            opacity: 0.75;
            transition: all 0.2s ease;
            position: relative;
        }

        .achievement-sdg-badge:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .achievement-sdg-badge::after {
            content: attr(data-sdg-name);
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.85);
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .achievement-sdg-badge:hover::after {
            opacity: 1;
        }
        
        .filter-bar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #adb5bd;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .category-tabs .nav-pills {
                flex-wrap: wrap;
            }
            
            .category-tabs .nav-pills .nav-link {
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-trophy"></i> หอเกียรติยศ
                    </h1>
                    <p class="text-muted">นักเรียนที่สร้างชื่อเสียงให้กับโรงเรียน</p>
                </div>
                <div>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Category Tabs -->
        <div class="category-tabs">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $category == 'all' ? 'active' : ''; ?>" 
                       href="?category=all"
                       style="<?php echo $category == 'all' ? 'background: linear-gradient(135deg, #667eea, #764ba2);' : ''; ?>">
                        <i class="fas fa-th"></i>
                        <span>ทั้งหมด</span>
                        <span class="stat-badge"><?php echo array_sum($stats); ?></span>
                    </a>
                </li>
                <?php 
                $categories = ['academic', 'sports', 'music', 'scholarship', 'outstanding'];
                foreach ($categories as $cat): 
                    $cat_info = getCategoryInfo($cat);
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $category == $cat ? 'active' : ''; ?>" 
                       href="?category=<?php echo $cat; ?>"
                       style="<?php echo $category == $cat ? 'background: ' . $cat_info['color'] . ';' : ''; ?>">
                        <i class="fas <?php echo $cat_info['icon']; ?>"></i>
                        <span><?php echo $cat_info['name']; ?></span>
                        <?php if ($stats[$cat] > 0): ?>
                        <span class="stat-badge"><?php echo $stats[$cat]; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                
                <div class="col-md-4">
                    <label class="form-label">ปีการศึกษา</label>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        <option value="">ทุกปี</option>
                        <?php foreach ($years as $y): ?>
                        <option value="<?php echo $y; ?>" <?php echo $year_filter == $y ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="ชื่อนักเรียน, รางวัล, ผลงาน..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Achievement Grid -->
        <?php if (!empty($achievements)): ?>
        <div class="achievement-grid">
            <?php foreach ($achievements as $achievement): 
                $cat_info = getCategoryInfo($achievement['category']);
            ?>
            <div class="achievement-card <?php echo $achievement['featured'] ? 'featured' : ''; ?>">
                <?php if ($achievement['featured']): ?>
                    <i class="fas fa-star featured-star"></i>
                <?php endif; ?>
                <?php if ($achievement['image_path']): ?>
                <img src="../<?php echo htmlspecialchars($achievement['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($achievement['student_name']); ?>" 
                     class="achievement-image">
                <?php else: ?>
                <div class="achievement-image d-flex align-items-center justify-content-center">
                    <i class="fas <?php echo $cat_info['icon']; ?>" style="font-size: 3rem; color: #dee2e6;"></i>
                </div>
                <?php endif; ?>
                
                <div class="achievement-body">
                    <span class="achievement-category" style="background: <?php echo $cat_info['color']; ?>;">
                        <i class="fas <?php echo $cat_info['icon']; ?> me-1"></i>
                        <?php echo $cat_info['name']; ?>
                    </span>

                    <?php if (!empty($achievement['sdg_goals'])): ?>
                    <div class="achievement-sdg-badges">
                        <?php
                        $sdg_values = array_filter(array_map('trim', explode(',', $achievement['sdg_goals'])));
                        foreach ($sdg_values as $sdg) {
                            $sdgKey = (int)$sdg;
                            if (!isset($sdg_meta[$sdgKey])) {
                                continue;
                            }
                            $meta = $sdg_meta[$sdgKey];
                        ?>
                        <span class="achievement-sdg-badge" style="background-color: <?php echo $meta['color']; ?>" data-sdg-name="<?php echo htmlspecialchars($meta['name']); ?>">
                            <?php echo $sdgKey; ?>
                        </span>
                        <?php } ?>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="achievement-title" title="<?php echo htmlspecialchars($achievement['title']); ?>">
                        <?php 
                        $title = htmlspecialchars($achievement['title']);
                        echo mb_strlen($title) > 50 ? mb_substr($title, 0, 50) . '...' : $title;
                        ?>
                    </h3>
                    
                    <div class="achievement-name">
                        <i class="fas fa-user-graduate me-1"></i>
                        <?php echo htmlspecialchars($achievement['student_name']); ?>
                    </div>
                    
                    <div class="achievement-meta">
                        <?php if ($achievement['class']): ?>
                        <span>
                            <i class="fas fa-school"></i>
                            <?php echo htmlspecialchars($achievement['class']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($achievement['year']): ?>
                        <span>
                            <i class="fas fa-calendar"></i>
                            <?php echo htmlspecialchars($achievement['year']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-3">
                        <a href="view.php?id=<?php echo $achievement['id']; ?>" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> ดูรายละเอียด
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?category=<?php echo $category; ?>&year=<?php echo $year_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page-1; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?category=<?php echo $category; ?>&year=<?php echo $year_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?category=<?php echo $category; ?>&year=<?php echo $year_filter; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page+1; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-trophy"></i>
            <h4>ไม่พบข้อมูลหอเกียรติยศ</h4>
            <p>ยังไม่มีข้อมูลในหมวดหมู่นี้ หรือไม่พบข้อมูลที่ตรงกับการค้นหา</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
