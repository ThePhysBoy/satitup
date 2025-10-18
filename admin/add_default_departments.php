<?php
/**
 * Add Default Departments - เพิ่มข้อมูลหน่วยงานเริ่มต้น
 */

// Include database connection
$conn = require_once 'includes/db_config.php';
require_once 'includes/auth_functions.php';

// Require user to be logged in and have permission
requireLogin();
if (!isAdmin()) {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_departments'])) {
    // Default departments data
    $departments = [
        // Academic departments (มัธยม)
        [1, 'วิทยาศาสตร์และเทคโนโลยี', 'กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี', 'academic', NULL, 1],
        [2, 'สังคมศึกษา', 'กลุ่มสาระการเรียนรู้สังคมศึกษา', 'academic', NULL, 2],
        [3, 'ภาษาต่างประเทศ', 'กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ', 'academic', NULL, 3],
        [4, 'คณิตศาสตร์', 'กลุ่มสาระการเรียนรู้คณิตศาสตร์', 'academic', NULL, 4],
        [5, 'สุขศึกษาและพลศึกษา', 'กลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา', 'academic', NULL, 5],
        [6, 'ภาษาไทย', 'กลุ่มสาระการเรียนรู้ภาษาไทย', 'academic', NULL, 6],
        [7, 'ศิลปะ', 'กลุ่มสาระการเรียนรู้ศิลปะ', 'academic', NULL, 7],
        [8, 'การงานอาชีพ', 'กลุ่มสาระการเรียนรู้การงานอาชีพ', 'academic', NULL, 8],

        // Support departments (สายสนับสนุน)
        [9, 'งานบริหารทั่วไป', 'บุคลากรสายสนับสนุนงานบริหารทั่วไป', 'support', 'administration', 1],
        [10, 'งานวิชาการ', 'บุคลากรสายสนับสนุนงานวิชาการ', 'support', 'academic_support', 2],
        [11, 'งานกิจการนักเรียน', 'บุคลากรสายสนับสนุนงานกิจการนักเรียน', 'support', 'student_affairs', 3],
        [12, 'งานแผนงาน', 'บุคลากรสายสนับสนุนงานแผนงาน', 'support', 'planning', 4],
        [13, 'ห้องปฏิบัติการทางวิทยาศาสตร์', 'บุคลากรสายสนับสนุนห้องปฏิบัติการทางวิทยาศาสตร์', 'support', NULL, 5],
        [14, 'ห้องสมุด', 'บุคลากรสายสนับสนุนห้องสมุด', 'support', NULL, 6],

        // Primary education departments (ประถมศึกษา)
        [15, 'ประถมศึกษาปีที่ 1', 'ครูประจำชั้นประถมศึกษาปีที่ 1', 'primary', NULL, 1],
        [16, 'ประถมศึกษาปีที่ 2', 'ครูประจำชั้นประถมศึกษาปีที่ 2', 'primary', NULL, 2],
        [17, 'ประถมศึกษาปีที่ 3', 'ครูประจำชั้นประถมศึกษาปีที่ 3', 'primary', NULL, 3],
        [18, 'ประถมศึกษาปีที่ 4', 'ครูประจำชั้นประถมศึกษาปีที่ 4', 'primary', NULL, 4],
        [19, 'ประถมศึกษาปีที่ 5', 'ครูประจำชั้นประถมศึกษาปีที่ 5', 'primary', NULL, 5],
        [20, 'ประถมศึกษาปีที่ 6', 'ครูประจำชั้นประถมศึกษาปีที่ 6', 'primary', NULL, 6],
        [21, 'ครูพิเศษประถมศึกษา', 'ครูผู้สอนวิชาพิเศษระดับประถมศึกษา', 'primary', NULL, 7]
    ];

    $success_count = 0;
    $error_count = 0;

    foreach ($departments as $dept) {
        // ตรวจสอบว่าคอลัมน์ support_type มีอยู่หรือไม่
        $res = $conn->query("SHOW COLUMNS FROM departments LIKE 'support_type'");
        if ($res && $res->num_rows > 0) {
            $stmt = $conn->prepare("INSERT IGNORE INTO departments (id, name, description, type, support_type, order_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $dept[0], $dept[1], $dept[2], $dept[3], $dept[4], $dept[5]);
        } else {
            $stmt = $conn->prepare("INSERT IGNORE INTO departments (id, name, description, type, order_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $dept[0], $dept[1], $dept[2], $dept[3], $dept[5]);
        }

        if ($stmt->execute()) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    if ($error_count == 0) {
        $message = "เพิ่มข้อมูลหน่วยงานทั้งหมด $success_count รายการเรียบร้อยแล้ว";
    } else {
        $error = "เพิ่มข้อมูลสำเร็จ $success_count รายการ มีข้อผิดพลาด $error_count รายการ";
    }
}

// Get current departments count
$result = $conn->query("SELECT COUNT(*) as count FROM departments");
$current_count = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลหน่วยงานเริ่มต้น - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
        }
        .btn-warning:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .department-list {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<?php include 'navbar_admin.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item"><a href="department_manager.php">จัดการหน่วยงาน</a></li>
                    <li class="breadcrumb-item active">เพิ่มข้อมูลเริ่มต้น</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>สถานะข้อมูลหน่วยงาน</h5>
                    <span class="badge bg-<?php echo $current_count > 0 ? 'success' : 'warning'; ?>">
                        <?php echo $current_count; ?> หน่วยงาน
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($current_count == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ไม่พบข้อมูลหน่วยงานในระบบ กรุณาเพิ่มข้อมูลหน่วยงานเริ่มต้น
                        </div>
                    <?php elseif ($current_count < 10): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            พบหน่วยงาน <?php echo $current_count; ?> รายการ (น้อยกว่าปกติ)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            พบหน่วยงาน <?php echo $current_count; ?> รายการ (ปกติ)
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add Default Departments Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>เพิ่มข้อมูลหน่วยงานเริ่มต้น</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted">
                        การเพิ่มข้อมูลหน่วยงานเริ่มต้นจะเพิ่มข้อมูลหน่วยงานทั้งหมด 21 รายการ ได้แก่
                        สายวิชาการ 8 รายการ, สายสนับสนุน 6 รายการ, และประถมศึกษา 7 รายการ
                    </p>

                    <!-- Departments Preview -->
                    <div class="mb-4">
                        <h6><i class="fas fa-list me-2"></i>รายการหน่วยงานที่จะเพิ่ม:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>สายวิชาการ (8 รายการ):</strong>
                                <ul class="list-unstyled small">
                                    <li>วิทยาศาสตร์และเทคโนโลยี</li>
                                    <li>สังคมศึกษา</li>
                                    <li>ภาษาต่างประเทศ</li>
                                    <li>คณิตศาสตร์</li>
                                    <li>สุขศึกษาและพลศึกษา</li>
                                    <li>ภาษาไทย</li>
                                    <li>ศิลปะ</li>
                                    <li>การงานอาชีพ</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>สายสนับสนุน (6 รายการ):</strong>
                                <ul class="list-unstyled small">
                                    <li>งานบริหารทั่วไป</li>
                                    <li>งานวิชาการ</li>
                                    <li>งานกิจการนักเรียน</li>
                                    <li>งานแผนงาน</li>
                                    <li>ห้องปฏิบัติการทางวิทยาศาสตร์</li>
                                    <li>ห้องสมุด</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>ประถมศึกษา (7 รายการ):</strong>
                                <ul class="list-unstyled small">
                                    <li>ประถมศึกษาปีที่ 1</li>
                                    <li>ประถมศึกษาปีที่ 2</li>
                                    <li>ประถมศึกษาปีที่ 3</li>
                                    <li>ประถมศึกษาปีที่ 4</li>
                                    <li>ประถมศึกษาปีที่ 5</li>
                                    <li>ประถมศึกษาปีที่ 6</li>
                                    <li>ครูพิเศษประถมศึกษา</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือน:</strong> การเพิ่มข้อมูลนี้จะเพิ่มข้อมูลกลับเข้าไปในฐานข้อมูล
                        หากคุณได้ลบข้อมูลบางรายการออกไปแล้ว ข้อมูลจะถูกเพิ่มกลับเข้ามาใหม่
                    </div>

                    <form method="POST">
                        <div class="text-center">
                            <button type="submit" name="add_departments" class="btn btn-primary btn-lg" onclick="return confirm('คุณต้องการเพิ่มข้อมูลหน่วยงานเริ่มต้นทั้งหมด 21 รายการใช่หรือไม่?');">
                                <i class="fas fa-download me-2"></i>เพิ่มข้อมูลหน่วยงานเริ่มต้น
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Navigation -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <a href="department_manager.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-cog me-2"></i>จัดการหน่วยงาน
                    </a>
                    <a href="staff/create.php" class="btn btn-outline-success">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มบุคลากร
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
