<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบระบบจัดการบุคลากร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">🔍 ตรวจสอบระบบจัดการบุคลากร</h1>
    
    <?php
    // Include database connection
    $conn = require_once '../includes/db_config.php';
    require_once './staff_functions.php';
    
    $all_ok = true;
    ?>
    
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-database"></i> ตรวจสอบฐานข้อมูล</h5>
        </div>
        <div class="card-body">
            <?php
            // Check tables
            $tables = ['departments', 'staff', 'staff_positions'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    echo "<p class='text-success'><i class='fas fa-check-circle'></i> ตาราง <strong>$table</strong> มีอยู่แล้ว</p>";
                } else {
                    echo "<p class='text-danger'><i class='fas fa-times-circle'></i> ตาราง <strong>$table</strong> ไม่พบ</p>";
                    $all_ok = false;
                }
            }
            ?>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-building"></i> ข้อมูลแผนก/หน่วยงาน</h5>
        </div>
        <div class="card-body">
            <?php
            $depts = getDepartments(null, $conn);
            if (count($depts) > 0) {
                echo "<p class='text-success'><i class='fas fa-check-circle'></i> พบ <strong>" . count($depts) . "</strong> แผนก/หน่วยงาน</p>";
                
                // Count by type
                $academic = 0;
                $service = 0;
                foreach ($depts as $dept) {
                    if ($dept['type'] == 'academic') $academic++;
                    else $service++;
                }
                echo "<ul>";
                echo "<li>สายวิชาการ: $academic แผนก</li>";
                echo "<li>สายบริการ: $service หน่วยงาน</li>";
                echo "</ul>";
            } else {
                echo "<p class='text-danger'><i class='fas fa-times-circle'></i> ไม่พบข้อมูลแผนก/หน่วยงาน</p>";
                $all_ok = false;
            }
            ?>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-users"></i> ข้อมูลบุคลากร</h5>
        </div>
        <div class="card-body">
            <?php
            $result = $conn->query("SELECT COUNT(*) as count FROM staff");
            $row = $result->fetch_assoc();
            $staff_count = $row['count'];
            
            if ($staff_count > 0) {
                echo "<p class='text-success'><i class='fas fa-check-circle'></i> พบ <strong>$staff_count</strong> บุคลากร</p>";
                
                // Show sample staff
                $result = $conn->query("SELECT s.*, d.name as dept_name FROM staff s LEFT JOIN departments d ON s.department_id = d.id LIMIT 5");
                if ($result && $result->num_rows > 0) {
                    echo "<h6>ตัวอย่างข้อมูลบุคลากร:</h6>";
                    echo "<ul>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['title']} {$row['first_name']} {$row['last_name']} - {$row['dept_name']}</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p class='text-warning'><i class='fas fa-exclamation-triangle'></i> ยังไม่มีข้อมูลบุคลากร</p>";
                echo "<a href='add_sample_data.php' class='btn btn-primary btn-sm'>เพิ่มข้อมูลตัวอย่าง</a>";
            }
            ?>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-folder"></i> ตรวจสอบโฟลเดอร์อัพโหลด</h5>
        </div>
        <div class="card-body">
            <?php
            $upload_dir = '../../uploads/staff';
            if (is_dir($upload_dir)) {
                echo "<p class='text-success'><i class='fas fa-check-circle'></i> โฟลเดอร์ <strong>uploads/staff</strong> มีอยู่แล้ว</p>";
                
                // Check write permission
                if (is_writable($upload_dir)) {
                    echo "<p class='text-success'><i class='fas fa-check-circle'></i> สามารถเขียนไฟล์ได้</p>";
                } else {
                    echo "<p class='text-danger'><i class='fas fa-times-circle'></i> ไม่สามารถเขียนไฟล์ได้</p>";
                    $all_ok = false;
                }
            } else {
                echo "<p class='text-danger'><i class='fas fa-times-circle'></i> โฟลเดอร์ <strong>uploads/staff</strong> ไม่พบ</p>";
                $all_ok = false;
            }
            ?>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header <?php echo $all_ok ? 'bg-success' : 'bg-danger'; ?> text-white">
            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> สรุปผลการตรวจสอบ</h5>
        </div>
        <div class="card-body">
            <?php if ($all_ok): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>ระบบพร้อมใช้งาน!</strong> ทุกอย่างถูกต้องและพร้อมทำงาน
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <strong>พบปัญหา!</strong> กรุณาแก้ไขปัญหาที่พบก่อนใช้งาน
                </div>
            <?php endif; ?>
            
            <h5>ลิงก์ทดสอบระบบ:</h5>
            <div class="list-group">
                <a href="index.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-list"></i> หน้าจัดการบุคลากร (Admin)
                </a>
                <a href="create.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-plus"></i> เพิ่มบุคลากรใหม่
                </a>
                <?php if ($staff_count == 0): ?>
                <a href="add_sample_data.php" class="list-group-item list-group-item-action text-primary">
                    <i class="fas fa-database"></i> เพิ่มข้อมูลตัวอย่าง
                </a>
                <?php endif; ?>
                <a href="../../staff/index.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-users"></i> หน้าแสดงบุคลากร (Frontend)
                </a>
                <a href="../../staff/academic.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-chalkboard-teacher"></i> บุคลากรสายวิชาการ
                </a>
                <a href="../../staff/service.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-cog"></i> บุคลากรสายบริการ
                </a>
            </div>
        </div>
    </div>
    
    <?php $conn->close(); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
