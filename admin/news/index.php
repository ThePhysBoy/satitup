<?php
/**
 * News Index
 * List all news articles
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$direction = isset($_GET['direction']) ? $_GET['direction'] : 'desc';

// Allowed sort fields and directions
$allowed_sorts = ['id', 'title', 'status', 'category_id', 'created_at', 'published_at', 'views'];
$allowed_directions = ['asc', 'desc'];

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'created_at';
}

if (!in_array($direction, $allowed_directions)) {
    $direction = 'desc';
}

// Build query
$sql = "SELECT n.*, u.username
        FROM news n
        LEFT JOIN users u ON n.author_id = u.id
        ORDER BY n.$sort $direction
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$news_list = $result->fetch_all(MYSQLI_ASSOC);

// Get total count for pagination
$count_result = $conn->query("SELECT COUNT(*) as total FROM news");
$count_row = $count_result->fetch_assoc();
$total_news = $count_row['total'];
$total_pages = ceil($total_news / $per_page);

// Set page variables
$page_title = 'จัดการข่าวประชาสัมพันธ์';
$page_header_icon = '<i class="fas fa-newspaper me-2"></i>';

// Build content
ob_start();
?>

<div class="d-flex justify-content-between mb-4">
    <div>
        <a href="dashboard.php" class="btn btn-info btn-modern me-2">
            <i class="fas fa-tachometer-alt me-2"></i>แดชบอร์ด
        </a>
        <a href="search.php" class="btn btn-light btn-modern">
            <i class="fas fa-search me-2"></i>ค้นหาข่าว
        </a>
    </div>
    <div>
        <a href="create.php" class="btn btn-primary btn-modern">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มข่าวใหม่
        </a>
    </div>
</div>

<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>รายการข่าวทั้งหมด</h5>
        <a href="export_form.php" class="btn btn-sm btn-outline-primary btn-modern">
            <i class="fas fa-file-export me-2"></i>ส่งออกข้อมูล
        </a>
    </div>
    <div class="card-body-modern p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>
                            <a href="?sort=id&direction=<?php echo ($sort == 'id' && $direction == 'asc') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                ID
                                <?php if ($sort == 'id'): ?>
                                    <i class="fas fa-sort-<?php echo $direction == 'asc' ? 'up' : 'down'; ?> ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=title&direction=<?php echo ($sort == 'title' && $direction == 'asc') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                หัวข้อ
                                <?php if ($sort == 'title'): ?>
                                    <i class="fas fa-sort-<?php echo $direction == 'asc' ? 'up' : 'down'; ?> ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=status&direction=<?php echo ($sort == 'status' && $direction == 'asc') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                สถานะ
                                <?php if ($sort == 'status'): ?>
                                    <i class="fas fa-sort-<?php echo $direction == 'asc' ? 'up' : 'down'; ?> ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>ผู้เขียน</th>
                        <th>
                            <a href="?sort=created_at&direction=<?php echo ($sort == 'created_at' && $direction == 'asc') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                วันที่สร้าง
                                <?php if ($sort == 'created_at'): ?>
                                    <i class="fas fa-sort-<?php echo $direction == 'asc' ? 'up' : 'down'; ?> ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=views&direction=<?php echo ($sort == 'views' && $direction == 'asc') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                ยอดอ่าน
                                <?php if ($sort == 'views'): ?>
                                    <i class="fas fa-sort-<?php echo $direction == 'asc' ? 'up' : 'down'; ?> ms-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>ข่าวเด่น</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($news_list) > 0): ?>
                        <?php foreach ($news_list as $news): ?>
                            <tr>
                                <td><?php echo $news['id']; ?></td>
                                <td><?php echo htmlspecialchars(mb_strimwidth($news['title'], 0, 50, '...')); ?></td>
                                <td>
                                    <?php if ($news['status'] == 'published'): ?>
                                        <span class="badge bg-success">เผยแพร่</span>
                                    <?php elseif ($news['status'] == 'draft'): ?>
                                        <span class="badge bg-warning text-dark">แบบร่าง</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">รอตรวจสอบ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($news['username'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($news['created_at'])); ?></td>
                                <td><i class="fas fa-eye me-1"></i><?php echo $news['views']; ?></td>
                                <td>
                                    <?php if ($news['is_featured']): ?>
                                        <span class="badge bg-info"><i class="fas fa-star me-1"></i>ใช่</span>
                                        <a href="delete_featured.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-outline-secondary" title="ยกเลิกข่าวเด่น">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">ไม่ใช่</span>
                                        <a href="set_featured.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-outline-info" title="ตั้งเป็นข่าวเด่น">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="view.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-info btn-modern" title="ดูข่าว">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_new.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary btn-modern" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger btn-modern" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModal<?php echo $news['id']; ?>"
                                           title="ลบ">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $news['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">ยืนยันการลบข่าว</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>คุณแน่ใจหรือไม่ที่จะลบข่าว "<strong><?php echo htmlspecialchars($news['title']); ?></strong>"?</p>
                                                    <p class="text-danger">การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">ยกเลิก</button>
                                                    <a href="delete.php?id=<?php echo $news['id']; ?>" class="btn btn-danger btn-modern">ลบข่าว</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">ไม่พบข้อมูลข่าว</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($total_pages > 1): ?>
        <div class="card-footer-modern">
            <nav>
                <ul class="pagination pagination-modern justify-content-center mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&sort=<?php echo $sort; ?>&direction=<?php echo $direction; ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&direction=<?php echo $direction; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&direction=<?php echo $direction; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&direction=<?php echo $direction; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $total_pages; ?>&sort=<?php echo $sort; ?>&direction=<?php echo $direction; ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

// Include the template
include 'template.php';
?>