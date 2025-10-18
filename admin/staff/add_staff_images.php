<?php
/**
 * Add Staff Images / Update Image Paths
 * สร้างรูป placeholder และอัปเดต path ในฐานข้อมูล
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

// สร้างโฟลเดอร์สำหรับเก็บรูปภาพถ้ายังไม่มี
$upload_dir = '../../uploads/staff';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// สร้างรูป placeholder แบบสุ่มสีพื้นหลัง
function createPlaceholderImage($name, $filename, $upload_dir) {
    // สุ่มสีพื้นหลัง
    $colors = [
        ['r' => 102, 'g' => 126, 'b' => 234], // Blue
        ['r' => 118, 'g' => 75, 'b' => 162],  // Purple
        ['r' => 52, 'g' => 211, 'b' => 153],  // Teal
        ['r' => 251, 'g' => 146, 'b' => 60],  // Orange
        ['r' => 236, 'g' => 72, 'b' => 153],  // Pink
        ['r' => 16, 'g' => 185, 'b' => 129],  // Green
        ['r' => 139, 'g' => 92, 'b' => 246],  // Indigo
        ['r' => 244, 'g' => 63, 'b' => 94],   // Red
    ];
    
    $color = $colors[array_rand($colors)];
    
    // สร้างรูปภาพ
    $width = 400;
    $height = 400;
    $image = imagecreatetruecolor($width, $height);
    
    // สีพื้นหลัง
    $bg_color = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
    imagefill($image, 0, 0, $bg_color);
    
    // สีตัวอักษร (ขาว)
    $text_color = imagecolorallocate($image, 255, 255, 255);
    
    // ใช้ตัวอักษรแรกของชื่อ
    $initial = mb_substr($name, 0, 1, 'UTF-8');
    
    // ใช้ฟอนต์ในตัวของ PHP (ขนาดใหญ่สุด = 5)
    $font_size = 5;
    
    // คำนวณตำแหน่งกลางสำหรับข้อความ
    $text_width = imagefontwidth($font_size) * strlen($initial);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    // ใส่ข้อความขนาดใหญ่
    for ($i = 0; $i < 20; $i++) {
        for ($j = 0; $j < 20; $j++) {
            imagestring($image, 5, $x - 40 + $i*4, $y - 40 + $j*4, $initial, $text_color);
        }
    }
    
    // บันทึกรูปภาพ
    $filepath = $upload_dir . '/' . $filename;
    imagejpeg($image, $filepath, 90);
    imagedestroy($image);
    
    return 'uploads/staff/' . $filename;
}

// อ่านข้อมูลบุคลากรทั้งหมด
$sql = "SELECT id, first_name, last_name FROM staff";
$result = $conn->query($sql);

$messages = [];
$updated_count = 0;

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // สร้างชื่อไฟล์
        $filename = 'staff_' . $row['id'] . '_' . time() . '.jpg';
        
        // สร้างรูป placeholder
        $image_path = createPlaceholderImage($row['first_name'], $filename, $upload_dir);
        
        // อัปเดตในฐานข้อมูล
        $update_sql = "UPDATE staff SET image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $image_path, $row['id']);
        
        if ($stmt->execute()) {
            $updated_count++;
            $messages[] = "✓ เพิ่มรูปให้ " . $row['first_name'] . " " . $row['last_name'];
        }
        $stmt->close();
    }
}

// สร้างรูป placeholder ทั่วไปด้วย
$placeholder_path = $upload_dir . '/placeholder.jpg';
if (!file_exists($placeholder_path)) {
    $width = 400;
    $height = 400;
    $image = imagecreatetruecolor($width, $height);
    $gray = imagecolorallocate($image, 200, 200, 200);
    $dark_gray = imagecolorallocate($image, 100, 100, 100);
    imagefill($image, 0, 0, $gray);
    
    // วาดไอคอนผู้ใช้
    imagefilledellipse($image, 200, 150, 100, 100, $dark_gray); // หัว
    imagefilledarc($image, 200, 350, 200, 200, 0, 180, $dark_gray, IMG_ARC_PIE); // ตัว
    
    imagejpeg($image, $placeholder_path, 90);
    imagedestroy($image);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มรูปภาพบุคลากร - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
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
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }
        .staff-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
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
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        .counter {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-image"></i> อัปเดตรูปภาพบุคลากร</h1>
        
        <div class="info-card">
            <div class="counter"><?php echo $updated_count; ?></div>
            <div>รูปภาพที่เพิ่มสำเร็จ</div>
        </div>
        
        <?php if (!empty($messages)): ?>
        <div class="success-box">
            <h5><i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ:</h5>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php foreach ($messages as $message): ?>
                    <div><?php echo $message; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> หมายเหตุ:</h5>
            <ul class="mb-0">
                <li>ระบบได้สร้างรูป placeholder สีต่างๆ ให้กับบุคลากรทุกคน</li>
                <li>รูปจะแสดงตัวอักษรแรกของชื่อบุคลากร</li>
                <li>สามารถเปลี่ยนเป็นรูปจริงได้ภายหลังผ่าน Admin Panel</li>
                <li>รูปถูกบันทึกไว้ที่ <code>/uploads/staff/</code></li>
            </ul>
        </div>
        
        <?php
        // แสดงตัวอย่างรูปภาพ
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sample_sql = "SELECT image_path FROM staff LIMIT 12";
        $sample_result = $conn->query($sample_sql);
        
        if ($sample_result && $sample_result->num_rows > 0):
        ?>
        <h5 class="text-center mt-4">ตัวอย่างรูปภาพที่สร้าง:</h5>
        <div class="image-preview">
            <?php while($img = $sample_result->fetch_assoc()): ?>
                <img src="../../<?php echo $img['image_path']; ?>" class="staff-image" alt="Staff">
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
            <a href="../staff/" class="btn btn-custom">
                <i class="fas fa-cog"></i> จัดการบุคลากร
            </a>
            <a href="fix_and_populate_staff.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> รีเซ็ตข้อมูล
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center text-muted">
            <small>
                รูปภาพ placeholder ถูกสร้างเรียบร้อยแล้ว<br>
                สามารถอัปโหลดรูปจริงได้ทาง Admin Panel
            </small>
        </div>
    </div>
</body>
</html>
