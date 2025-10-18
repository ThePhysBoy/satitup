<?php
/**
 * Fix Staff Table and Populate with Sample Data
 * แก้ไขตารางและเพิ่มข้อมูลตัวอย่างสำหรับนำเสนอผู้บริหาร
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

$messages = [];
$errors = [];

// 1. เพิ่ม column position ถ้ายังไม่มี
$check_position = $conn->query("SHOW COLUMNS FROM staff LIKE 'position'");
if ($check_position->num_rows == 0) {
    if ($conn->query("ALTER TABLE staff ADD COLUMN position VARCHAR(255) AFTER last_name")) {
        $messages[] = "✓ เพิ่ม column position สำเร็จ";
    } else {
        $errors[] = "Error adding position column: " . $conn->error;
    }
} else {
    $messages[] = "✓ Column position มีอยู่แล้ว";
}

// 2. Clear existing staff data for clean demo
$conn->query("DELETE FROM staff");
$conn->query("ALTER TABLE staff AUTO_INCREMENT = 1");
$messages[] = "✓ ล้างข้อมูลเดิม";

// 3. ตรวจสอบและสร้างตาราง departments ถ้ายังไม่มี
$check_dept_table = $conn->query("SHOW TABLES LIKE 'departments'");
if ($check_dept_table->num_rows == 0) {
    $sql_dept = "CREATE TABLE departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type ENUM('academic', 'support', 'primary') DEFAULT 'academic',
        description TEXT,
        order_number INT DEFAULT 0
    )";
    $conn->query($sql_dept);
    $messages[] = "✓ สร้างตาราง departments";
}

// 4. Clear and add departments
$conn->query("DELETE FROM departments");
$conn->query("ALTER TABLE departments AUTO_INCREMENT = 1");

$departments = [
    // ฝ่ายบริหาร
    ['ฝ่ายบริหาร', 'support', 1],
    
    // กลุ่มสาระวิชาการ
    ['กลุ่มสาระการเรียนรู้คณิตศาสตร์', 'academic', 2],
    ['กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี', 'academic', 3],
    ['กลุ่มสาระการเรียนรู้ภาษาไทย', 'academic', 4],
    ['กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ', 'academic', 5],
    ['กลุ่มสาระการเรียนรู้สังคมศึกษา ศาสนา และวัฒนธรรม', 'academic', 6],
    ['กลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา', 'academic', 7],
    ['กลุ่มสาระการเรียนรู้ศิลปะ', 'academic', 8],
    ['กลุ่มสาระการเรียนรู้การงานอาชีพ', 'academic', 9],
    
    // ประถมศึกษา
    ['ระดับประถมศึกษา', 'primary', 10],
    
    // สายสนับสนุน
    ['ฝ่ายวิชาการ', 'support', 11],
    ['ฝ่ายกิจการนักเรียน', 'support', 12],
    ['ฝ่ายธุรการ', 'support', 13],
];

foreach ($departments as $dept) {
    $name = $conn->real_escape_string($dept[0]);
    $type = $dept[1];
    $order = $dept[2];
    
    $sql = "INSERT INTO departments (name, type, order_number) VALUES ('$name', '$type', $order)";
    $conn->query($sql);
}
$messages[] = "✓ เพิ่มแผนก " . count($departments) . " แผนก";

// 5. เพิ่มข้อมูลบุคลากรตัวอย่างที่ดูสมจริง
$staff_data = [
    // ฝ่ายบริหาร
    [
        'title' => 'ผศ.ดร.',
        'first_name' => 'สมชาย',
        'last_name' => 'ศรีสุข',
        'position' => 'ผู้อำนวยการโรงเรียน',
        'department_id' => 1,
        'email' => 'somchai.s@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1001',
        'office' => 'อาคาร 1 ชั้น 2 ห้อง 201',
        'academic_rank' => 'ผู้ช่วยศาสตราจารย์',
        'education' => "ปริญญาเอก - การบริหารการศึกษา มหาวิทยาลัยนเรศวร (2555)\nปริญญาโท - การบริหารการศึกษา มหาวิทยาลัยพะเยา (2548)\nปริญญาตรี - คณิตศาสตร์ มหาวิทยาลัยเชียงใหม่ (2543)",
        'experience' => "2563-ปัจจุบัน - ผู้อำนวยการโรงเรียนสาธิตมหาวิทยาลัยพะเยา\n2558-2562 - รองผู้อำนวยการฝ่ายวิชาการ\n2553-2557 - หัวหน้ากลุ่มสาระการเรียนรู้คณิตศาสตร์",
        'expertise' => 'การบริหารการศึกษา, การพัฒนาหลักสูตร, การประกันคุณภาพการศึกษา',
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'ดร.',
        'first_name' => 'วิไลวรรณ',
        'last_name' => 'สุขใจ',
        'position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
        'department_id' => 1,
        'email' => 'wilaiwan.s@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1002',
        'office' => 'อาคาร 1 ชั้น 2 ห้อง 202',
        'education' => "ปริญญาเอก - หลักสูตรและการสอน จุฬาลงกรณ์มหาวิทยาลัย (2557)\nปริญญาโท - การสอนภาษาอังกฤษ มหาวิทยาลัยเชียงใหม่ (2550)",
        'expertise' => 'การพัฒนาหลักสูตร, การสอนภาษาอังกฤษ, การวิจัยทางการศึกษา',
        'order_number' => 2
    ],
    
    // คณิตศาสตร์
    [
        'title' => 'ผศ.',
        'first_name' => 'ประภาส',
        'last_name' => 'คณิตกุล',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้คณิตศาสตร์',
        'department_id' => 2,
        'email' => 'prapas.k@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 2001',
        'academic_rank' => 'ผู้ช่วยศาสตราจารย์',
        'education' => "ปริญญาโท - คณิตศาสตร์ มหาวิทยาลัยเชียงใหม่ (2548)\nปริญญาตรี - คณิตศาสตร์ มหาวิทยาลัยนเรศวร (2543)",
        'expertise' => 'พีชคณิต, แคลคูลัส, สถิติ',
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'อ.',
        'first_name' => 'สายใจ',
        'last_name' => 'รักเลข',
        'position' => 'ครูคณิตศาสตร์',
        'department_id' => 2,
        'email' => 'saijai.r@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 2002',
        'education' => "ปริญญาตรี - คณิตศาสตร์ มหาวิทยาลัยพะเยา (2560)",
        'expertise' => 'คณิตศาสตร์พื้นฐาน, เรขาคณิต',
        'order_number' => 2
    ],
    
    // วิทยาศาสตร์
    [
        'title' => 'ดร.',
        'first_name' => 'พิชัย',
        'last_name' => 'วิทยากร',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้วิทยาศาสตร์',
        'department_id' => 3,
        'email' => 'pichai.w@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 3001',
        'education' => "ปริญญาเอก - ฟิสิกส์ มหาวิทยาลัยเชียงใหม่ (2558)\nปริญญาโท - ฟิสิกส์ มหาวิทยาลัยขอนแก่น (2552)",
        'expertise' => 'ฟิสิกส์, ดาราศาสตร์, STEM Education',
        'research' => "การพัฒนาชุดการเรียนรู้ STEM สำหรับนักเรียนมัธยม\nการใช้ AR ในการสอนวิทยาศาสตร์",
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'อ.',
        'first_name' => 'นภาพร',
        'last_name' => 'ชีวะวิทย์',
        'position' => 'ครูชีววิทยา',
        'department_id' => 3,
        'email' => 'napaporn.c@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 3002',
        'education' => "ปริญญาโท - ชีววิทยา มหาวิทยาลัยนเรศวร (2559)",
        'expertise' => 'ชีววิทยา, พฤกษศาสตร์, สิ่งแวดล้อม',
        'order_number' => 2
    ],
    [
        'title' => 'อ.',
        'first_name' => 'สมศักดิ์',
        'last_name' => 'เคมีศาสตร์',
        'position' => 'ครูเคมี',
        'department_id' => 3,
        'email' => 'somsak.k@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 3003',
        'education' => "ปริญญาตรี - เคมี มหาวิทยาลัยพะเยา (2561)",
        'expertise' => 'เคมีทั่วไป, เคมีอินทรีย์',
        'order_number' => 3
    ],
    
    // ภาษาไทย
    [
        'title' => 'ผศ.ดร.',
        'first_name' => 'มาลินี',
        'last_name' => 'ภาษาดี',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้ภาษาไทย',
        'department_id' => 4,
        'email' => 'malinee.p@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 4001',
        'academic_rank' => 'ผู้ช่วยศาสตราจารย์',
        'education' => "ปริญญาเอก - ภาษาไทย จุฬาลงกรณ์มหาวิทยาลัย (2556)\nปริญญาโท - ภาษาไทย มหาวิทยาลัยเชียงใหม่ (2549)",
        'expertise' => 'วรรณคดีไทย, ภาษาศาสตร์, การเขียนเชิงสร้างสรรค์',
        'awards' => "ครูภาษาไทยดีเด่น ประจำปี 2565\nรางวัลผลงานวิจัยดีเด่น สาขาภาษาไทย",
        'is_head' => 1,
        'order_number' => 1
    ],
    
    // ภาษาต่างประเทศ
    [
        'title' => 'Dr.',
        'first_name' => 'Jennifer',
        'last_name' => 'Smith',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้ภาษาต่างประเทศ',
        'department_id' => 5,
        'email' => 'jennifer.s@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 5001',
        'education' => "Ph.D. - Applied Linguistics, University of Edinburgh (2015)\nM.A. - TESOL, University of London (2010)",
        'expertise' => 'English Teaching, Curriculum Development, Language Assessment',
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'อ.',
        'first_name' => 'วรรณา',
        'last_name' => 'อังกฤษ',
        'position' => 'ครูภาษาอังกฤษ',
        'department_id' => 5,
        'email' => 'wanna.a@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 5002',
        'education' => "ปริญญาโท - การสอนภาษาอังกฤษ มหาวิทยาลัยพะเยา (2562)",
        'expertise' => 'English Grammar, Conversation, TOEIC Preparation',
        'order_number' => 2
    ],
    
    // สังคมศึกษา
    [
        'title' => 'อ.',
        'first_name' => 'ประวิทย์',
        'last_name' => 'ประวัติศาสตร์',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้สังคมศึกษา',
        'department_id' => 6,
        'email' => 'prawit.p@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 6001',
        'education' => "ปริญญาโท - ประวัติศาสตร์ มหาวิทยาลัยเชียงใหม่ (2557)",
        'expertise' => 'ประวัติศาสตร์ไทย, ประวัติศาสตร์ล้านนา',
        'is_head' => 1,
        'order_number' => 1
    ],
    
    // พลศึกษา
    [
        'title' => 'อ.',
        'first_name' => 'กิตติพงษ์',
        'last_name' => 'แข็งแรง',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา',
        'department_id' => 7,
        'email' => 'kittipong.k@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 7001',
        'education' => "ปริญญาโท - พลศึกษา มหาวิทยาลัยพะเยา (2560)",
        'expertise' => 'กีฬาบาสเกตบอล, ฟุตบอล, วอลเลย์บอล',
        'awards' => "โค้ชทีมบาสเกตบอลแชมป์ระดับภาค 3 ปีซ้อน",
        'is_head' => 1,
        'order_number' => 1
    ],
    
    // ศิลปะ
    [
        'title' => 'อ.',
        'first_name' => 'ศิริพร',
        'last_name' => 'ศิลปกุล',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้ศิลปะ',
        'department_id' => 8,
        'email' => 'siriporn.s@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 8001',
        'education' => "ปริญญาโท - ศิลปศึกษา มหาวิทยาลัยเชียงใหม่ (2558)",
        'expertise' => 'ทัศนศิลป์, ดนตรีไทย, นาฏศิลป์',
        'is_head' => 1,
        'order_number' => 1
    ],
    
    // การงานอาชีพ
    [
        'title' => 'อ.',
        'first_name' => 'ทักษิณ',
        'last_name' => 'อาชีพดี',
        'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้การงานอาชีพ',
        'department_id' => 9,
        'email' => 'thaksin.a@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 9001',
        'education' => "ปริญญาโท - เทคโนโลยีการศึกษา มหาวิทยาลัยพะเยา (2559)",
        'expertise' => 'คอมพิวเตอร์, หุ่นยนต์, IoT',
        'is_head' => 1,
        'order_number' => 1
    ],
    
    // ประถมศึกษา
    [
        'title' => 'อ.',
        'first_name' => 'สุภาพร',
        'last_name' => 'รักเด็ก',
        'position' => 'หัวหน้าระดับประถมศึกษา',
        'department_id' => 10,
        'email' => 'supaporn.r@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1101',
        'education' => "ปริญญาโท - ประถมศึกษา มหาวิทยาลัยนเรศวร (2556)",
        'expertise' => 'การสอนประถมศึกษา, จิตวิทยาเด็ก',
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'อ.',
        'first_name' => 'อารีย์',
        'last_name' => 'ใจอ่อน',
        'position' => 'ครูประจำชั้น ป.1',
        'department_id' => 10,
        'email' => 'aree.j@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1102',
        'education' => "ปริญญาตรี - ประถมศึกษา มหาวิทยาลัยพะเยา (2563)",
        'expertise' => 'การสอนอ่านเขียน, กิจกรรมพัฒนาผู้เรียน',
        'order_number' => 2
    ],
    [
        'title' => 'อ.',
        'first_name' => 'นิตยา',
        'last_name' => 'รักงาน',
        'position' => 'ครูประจำชั้น ป.2',
        'department_id' => 10,
        'email' => 'nitaya.r@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1103',
        'education' => "ปริญญาตรี - ประถมศึกษา มหาวิทยาลัยราชภัฏเชียงราย (2562)",
        'order_number' => 3
    ],
    
    // ฝ่ายสนับสนุน
    [
        'title' => 'นาง',
        'first_name' => 'พัชรี',
        'last_name' => 'งานดี',
        'position' => 'หัวหน้าฝ่ายธุรการ',
        'department_id' => 13,
        'email' => 'patcharee.n@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1301',
        'education' => "ปริญญาตรี - บริหารธุรกิจ มหาวิทยาลัยพะเยา (2555)",
        'expertise' => 'งานสารบรรณ, การจัดการสำนักงาน',
        'is_head' => 1,
        'order_number' => 1
    ],
    [
        'title' => 'นางสาว',
        'first_name' => 'จิราพร',
        'last_name' => 'บัญชีเก่ง',
        'position' => 'เจ้าหน้าที่การเงิน',
        'department_id' => 13,
        'email' => 'jiraporn.b@satit.up.ac.th',
        'phone' => '054-466666 ต่อ 1302',
        'education' => "ปริญญาตรี - การบัญชี มหาวิทยาลัยพะเยา (2560)",
        'order_number' => 2
    ]
];

// Insert staff data
$staff_count = 0;
foreach ($staff_data as $staff) {
    $columns = [];
    $values = [];
    $types = "";
    
    foreach ($staff as $key => $value) {
        if ($value !== null) {
            $columns[] = $key;
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
    }
    
    $sql = "INSERT INTO staff (" . implode(", ", $columns) . ") VALUES (" . str_repeat("?,", count($values)-1) . "?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            $staff_count++;
        }
        $stmt->close();
    }
}
$messages[] = "✓ เพิ่มบุคลากร $staff_count คน";

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix & Populate Staff Data - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 30px;
            text-align: center;
        }
        .alert {
            border-radius: 10px;
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
        .stats-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
            text-align: center;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .demo-info {
            background: #e7f5ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-users"></i> ข้อมูลบุคลากรสำหรับนำเสนอผู้บริหาร</h1>
        
        <?php if (!empty($messages)): ?>
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ:</h5>
                <ul class="mb-0">
                    <?php foreach ($messages as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-building fa-2x mb-2" style="color: #764ba2;"></i>
                    <div class="stats-number">13</div>
                    <div>แผนก/กลุ่มสาระ</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-user-tie fa-2x mb-2" style="color: #764ba2;"></i>
                    <div class="stats-number">20</div>
                    <div>บุคลากรทั้งหมด</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color: #764ba2;"></i>
                    <div class="stats-number">10</div>
                    <div>สายวิชาการ</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-graduation-cap fa-2x mb-2" style="color: #764ba2;"></i>
                    <div class="stats-number">5</div>
                    <div>ปริญญาเอก</div>
                </div>
            </div>
        </div>
        
        <div class="demo-info">
            <h5><i class="fas fa-info-circle"></i> ข้อมูลที่เตรียมไว้สำหรับนำเสนอ:</h5>
            <ul class="mb-0">
                <li><strong>ผู้บริหาร:</strong> ผู้อำนวยการ, รองผู้อำนวยการ</li>
                <li><strong>หัวหน้ากลุ่มสาระ:</strong> ทุกกลุ่มสาระการเรียนรู้</li>
                <li><strong>ครูผู้สอน:</strong> ตัวอย่างในแต่ละกลุ่มสาระ</li>
                <li><strong>บุคลากรสนับสนุน:</strong> ธุรการ, การเงิน</li>
                <li><strong>ข้อมูล CV:</strong> การศึกษา, ประสบการณ์, ความเชี่ยวชาญ</li>
            </ul>
        </div>
        
        <div class="alert alert-info">
            <h5><i class="fas fa-star"></i> จุดเด่นที่พร้อมนำเสนอ:</h5>
            <ol class="mb-0">
                <li>แสดงโครงสร้างองค์กรที่ชัดเจน</li>
                <li>ข้อมูล CV ครบถ้วนของบุคลากรทุกคน</li>
                <li>แยกประเภทบุคลากรเป็นหมวดหมู่</li>
                <li>แสดงคุณวุฒิและความเชี่ยวชาญ</li>
                <li>ระบบค้นหาและกรองข้อมูลที่ใช้งานง่าย</li>
            </ol>
        </div>
        
        <div class="text-center mt-4">
            <h5 class="mb-3">เข้าชมระบบได้ที่:</h5>
            <a href="../../staff/" class="btn btn-custom" target="_blank">
                <i class="fas fa-users"></i> ดูหน้าแสดงบุคลากร
            </a>
            <a href="../" class="btn btn-custom">
                <i class="fas fa-cog"></i> จัดการข้อมูลบุคลากร
            </a>
            <a href="../../staff/staff_detail.php?id=1" class="btn btn-custom" target="_blank">
                <i class="fas fa-user"></i> ดูตัวอย่าง CV
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-muted text-center">
            <small>
                ระบบพร้อมนำเสนอผู้บริหาร<br>
                ข้อมูลเป็นตัวอย่างสำหรับการสาธิต สามารถแก้ไขได้ทุกรายการ
            </small>
        </div>
    </div>
</body>
</html>
