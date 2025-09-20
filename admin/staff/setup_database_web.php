<?php
// Include database connection
$conn = require_once '../includes/db_config.php';

// Path to the SQL file
$sql_file = __DIR__ . '/staff_tables.sql';

// Read the SQL file content
$sql_commands = file_get_contents($sql_file);

if ($sql_commands === false) {
    die("Error: Could not read SQL file.");
}

// Execute SQL commands
if ($conn->multi_query($sql_commands)) {
    $success = true;
    $error = "";
    
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
        
        // Check for errors
        if ($conn->error) {
            $success = false;
            $error = $conn->error;
            break;
        }
        
        // While there are more results
    } while ($conn->more_results() && $conn->next_result());
    
    if ($success) {
        echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px;">
            <h3>สำเร็จ!</h3>
            <p>ตั้งค่าฐานข้อมูลสำหรับระบบจัดการบุคลากรเรียบร้อยแล้ว</p>
            <p><a href="../index.php" style="color: #155724; text-decoration: underline;">กลับไปหน้าแดชบอร์ด</a></p>
            <p><a href="../../staff/index.php" style="color: #155724; text-decoration: underline;">ไปที่หน้าบุคลากร</a></p>
        </div>';
    } else {
        echo '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;">
            <h3>เกิดข้อผิดพลาด!</h3>
            <p>เกิดข้อผิดพลาดในการตั้งค่าฐานข้อมูล: ' . $error . '</p>
            <p><a href="../index.php" style="color: #721c24; text-decoration: underline;">กลับไปหน้าแดชบอร์ด</a></p>
        </div>';
    }
} else {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;">
        <h3>เกิดข้อผิดพลาด!</h3>
        <p>เกิดข้อผิดพลาดในการตั้งค่าฐานข้อมูล: ' . $conn->error . '</p>
        <p><a href="../index.php" style="color: #721c24; text-decoration: underline;">กลับไปหน้าแดชบอร์ด</a></p>
    </div>';
}

$conn->close();
?>
