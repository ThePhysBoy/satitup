<?php
/**
 * Insert Management Data
 * This script inserts all management committee members into the database
 */

require_once 'db_connect.php';

if (!$conn) {
    die("Database connection failed");
}

// Clear existing data (optional - comment out if you want to keep existing data)
// $conn->query("TRUNCATE TABLE management");

// Management data array
$management_data = [
    // ผู้อำนวยการ
    ['รศ.ดร.', 'ชยันต์', 'บุณยรักษ์', 'ผู้อำนวยการ ประธานกรรมการ', 1],
    
    // กรรมการผู้ทรงคุณวุฒิ
    ['รศ.', 'นุชนาฏ', 'ดีเจริญ', 'กรรมการผู้ทรงคุณวุฒิ', 2],
    ['ผศ.ดร.', 'วสันต์', 'สรรพสุข', 'กรรมการผู้ทรงคุณวุฒิ', 3],
    ['ผศ.ดร.', 'สกุลคุณ', 'มากคุณ', 'กรรมการผู้ทรงคุณวุฒิ', 4],
    
    // รองผู้อำนวยการ
    ['ผศ.ดร.', 'บุหรัน', 'พันธุ์สวรรค์', 'รองผู้อำนวยการฝ่ายบริหาร กรรมการ', 5],
    ['ผศ.ดร.', 'ชนม์เจริญ', 'ชัยรัตน์สิริพงศ์', 'รองผู้อำนวยการฝ่ายวิชาการ กรรมการ', 6],
    ['ดร.', 'ชัชวาล', 'วงศ์ชัย', 'รองผู้อำนวยการฝ่ายกิจการนักเรียน กรรมการ', 7],
    ['อ.', 'อรรถพล', 'คณิตชรางกูร', 'รองผู้อำนวยการฝ่ายแผนงาน กรรมการ', 8],
    
    // ผู้ช่วยผู้อำนวยการ
    ['รศ.ดร.', 'วิจิตรา', 'จิตอ่อนน้อม', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 9],
    ['ดร.', 'ตระกูลพันธ์', 'ยุชมภู', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 10],
    ['ดร.', 'คงอมร', 'เหมรัตน์รักษ์', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 11],
    ['อ.', 'พันธิตรา', 'กมล', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 12],
    ['อ.', 'อธิพงศ์', 'สัทรรรมนุวงศ์', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 13],
    ['อ.', 'ปพิชญา', 'ปานใจ', 'ผู้ช่วยผู้อำนวยการ กรรมการ', 14],
    
    // ประธานผู้รับผิดชอบหลักสูตร
    ['อ.', 'พิมพ์พร', 'รรรมสนธิ', 'ประธานผู้รับผิดชอบหลักสูตร กรรมการ', 15],
    ['ดร.', 'นิภาวรรณ', 'นฤเปรมปรีดิ์', 'ประธานผู้รับผิดชอบหลักสูตร กรรมการ', 16],
    ['ผศ.', 'พชรรัช', 'ไชยมงคล', 'ประธานผู้รับผิดชอบหลักสูตร กรรมการ', 17],
    ['ผศ.ดร.', 'ศุภชัย', 'วันประโคน', 'ประธานผู้รับผิดชอบหลักสูตร กรรมการ', 18],
    
    // ตัวแทนอาจารย์
    ['ดร.', 'ดรุณี', 'อภัยกาวี', 'ตัวแทนอาจารย์ กรรมการ', 19],
    ['อ.', 'วุฒิไกร', 'ใจคำพู', 'ตัวแทนอาจารย์ กรรมการ', 20],
    
    // หัวหน้าสำนักงาน/หัวหน้างาน
    ['', 'อินทนิล', 'จินดากาศ', 'หัวหน้าสำนักงาน กรรมการและเลขานุการ', 21],
    ['', 'พนิดา', 'ดุมดก', 'หัวหน้างานวิชาการ ผู้ช่วยเลขานุการ', 22],
    ['', 'กานต์ชนก', 'บุญแข็ง', 'หัวหน้างานกิจการนักเรียน ผู้ช่วยเลขานุการ', 23],
];

$inserted = 0;
$errors = [];

foreach ($management_data as $data) {
    list($title, $first_name, $last_name, $position, $order) = $data;
    
    $stmt = $conn->prepare("INSERT INTO management (title, first_name, last_name, management_position, order_number, status, email, phone, bio, image_path) VALUES (?, ?, ?, ?, ?, 'active', '', '', '', '')");
    
    if ($stmt) {
        $stmt->bind_param('ssssi', $title, $first_name, $last_name, $position, $order);
        
        if ($stmt->execute()) {
            $inserted++;
            echo "✓ เพิ่ม: {$title}{$first_name} {$last_name} - {$position}<br>";
        } else {
            $errors[] = "✗ ผิดพลาด: {$title}{$first_name} {$last_name} - " . $stmt->error;
            echo "✗ ผิดพลาด: {$title}{$first_name} {$last_name} - " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        $errors[] = "✗ ไม่สามารถเตรียม statement: " . $conn->error;
        echo "✗ ไม่สามารถเตรียม statement: " . $conn->error . "<br>";
    }
}

echo "<br><hr><br>";
echo "<h3>สรุปผลการเพิ่มข้อมูล</h3>";
echo "<p>✓ เพิ่มสำเร็จ: <strong>{$inserted}</strong> รายการ</p>";
echo "<p>✗ ผิดพลาด: <strong>" . count($errors) . "</strong> รายการ</p>";

if (!empty($errors)) {
    echo "<br><h4>รายละเอียดข้อผิดพลาด:</h4>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>{$error}</li>";
    }
    echo "</ul>";
}

echo "<br><hr><br>";
echo "<p><a href='about-management.php' class='btn btn-primary'>ดูหน้าคณะกรรมการบริหาร</a></p>";
echo "<p><a href='admin/management/index.php' class='btn btn-secondary'>ไปหน้าจัดการผู้บริหาร (Admin)</a></p>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลคณะกรรมการบริหาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: 20px auto;
        }
        h3 {
            color: #4e73df;
        }
        .btn {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Results will be displayed here -->
    </div>
</body>
</html>

