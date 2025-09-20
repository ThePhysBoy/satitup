<?php
// Include necessary files
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once './staff_functions.php';

// Require user to be logged in and have permission
requireLogin();
if (!canManageStaff()) {
    header("Location: ../index.php");
    exit;
}

// Get departments for filtering
$departments = getDepartments(null, $conn);

// Initialize filter variables
$filter_department = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build WHERE clause for filtering
$where_clause = "WHERE 1=1";
$params = [];
$types = "";

if ($filter_department > 0) {
    $where_clause .= " AND s.department_id = ?";
    $params[] = $filter_department;
    $types .= "i";
}

if ($filter_type !== '') {
    $where_clause .= " AND d.type = ?";
    $params[] = $filter_type;
    $types .= "s";
}

if ($search !== '') {
    $search_term = "%$search%";
    $where_clause .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.title LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Count total items for pagination
$count_sql = "SELECT COUNT(*) as total FROM staff s 
              LEFT JOIN departments d ON s.department_id = d.id 
              $where_clause";

$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get staff list with pagination
$sql = "SELECT s.*, d.name as department_name, d.type as department_type 
        FROM staff s 
        LEFT JOIN departments d ON s.department_id = d.id 
        $where_clause 
        ORDER BY d.type, d.order_number, s.is_head DESC, s.order_number, s.first_name 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$limit_params = array_merge($params, [$items_per_page, $offset]);
$types .= "ii";
$stmt->bind_param($types, ...$limit_params);
$stmt->execute();
$result = $stmt->get_result();
$staff_list = $result->fetch_all(MYSQLI_ASSOC);

// Handle staff deletion
if (isset($_POST['delete_staff']) && isset($_POST['staff_id'])) {
    $staff_id = (int)$_POST['staff_id'];
    
    // Get staff info to delete image
    $staff = getStaffById($staff_id, $conn);
    
    // Delete staff positions first (due to foreign key constraint)
    $delete_positions = $conn->prepare("DELETE FROM staff_positions WHERE staff_id = ?");
    $delete_positions->bind_param('i', $staff_id);
    $delete_positions->execute();
    
    // Delete staff
    $delete_stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    $delete_stmt->bind_param('i', $staff_id);
    
    if ($delete_stmt->execute()) {
        // Delete image file if exists
        if ($staff && !empty($staff['image_path']) && file_exists('../../' . $staff['image_path'])) {
            unlink('../../' . $staff['image_path']);
        }
        $success_message = "ลบข้อมูลบุคลากรเรียบร้อยแล้ว";
    } else {
        $error_message = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error;
    }
    
    // Redirect to avoid form resubmission
    header("Location: index.php?success=" . urlencode($success_message) . "&error=" . urlencode($error_message) . 
           "&department=" . $filter_department . "&type=" . $filter_type . "&search=" . urlencode($search) . "&page=" . $page);
    exit;
}

// Set page title
$page_title = "จัดการบุคลากร";
$include_summernote = false;

// Set template variables
$page_header_icon = '<i class="fas fa-users me-3"></i>';
$back_button = false;

// Start content output
ob_start();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการข้อมูลบุคลากร</h1>
        <a href="create.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มบุคลากรใหม่
        </a>
    </div>

    <?php if (isset($_GET['success']) && !empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ค้นหาและกรองข้อมูล</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">ค้นหา</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อ, นามสกุล, คำนำหน้า">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">ประเภทบุคลากร</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">ทั้งหมด</option>
                        <option value="academic" <?php echo $filter_type === 'academic' ? 'selected' : ''; ?>>สายวิชาการ</option>
                        <option value="primary" <?php echo $filter_type === 'primary' ? 'selected' : ''; ?>>ประถมศึกษา</option>
                        <option value="support" <?php echo $filter_type === 'support' ? 'selected' : ''; ?>>สายสนับสนุน</option>
                        <option value="service" <?php echo $filter_type === 'service' ? 'selected' : ''; ?>>สายบริการ (เก่า)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">หน่วยงาน/กลุ่มสาระ</label>
                    <select class="form-select" id="department" name="department">
                        <option value="0">ทั้งหมด</option>
                        <optgroup label="สายวิชาการ">
                            <?php foreach ($departments as $dept): ?>
                                <?php if ($dept['type'] === 'academic'): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo $filter_department == $dept['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="สายบริการ">
                            <?php foreach ($departments as $dept): ?>
                                <?php if ($dept['type'] === 'service'): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo $filter_department == $dept['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการบุคลากรทั้งหมด</h6>
        </div>
        <div class="card-body">
            <?php if (count($staff_list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="80">รูปภาพ</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>ตำแหน่ง</th>
                                <th>หน่วยงาน/กลุ่มสาระ</th>
                                <th>ประเภท</th>
                                <th>สถานะ</th>
                                <th width="150">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_list as $staff): ?>
                                <?php 
                                // Get primary position
                                $positions = getStaffPositions($staff['id'], $conn);
                                $primary_position = 'ไม่ระบุ';
                                foreach ($positions as $pos) {
                                    if ($pos['is_primary']) {
                                        $primary_position = $pos['position_name'];
                                        break;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($staff['image_path'])): ?>
                                            <img src="../../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                                                 class="img-thumbnail" alt="รูปภาพบุคลากร" style="max-height: 60px;">
                                        <?php else: ?>
                                            <img src="../../assets/img/user-placeholder.png" 
                                                 class="img-thumbnail" alt="ไม่มีรูปภาพ" style="max-height: 60px;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(formatThaiName($staff['title'], $staff['first_name'], $staff['last_name'])); ?>
                                        <?php if ($staff['is_head']): ?>
                                            <span class="badge bg-primary ms-2">หัวหน้า</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($primary_position); ?></td>
                                    <td><?php echo htmlspecialchars($staff['department_name'] ?? 'ไม่ระบุ'); ?></td>
                                    <td>
                                        <?php if ($staff['department_type'] === 'academic'): ?>
                                            <span class="badge bg-info">สายวิชาการ</span>
                                        <?php elseif ($staff['department_type'] === 'service'): ?>
                                            <span class="badge bg-success">สายบริการ</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ไม่ระบุ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($staff['status'] === 'active'): ?>
                                            <span class="badge bg-success">เปิดใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">ปิดใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $staff['id']; ?>" class="btn btn-info" title="ดูข้อมูล">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $staff['id']; ?>" class="btn btn-warning" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="ลบ" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $staff['id']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $staff['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ยืนยันการลบข้อมูล</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณต้องการลบข้อมูลบุคลากร <strong><?php echo htmlspecialchars(formatThaiName($staff['title'], $staff['first_name'], $staff['last_name'])); ?></strong> ใช่หรือไม่?</p>
                                                        <p class="text-danger">การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form method="POST">
                                                            <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                            <button type="submit" name="delete_staff" class="btn btn-danger">ยืนยันการลบ</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&department=<?php echo $filter_department; ?>&type=<?php echo $filter_type; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&department=<?php echo $filter_department; ?>&type=<?php echo $filter_type; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&department=<?php echo $filter_department; ?>&type=<?php echo $filter_type; ?>&search=<?php echo urlencode($search); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&department=<?php echo $filter_department; ?>&type=<?php echo $filter_type; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?>&department=<?php echo $filter_department; ?>&type=<?php echo $filter_type; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center p-4">
                    <p class="text-muted">ไม่พบข้อมูลบุคลากร</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- End of Page Content -->

<?php
// End content output
$content = ob_get_clean();

// Include template
include '../news/template.php';
?>
