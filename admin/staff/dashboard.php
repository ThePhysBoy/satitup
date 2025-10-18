<?php
/**
 * Staff Dashboard
 * Shows statistics and overview of staff management system
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'staff_functions.php';

// Require user to be logged in and have staff access permission
requireStaffAccess();

// Get statistics
$stats = getStaffStatistics($conn);
$departments = getAllDepartments($conn);

// Set page variables
$page_title = 'แดชบอร์ดระบบจัดการบุคลากร';
$page_header_icon = '<i class="fas fa-tachometer-alt me-2"></i>';

// Build content
ob_start();
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-primary border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-primary">บุคลากรทั้งหมด</h6>
                        <div class="h3"><?php echo $stats['total']; ?> คน</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-success border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-success">บุคลากรสายวิชาการ</h6>
                        <div class="h3"><?php echo $stats['academic']; ?> คน</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-warning border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-warning">บุคลากรสายสนับสนุน</h6>
                        <div class="h3"><?php echo $stats['support']; ?> คน</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-info border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-info">จำนวนแผนก/ฝ่าย</h6>
                        <div class="h3"><?php echo $stats['departments']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Staff by Department -->
    <div class="col-lg-6">
        <div class="card-modern mb-4">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-sitemap me-2"></i>บุคลากรตามแผนก/ฝ่าย</h6>
                <a href="index.php" class="btn btn-sm btn-primary btn-modern">ดูทั้งหมด</a>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>แผนก/ฝ่าย</th>
                                <th>จำนวนบุคลากร</th>
                                <th>สัดส่วน</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                    <td><?php echo $dept['staff_count']; ?> คน</td>
                                    <td>
                                        <?php 
                                            $percentage = ($stats['total'] > 0) ? round(($dept['staff_count'] / $stats['total']) * 100) : 0;
                                            echo $percentage . '%';
                                        ?>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%" 
                                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="index.php?department_id=<?php echo $dept['id']; ?>" class="btn btn-sm btn-outline-primary btn-modern">
                                            <i class="fas fa-eye"></i> ดูบุคลากร
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff by Position Type -->
    <div class="col-lg-6">
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h6 class="m-0"><i class="fas fa-user-tag me-2"></i>บุคลากรตามประเภทตำแหน่ง</h6>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                    <h5>สายวิชาการ</h5>
                                    <h2 class="mb-0"><?php echo $stats['academic']; ?></h2>
                                    <div class="small">คิดเป็น <?php echo ($stats['total'] > 0) ? round(($stats['academic'] / $stats['total']) * 100) : 0; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-warning text-white shadow">
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-user-tie fa-3x mb-3"></i>
                                    <h5>สายสนับสนุน</h5>
                                    <h2 class="mb-0"><?php echo $stats['support']; ?></h2>
                                    <div class="small">คิดเป็น <?php echo ($stats['total'] > 0) ? round(($stats['support'] / $stats['total']) * 100) : 0; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-success text-white shadow">
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                                    <h5>ครูประจำชั้น</h5>
                                    <h2 class="mb-0"><?php echo $stats['primary']; ?></h2>
                                    <div class="small">คิดเป็น <?php echo ($stats['total'] > 0) ? round(($stats['primary'] / $stats['total']) * 100) : 0; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-info text-white shadow">
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-cogs fa-3x mb-3"></i>
                                    <h5>ฝ่ายบริหาร</h5>
                                    <h2 class="mb-0"><?php echo $stats['management']; ?></h2>
                                    <div class="small">คิดเป็น <?php echo ($stats['total'] > 0) ? round(($stats['management'] / $stats['total']) * 100) : 0; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-12">
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h6 class="m-0"><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h6>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="index.php" class="btn btn-primary btn-modern w-100 py-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div>จัดการบุคลากร</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="create.php" class="btn btn-success btn-modern w-100 py-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <div>เพิ่มบุคลากรใหม่</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="academic.php" class="btn btn-info btn-modern w-100 py-3">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                            <div>บุคลากรสายวิชาการ</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="service.php" class="btn btn-warning btn-modern w-100 py-3">
                            <i class="fas fa-user-tie fa-2x mb-2"></i>
                            <div>บุคลากรสายสนับสนุน</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the template
$template_path = file_exists('template.php') ? 'template.php' : '../template.php';
include $template_path;
?>

<?php
/**
 * Get staff statistics
 * 
 * @param mysqli $conn Database connection
 * @return array The statistics
 */
function getStaffStatistics($conn) {
    $stats = [
        'total' => 0,
        'academic' => 0,
        'support' => 0,
        'primary' => 0,
        'management' => 0,
        'departments' => 0
    ];
    
    // Get total staff count
    $result = $conn->query("SELECT COUNT(*) as count FROM staff");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total'] = $row['count'];
    }
    
    // Get academic staff count - using department type instead of staff_type
    $result = $conn->query("SELECT COUNT(*) as count FROM staff s 
                           JOIN departments d ON s.department_id = d.id 
                           WHERE d.type = 'academic'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['academic'] = $row['count'];
    }
    
    // Get support staff count - using department type instead of staff_type
    $result = $conn->query("SELECT COUNT(*) as count FROM staff s 
                           JOIN departments d ON s.department_id = d.id 
                           WHERE d.type = 'service'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['support'] = $row['count'];
    }
    
    // Get primary teacher count
    $result = $conn->query("SELECT COUNT(*) as count FROM staff WHERE is_primary_teacher = 1");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['primary'] = $row['count'];
    }
    
    // Get management staff count
    $result = $conn->query("SELECT COUNT(*) as count FROM staff WHERE position LIKE '%ผู้อำนวยการ%' OR position LIKE '%รองผู้อำนวยการ%' OR position LIKE '%หัวหน้า%'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['management'] = $row['count'];
    }
    
    // Get department count
    $result = $conn->query("SELECT COUNT(*) as count FROM departments");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['departments'] = $row['count'];
    }
    
    return $stats;
}

/**
 * Get all departments with staff count
 * 
 * @param mysqli $conn Database connection
 * @return array The departments
 */
function getAllDepartments($conn) {
    $departments = [];
    
    $sql = "SELECT d.*, COUNT(s.id) as staff_count 
            FROM departments d
            LEFT JOIN staff s ON d.id = s.department_id
            GROUP BY d.id
            ORDER BY d.name ASC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    }
    
    return $departments;
}
?>
