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
        echo '<div class="alert alert-success">ตั้งค่าฐานข้อมูลสำหรับระบบจัดการบุคลากรเรียบร้อยแล้ว</div>';
    } else {
        echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการตั้งค่าฐานข้อมูล: ' . $error . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการตั้งค่าฐานข้อมูล: ' . $conn->error . '</div>';
}

$conn->close();
?>