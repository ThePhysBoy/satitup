<?php
// Include database connection
$conn = require_once '../includes/db_config.php';

$messages = [];
$errors = [];

// 1. First check current ENUM values
$check_sql = "SHOW COLUMNS FROM departments WHERE Field = 'type'";
$check_result = $conn->query($check_sql);
$current_enum = '';

if ($check_result && $row = $check_result->fetch_assoc()) {
    $current_enum = $row['Type'];
    $messages[] = "ENUM ปัจจุบัน: " . $current_enum;
}

// 2. Update ENUM to include all types
$sql_alter = "ALTER TABLE departments MODIFY COLUMN type ENUM('academic', 'support', 'primary', 'service') NOT NULL";
if ($conn->query($sql_alter) === TRUE) {
    $messages[] = "✅ อัปเดต ENUM type เพื่อรองรับทุกประเภทสำเร็จ";
} else {
    // ถ้า error แต่เป็นเพราะมีอยู่แล้วก็ไม่เป็นไร
    if (strpos($conn->error, 'Duplicate') === false) {
        $errors[] = "⚠️ Warning: " . $conn->error;
    }
}

// 3. Update existing 'service' to 'support'
$sql_update = "UPDATE departments SET type = 'support' WHERE type = 'service'";
if ($conn->query($sql_update) === TRUE) {
    $affected = $conn->affected_rows;
    if ($affected > 0) {
        $messages[] = "✅ อัปเดต 'service' เป็น 'support' จำนวน $affected แผนก";
    } else {
        $messages[] = "ℹ️ ไม่มีแผนกประเภท 'service' ที่ต้องอัปเดต";
    }
} else {
    $errors[] = "❌ Error updating service to support: " . $conn->error;
}

// 4. Update descriptions
$sql_desc = "UPDATE departments SET 
             name = REPLACE(name, 'สายบริการ', 'สายสนับสนุน'),
             description = REPLACE(description, 'สายบริการ', 'สายสนับสนุน') 
             WHERE type = 'support'";
if ($conn->query($sql_desc) === TRUE) {
    $affected = $conn->affected_rows;
    if ($affected > 0) {
        $messages[] = "✅ อัปเดตชื่อและคำอธิบายเป็น 'สายสนับสนุน' จำนวน $affected แผนก";
    }
} else {
    $errors[] = "❌ Error updating descriptions: " . $conn->error;
}

