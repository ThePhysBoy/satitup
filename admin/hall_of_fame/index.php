<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// รับพารามิเตอร์การกรอง
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// สร้าง query
$where_conditions = ["1=1"];

if ($category_filter) {
    $where_conditions[] = "category = '" . $conn->real_escape_string($category_filter) . "'";
}

if ($status_filter) {
    $where_conditions[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
}

if ($search) {
    $search_safe = $conn->real_escape_string($search);
    $where_conditions[] = "(student_name LIKE '%$search_safe%' OR title LIKE '%$search_safe%')";
}

$where_clause = " WHERE " . implode(" AND ", $where_conditions);

// นับจำนวนทั้งหมด
$count_query = "SELECT COUNT(*) as total FROM hall_of_fame" . $where_clause;
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// ดึงข้อมูล
$sql = "SELECT * FROM hall_of_fame" . $where_clause . 
       " ORDER BY created_at DESC
        LIMIT $offset, $per_page";

$result = $conn->query($sql);
$achievements = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $achievements[] = $row;
    }
}

// ดึงสถิติ
$stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured
              FROM hall_of_fame";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// ดึงสถิติตามหมวดหมู่
$category_stats_sql = "SELECT category, COUNT(*) as count 
                       FROM hall_of_fame 
                       GROUP BY category";
$category_stats_result = $conn->query($category_stats_sql);
$category_stats = [];
while ($row = $category_stats_result->fetch_assoc()) {
    $category_stats[$row['category']] = $row['count'];
}

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

// จัดการลบ
if (isset($_GET['delete']) && isset($_GET['confirm'])) {
    $delete_id = intval($_GET['delete']);
    
    // ดึงข้อมูลไฟล์เพื่อลบ
    $file_sql = "SELECT image_path, certificate_path FROM hall_of_fame WHERE id = $delete_id";
    $file_result = $conn->query($file_sql);
    if ($file_row = $file_result->fetch_assoc()) {
        // ลบไฟล์รูปภาพ
        if ($file_row['image_path'] && file_exists('../../' . $file_row['image_path'])) {
            unlink('../../' . $file_row['image_path']);
        }
        // ลบไฟล์ใบประกาศ
        if ($file_row['certificate_path'] && file_exists('../../' . $file_row['certificate_path'])) {
            unlink('../../' . $file_row['certificate_path']);
        }
        
        // ลบรูปภาพในแกลเลอรี่
        $gallery_sql = "SELECT image_path FROM hall_of_fame_gallery WHERE hall_id = $delete_id";
        $gallery_result = $conn->query($gallery_sql);
        while ($gallery_row = $gallery_result->fetch_assoc()) {
            if (file_exists('../../' . $gallery_row['image_path'])) {
                unlink('../../' . $gallery_row['image_path']);
            }
        }
    }
    
    // ลบข้อมูลจากฐานข้อมูล
    $delete_sql = "DELETE FROM hall_of_fame WHERE id = $delete_id";
    if ($conn->query($delete_sql)) {
        $_SESSION['success'] = "ลบข้อมูลสำเร็จ";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    }
    
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหอเกียรติยศ - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: #f5f5f5;
        }
        
        .sidebar {
            background: #2c3e50;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .page-header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .data-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .category-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }
        
        .thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .status-badge {
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.85rem;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">
                    <i class="fas fa-trophy"></i> Admin Panel
                </h4>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="add.php">
                        <i class="fas fa-plus me-2"></i> เพิ่มรางวัล
                    </a>
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-2"></i> กลับหน้า Admin
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-trophy me-2"></i> จัดการหอเกียรติยศ</h2>
                            <p class="text-muted mb-0">จัดการข้อมูลนักเรียนที่สร้างชื่อเสียง</p>
                        </div>
                        <div>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> เพิ่มรางวัลใหม่
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- แสดงข้อความแจ้งเตือน -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-primary"><?php echo $stats['total']; ?></div>
                                    <div class="stat-label">ทั้งหมด</div>
                                </div>
                                <div>
                                    <i class="fas fa-trophy fa-2x text-primary opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-success"><?php echo $stats['active']; ?></div>
                                    <div class="stat-label">เผยแพร่</div>
                                </div>
                                <div>
                                    <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-warning"><?php echo $stats['featured']; ?></div>
                                    <div class="stat-label">แนะนำ</div>
                                </div>
                                <div>
                                    <i class="fas fa-star fa-2x text-warning opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-danger"><?php echo $stats['inactive']; ?></div>
                                    <div class="stat-label">ไม่เผยแพร่</div>
                                </div>
                                <div>
                                    <i class="fas fa-times-circle fa-2x text-danger opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">หมวดหมู่</label>
                            <select name="category" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="academic" <?php echo $category_filter == 'academic' ? 'selected' : ''; ?>>วิชาการ</option>
                                <option value="sports" <?php echo $category_filter == 'sports' ? 'selected' : ''; ?>>กีฬา</option>
                                <option value="music" <?php echo $category_filter == 'music' ? 'selected' : ''; ?>>ดนตรี</option>
                                <option value="scholarship" <?php echo $category_filter == 'scholarship' ? 'selected' : ''; ?>>ทุนการศึกษา</option>
                                <option value="outstanding" <?php echo $category_filter == 'outstanding' ? 'selected' : ''; ?>>ความโดดเด่น</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>เผยแพร่</option>
                                <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>ไม่เผยแพร่</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">ค้นหา</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="ชื่อนักเรียน, รางวัล..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Data Table -->
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="60">รูป</th>
                                    <th>ชื่อรางวัล</th>
                                    <th>นักเรียน</th>
                                    <th>หมวดหมู่</th>
                                    <th>ปี</th>
                                    <th>สถานะ</th>
                                    <th width="100">การดู</th>
                                    <th width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($achievements)): ?>
                                    <?php foreach ($achievements as $item): 
                                        $cat_info = getCategoryInfo($item['category']);
                                    ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td>
                                            <?php if ($item['image_path']): ?>
                                            <img src="../../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                 class="thumbnail"
                                                 alt="<?php echo htmlspecialchars($item['student_name']); ?>">
                                            <?php else: ?>
                                            <div class="thumbnail bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas <?php echo $cat_info['icon']; ?> text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                            <?php if ($item['featured']): ?>
                                            <i class="fas fa-star text-warning ms-1" title="แนะนำ"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['student_name']); ?></td>
                                        <td>
                                            <span class="category-badge" 
                                                  style="background: <?php echo $cat_info['color']; ?>;">
                                                <i class="fas <?php echo $cat_info['icon']; ?> me-1"></i>
                                                <?php echo $cat_info['name']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['year']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $item['status']; ?>">
                                                <?php echo $item['status'] == 'active' ? 'เผยแพร่' : 'ไม่เผยแพร่'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($item['views']); ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $item['id']; ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../../hall_of_fame/view.php?id=<?php echo $item['id']; ?>" 
                                               target="_blank"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">ไม่พบข้อมูล</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function confirmDelete(id) {
        if (confirm('คุณแน่ใจที่จะลบรายการนี้? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
            window.location.href = '?delete=' + id + '&confirm=1';
        }
    }
    </script>
</body>
</html>
