<?php
// Include necessary files
require_once '../admin/includes/db_config.php';
require_once '../admin/staff/staff_functions.php';

// Get academic departments
$academic_departments = getDepartments('academic', $conn);

// Get current department ID from URL
$current_department_id = isset($_GET['department']) ? (int)$_GET['department'] : 0;

// Get staff data
$staff_data = [];
if ($current_department_id > 0) {
    // Get staff for specific department
    $department = getDepartmentById($current_department_id, $conn);
    if ($department && $department['type'] === 'academic') {
        $staff_list = getStaffByDepartment($current_department_id, $conn);
        $staff_data[] = [
            'department' => $department,
            'staff' => $staff_list
        ];
    } else {
        // Redirect to academic page if department is not academic
        header("Location: academic.php");
        exit;
    }
} else {
    // Get all academic staff
    $staff_data = getStaffByType('academic', $conn);
}

// Set page title based on current view
$page_title = "บุคลากรสายวิชาการ";
if ($current_department_id > 0 && isset($department)) {
    $page_title .= " - " . $department['name'];
}

// Include custom header
include 'header_fix.php';
include 'navbar_fix.php';
?>

<!-- Page Content -->
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="index.php">บุคลากร</a></li>
                    <li class="breadcrumb-item active" aria-current="page">สายวิชาการ</li>
                    <?php if ($current_department_id > 0 && isset($department)): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($department['name']); ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h1 class="border-bottom pb-3 mb-4"><?php echo $page_title; ?></h1>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">ประเภทบุคลากร</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="academic.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-chalkboard-teacher me-2"></i> บุคลากรสายวิชาการ
                    </a>
                    <a href="service.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-cog me-2"></i> บุคลากรสายบริการ
                    </a>
                </div>
                
                <div class="card-header bg-light">
                    <h6 class="mb-0">กลุ่มสาระการเรียนรู้</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="academic.php" class="list-group-item list-group-item-action <?php echo $current_department_id === 0 ? 'active' : ''; ?>">
                        <i class="fas fa-users me-2"></i> ทุกกลุ่มสาระ
                    </a>
                    <?php foreach ($academic_departments as $dept): ?>
                        <a href="academic.php?department=<?php echo $dept['id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo $current_department_id === (int)$dept['id'] ? 'active' : ''; ?>">
                            <i class="fas fa-angle-right me-2"></i> <?php echo htmlspecialchars($dept['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <?php if (empty($staff_data)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> ไม่พบข้อมูลบุคลากรสายวิชาการ
                </div>
            <?php else: ?>
                <?php foreach ($staff_data as $data): ?>
                    <?php if (!empty($data['staff'])): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><?php echo htmlspecialchars($data['department']['name']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php 
                                    // First display department head
                                    $has_head = false;
                                    foreach ($data['staff'] as $staff):
                                        if ($staff['is_head']):
                                            $has_head = true;
                                    ?>
                                        <div class="col-12 mb-4">
                                            <div class="card staff-card staff-head-card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 text-center">
                                                            <?php if (!empty($staff['image_path'])): ?>
                                                                <img src="../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                                                                     class="img-fluid rounded-circle staff-image" alt="รูปบุคลากร">
                                                            <?php else: ?>
                                                                <img src="../assets/img/user-placeholder.png" 
                                                                     class="img-fluid rounded-circle staff-image" alt="ไม่มีรูปภาพ">
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <h5 class="card-title">
                                                                <?php echo htmlspecialchars(formatThaiName($staff['title'], $staff['first_name'], $staff['last_name'])); ?>
                                                                <span class="badge bg-primary ms-2">หัวหน้ากลุ่มสาระ</span>
                                                            </h5>
                                                            <?php
                                                            // Get positions
                                                            $staff_positions = getStaffPositions($staff['id'], $conn);
                                                            $primary_position = 'ไม่ระบุตำแหน่ง';
                                                            foreach ($staff_positions as $pos) {
                                                                if ($pos['is_primary']) {
                                                                    $primary_position = $pos['position_name'];
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <p class="card-text text-primary"><?php echo htmlspecialchars($primary_position); ?></p>
                                                            
                                                            <?php if (!empty($staff['email']) || !empty($staff['phone'])): ?>
                                                                <div class="mt-3">
                                                                    <?php if (!empty($staff['email'])): ?>
                                                                        <p><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($staff['email']); ?></p>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if (!empty($staff['phone'])): ?>
                                                                        <p><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($staff['phone']); ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($staff['education'])): ?>
                                                                <div class="mt-3">
                                                                    <h6>ประวัติการศึกษา</h6>
                                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($staff['education'])); ?></p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                    
                                    <?php 
                                    // Then display other staff
                                    foreach ($data['staff'] as $staff):
                                        if (!$staff['is_head']):
                                    ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card staff-card h-100">
                                                <div class="card-body text-center">
                                                    <?php if (!empty($staff['image_path'])): ?>
                                                        <img src="../<?php echo htmlspecialchars($staff['image_path']); ?>" 
                                                             class="img-fluid rounded-circle staff-image mb-3" alt="รูปบุคลากร">
                                                    <?php else: ?>
                                                        <img src="../assets/img/user-placeholder.png" 
                                                             class="img-fluid rounded-circle staff-image mb-3" alt="ไม่มีรูปภาพ">
                                                    <?php endif; ?>
                                                    
                                                    <h5 class="card-title">
                                                        <?php echo htmlspecialchars(formatThaiName($staff['title'], $staff['first_name'], $staff['last_name'])); ?>
                                                    </h5>
                                                    
                                                    <?php
                                                    // Get positions
                                                    $staff_positions = getStaffPositions($staff['id'], $conn);
                                                    $primary_position = 'ไม่ระบุตำแหน่ง';
                                                    foreach ($staff_positions as $pos) {
                                                        if ($pos['is_primary']) {
                                                            $primary_position = $pos['position_name'];
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    <p class="card-text text-primary"><?php echo htmlspecialchars($primary_position); ?></p>
                                                    
                                                    <?php if (!empty($staff['email'])): ?>
                                                        <p class="card-text"><small><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($staff['email']); ?></small></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($staff['education'])): ?>
                                                        <div class="mt-2 text-start">
                                                            <small class="text-muted">
                                                                <strong>การศึกษา:</strong> <?php echo nl2br(htmlspecialchars(substr($staff['education'], 0, 100))); ?>
                                                                <?php if (strlen($staff['education']) > 100): ?>...<?php endif; ?>
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Custom styles for staff page -->
<style>
:root {
    --primary-color: #8B7AA8;
    --primary-light: #B8A9D4;
    --primary-dark: #6B5A88;
    --secondary-color: #9C89B8;
    --accent-color: #F0A6CA;
    --light-accent: #F3EDF7;
}

.staff-image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 5px solid var(--light-accent);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.staff-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.staff-head-card {
    border-left: 4px solid var(--primary-color);
    background-color: var(--light-accent);
}

.list-group-item.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.card-header.bg-primary {
    background-color: var(--primary-color) !important;
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
}
</style>

<?php
// Include footer
include 'footer_fix.php';
?>