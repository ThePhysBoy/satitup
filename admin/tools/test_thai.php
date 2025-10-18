<?php
// Force UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Test database connection with UTF-8
$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');
$conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

// Get announcement data
$id = 1;
$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Test Thai Encoding</title>
    <style>
        body { 
            font-family: 'Sarabun', 'Prompt', sans-serif; 
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        pre {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>ทดสอบการแสดงผลภาษาไทย</h1>
    
    <div class="box">
        <h2>1. ทดสอบ HTML โดยตรง</h2>
        <p>ข้อความภาษาไทย: สวัสดีครับ ทดสอบภาษาไทย ๑๒๓๔๕๖๗๘๙๐</p>
        <p>ตัวอย่างประโยค: โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
    </div>
    
    <div class="box">
        <h2>2. ข้อมูลจากฐานข้อมูล (ID: <?php echo $id; ?>)</h2>
        <?php if ($data): ?>
            <p><strong>Title (Raw):</strong> <?php echo $data['title']; ?></p>
            <p><strong>Title (htmlspecialchars):</strong> <?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Content:</strong> <?php echo htmlspecialchars($data['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($data['department'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
            
            <h3>Debug Info:</h3>
            <pre><?php 
                echo "Title bytes: ";
                for ($i = 0; $i < strlen($data['title']) && $i < 50; $i++) {
                    echo dechex(ord($data['title'][$i])) . " ";
                }
                echo "\n";
                echo "Encoding detected: " . mb_detect_encoding($data['title'], 'UTF-8, ISO-8859-1, Windows-1252, TIS-620', true);
            ?></pre>
        <?php else: ?>
            <p>ไม่พบข้อมูล</p>
        <?php endif; ?>
    </div>
    
    <div class="box">
        <h2>3. Database Charset Info</h2>
        <pre><?php
            $result = $conn->query("SHOW VARIABLES LIKE 'character_set_%'");
            while ($row = $result->fetch_assoc()) {
                echo $row['Variable_name'] . ": " . $row['Value'] . "\n";
            }
        ?></pre>
    </div>
    
    <div class="box">
        <h2>4. PHP Info</h2>
        <pre><?php
            echo "default_charset: " . ini_get('default_charset') . "\n";
            echo "mbstring.internal_encoding: " . ini_get('mbstring.internal_encoding') . "\n";
            echo "mbstring.http_output: " . ini_get('mbstring.http_output') . "\n";
        ?></pre>
    </div>
    
    <div class="box">
        <h2>5. แก้ไขข้อมูล</h2>
        <p>หากข้อมูลแสดงผลเป็น ??????? ให้ใช้เครื่องมือนี้:</p>
        <a href="fix_thai_encoding.php" class="btn" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
            เปิดเครื่องมือแก้ไข Encoding
        </a>
    </div>
</body>
</html>
