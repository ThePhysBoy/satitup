<?php
/**
 * Fix CV Column Error
 * แก้ไข error Unknown column 'cv_file_path'
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satitup";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$messages = [];
$errors = [];

// 1. ตรวจสอบและเพิ่ม column cv_file_path
$check_cv = $conn->query("SHOW COLUMNS FROM staff LIKE 'cv_file_path'");
if ($check_cv->num_rows == 0) {
    // เพิ่ม column cv_file_path
    $sql = "ALTER TABLE staff ADD COLUMN cv_file_path VARCHAR(500) DEFAULT NULL";
    
    if ($conn->query($sql)) {
        $messages[] = "✅ เพิ่ม column cv_file_path สำเร็จ!";
    } else {
        $errors[] = "❌ Error: " . $conn->error;
    }
} else {
    $messages[] = "✅ Column cv_file_path มีอยู่แล้ว";
}

// 2. ตรวจสอบโครงสร้างตาราง staff
$columns_check = $conn->query("SHOW COLUMNS FROM staff");
$existing_columns = [];
while ($col = $columns_check->fetch_assoc()) {
    $existing_columns[] = $col['Field'];
}

// 3. สร้างโฟลเดอร์สำหรับเก็บ CV
$cv_dir = '../../uploads/cv';
if (!file_exists($cv_dir)) {
    if (mkdir($cv_dir, 0777, true)) {
        $messages[] = "✅ สร้างโฟลเดอร์ uploads/cv สำเร็จ";
        
        // สร้างไฟล์ .htaccess เพื่อป้องกันการเข้าถึงโดยตรง
        $htaccess_content = "Options -Indexes\n";
        file_put_contents($cv_dir . '/.htaccess', $htaccess_content);
    } else {
        $errors[] = "❌ ไม่สามารถสร้างโฟลเดอร์ uploads/cv";
    }
} else {
    $messages[] = "✅ โฟลเดอร์ uploads/cv พร้อมใช้งาน";
}

// 4. ตรวจสอบ permissions
if (is_writable($cv_dir)) {
    $messages[] = "✅ โฟลเดอร์ uploads/cv สามารถเขียนได้";
} else {
    $errors[] = "❌ โฟลเดอร์ uploads/cv ไม่สามารถเขียนได้";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไข CV Column Error - โรงเรียนสาธิต</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container-custom {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 30px;
            text-align: center;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .column-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        .column-item {
            padding: 5px 10px;
            background: white;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 3px solid #667eea;
        }
        .highlight {
            background: #ffc107;
            color: #000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-tools"></i> แก้ไข CV Column Error</h1>
        
        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <h4><i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด:</h4>
            <?php foreach ($errors as $error): ?>
                <div><?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($messages)): ?>
        <div class="success-box">
            <h4><i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ:</h4>
            <?php foreach ($messages as $message): ?>
                <div><?php echo $message; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="info-box">
            <h4><i class="fas fa-info-circle"></i> Columns ในตาราง staff:</h4>
            <div class="column-list">
                <?php foreach ($existing_columns as $column): ?>
                    <div class="column-item <?php echo $column == 'cv_file_path' ? 'highlight' : ''; ?>">
                        <?php echo $column; ?>
                        <?php if ($column == 'cv_file_path'): ?>
                            <span class="badge bg-success ms-2">CV Column</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (empty($errors)): ?>
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle"></i> ระบบ CV พร้อมใช้งาน!</h5>
            <p>✅ Column cv_file_path พร้อมแล้ว<br>
               ✅ โฟลเดอร์ uploads/cv พร้อมแล้ว<br>
               ✅ สามารถอัปโหลด CV ได้แล้ว</p>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> กรุณาแก้ไขข้อผิดพลาดด้านบน</h5>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="edit.php?id=4" class="btn btn-custom">
                <i class="fas fa-edit"></i> กลับไปหน้าแก้ไขบุคลากร
            </a>
            <a href="index.php" class="btn btn-custom">
                <i class="fas fa-users"></i> ไปหน้าจัดการบุคลากร
            </a>
            <a href="view.php?id=4" class="btn btn-custom">
                <i class="fas fa-eye"></i> ดูรายละเอียดบุคลากร
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center">
            <h5>📋 ขั้นตอนการอัปโหลด CV:</h5>
            <ol class="text-start" style="max-width: 600px; margin: 20px auto;">
                <li>ไปที่หน้า <strong>แก้ไขบุคลากร</strong></li>
                <li>เลื่อนไปที่ช่อง <strong>"อัปโหลด CV"</strong></li>
                <li>เลือกไฟล์ PDF (ไม่เกิน 10MB)</li>
                <li>คลิก <strong>บันทึกการแก้ไข</strong></li>
                <li>CV จะแสดงในหน้า <strong>ดูรายละเอียด</strong></li>
            </ol>
        </div>
    </div>
</body>
</html>
