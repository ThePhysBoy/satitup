<?php
/**
 * News Management Index - Using Modern Template
 * This page displays a list of news articles and allows management
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news management permission
requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header("Location: ../index.php");
    exit;
}

// Get current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get filter values
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query conditions
$conditions = [];
$params = [];
$types = '';

if ($category_id > 0) {
    $conditions[] = "n.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search)) {
    $conditions[] = "(n.title LIKE ? OR n.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$where_clause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : '';

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM news n $where_clause";
$stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$total_rows = $result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $per_page);

// Get news list
$sql = "SELECT n.*, u.username, u.full_name
        FROM news n
        LEFT JOIN users u ON n.author_id = u.id
        $where_clause
        ORDER BY n.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$news_list = $result->fetch_all(MYSQLI_ASSOC);

// Get categories for filter
$stmt = $conn->prepare("SELECT * FROM news_categories ORDER BY name");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];

    // Check if user is authorized to delete
    if (isAdmin() || isPrOfficer()) {
        // First delete associated images from disk
        $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ?");
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();
        $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($images as $image) {
            $image_path = '../../' . $image['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete featured image if exists
        $stmt = $conn->prepare("SELECT featured_image FROM news WHERE id = ?");
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();
        $featured_image = $stmt->get_result()->fetch_assoc()['featured_image'];

        if ($featured_image) {
            $image_path = '../../' . $featured_image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Then delete the news (cascade will delete images from database)
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();

        // Redirect to refresh the page
        header("Location: index_template_example.php");
        exit;
    }
}

// Set page variables for template
$page_title = "จัดการข่าวและกิจกรรม";
$page_header_icon = '<i class="fas fa-newspaper me-3"></i>';
$back_button = false;

// Build content
ob_start();
?>

<!-- Filters -->
<div class="card-modern">
    <div class="card-header-modern">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>ค้นหาและกรองข่าว</h6>
    </div>
    <div class="card-body-modern">
        <form action="index_template_example.php" method="get" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label-modern">ค้นหา</label>
                <input type="text" class="form-control form-control-modern" id="search" name="search" placeholder="ค้นหาจากชื่อหรือเนื้อหา" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <label for="category" class="form-label-modern">หมวดหมู่</label>
                <select class="form-select form-select-modern" id="category" name="category">
                    <option value="0">ทั้งหมด</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary-gradient me-2">
                    <i class="fas fa-search me-1"></i> ค้นหา
                </button>
                <a href="index_template_example.php" class="btn btn-glass">
                    <i class="fas fa-redo me-1"></i> ล้างตัวกรอง
                </a>
            </div>
        </form>
    </div>
</div>

<!-- News Table -->
<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>รายการข่าวและกิจกรรม</h6>
        <div class="d-flex align-items-center">
            <a href="create.php" class="btn btn-primary-gradient me-2">
                <i class="fas fa-plus me-1"></i> เพิ่มข่าวใหม่
            </a>
            <span class="badge bg-primary fs-6"><?php echo $total_rows; ?> รายการ</span>
        </div>
    </div>
    <div class="card-body-modern">
        <?php if (count($news_list) > 0): ?>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="60">รูปภาพ</th>
                            <th>หัวข้อ</th>
                            <th>สถานะ</th>
                            <th>ผู้เขียน</th>
                            <th>วันที่</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_list as $news): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($news['featured_image'])): ?>
                                        <img src="../../<?php echo htmlspecialchars($news['featured_image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background-color: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                    <?php if (!empty($news['excerpt'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($news['excerpt'], 0, 100)) . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($news['status'] === 'published'): ?>
                                        <span class="status-badge status-published">เผยแพร่แล้ว</span>
                                    <?php else: ?>
                                        <span class="status-badge status-draft">ฉบับร่าง</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($news['full_name'] ?? $news['username']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($news['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="view.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-info" title="ดู">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                title="ลบ"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?php echo $news['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($news['title']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center pagination-modern">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page - 1); ?>&category=<?php echo $category_id; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page + 1); ?>&category=<?php echo $category_id; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>ไม่พบข่าว</h4>
                <p class="mb-0">ไม่พบข่าวที่ตรงกับเงื่อนไขที่ค้นหา</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border);">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบข่าว
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบข่าว "<span id="deleteNewsTitle"></span>" ใช่หรือไม่?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> การลบข่าวจะไม่สามารถกู้คืนได้</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="index_template_example.php">
                    <input type="hidden" name="delete_id" id="deleteNewsId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">ลบข่าว</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');

            document.getElementById('deleteNewsId').value = id;
            document.getElementById('deleteNewsTitle').textContent = title;
        });
    }
});
</script>

<?php
$content = ob_get_clean();

// Include the template
include 'template.php';
?>
