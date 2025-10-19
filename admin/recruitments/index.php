<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireRankingsAccess();

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM recruitment_announcements WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR position_title LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $types .= 'ss';
}

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

$sql .= " ORDER BY published_date DESC, created_at DESC";
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$announcements = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประกาศรับสมัครงาน - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-user-tie text-primary me-2"></i>จัดการประกาศรับสมัครงาน</h1>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>เพิ่มประกาศใหม่</a>
    </div>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อประกาศหรือชื่อตำแหน่ง" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">สถานะทั้งหมด</option>
                <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>ฉบับร่าง</option>
                <option value="open" <?php echo $statusFilter === 'open' ? 'selected' : ''; ?>>เปิดรับสมัคร</option>
                <option value="closed" <?php echo $statusFilter === 'closed' ? 'selected' : ''; ?>>ปิดรับสมัคร</option>
                <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" type="submit"><i class="fas fa-search me-2"></i>ค้นหา</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ตำแหน่ง</th>
                            <th>หัวข้อ</th>
                            <th>หน่วยงาน</th>
                            <th>ประกาศ</th>
                            <th>ปิดรับ</th>
                            <th>สถานะ</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($announcements)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">ไม่มีประกาศรับสมัครงาน</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($announcements as $announce): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($announce['position_title'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($announce['title']); ?></td>
                                    <td><?php echo htmlspecialchars($announce['department'] ?? '-'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($announce['published_date'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($announce['application_deadline'])); ?></td>
                                    <td>
                                        <?php
                                        $badge = 'secondary';
                                        $text = 'ฉบับร่าง';
                                        if ($announce['status'] === 'open') { $badge = 'success'; $text = 'เปิดรับสมัคร'; }
                                        elseif ($announce['status'] === 'closed') { $badge = 'secondary'; $text = 'ปิดรับสมัคร'; }
                                        elseif ($announce['status'] === 'cancelled') { $badge = 'danger'; $text = 'ยกเลิก'; }
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>"><?php echo $text; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?id=<?php echo $announce['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="../../recruitments/view.php?id=<?php echo $announce['id']; ?>" class="btn btn-sm btn-info" target="_blank" rel="noopener"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
