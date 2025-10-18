<?php
/**
 * Simple Add Staff Images
 * อัปเดต path รูปภาพให้บุคลากรทุกคน
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

// Array ของรูป placeholder ที่มีอยู่แล้ว
$placeholder_images = [
    'images/faculties/faculty-med.jpg',
    'images/faculties/faculty-eng.jpg',
    'images/faculties/faculty-science.jpg',
    'images/faculties/faculty-libarts.jpg',
    'images/faculties/faculty-ed.jpg',
    'images/faculties/faculty-nurse.jpg',
    'images/faculties/faculty-pharm.jpg',
    'images/faculties/faculty-law.jpg',
    'images/faculties/faculty-arch.jpg',
    'images/faculties/faculty-dent.jpg',
    'images/faculties/faculty-medsci.jpg',
    'images/faculties/faculty-ams.jpg',
    'images/faculties/faculty-agri.jpg',
    'images/faculties/faculty-ict.jpg',
    'images/faculties/faculty-polsci.jpg',
    'images/faculties/faculty-scm.jpg',
    'images/faculties/does-1.jpg',
    'images/faculties/dra.jpg',
    'images/faculties/dsa.jpg',
    'images/faculties/hru.jpg'
];

// ถ้าไม่มีรูปในโฟลเดอร์ faculties ให้ใช้ placeholder ทั่วไป
$default_image = 'images/comingsoon.png';

// อ่านข้อมูลบุคลากรทั้งหมด
$sql = "SELECT id, first_name, last_name, department_id FROM staff ORDER BY id";
$result = $conn->query($sql);

$messages = [];
$updated_count = 0;

if ($result && $result->num_rows > 0) {
    $index = 0;
    while($row = $result->fetch_assoc()) {
        // เลือกรูปจาก array แบบวนรอบ
        if (isset($placeholder_images[$index])) {
            $image_path = $placeholder_images[$index];
        } else {
            // ถ้าบุคลากรมีมากกว่ารูป ให้วนกลับไปใช้รูปแรก
            $image_path = $placeholder_images[$index % count($placeholder_images)];
        }
        
        // ตรวจสอบว่ารูปมีอยู่จริงหรือไม่
        $full_path = '../../' . $image_path;
        if (!file_exists($full_path)) {
            $image_path = $default_image;
        }
        
        // อัปเดตในฐานข้อมูล
        $update_sql = "UPDATE staff SET image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $image_path, $row['id']);
        
        if ($stmt->execute()) {
            $updated_count++;
            $messages[] = "✓ เพิ่มรูปให้ " . $row['first_name'] . " " . $row['last_name'] . " (" . $image_path . ")";
        }
        $stmt->close();
        
        $index++;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัปเดตรูปภาพบุคลากร - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
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
            max-width: 1000px;
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
            max-height: 400px;
            overflow-y: auto;
        }
        .info-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .counter {
            font-size: 4rem;
            font-weight: bold;
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
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .staff-preview {
            text-align: center;
        }
        .staff-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .staff-preview .name {
            font-size: 0.75rem;
            margin-top: 5px;
            color: #666;
        }
        .alert-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-camera"></i> อัปเดตรูปภาพบุคลากร</h1>
        
        <div class="info-card">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <div class="counter"><?php echo $updated_count; ?></div>
            <div>รูปภาพที่อัปเดตสำเร็จ</div>
        </div>
        
        <?php if ($updated_count > 0): ?>
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle"></i> ดำเนินการเรียบร้อย!</h5>
            <p>ระบบได้อัปเดตรูปภาพให้บุคลากรทั้งหมด <?php echo $updated_count; ?> คน</p>
        </div>
        
        <?php if (!empty($messages)): ?>
        <div class="success-box">
            <h6>รายละเอียด:</h6>
            <?php foreach ($messages as $message): ?>
                <div class="small"><?php echo $message; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        
        <div class="alert-custom">
            <h5><i class="fas fa-info-circle"></i> ข้อมูลเพิ่มเติม</h5>
            <ul class="mb-0">
                <li>ระบบใช้รูปจากโฟลเดอร์ <code>images/faculties/</code></li>
                <li>รูปภาพแต่ละรูปถูกกำหนดให้บุคลากรแต่ละคน</li>
                <li>สามารถเปลี่ยนเป็นรูปจริงได้ผ่าน Admin Panel</li>
                <li>รูปภาพแสดงในหน้าบุคลากรทันที</li>
            </ul>
        </div>
        
        <?php
        // แสดงตัวอย่างบุคลากรพร้อมรูป
        $conn = new mysqli($servername, $username, $password, $dbname);
        $preview_sql = "SELECT first_name, last_name, image_path FROM staff LIMIT 20";
        $preview_result = $conn->query($preview_sql);
        
        if ($preview_result && $preview_result->num_rows > 0):
        ?>
        <h5 class="text-center mt-4">ตัวอย่างบุคลากรพร้อมรูปภาพ:</h5>
        <div class="image-grid">
            <?php while($staff = $preview_result->fetch_assoc()): ?>
                <div class="staff-preview">
                    <img src="../../<?php echo $staff['image_path']; ?>" 
                         alt="<?php echo $staff['first_name']; ?>"
                         onerror="this.src='../../images/comingsoon.png'">
                    <div class="name"><?php echo $staff['first_name']; ?></div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php 
        endif;
        $conn->close();
        ?>
        
        <div class="text-center mt-4">
            <a href="../../staff/" class="btn btn-custom" target="_blank">
                <i class="fas fa-users"></i> ดูหน้าแสดงบุคลากร
            </a>
            <a href="../../staff/staff_detail.php?id=1" class="btn btn-custom" target="_blank">
                <i class="fas fa-user"></i> ดูตัวอย่าง CV
            </a>
            <a href="../staff/" class="btn btn-custom">
                <i class="fas fa-cog"></i> จัดการบุคลากร  
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center text-muted">
            <small>
                <i class="fas fa-check"></i> ระบบพร้อมนำเสนอผู้บริหาร<br>
                รูปภาพทุกรูปแสดงผลเรียบร้อยแล้ว
            </small>
        </div>
    </div>
</body>
</html>
