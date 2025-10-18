<?php
// Fix Thai encoding in database
header('Content-Type: text/html; charset=UTF-8');

$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireAdmin();

$message = '';

// Function to fix encoding
function fixEncoding($text) {
    // Try different encoding conversions
    $conversions = [
        ['from' => 'Windows-1252', 'to' => 'UTF-8'],
        ['from' => 'ISO-8859-1', 'to' => 'UTF-8'],
        ['from' => 'TIS-620', 'to' => 'UTF-8']
    ];
    
    foreach ($conversions as $conv) {
        $converted = @iconv($conv['from'], $conv['to'] . '//IGNORE', $text);
        if ($converted !== false && !preg_match('/\?{3,}/', $converted)) {
            return $converted;
        }
    }
    
    // If all else fails, try mb_convert_encoding
    $converted = @mb_convert_encoding($text, 'UTF-8', 'auto');
    if ($converted !== false) {
        return $converted;
    }
    
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_encoding'])) {
    // Fix announcements table
    $updated = 0;
    
    $result = $conn->query("SELECT id, title, content, department FROM announcements WHERE title LIKE '%?%' OR content LIKE '%?%' OR department LIKE '%?%'");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $newTitle = fixEncoding($row['title']);
            $newContent = fixEncoding($row['content']);
            $newDept = fixEncoding($row['department']);
            
            $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, department = ? WHERE id = ?");
            $stmt->bind_param('sssi', $newTitle, $newContent, $newDept, $row['id']);
            if ($stmt->execute()) {
                $updated++;
            }
        }
    }
    
    $message = "อัปเดต encoding สำเร็จ $updated รายการ";
}

// Test current database charset
$charset_result = $conn->query("SHOW VARIABLES LIKE 'character_set_%'");
$charsets = [];
while ($row = $charset_result->fetch_assoc()) {
    $charsets[$row['Variable_name']] = $row['Value'];
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขปัญหา Encoding ภาษาไทย</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">แก้ไขปัญหา Encoding ภาษาไทย</h4>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5>Database Charset Settings:</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>character_set_client:</strong> <?php echo $charsets['character_set_client'] ?? 'N/A'; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>character_set_connection:</strong> <?php echo $charsets['character_set_connection'] ?? 'N/A'; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>character_set_database:</strong> <?php echo $charsets['character_set_database'] ?? 'N/A'; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>character_set_results:</strong> <?php echo $charsets['character_set_results'] ?? 'N/A'; ?>
                                </li>
                            </ul>
                        </div>
                        
                        <form method="post">
                            <button type="submit" name="fix_encoding" class="btn btn-primary">
                                แก้ไข Encoding ข้อมูลที่มีปัญหา
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <h5>ทดสอบการแสดงผลภาษาไทย:</h5>
                            <p>ทดสอบ: ภาษาไทย กขคง ๑๒๓๔ English 1234</p>
                            <p>หากข้อความด้านบนแสดงผลถูกต้อง แสดงว่า encoding ทำงานปกติ</p>
                        </div>
                        
                        <a href="../index.php" class="btn btn-secondary">กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
