<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM international_assignments WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR person_name LIKE ? OR country LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $types .= 'sss';
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
$assignments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประกาศการไปต่างประเทศ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-globe text-primary me-2"></i>จัดการประกาศการไปต่างประเทศ</h1>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>เพิ่มประกาศใหม่</a>
    </div>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาหัวข้อหรือชื่อผู้เดินทาง" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">สถานะทั้งหมด</option>
                <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>ฉบับร่าง</option>
                <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
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
                            <th>ชื่อผู้เดินทาง</th>
                            <th>หัวข้อประกาศ</th>
                            <th>ประเทศ</th>
                            <th>เริ่มเดินทาง</th>
                            <th>สิ้นสุด</th>
                            <th>สถานะ</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">ไม่มีประกาศการไปต่างประเทศ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['person_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['country']); ?></td>
                                    <td><?php echo !empty($item['start_date']) ? date('d/m/Y', strtotime($item['start_date'])) : '-'; ?></td>
                                    <td><?php echo !empty($item['end_date']) ? date('d/m/Y', strtotime($item['end_date'])) : '-'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $item['status'] === 'published' ? 'success' : 'secondary'; ?>"><?php echo $item['status'] === 'published' ? 'เผยแพร่' : 'ฉบับร่าง'; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="../../international/view.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" target="_blank" rel="noopener"><i class="fas fa-eye"></i></a>
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
