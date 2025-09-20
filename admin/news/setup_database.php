<?php
/**
 * Setup News Database Tables
 * This script creates the necessary database tables for the news system
 */

// Include database connection
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require admin privileges
requireAdmin();

// Read the SQL file
$sql = file_get_contents('news_tables.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    $success = true;
    $error = "";
    
    // Process all result sets
    do {
        // Store result
        if ($result = $conn->store_result()) {
            $result->free();
        }
        
        // Check for errors
        if ($conn->error) {
            $success = false;
            $error = $conn->error;
            break;
        }
    } while ($conn->next_result());
} else {
    $success = false;
    $error = $conn->error;
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าฐานข้อมูลข่าว - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fa;
            padding: 2rem;
        }
        
        .setup-card {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 2rem;
        }
        
        .icon-large {
            font-size: 4rem;
        }
        
        .success-icon {
            color: #1cc88a;
        }
        
        .error-icon {
            color: #e74a3b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card">
            <div class="text-center mb-4">
                <?php if ($success): ?>
                    <i class="fas fa-check-circle icon-large success-icon mb-3"></i>
                    <h2>ตั้งค่าฐานข้อมูลสำเร็จ</h2>
                    <p class="text-muted">ระบบได้สร้างตารางฐานข้อมูลสำหรับระบบจัดการข่าวเรียบร้อยแล้ว</p>
                <?php else: ?>
                    <i class="fas fa-times-circle icon-large error-icon mb-3"></i>
                    <h2>เกิดข้อผิดพลาด</h2>
                    <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                <a href="../index.php" class="btn btn-primary me-2">
                    <i class="fas fa-home me-1"></i> กลับหน้าแผงควบคุม
                </a>
                <?php if ($success): ?>
                    <a href="index.php" class="btn btn-success">
                        <i class="fas fa-newspaper me-1"></i> ไปยังระบบจัดการข่าว
                    </a>
                <?php else: ?>
                    <a href="setup_database.php" class="btn btn-warning">
                        <i class="fas fa-redo me-1"></i> ลองใหม่อีกครั้ง
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
