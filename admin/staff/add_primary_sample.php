<?php
// Include database connection
$conn = require_once '../includes/db_config.php';

echo "<h2>เพิ่มข้อมูลตัวอย่างบุคลากรประถมศึกษา</h2>";

// Sample primary education staff data
$primary_staff = [
    // ประถมศึกษาปีที่ 1
    [
        'title' => 'นางสาว',
        'first_name' => 'พิมพ์ใจ',
        'last_name' => 'สดใส',
        'department_id' => 15, // ประถมศึกษาปีที่ 1
        'email' => 'pimjai@school.ac.th',
        'phone' => '081-111-2222',
        'education' => 'ปริญญาโท การศึกษาประถมศึกษา มหาวิทยาลัยพะเยา',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 1 ประสบการณ์สอน 8 ปี',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.1'
    ],
    // ประถมศึกษาปีที่ 2
    [
        'title' => 'นาง',
        'first_name' => 'รัตนา',
        'last_name' => 'แสงทอง',
        'department_id' => 16, // ประถมศึกษาปีที่ 2
        'email' => 'rattana@school.ac.th',
        'phone' => '082-222-3333',
        'education' => 'ปริญญาตรี การประถมศึกษา มหาวิทยาลัยนเรศวร',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 2',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.2'
    ],
    // ประถมศึกษาปีที่ 3
    [
        'title' => 'นาย',
        'first_name' => 'วีระ',
        'last_name' => 'มั่นคง',
        'department_id' => 17, // ประถมศึกษาปีที่ 3
        'email' => 'weera@school.ac.th',
        'phone' => '083-333-4444',
        'education' => 'ปริญญาโท การบริหารการศึกษา มหาวิทยาลัยเชียงใหม่',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 3',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.3'
    ],
    // ประถมศึกษาปีที่ 4
    [
        'title' => 'นางสาว',
        'first_name' => 'สุภาพร',
        'last_name' => 'เจริญผล',
        'department_id' => 18, // ประถมศึกษาปีที่ 4
        'email' => 'supaporn@school.ac.th',
        'phone' => '084-444-5555',
        'education' => 'ปริญญาตรี การประถมศึกษา จุฬาลงกรณ์มหาวิทยาลัย',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 4',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.4'
    ],
    // ประถมศึกษาปีที่ 5
    [
        'title' => 'นาง',
        'first_name' => 'ดวงใจ',
        'last_name' => 'ศรีสุข',
        'department_id' => 19, // ประถมศึกษาปีที่ 5
        'email' => 'duangjai@school.ac.th',
        'phone' => '085-555-6666',
        'education' => 'ปริญญาโท หลักสูตรและการสอน มหาวิทยาลัยขอนแก่น',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 5',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.5'
    ],
    // ประถมศึกษาปีที่ 6
    [
        'title' => 'นาย',
        'first_name' => 'ชัยวัฒน์',
        'last_name' => 'วิชัยดี',
        'department_id' => 20, // ประถมศึกษาปีที่ 6
        'email' => 'chaiwat@school.ac.th',
        'phone' => '086-666-7777',
        'education' => 'ปริญญาโท การศึกษาประถมศึกษา มหาวิทยาลัยศรีนครินทรวิโรฒ',
        'bio' => 'ครูประจำชั้นประถมศึกษาปีที่ 6 และหัวหน้าระดับประถมศึกษา',
        'is_head' => 1,
        'position' => 'ครูประจำชั้น ป.6 / หัวหน้าระดับประถมศึกษา'
    ],
    // ครูพิเศษประถมศึกษา
    [
        'title' => 'นางสาว',
        'first_name' => 'ณัฐธิดา',
        'last_name' => 'รักการสอน',
        'department_id' => 21, // ครูพิเศษประถมศึกษา
        'email' => 'natthida@school.ac.th',
        'phone' => '087-777-8888',
        'education' => 'ปริญญาตรี ภาษาอังกฤษ มหาวิทยาลัยธรรมศาสตร์',
        'bio' => 'ครูผู้สอนภาษาอังกฤษระดับประถมศึกษา',
        'is_head' => 0,
        'position' => 'ครูผู้สอนภาษาอังกฤษ'
    ],
    [
        'title' => 'นาย',
        'first_name' => 'อนุชา',
        'last_name' => 'ศิลปะดี',
        'department_id' => 21, // ครูพิเศษประถมศึกษา
        'email' => 'anucha@school.ac.th',
        'phone' => '088-888-9999',
        'education' => 'ปริญญาตรี ศิลปศึกษา มหาวิทยาลัยศิลปากร',
        'bio' => 'ครูผู้สอนศิลปะระดับประถมศึกษา',
        'is_head' => 0,
        'position' => 'ครูผู้สอนศิลปะ'
    ],
    [
        'title' => 'นางสาว',
        'first_name' => 'จิราพร',
        'last_name' => 'กีฬาเก่ง',
        'department_id' => 21, // ครูพิเศษประถมศึกษา
        'email' => 'jiraporn@school.ac.th',
        'phone' => '089-999-0000',
        'education' => 'ปริญญาตรี พลศึกษา มหาวิทยาลัยการกีฬาแห่งชาติ',
        'bio' => 'ครูผู้สอนพลศึกษาระดับประถมศึกษา',
        'is_head' => 0,
        'position' => 'ครูผู้สอนพลศึกษา'
    ]
];

// Insert sample primary staff
foreach ($primary_staff as $staff) {
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
            
            echo "<p>✅ เพิ่มข้อมูล: {$staff['title']} {$staff['first_name']} {$staff['last_name']} - {$staff['position']}</p>";
        } else {
            echo "<p>❌ ไม่สามารถเพิ่มข้อมูล: {$staff['title']} {$staff['first_name']} {$staff['last_name']} - {$conn->error}</p>";
        }
    } else {
        echo "<p>⚠️ มีข้อมูลแล้ว: {$staff['title']} {$staff['first_name']} {$staff['last_name']}</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>ไปที่หน้าจัดการบุคลากร</a></p>";
echo "<p><a href='../../staff/primary.php'>ดูหน้าบุคลากรประถมศึกษา</a></p>";
echo "<p><a href='../../staff/index.php'>ดูหน้าบุคลากรทั้งหมด</a></p>";

$conn->close();
?>
