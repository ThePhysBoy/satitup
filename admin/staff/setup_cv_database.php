<?php
/**
 * Setup CV Database for Staff System
 * สคริปต์สำหรับสร้าง/อัปเดตตารางเพื่อรองรับข้อมูล CV ของบุคลากร
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

// 1. สร้างตาราง departments ถ้ายังไม่มี
$sql_departments = "CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('academic', 'support', 'primary') NOT NULL DEFAULT 'academic',
    description TEXT,
    order_number INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_departments)) {
    $messages[] = "✓ ตาราง departments พร้อมใช้งาน";
} else {
    $errors[] = "Error creating departments: " . $conn->error;
}

// 2. สร้างตาราง staff ถ้ายังไม่มี
$sql_staff = "CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) UNIQUE,
    title VARCHAR(50) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    position VARCHAR(255),
    academic_rank VARCHAR(100),
    department_id INT,
    image_path VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    office VARCHAR(50),
    bio TEXT,
    education TEXT,
    experience TEXT,
    expertise TEXT,
    research TEXT,
    publications TEXT,
    awards TEXT,
    additional_info TEXT,
    is_head TINYINT(1) DEFAULT 0,
    order_number INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_department (department_id),
    INDEX idx_status (status),
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_staff)) {
    $messages[] = "✓ ตาราง staff พร้อมใช้งาน";
} else {
    $errors[] = "Error creating staff: " . $conn->error;
}

// 3. เพิ่ม columns ใหม่ถ้ายังไม่มี (สำหรับตารางที่มีอยู่แล้ว)
$new_columns = [
    'employee_id' => "ALTER TABLE staff ADD COLUMN employee_id VARCHAR(20) UNIQUE AFTER id",
    'academic_rank' => "ALTER TABLE staff ADD COLUMN academic_rank VARCHAR(100) AFTER position",
    'office' => "ALTER TABLE staff ADD COLUMN office VARCHAR(50) AFTER phone",
    'bio' => "ALTER TABLE staff ADD COLUMN bio TEXT AFTER office",
    'experience' => "ALTER TABLE staff ADD COLUMN experience TEXT AFTER education",
    'expertise' => "ALTER TABLE staff ADD COLUMN expertise TEXT AFTER experience",
    'research' => "ALTER TABLE staff ADD COLUMN research TEXT AFTER expertise",
    'publications' => "ALTER TABLE staff ADD COLUMN publications TEXT AFTER research",
    'awards' => "ALTER TABLE staff ADD COLUMN awards TEXT AFTER publications",
    'additional_info' => "ALTER TABLE staff ADD COLUMN additional_info TEXT AFTER awards"
];

foreach ($new_columns as $column => $sql) {
    $check = $conn->query("SHOW COLUMNS FROM staff LIKE '$column'");
    if ($check && $check->num_rows == 0) {
        if ($conn->query($sql)) {
            $messages[] = "✓ เพิ่ม column $column สำเร็จ";
        } else {
            // ไม่แสดง error ถ้า column มีอยู่แล้ว
            if (!strpos($conn->error, 'Duplicate column')) {
                $errors[] = "Error adding column $column: " . $conn->error;
            }
        }
    }
}

// 4. สร้างตาราง staff_positions ถ้ายังไม่มี
$sql_positions = "CREATE TABLE IF NOT EXISTS staff_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    position_name VARCHAR(255) NOT NULL,
    department VARCHAR(255),
    start_date DATE,
    end_date DATE,
    is_primary TINYINT(1) DEFAULT 0,
    is_current TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    INDEX idx_staff (staff_id),
    INDEX idx_current (is_current)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_positions)) {
    $messages[] = "✓ ตาราง staff_positions พร้อมใช้งาน";
} else {
    $errors[] = "Error creating staff_positions: " . $conn->error;
}

// 5. เพิ่มข้อมูล departments ถ้ายังไม่มี
$check_dept = $conn->query("SELECT COUNT(*) as count FROM departments");
$row = $check_dept->fetch_assoc();

if ($row['count'] == 0) {
    $departments = [
        // Academic departments
        ['กลุ่มสาระการเรียนรู้คณิตศาสตร์', 'academic', 1],
        ['กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี', 'academic', 2],
        ['กลุ่มสาระการเรียนรู้ภาษาไทย', 'academic', 3],
        ['กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ', 'academic', 4],
        ['กลุ่มสาระการเรียนรู้สังคมศึกษา ศาสนา และวัฒนธรรม', 'academic', 5],
        ['กลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา', 'academic', 6],
        ['กลุ่มสาระการเรียนรู้ศิลปะ', 'academic', 7],
        ['กลุ่มสาระการเรียนรู้การงานอาชีพ', 'academic', 8],
        
        // Primary departments
        ['ประถมศึกษาปีที่ 1', 'primary', 1],
        ['ประถมศึกษาปีที่ 2', 'primary', 2],
        ['ประถมศึกษาปีที่ 3', 'primary', 3],
        ['ประถมศึกษาปีที่ 4', 'primary', 4],
        ['ประถมศึกษาปีที่ 5', 'primary', 5],
        ['ประถมศึกษาปีที่ 6', 'primary', 6],
        
        // Support departments
        ['ฝ่ายบริหาร', 'support', 1],
        ['ฝ่ายวิชาการ', 'support', 2],
        ['ฝ่ายกิจการนักเรียน', 'support', 3],
        ['ฝ่ายบริหารทั่วไป', 'support', 4],
        ['ฝ่ายแผนงานและประกันคุณภาพ', 'support', 5],
    ];
    
    foreach ($departments as $dept) {
        $name = $conn->real_escape_string($dept[0]);
        $type = $dept[1];
        $order = $dept[2];
        
        $sql = "INSERT INTO departments (name, type, order_number) VALUES ('$name', '$type', $order)";
        if ($conn->query($sql)) {
            $messages[] = "✓ เพิ่มแผนก: $name";
        }
    }
}

// 6. เพิ่มข้อมูลบุคลากรตัวอย่าง (ถ้ายังไม่มี)
$check_staff = $conn->query("SELECT COUNT(*) as count FROM staff");
$row = $check_staff->fetch_assoc();

if ($row['count'] == 0) {
    // เพิ่มบุคลากรตัวอย่าง
    $sample_staff = [
        [
            'title' => 'ดร.',
            'first_name' => 'สมชาย',
            'last_name' => 'ใจดี',
            'position' => 'ผู้อำนวยการโรงเรียน',
            'department_id' => 15, // ฝ่ายบริหาร
            'email' => 'somchai@satit.up.ac.th',
            'phone' => '054-466666 ต่อ 1234',
            'office' => 'อาคาร 1 ชั้น 2',
            'bio' => 'ผู้อำนวยการโรงเรียนสาธิตมหาวิทยาลัยพะเยา มีประสบการณ์ในการบริหารการศึกษามากกว่า 20 ปี',
            'education' => "ปริญญาเอก - การบริหารการศึกษา มหาวิทยาลัยนเรศวร\nปริญญาโท - การบริหารการศึกษา มหาวิทยาลัยพะเยา\nปริญญาตรี - คณิตศาสตร์ มหาวิทยาลัยเชียงใหม่",
            'experience' => "2565-ปัจจุบัน - ผู้อำนวยการโรงเรียนสาธิตมหาวิทยาลัยพะเยา\n2560-2564 - รองผู้อำนวยการฝ่ายวิชาการ",
            'expertise' => 'การบริหารการศึกษา, การพัฒนาหลักสูตร, การประกันคุณภาพการศึกษา',
            'is_head' => 1,
            'order_number' => 1
        ],
        [
            'title' => 'ผศ.ดร.',
            'first_name' => 'มานี',
            'last_name' => 'รักเรียน',
            'position' => 'หัวหน้ากลุ่มสาระการเรียนรู้วิทยาศาสตร์',
            'department_id' => 2, // วิทยาศาสตร์
            'email' => 'manee@satit.up.ac.th',
            'phone' => '054-466666 ต่อ 2345',
            'bio' => 'อาจารย์ผู้เชี่ยวชาญด้านการสอนวิทยาศาสตร์',
            'education' => "ปริญญาเอก - วิทยาศาสตร์ศึกษา จุฬาลงกรณ์มหาวิทยาลัย\nปริญญาโท - ฟิสิกส์ มหาวิทยาลัยเชียงใหม่",
            'expertise' => 'ฟิสิกส์, วิทยาศาสตร์ทั่วไป, STEM Education',
            'research' => "การพัฒนาชุดการเรียนรู้วิทยาศาสตร์แบบ STEM\nการใช้เทคโนโลยี AR ในการสอนวิทยาศาสตร์",
            'is_head' => 1,
            'order_number' => 1
        ]
    ];
    
    foreach ($sample_staff as $staff) {
        $columns = array_keys($staff);
        $values = array_values($staff);
        $placeholders = array_fill(0, count($values), '?');
        
        $sql = "INSERT INTO staff (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $types = str_repeat('s', count($values));
            $stmt->bind_param($types, ...$values);
            
            if ($stmt->execute()) {
                $messages[] = "✓ เพิ่มบุคลากรตัวอย่าง: " . $staff['first_name'];
            } else {
                $errors[] = "Error adding sample staff: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup CV Database - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container-custom {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 900px;
            width: 100%;
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
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <h1><i class="fas fa-database"></i> Setup CV Database for Staff</h1>
        
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
        
        <div class="info-box">
            <h5>📊 โครงสร้างฐานข้อมูลที่สร้าง:</h5>
            <ul>
                <li><strong>departments</strong> - เก็บข้อมูลแผนก/กลุ่มสาระ</li>
                <li><strong>staff</strong> - เก็บข้อมูลบุคลากรและ CV</li>
                <li><strong>staff_positions</strong> - เก็บตำแหน่งเพิ่มเติมของบุคลากร</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h5>📋 ฟิลด์สำหรับ CV ที่เพิ่มใหม่:</h5>
            <ul>
                <li><strong>bio</strong> - ประวัติโดยย่อ</li>
                <li><strong>education</strong> - ประวัติการศึกษา</li>
                <li><strong>experience</strong> - ประสบการณ์การทำงาน</li>
                <li><strong>expertise</strong> - ความเชี่ยวชาญ</li>
                <li><strong>research</strong> - ผลงานวิจัย</li>
                <li><strong>publications</strong> - ผลงานตีพิมพ์</li>
                <li><strong>awards</strong> - รางวัลและเกียรติยศ</li>
                <li><strong>additional_info</strong> - ข้อมูลเพิ่มเติม</li>
            </ul>
        </div>
        
        <div class="text-center mt-4">
            <a href="../staff/" class="btn btn-custom">
                <i class="fas fa-users"></i> ดูหน้าจัดการบุคลากร
            </a>
            <a href="../../staff/" class="btn btn-custom" target="_blank">
                <i class="fas fa-eye"></i> ดูหน้าแสดงบุคลากร
            </a>
            <a href="../login.php" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> หน้า Login
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-muted text-center">
            <small>
                ระบบ CV สำหรับบุคลากรพร้อมใช้งาน<br>
                สามารถเพิ่ม/แก้ไขข้อมูล CV ผ่านหน้า Admin
            </small>
        </div>
    </div>
</body>
</html>
