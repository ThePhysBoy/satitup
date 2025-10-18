<?php
/**
 * Add CV Upload Feature to Staff System
 * เพิ่มระบบอัปโหลด CV แบบ PDF
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

// 1. เพิ่ม column cv_file_path ถ้ายังไม่มี
$check_cv = $conn->query("SHOW COLUMNS FROM staff LIKE 'cv_file_path'");
if ($check_cv->num_rows == 0) {
    if ($conn->query("ALTER TABLE staff ADD COLUMN cv_file_path VARCHAR(500) AFTER additional_info")) {
        $messages[] = "✓ เพิ่ม column cv_file_path สำเร็จ";
    } else {
        $errors[] = "Error adding cv_file_path column: " . $conn->error;
    }
} else {
    $messages[] = "✓ Column cv_file_path มีอยู่แล้ว";
}

// 2. สร้างโฟลเดอร์สำหรับเก็บไฟล์ CV
$cv_dir = '../../uploads/cv';
if (!file_exists($cv_dir)) {
    if (mkdir($cv_dir, 0777, true)) {
        $messages[] = "✓ สร้างโฟลเดอร์ uploads/cv สำเร็จ";
    } else {
        $errors[] = "ไม่สามารถสร้างโฟลเดอร์ uploads/cv";
    }
} else {
    $messages[] = "✓ โฟลเดอร์ uploads/cv มีอยู่แล้ว";
}

// 3. เพิ่มไฟล์ CV ตัวอย่าง
$sample_cv_content = '%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /MediaBox [0 0 612 792] /Contents 4 0 R >>
endobj
4 0 obj
<< /Length 200 >>
stream
BT
/F1 24 Tf
100 700 Td
(Curriculum Vitae) Tj
0 -50 Td
/F1 18 Tf
(Sample CV Document) Tj
0 -30 Td
/F1 12 Tf
(This is a sample CV for demonstration purposes) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f 
0000000015 00000 n 
0000000074 00000 n 
0000000131 00000 n 
0000000308 00000 n 
trailer
<< /Size 5 /Root 1 0 R >>
startxref
560
%%EOF';

// สร้างไฟล์ CV ตัวอย่าง
$sample_files = [
    'cv_sample_1.pdf' => 'ผู้อำนวยการ',
    'cv_sample_2.pdf' => 'รองผู้อำนวยการ',
    'cv_sample_3.pdf' => 'หัวหน้ากลุ่มสาระ'
];

foreach ($sample_files as $filename => $position) {
    $filepath = $cv_dir . '/' . $filename;
    if (!file_exists($filepath)) {
        if (file_put_contents($filepath, $sample_cv_content)) {
            $messages[] = "✓ สร้างไฟล์ตัวอย่าง $filename";
        }
    }
}

// 4. อัปเดต CV path ให้กับบุคลากรบางคน
$update_samples = [
    1 => 'uploads/cv/cv_sample_1.pdf',
    2 => 'uploads/cv/cv_sample_2.pdf',
    3 => 'uploads/cv/cv_sample_3.pdf'
];

foreach ($update_samples as $staff_id => $cv_path) {
    $sql = "UPDATE staff SET cv_file_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cv_path, $staff_id);
    if ($stmt->execute()) {
        $messages[] = "✓ อัปเดต CV path สำหรับ Staff ID $staff_id";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มระบบ CV Upload - โรงเรียนสาธิต</title>
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
        .success-item {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error-item {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .feature-card {
            background: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-file-pdf"></i> เพิ่มระบบ CV Upload</h1>
        
        <?php if (!empty($messages)): ?>
            <h3>ดำเนินการสำเร็จ:</h3>
            <?php foreach ($messages as $message): ?>
                <div class="success-item">
                    <?php echo $message; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h3>พบข้อผิดพลาด:</h3>
            <?php foreach ($errors as $error): ?>
                <div class="error-item">
                    <?php echo $error; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="feature-card">
            <h4><i class="fas fa-check-circle text-success"></i> คุณสมบัติที่เพิ่ม:</h4>
            <ul>
                <li><strong>Column ใหม่:</strong> cv_file_path ในตาราง staff</li>
                <li><strong>โฟลเดอร์:</strong> uploads/cv/ สำหรับเก็บไฟล์ CV</li>
                <li><strong>ไฟล์ตัวอย่าง:</strong> 3 ไฟล์ PDF สำหรับทดสอบ</li>
                <li><strong>รองรับ:</strong> อัปโหลดไฟล์ PDF ขนาดไม่เกิน 10MB</li>
            </ul>
        </div>
        
        <div class="feature-card">
            <h4><i class="fas fa-info-circle text-info"></i> วิธีใช้งาน:</h4>
            <ol>
                <li>ไปที่หน้า <strong>แก้ไขบุคลากร</strong></li>
                <li>จะเห็นช่อง <strong>"อัปโหลด CV (PDF)"</strong> ใหม่</li>
                <li>เลือกไฟล์ PDF และบันทึก</li>
                <li>CV จะแสดงในหน้า <strong>ดูรายละเอียด</strong> ทันที</li>
            </ol>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-custom">
                <i class="fas fa-users"></i> ไปหน้าจัดการบุคลากร
            </a>
            <a href="view.php?id=1" class="btn btn-custom">
                <i class="fas fa-eye"></i> ดูตัวอย่าง CV
            </a>
            <a href="edit.php?id=1" class="btn btn-custom">
                <i class="fas fa-upload"></i> ทดลองอัปโหลด CV
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center text-muted">
            <small>
                ระบบ CV Upload พร้อมใช้งานแล้ว<br>
                รองรับไฟล์ PDF ขนาดไม่เกิน 10MB
            </small>
        </div>
    </div>
</body>
</html>