// 5. Insert primary education departments
$primary_departments = [
    ['id' => 15, 'name' => 'ประถมศึกษาปีที่ 1', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 1', 'type' => 'primary', 'order_number' => 1],
    ['id' => 16, 'name' => 'ประถมศึกษาปีที่ 2', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 2', 'type' => 'primary', 'order_number' => 2],
    ['id' => 17, 'name' => 'ประถมศึกษาปีที่ 3', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 3', 'type' => 'primary', 'order_number' => 3],
    ['id' => 18, 'name' => 'ประถมศึกษาปีที่ 4', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 4', 'type' => 'primary', 'order_number' => 4],
    ['id' => 19, 'name' => 'ประถมศึกษาปีที่ 5', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 5', 'type' => 'primary', 'order_number' => 5],
    ['id' => 20, 'name' => 'ประถมศึกษาปีที่ 6', 'description' => 'ครูประจำชั้นประถมศึกษาปีที่ 6', 'type' => 'primary', 'order_number' => 6],
    ['id' => 21, 'name' => 'ครูพิเศษประถมศึกษา', 'description' => 'ครูผู้สอนวิชาพิเศษระดับประถมศึกษา', 'type' => 'primary', 'order_number' => 7]
];

$added_count = 0;
$updated_count = 0;

foreach ($primary_departments as $dept) {
    // Check if exists
    $check_stmt = $conn->prepare("SELECT id FROM departments WHERE id = ?");
    $check_stmt->bind_param("i", $dept['id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing
        $update_stmt = $conn->prepare("UPDATE departments SET name = ?, description = ?, type = ?, order_number = ? WHERE id = ?");
        $update_stmt->bind_param("sssii", $dept['name'], $dept['description'], $dept['type'], $dept['order_number'], $dept['id']);
        if ($update_stmt->execute()) {
            if ($conn->affected_rows > 0) {
                $updated_count++;
            }
        }
        $update_stmt->close();
    } else {
        // Insert new
        $insert_stmt = $conn->prepare("INSERT INTO departments (id, name, description, type, order_number) VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isssi", $dept['id'], $dept['name'], $dept['description'], $dept['type'], $dept['order_number']);
        if ($insert_stmt->execute()) {
            $added_count++;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}

if ($added_count > 0) {
    $messages[] = "✅ เพิ่มแผนกประถมศึกษาใหม่ $added_count แผนก";
}
if ($updated_count > 0) {
    $messages[] = "✅ อัปเดตแผนกประถมศึกษา $updated_count แผนก";
}
if ($added_count == 0 && $updated_count == 0) {
    $messages[] = "ℹ️ แผนกประถมศึกษามีข้อมูลครบถ้วนแล้ว";
}

// 6. Final cleanup - remove 'service' from ENUM if no more service departments exist
$check_service = "SELECT COUNT(*) as count FROM departments WHERE type = 'service'";
$result = $conn->query($check_service);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sql_final = "ALTER TABLE departments MODIFY COLUMN type ENUM('academic', 'support', 'primary') NOT NULL";
    if ($conn->query($sql_final) === TRUE) {
        $messages[] = "✅ ลบ 'service' ออกจาก ENUM สำเร็จ";
    } else {
        // ไม่ต้องแสดง error ถ้าลบไม่ได้ก็ไม่เป็นไร
    }
} else {
    $messages[] = "⚠️ ยังมีแผนกประเภท 'service' อยู่ " . $row['count'] . " แผนก";
}

// 7. Show current statistics
$stats_sql = "SELECT type, COUNT(*) as count FROM departments GROUP BY type ORDER BY type";
$stats_result = $conn->query($stats_sql);
$stats = [];
while ($row = $stats_result->fetch_assoc()) {
    $stats[$row['type']] = $row['count'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตฐานข้อมูลแผนก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            max-width: 800px; 
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            padding: 40px;
        }
        h2 { 
            color: #6B5A88; 
            margin-bottom: 30px;
            font-weight: bold;
        }
        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-academic { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-primary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .stat-support { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .stat-service { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center"><i class="fas fa-database me-2"></i>อัปเดตฐานข้อมูลแผนก/หน่วยงาน</h2>
        
        <?php if (!empty($messages)): ?>
            <div class="alert alert-success" role="alert">
                <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i>ผลการดำเนินการ</h5>
                <ul class="mb-0">
                    <?php foreach ($messages as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading"><i class="fas fa-times-circle me-2"></i>ข้อผิดพลาด</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3"><i class="fas fa-chart-pie me-2"></i>สถิติแผนก/หน่วยงาน</h4>
            </div>
            <?php if (isset($stats['academic'])): ?>
            <div class="col-md-4">
                <div class="stat-card stat-academic">
                    <h5><i class="fas fa-graduation-cap me-2"></i>สายวิชาการ</h5>
                    <div class="display-4"><?php echo $stats['academic']; ?></div>
                    <small>แผนก</small>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats['primary'])): ?>
            <div class="col-md-4">
                <div class="stat-card stat-primary">
                    <h5><i class="fas fa-child me-2"></i>ประถมศึกษา</h5>
                    <div class="display-4"><?php echo $stats['primary']; ?></div>
                    <small>ชั้นเรียน</small>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats['support'])): ?>
            <div class="col-md-4">
                <div class="stat-card stat-support">
                    <h5><i class="fas fa-users-cog me-2"></i>สายสนับสนุน</h5>
                    <div class="display-4"><?php echo $stats['support']; ?></div>
                    <small>หน่วยงาน</small>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats['service'])): ?>
            <div class="col-md-12 mt-2">
                <div class="stat-card stat-service">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>สายบริการ (เก่า - ควรอัปเดต)</h5>
                    <div class="display-4"><?php echo $stats['service']; ?></div>
                    <small>หน่วยงาน</small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="check_departments.php" class="btn btn-info text-white me-2">
                <i class="fas fa-search me-2"></i>ตรวจสอบข้อมูล
            </a>
            <a href="index.php" class="btn btn-primary me-2">
                <i class="fas fa-users me-2"></i>จัดการบุคลากร
            </a>
            <a href="create.php" class="btn btn-success me-2">
                <i class="fas fa-plus me-2"></i>เพิ่มบุคลากร
            </a>
            <a href="../../staff/index.php" class="btn btn-secondary">
                <i class="fas fa-eye me-2"></i>ดูหน้าบุคลากร
            </a>
        </div>
        
        <?php if (empty($stats['primary']) || $stats['primary'] == 0): ?>
        <div class="alert alert-warning mt-4">
            <strong><i class="fas fa-exclamation-circle me-2"></i>คำแนะนำ:</strong> 
            หากยังไม่มีข้อมูลประถมศึกษา ให้รีเฟรชหน้านี้อีกครั้ง หรือตรวจสอบการเชื่อมต่อฐานข้อมูล
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>