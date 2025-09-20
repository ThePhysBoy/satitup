<?php
// Include database connection
$conn = require_once '../includes/db_config.php';

echo "<h2>เพิ่มข้อมูลตัวอย่างบุคลากร</h2>";

// Sample staff data
$sample_staff = [
    // Academic staff
    [
        'title' => 'นางสาว',
        'first_name' => 'สมศรี',
        'last_name' => 'ใจดี',
        'department_id' => 1, // วิทยาศาสตร์และเทคโนโลยี
        'email' => 'somsri@school.ac.th',
        'phone' => '081-234-5678',
        'education' => 'ปริญญาโท วิทยาศาสตร์ มหาวิทยาลัยพะเยา',
        'bio' => 'ครูผู้สอนวิชาวิทยาศาสตร์ ประสบการณ์ 10 ปี',
        'is_head' => 1,
        'position' => 'หัวหน้ากลุ่มสาระวิทยาศาสตร์'
    ],
    [
        'title' => 'นาย',
        'first_name' => 'สมชาย',
        'last_name' => 'รักเรียน',
        'department_id' => 1,
        'email' => 'somchai@school.ac.th',
        'phone' => '082-345-6789',
        'education' => 'ปริญญาตรี วิทยาศาสตร์ มหาวิทยาลัยเชียงใหม่',
        'bio' => 'ครูผู้สอนวิชาเคมี',
        'is_head' => 0,
        'position' => 'ครูผู้สอน'
    ],
    [
        'title' => 'นางสาว',
        'first_name' => 'มาลี',
        'last_name' => 'สว่างใจ',
        'department_id' => 4, // คณิตศาสตร์
        'email' => 'malee@school.ac.th',
        'phone' => '083-456-7890',
        'education' => 'ปริญญาโท คณิตศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย',
        'bio' => 'ครูผู้สอนวิชาคณิตศาสตร์ ชั้นมัธยมศึกษาตอนปลาย',
        'is_head' => 1,
        'position' => 'หัวหน้ากลุ่มสาระคณิตศาสตร์'
    ],
    // Service staff
    [
        'title' => 'นาง',
        'first_name' => 'วิภา',
        'last_name' => 'งามสง่า',
        'department_id' => 9, // งานบริหาร
        'email' => 'wipa@school.ac.th',
        'phone' => '084-567-8901',
        'education' => 'ปริญญาตรี บริหารธุรกิจ มหาวิทยาลัยพะเยา',
        'bio' => 'เจ้าหน้าที่บริหารงานทั่วไป',
        'is_head' => 0,
        'position' => 'เจ้าหน้าที่บริหารงานทั่วไป'
    ],
    [
        'title' => 'นาย',
        'first_name' => 'ประเสริฐ',
        'last_name' => 'มีความสุข',
        'department_id' => 10, // งานวิชาการ
        'email' => 'prasert@school.ac.th',
        'phone' => '085-678-9012',
        'education' => 'ปริญญาตรี การศึกษา มหาวิทยาลัยนเรศวร',
        'bio' => 'เจ้าหน้าที่งานวิชาการ',
        'is_head' => 1,
        'position' => 'หัวหน้างานวิชาการ'
    ]
];

// Insert sample staff
foreach ($sample_staff as $staff) {
    // Check if staff already exists
    $check_stmt = $conn->prepare("SELECT id FROM staff WHERE first_name = ? AND last_name = ?");
    $check_stmt->bind_param('ss', $staff['first_name'], $staff['last_name']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // Insert staff
        $stmt = $conn->prepare("INSERT INTO staff (title, first_name, last_name, department_id, email, phone, education, bio, is_head, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param('sssissssi', 
            $staff['title'], 
            $staff['first_name'], 
            $staff['last_name'], 
            $staff['department_id'], 
            $staff['email'], 
            $staff['phone'], 
            $staff['education'], 
            $staff['bio'], 
            $staff['is_head']
        );
        
        if ($stmt->execute()) {
            $staff_id = $conn->insert_id;
            
            // Insert position
            $pos_stmt = $conn->prepare("INSERT INTO staff_positions (staff_id, position_name, is_primary) VALUES (?, ?, 1)");
            $pos_stmt->bind_param('is', $staff_id, $staff['position']);
            $pos_stmt->execute();
            
            echo "<p>✅ เพิ่มข้อมูล: {$staff['title']} {$staff['first_name']} {$staff['last_name']}</p>";
        } else {
            echo "<p>❌ ไม่สามารถเพิ่มข้อมูล: {$staff['title']} {$staff['first_name']} {$staff['last_name']} - {$conn->error}</p>";
        }
    } else {
        echo "<p>⚠️ มีข้อมูลแล้ว: {$staff['title']} {$staff['first_name']} {$staff['last_name']}</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>ไปที่หน้าจัดการบุคลากร</a></p>";
echo "<p><a href='../../staff/index.php'>ไปที่หน้าแสดงบุคลากร (Frontend)</a></p>";

$conn->close();
?>
