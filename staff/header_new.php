<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บุคลากร - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- Custom Staff CSS -->
    <style>
        :root {
            --primary-color: #8B7AA8;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-color: #F0A6CA;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.12);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Sarabun', 'Kanit', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* Hero Header Section */
        .hero-header {
            background: var(--primary-gradient);
            padding: 100px 0 50px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }
        
        .hero-header::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 300%;
            height: 300%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: movePattern 20s linear infinite;
        }
        
        @keyframes movePattern {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            color: white;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-subtitle {
            color: rgba(255,255,255,0.95);
            font-size: 1.2rem;
            font-weight: 300;
            animation: fadeInUp 1s ease;
        }
        
        .hero-stats {
            margin-top: 40px;
            display: flex;
            gap: 40px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease;
        }
        
        .stat-item {
            text-align: center;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.95;
        }
        
        /* Breadcrumb Custom */
        .breadcrumb-section {
            background: white;
            padding: 15px 0;
            box-shadow: var(--shadow-sm);
            position: relative;
            z-index: 10;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
            background: transparent;
        }
        
        .breadcrumb-custom .breadcrumb-item {
            font-size: 0.95rem;
        }
        
        .breadcrumb-custom .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-custom .breadcrumb-item a:hover {
            color: #667eea;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--text-light);
        }
        
        /* Search Bar */
        .search-section {
            background: white;
            padding: 30px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .search-box {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 122, 168, 0.1);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-box button:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: var(--shadow-md);
        }
        
        /* Filter Tags */
        .filter-tags {
            margin-top: 20px;
            text-align: center;
        }
        
        .filter-tag {
            display: inline-block;
            padding: 8px 20px;
            margin: 5px;
            background: var(--bg-light);
            border: 1px solid #dee2e6;
            border-radius: 25px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .filter-tag:hover,
        .filter-tag.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        /* Animations */
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .hero-stats {
                gap: 20px;
            }
            
            .search-box input {
                font-size: 0.9rem;
                padding: 12px 45px 12px 15px;
            }
            
            .search-box button {
                padding: 8px 20px;
                font-size: 0.9rem;
            }
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.95);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .loading-overlay.show {
            display: flex;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Hero Header -->
<section class="hero-header">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title">
                <i class="fas fa-users"></i> บุคลากรโรงเรียนสาธิต
            </h1>
            <p class="hero-subtitle">
                ทีมผู้เชี่ยวชาญที่มุ่งมั่นพัฒนาการศึกษาสู่ความเป็นเลิศ
            </p>
            
            <!-- Statistics -->
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="50">0</span>
                    <span class="stat-label">บุคลากรทั้งหมด</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="13">0</span>
                    <span class="stat-label">กลุ่มสาระ/แผนก</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="15">0</span>
                    <span class="stat-label">ปริญญาเอก</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="25">0</span>
                    <span class="stat-label">ปริญญาโท</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item">
                    <a href="../index.php"><i class="fas fa-home"></i> หน้าหลัก</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">บุคลากร</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-box">
            <input type="text" id="staffSearch" placeholder="ค้นหาบุคลากร... (ชื่อ, แผนก, ตำแหน่ง)">
            <button type="button" onclick="searchStaff()">
                <i class="fas fa-search"></i> ค้นหา
            </button>
        </div>
        
        <div class="filter-tags">
            <a href="?type=all" class="filter-tag <?php echo (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> ทั้งหมด
            </a>
            <a href="?type=academic" class="filter-tag <?php echo (isset($_GET['type']) && $_GET['type'] == 'academic') ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i> สายวิชาการ
            </a>
            <a href="?type=primary" class="filter-tag <?php echo (isset($_GET['type']) && $_GET['type'] == 'primary') ? 'active' : ''; ?>">
                <i class="fas fa-child"></i> ประถมศึกษา
            </a>
            <a href="?type=support" class="filter-tag <?php echo (isset($_GET['type']) && $_GET['type'] == 'support') ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> สายสนับสนุน
            </a>
            <a href="?type=management" class="filter-tag <?php echo (isset($_GET['type']) && $_GET['type'] == 'management') ? 'active' : ''; ?>">
                <i class="fas fa-user-tie"></i> ผู้บริหาร
            </a>
        </div>
    </div>
</section>

<!-- JavaScript for Counter Animation -->
<script>
    // Counter Animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        counters.forEach(counter => {
            const animate = () => {
                const value = +counter.getAttribute('data-count');
                const data = +counter.innerText;
                const time = value / speed;
                
                if (data < value) {
                    counter.innerText = Math.ceil(data + time);
                    setTimeout(animate, 1);
                } else {
                    counter.innerText = value;
                }
            }
            animate();
        });
    });
    
    // Search Function
    function searchStaff() {
        const searchValue = document.getElementById('staffSearch').value;
        if (searchValue.trim()) {
            window.location.href = `?search=${encodeURIComponent(searchValue)}`;
        }
    }
    
    // Enter key for search
    document.getElementById('staffSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchStaff();
        }
    });
</script>
