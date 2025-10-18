<?php
/**
 * News Admin Dashboard
 * หน้าแดชบอร์ดสำหรับจัดการข่าวสาร
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Include database connection
$conn = require_once '../../admin/includes/db_config.php';

// Get news statistics
$news_count_query = $conn->query("SELECT COUNT(*) as count FROM news WHERE status = 'published'");
$news_count = $news_count_query ? $news_count_query->fetch_assoc()['count'] : 0;

$draft_count_query = $conn->query("SELECT COUNT(*) as count FROM news WHERE status = 'draft'");
$draft_count = $draft_count_query ? $draft_count_query->fetch_assoc()['count'] : 0;

$total_news_query = $conn->query("SELECT COUNT(*) as count FROM news");
$total_news = $total_news_query ? $total_news_query->fetch_assoc()['count'] : 0;

// Get recent news
$recent_news = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข่าวสาร - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: all 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.success { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card h3 { font-size: 2.5rem; margin: 0; }
        .stat-card p { margin: 0; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <h5><i class="fas fa-newspaper me-2"></i>News Admin</h5>
                    <hr class="text-white">
                </div>
                <a href="index.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="../../admin/news/index.php"><i class="fas fa-list me-2"></i>จัดการข่าว</a>
                <a href="../../admin/news/create.php"><i class="fas fa-plus me-2"></i>เพิ่มข่าวใหม่</a>
                <a href="../../admin/news/categories.php"><i class="fas fa-folder me-2"></i>หมวดหมู่</a>
                <hr class="text-white">
                <a href="../../admin/index.php"><i class="fas fa-cog me-2"></i>Admin หลัก</a>
                <a href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="fas fa-newspaper me-2"></i>จัดการข่าวสาร</h2>
                        <p class="text-muted">Dashboard สำหรับจัดการข่าวสารและประกาศ</p>
                    </div>
                    <div>
                        <span class="text-muted">ยินดีต้อนรับ, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong></span>
                    </div>
                </div>

                <?php if (isset($_SESSION['login_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับกลับ <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['login_success']); ?>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card primary">
                            <i class="fas fa-newspaper fa-2x mb-3"></i>
                            <h3><?php echo $total_news; ?></h3>
                            <p>ข่าวสารทั้งหมด</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card success">
                            <i class="fas fa-check-circle fa-2x mb-3"></i>
                            <h3><?php echo $news_count; ?></h3>
                            <p>ข่าวที่เผยแพร่แล้ว</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card info">
                            <i class="fas fa-file-alt fa-2x mb-3"></i>
                            <h3><?php echo $draft_count; ?></h3>
                            <p>แบบร่าง</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="../../admin/news/create.php" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-2"></i>เพิ่มข่าวใหม่
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../../admin/news/index.php" class="btn btn-primary w-100">
                                    <i class="fas fa-list me-2"></i>จัดการข่าว
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../../admin/news/categories.php" class="btn btn-info w-100">
                                    <i class="fas fa-folder me-2"></i>หมวดหมู่
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../../admin/index.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-home me-2"></i>Admin หลัก
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent News -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>ข่าวล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_news && $recent_news->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ชื่อข่าว</th>
                                            <th>สถานะ</th>
                                            <th>วันที่สร้าง</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($news = $recent_news->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($news['title']); ?></td>
                                                <td>
                                                    <?php if ($news['status'] === 'published'): ?>
                                                        <span class="badge bg-success">เผยแพร่แล้ว</span>
                                                    <?php elseif ($news['status'] === 'draft'): ?>
                                                        <span class="badge bg-secondary">แบบร่าง</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">เก็บถาวร</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?></td>
                                                <td>
                                                    <a href="../../admin/news/edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>ยังไม่มีข่าวสารในระบบ
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
