<?php
/**
 * Department Manager - จัดการข้อมูลหน่วยงาน/กลุ่มสาระ
 * สำหรับเพิ่ม ลบ แก้ไขข้อมูล departments ด้วยตนเอง
 */

// Include database connection
$conn = require_once 'includes/db_config.php';
require_once 'includes/auth_functions.php';

// Require user to be logged in and have permission
requireLogin();
if (!canManageStaff()) {
    header("Location: index.php");
    exit;
}

// Handle form submissions
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_department'])) {
        // Add new department
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $_POST['type'] ?? 'academic';
        $support_type = trim($_POST['support_type'] ?? '');
        $order_number = (int)($_POST['order_number'] ?? 0);

        if (empty($name)) {
            $error = "กรุณากรอกชื่อหน่วยงาน";
        } else {
            // Get next available ID
            $result = $conn->query("SELECT MAX(id) as max_id FROM departments");
            $next_id = ($result->fetch_assoc()['max_id'] ?? 0) + 1;

            // Insert new department
            $stmt = $conn->prepare("INSERT INTO departments (id, name, description, type, support_type, order_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $next_id, $name, $description, $type, $support_type, $order_number);

            if ($stmt->execute()) {
                $message = "เพิ่มหน่วยงานใหม่เรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการเพิ่มหน่วยงาน: " . $stmt->error;
            }
        }
    } elseif (isset($_POST['delete_department'])) {
        // Delete department
        $dept_id = (int)($_POST['department_id'] ?? 0);

        if ($dept_id > 0) {
            $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->bind_param("i", $dept_id);

            if ($stmt->execute()) {
                $message = "ลบหน่วยงานเรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการลบหน่วยงาน: " . $stmt->error;
            }
        }
    }
}

// Get all departments
$departments = getDepartments(null, $conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหน่วยงาน - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

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
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
        }
        .badge {
            font-size: 0.8rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
                    <li class="breadcrumb-item active">จัดการหน่วยงาน</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Departments List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>หน่วยงานทั้งหมด</h5>
                    <span class="badge bg-primary"><?php echo count($departments); ?> รายการ</span>
                </div>
                <div class="card-body">
                    <?php if (empty($departments)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> ไม่พบข้อมูลหน่วยงาน
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ชื่อหน่วยงาน</th>
                                        <th>ประเภท</th>
                                        <th>ประเภทย่อย</th>
                                        <th>ลำดับ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $dept): ?>
                                        <tr>
                                            <td><?php echo $dept['id']; ?></td>
                                            <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                            <td>
                                                <?php
                                                $type_badges = [
                                                    'academic' => '<span class="badge bg-info">สายวิชาการ</span>',
                                                    'support' => '<span class="badge bg-success">สายสนับสนุน</span>',
                                                    'primary' => '<span class="badge bg-warning">ประถมศึกษา</span>'
                                                ];
                                                echo $type_badges[$dept['type']] ?? '<span class="badge bg-secondary">ไม่ระบุ</span>';
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($dept['support_type'] ?? '-'); ?></td>
                                            <td><?php echo $dept['order_number']; ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('คุณต้องการลบหน่วยงานนี้ใช่หรือไม่?');">
                                                    <input type="hidden" name="department_id" value="<?php echo $dept['id']; ?>">
                                                    <button type="submit" name="delete_department" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> ลบ
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Add New Department -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>เพิ่มหน่วยงานใหม่</h5>
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

                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">ชื่อหน่วยงาน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">ประเภท</label>
                            <select class="form-select" id="type" name="type">
                                <option value="academic">สายวิชาการ</option>
                                <option value="support">สายสนับสนุน</option>
                                <option value="primary">ประถมศึกษา</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="support_type" class="form-label">ประเภทย่อย (สายสนับสนุนเท่านั้น)</label>
                            <input type="text" class="form-control" id="support_type" name="support_type"
                                   placeholder="เช่น administration, academic_support, student_affairs, planning">
                            <small class="form-text text-muted">เว้นว่างไว้ถ้าไม่ใช่สายสนับสนุน</small>
                        </div>

                        <div class="mb-3">
                            <label for="order_number" class="form-label">ลำดับการแสดง</label>
                            <input type="number" class="form-control" id="order_number" name="order_number" min="0" value="0">
                            <small class="form-text text-muted">ตัวเลขน้อยจะแสดงก่อน (0 คือค่าเริ่มต้น)</small>
                        </div>

                        <button type="submit" name="add_department" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>เพิ่มหน่วยงาน
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>ข้อมูลการใช้งาน</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li>• <strong>สายวิชาการ:</strong> กลุ่มสาระการเรียนรู้</li>
                        <li>• <strong>สายสนับสนุน:</strong> หน่วยงานสนับสนุน</li>
                        <li>• <strong>ประถมศึกษา:</strong> ชั้นประถมศึกษา</li>
                        <li>• <strong>ประเภทย่อย:</strong> สำหรับสายสนับสนุนเท่านั้น</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
