<?php
// Include necessary files
$conn = require_once '../admin/includes/db_config.php';
require_once '../admin/staff/staff_functions.php';

// Get staff by support type - administration
$department_id = 8;
//$support_type = 'administration';
//$staff_data = getStaffBySupportType($support_type, $conn);
$staff_data = getStaffByDepartment($department_id, $conn);
// Set page title
$page_title = "บุคลากรงานบริหารทั่วไป";

// Include custom header
include 'header_new.php';

// Include navbar
require_once '../navbar.php';
?>

<!-- Page Content -->
<div class="container mt-3">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="#">บุคลากรสายสนับสนุน</a></li>
                    <li class="breadcrumb-item active" aria-current="page">งานบริหารทั่วไป</li>
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
        <!-- Main Content -->
        <div class="col-12">
            <?php if (empty($staff_data)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> ไม่พบข้อมูลบุคลากรงานบริหารทั่วไป
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($staff_data as $staff): ?>
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
                                        <?php if ($staff['is_head']): ?>
                                            <span class="badge bg-primary ms-2">หัวหน้า</span>
                                        <?php endif; ?>
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

                                    <div class="mt-3">
                                        <a href="public_view.php?id=<?php echo $staff['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user-circle"></i> ดูประวัติ/CV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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

.badge.bg-primary {
    background-color: var(--primary-color) !important;
}
</style>

