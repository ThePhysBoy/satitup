<?php
// Include database connection
$conn = require_once '../includes/db_config.php';

echo "<h2>อัพเดตประเภทแผนก/หน่วยงาน</h2>";

// First, alter the enum to include 'primary'
$sql = "ALTER TABLE departments MODIFY COLUMN type ENUM('academic', 'support', 'primary', 'service') NOT NULL";
if ($conn->query($sql) === TRUE) {
    echo "<p>✅ อัพเดต ENUM type สำเร็จ</p>";
} else {
    echo "<p>⚠️ ไม่สามารถอัพเดต ENUM: " . $conn->error . "</p>";
}

// Update existing 'service' to 'support'
$sql = "UPDATE departments SET type = 'support' WHERE type = 'service'";
if ($conn->query($sql) === TRUE) {
    $affected = $conn->affected_rows;
    echo "<p>✅ อัพเดต service เป็น support จำนวน $affected แผนก</p>";
} else {
    echo "<p>❌ ไม่สามารถอัพเดต: " . $conn->error . "</p>";
}

// Update descriptions to use 'สายสนับสนุน' instead of 'สายบริการ'
$sql = "UPDATE departments SET description = REPLACE(description, 'สายบริการ', 'สายสนับสนุน') WHERE type = 'support'";
if ($conn->query($sql) === TRUE) {
    $affected = $conn->affected_rows;
    echo "<p>✅ อัพเดตคำอธิบายเป็นสายสนับสนุน จำนวน $affected แผนก</p>";
} else {
    echo "<p>❌ ไม่สามารถอัพเดตคำอธิบาย: " . $conn->error . "</p>";
}

// Now remove 'service' from enum
$sql = "ALTER TABLE departments MODIFY COLUMN type ENUM('academic', 'support', 'primary') NOT NULL";
if ($conn->query($sql) === TRUE) {
    echo "<p>✅ ลบ 'service' ออกจาก ENUM สำเร็จ</p>";
} else {
    echo "<p>⚠️ ไม่สามารถลบ 'service': " . $conn->error . "</p>";
}

// Insert new primary education departments if they don't exist
$primary_departments = [
    [15, 'ประถมศึกษาปีที่ 1', 'ครูประจำชั้นประถมศึกษาปีที่ 1', 'primary', 1],
    [16, 'ประถมศึกษาปีที่ 2', 'ครูประจำชั้นประถมศึกษาปีที่ 2', 'primary', 2],
    [17, 'ประถมศึกษาปีที่ 3', 'ครูประจำชั้นประถมศึกษาปีที่ 3', 'primary', 3],
    [18, 'ประถมศึกษาปีที่ 4', 'ครูประจำชั้นประถมศึกษาปีที่ 4', 'primary', 4],
    [19, 'ประถมศึกษาปีที่ 5', 'ครูประจำชั้นประถมศึกษาปีที่ 5', 'primary', 5],
    [20, 'ประถมศึกษาปีที่ 6', 'ครูประจำชั้นประถมศึกษาปีที่ 6', 'primary', 6],
    [21, 'ครูพิเศษประถมศึกษา', 'ครูผู้สอนวิชาพิเศษระดับประถมศึกษา', 'primary', 7]
];

echo "<h3>เพิ่มแผนกประถมศึกษา:</h3>";
foreach ($primary_departments as $dept) {
    $check = $conn->prepare("SELECT id FROM departments WHERE id = ?");
    $check->bind_param('i', $dept[0]);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO departments (id, name, description, type, order_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $dept[0], $dept[1], $dept[2], $dept[3], $dept[4]);
        
        if ($stmt->execute()) {
            echo "<p>✅ เพิ่ม: {$dept[1]}</p>";
        } else {
            echo "<p>❌ ไม่สามารถเพิ่ม {$dept[1]}: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>⚠️ มีอยู่แล้ว: {$dept[1]}</p>";
    }
}

echo "<hr>";
echo "<h3>สรุปแผนก/หน่วยงานทั้งหมด:</h3>";

// Show summary
$types = ['academic' => 'สายวิชาการ', 'support' => 'สายสนับสนุน', 'primary' => 'ประถมศึกษา'];
foreach ($types as $type => $label) {
    $result = $conn->query("SELECT COUNT(*) as count FROM departments WHERE type = '$type'");
    $row = $result->fetch_assoc();
    echo "<p>$label: {$row['count']} แผนก</p>";
}

echo "<hr>";
echo "<p><a href='index.php' class='btn btn-primary'>ไปที่หน้าจัดการบุคลากร</a></p>";
echo "<p><a href='../../staff/index.php' class='btn btn-success'>ไปที่หน้าแสดงบุคลากร</a></p>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัพเดตแผนก/หน่วยงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
</body>
</html>
