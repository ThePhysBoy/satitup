<?php
// ไฟล์แสดงข่าวกิจกรรม
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/functions.php';

// ดึงข่าวกิจกรรมล่าสุด (category_id = 1 คือหมวดหมู่กิจกรรม)
$events_news = getLatestNews($conn, 6, 1);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข่าวกิจกรรม - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fa;
        }
        .page-header {
            background-color: #7b3b95;
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .event-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,.12);
        }
        .event-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
            background-color: #f0f0f0;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .event-meta {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 15px;
        }
        .event-meta i {
            margin-right: 5px;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
            font-weight: 600;
        }
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #7b3b95;
        }
        .home-btn {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            background-color: white;
            color: #7b3b95;
            border: 2px solid #7b3b95;
        }
        .home-btn:hover {
            background-color: #7b3b95;
            color: white;
        }
        .category-filter {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .filter-btn {
            border-radius: 50px;
            padding: 8px 16px;
            font-size: 0.9rem;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #495057;
            transition: all 0.2s ease;
        }
        .filter-btn:hover, .filter-btn.active {
            background-color: #7b3b95;
            color: white;
            border-color: #7b3b95;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">ข่าวกิจกรรม</h1>
                <a href="../index.php" class="btn home-btn">
                    <i class="fas fa-home me-2"></i>กลับหน้าหลัก
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="category-filter">
            <h5 class="mb-3">หมวดหมู่กิจกรรม</h5>
            <div>
                <button class="filter-btn active">ทั้งหมด</button>
                <button class="filter-btn">กิจกรรมวิชาการ</button>
                <button class="filter-btn">กิจกรรมกีฬา</button>
                <button class="filter-btn">กิจกรรมศิลปวัฒนธรรม</button>
                <button class="filter-btn">กิจกรรมจิตอาสา</button>
                <button class="filter-btn">กิจกรรมพิเศษ</button>
            </div>
        </div>

        <div class="row g-4">
            <?php if (count($events_news) > 0): ?>
                <?php foreach ($events_news as $event): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card event-card">
                            <div class="position-relative">
                                <?php if ($event['featured_image']): ?>
                                    <img class="event-img" src="../<?php echo htmlspecialchars($event['featured_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php else: ?>
                                    <img class="event-img" src="../images/comingsoon.png" alt="ไม่มีรูปภาพ">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="detail.php?slug=<?php echo urlencode($event['slug']); ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </a>
                                </h5>
                                <div class="event-meta">
                                    <span>
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo $event['published_at'] ? date('d/m/Y', strtotime($event['published_at'])) : date('d/m/Y', strtotime($event['created_at'])); ?>
                                    </span>
                                    <span>
                                        <i class="far fa-eye"></i>
                                        <?php echo number_format((int)($event['view_count'] ?? 0)); ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($event['full_name'] ?? 'ระบบ'); ?>
                                </small>
                            </div>
                            <a href="detail.php?slug=<?php echo urlencode($event['slug']); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>ไม่พบข่าวกิจกรรม</h4>
                        <p class="mb-0">ยังไม่มีข่าวกิจกรรมในขณะนี้ โปรดกลับมาตรวจสอบใหม่ในภายหลัง</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-5 text-center">
            <a href="index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-newspaper me-2"></i>ดูข่าวและกิจกรรมทั้งหมด
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ฟังก์ชันสำหรับปุ่มกรองหมวดหมู่
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    // ในกรณีจริงจะต้องมีการส่ง AJAX request เพื่อกรองข้อมูลตามหมวดหมู่
                });
            });
        });
    </script>
</body>
</html>