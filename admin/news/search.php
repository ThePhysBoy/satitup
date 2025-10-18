<?php
/**
 * News Search
 * Search news articles
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Initialize variables
$search_term = '';
$category_id = '';
$status = '';
$search_results = [];
$total_results = 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$categories = getAllCategories($conn);

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search_term = trim($_GET['search_term'] ?? '');
    $category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
    
    // Build SQL query
    $sql = "SELECT n.*, u.username, u.full_name
            FROM news n
            LEFT JOIN users u ON n.author_id = u.id
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    // Add search term condition if provided
    if (!empty($search_term)) {
        $sql .= " AND (n.title LIKE ? OR n.content LIKE ? OR n.excerpt LIKE ?)";
        $search_param = "%$search_term%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'sss';
    }
    
    // Add category filter if provided
    if ($category_id !== null) {
        $sql .= " AND n.category_id = ?";
        $params[] = $category_id;
        $types .= 'i';
    }
    
    // Add status filter if provided
    if ($status !== null) {
        $sql .= " AND n.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    // Count total results
    $count_sql = str_replace("SELECT n.*, u.username, u.full_name", "SELECT COUNT(*) as count", $sql);
    $stmt = $conn->prepare($count_sql);
    
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_results = $row['count'];
    }
    
    // Get paginated results
    $sql .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $search_results = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Calculate pagination
$total_pages = ceil($total_results / $per_page);

// Set page variables
$page_title = 'ค้นหาข่าว';
$page_header_icon = '<i class="fas fa-search me-2"></i>';
$back_button = true;
$back_button_url = 'index.php';
$back_button_text = 'กลับไปหน้ารายการข่าว';

// Build content
ob_start();
?>

<div class="card-modern mb-4">
    <div class="card-header-modern">
        <h5><i class="fas fa-search me-2"></i>ค้นหาข่าว</h5>
    </div>
    <div class="card-body-modern">
        <form method="get" action="search.php" class="row g-3">
            <div class="col-md-6">
                <label class="form-label-modern">คำค้นหา</label>
                <input type="text" class="form-control form-control-modern" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="ค้นหาจากชื่อเรื่อง, เนื้อหา หรือคำอธิบายย่อ">
            </div>
            
            <div class="col-md-3">
                <label class="form-label-modern">หมวดหมู่</label>
                <select class="form-select form-select-modern" name="category_id">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label-modern">สถานะ</label>
                <select class="form-select form-select-modern" name="status">
                    <option value="">ทั้งหมด</option>
                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>แบบร่าง</option>
                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>รอการตรวจสอบ</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" name="search" class="btn btn-primary btn-modern">
                    <i class="fas fa-search me-2"></i>ค้นหา
                </button>
                <a href="search.php" class="btn btn-secondary btn-modern ms-2">
                    <i class="fas fa-redo me-2"></i>ล้างการค้นหา
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['search'])): ?>
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <h5><i class="fas fa-list me-2"></i>ผลการค้นหา</h5>
        </div>
        <div class="card-body-modern">
            <?php if (empty($search_results)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>ไม่พบข้อมูล</h4>
                    <p class="mb-0">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <p class="text-muted-glass">พบทั้งหมด <?php echo $total_results; ?> รายการ</p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>สถานะ</th>
                                <th>ผู้เขียน</th>
                                <th>วันที่สร้าง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $news): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        
                                        switch ($news['status']) {
                                            case 'published':
                                                $status_class = 'status-published';
                                                $status_text = 'เผยแพร่';
                                                break;
                                            case 'draft':
                                                $status_class = 'status-draft';
                                                $status_text = 'แบบร่าง';
                                                break;
                                            case 'pending':
                                                $status_class = 'status-pending';
                                                $status_text = 'รอการตรวจสอบ';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($news['full_name'] ?? $news['username'] ?? '-'); ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="view.php?id=<?php echo $news['id']; ?>" class="btn btn-info" title="ดู">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_new.php?id=<?php echo $news['id']; ?>" class="btn btn-primary" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $news['id']; ?>" class="btn btn-danger" 
                                               data-confirm-delete="true" 
                                               data-confirm-message="คุณต้องการลบข่าวนี้ใช่หรือไม่?" title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination pagination-modern justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?search=1&search_term=<?php echo urlencode($search_term); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?search=1&search_term=<?php echo urlencode($search_term); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?search=1&search_term=<?php echo urlencode($search_term); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// Include the template
include 'template.php';
?>
