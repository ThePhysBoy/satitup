<?php
// Simple test to check if all sections are working
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ทดสอบการแสดงผล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">ทดสอบการแสดงผลแต่ละส่วน</h1>
        
        <div class="row">
            <div class="col-12">
                <h2>1. ทดสอบ Header</h2>
                <div class="border p-3 mb-3">
                    <?php 
                    ob_start();
                    include 'header.php';
                    $content = ob_get_clean();
                    echo strlen($content) > 0 ? "✅ Header โหลดสำเร็จ (" . strlen($content) . " bytes)" : "❌ Header ไม่มีเนื้อหา";
                    ?>
                </div>
                
                <h2>2. ทดสอบ Navbar</h2>
                <div class="border p-3 mb-3">
                    <?php 
                    ob_start();
                    include 'navbar.php';
                    $content = ob_get_clean();
                    echo strlen($content) > 0 ? "✅ Navbar โหลดสำเร็จ (" . strlen($content) . " bytes)" : "❌ Navbar ไม่มีเนื้อหา";
                    ?>
                </div>
                
                <h2>3. ทดสอบ News Announcements</h2>
                <div class="border p-3 mb-3">
                    <?php 
                    ob_start();
                    include 'news_announcements.php';
                    $content = ob_get_clean();
                    echo strlen($content) > 0 ? "✅ News โหลดสำเร็จ (" . strlen($content) . " bytes)" : "❌ News ไม่มีเนื้อหา";
                    ?>
                </div>
                
                <h2>4. ทดสอบ Footer</h2>
                <div class="border p-3 mb-3">
                    <?php 
                    ob_start();
                    include 'footer.php';
                    $content = ob_get_clean();
                    echo strlen($content) > 0 ? "✅ Footer โหลดสำเร็จ (" . strlen($content) . " bytes)" : "❌ Footer ไม่มีเนื้อหา";
                    ?>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">ไปยังหน้า Index</a>
            <a href="debug_index.php" class="btn btn-warning">Debug แบบละเอียด</a>
        </div>
    </div>
</body>
</html>
