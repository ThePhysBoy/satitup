<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($announcement['title']); ?> - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B7AA8;      /* ม่วงอ่อนหลัก */
            --primary-light: #B8A9D4;      /* ม่วงอ่อนมาก */
            --primary-dark: #6B5A88;       /* ม่วงเข้ม */
            --secondary-color: #9C89B8;    /* ม่วงรอง */
            --accent-color: #F0A6CA;       /* ชมพูอ่อน */
            --light-accent: #F3EDF7;       /* ม่วงอ่อนมากๆ */
            --text-color: #4A4A4A;         /* สีข้อความหลัก */
            --text-muted: #6C757D;         /* สีข้อความรอง */
            --border-color: #E1D9EB;       /* สีขอบม่วงอ่อน */
            --bg-light: #FAFAFA;           /* พื้นหลังอ่อน */
            --white: #FFFFFF;              /* สีขาว */
        }
        
        body {
            font-family: 'Prompt', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-light);
            line-height: 1.6;
        }
        
        .announcement-header {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .announcement-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .announcement-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        .announcement-meta-item {
            display: flex;
            align-items: center;
            margin-right: 1.5rem;
        }
        
        .announcement-meta-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .announcement-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .announcement-content p {
            margin-bottom: 1.5rem;
        }
        
        .announcement-content img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin: 1rem 0;
        }
        
        .btn-back {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-back i {
            margin-right: 0.5rem;
        }
        
        .announcement-category {
            display: inline-block;
            background-color: var(--primary-light);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .announcement-header {
                padding: 2rem 0;
            }
            
            .announcement-meta {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .announcement-meta-item {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="announcement-header">
        <div class="container">
            <?php if (!empty($announcement['category'])): ?>
            <div class="announcement-category">
                <?php echo htmlspecialchars($announcement['category']); ?>
            </div>
            <?php endif; ?>
            
            <h1 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h1>
            
            <div class="announcement-meta">
                <div>
                    <div class="announcement-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>วันที่ประกาศ: <?php echo date('d/m/Y', strtotime($announcement['created_at'])); ?></span>
                    </div>
                    
                    <?php if (!empty($announcement['updated_at']) && $announcement['updated_at'] != $announcement['created_at']): ?>
                    <div class="announcement-meta-item">
                        <i class="fas fa-edit"></i>
                        <span>ปรับปรุงล่าสุด: <?php echo date('d/m/Y', strtotime($announcement['updated_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="announcement-meta-item">
                    <i class="fas fa-user"></i>
                    <span>โดย: ระบบ</span>
                </div>
            </div>
            
            <a href="announcements.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการประกาศ
            </a>
        </div>
    </header>
    
    <!-- Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="announcement-content">
                    <?php echo $announcement['content']; ?>
                </div>
                
                <div class="text-center mb-5">
                    <a href="announcements.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการประกาศ
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
