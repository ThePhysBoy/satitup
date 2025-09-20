<?php
/**
 * Database Configuration File
 * This file contains database connection settings
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Default XAMPP username
define('DB_PASS', '');         // Default XAMPP password (empty)
define('DB_NAME', 'satitup');  // Database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    user_type VARCHAR(50) DEFAULT 'general',
    full_name VARCHAR(100),
    position VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating users table: " . $conn->error);
}

// Create slideshow table if not exists
$sql = "CREATE TABLE IF NOT EXISTS slideshow (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    active TINYINT(1) DEFAULT 1,
    display_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating slideshow table: " . $conn->error);
}

// Create university_rankings table if not exists
$sql = "CREATE TABLE IF NOT EXISTS university_rankings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    ranking_type VARCHAR(50) DEFAULT 'general',
    active TINYINT(1) DEFAULT 1,
    display_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating university_rankings table: " . $conn->error);
}

// Create departments table if not exists
$sql = "CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('academic', 'support', 'primary') NOT NULL,
    order_number INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) !== TRUE) {
    die("Error creating departments table: " . $conn->error);
}

// Create staff table if not exists
$sql = "CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    department_id INT,
    image_path VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    education TEXT,
    bio TEXT,
    is_head TINYINT(1) DEFAULT 0,
    order_number INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) !== TRUE) {
    die("Error creating staff table: " . $conn->error);
}

// Create staff_positions table if not exists
$sql = "CREATE TABLE IF NOT EXISTS staff_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    position_name VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) !== TRUE) {
    die("Error creating staff_positions table: " . $conn->error);
}

// Insert initial departments if they don't exist
$departments = [
    // Academic departments (มัธยม)
    [1, 'วิทยาศาสตร์และเทคโนโลยี', 'กลุ่มสาระการเรียนรู้วิทยาศาสตร์และเทคโนโลยี', 'academic', 1],
    [2, 'สังคมศึกษา', 'กลุ่มสาระการเรียนรู้สังคมศึกษา', 'academic', 2],
    [3, 'ภาษาต่างประเทศ', 'กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ', 'academic', 3],
    [4, 'คณิตศาสตร์', 'กลุ่มสาระการเรียนรู้คณิตศาสตร์', 'academic', 4],
    [5, 'สุขศึกษาและพลศึกษา', 'กลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา', 'academic', 5],
    [6, 'ภาษาไทย', 'กลุ่มสาระการเรียนรู้ภาษาไทย', 'academic', 6],
    [7, 'ศิลปะ', 'กลุ่มสาระการเรียนรู้ศิลปะ', 'academic', 7],
    [8, 'การงานอาชีพ', 'กลุ่มสาระการเรียนรู้การงานอาชีพ', 'academic', 8],
    
    // Support departments (สายสนับสนุน)
    [9, 'งานบริหาร', 'บุคลากรสายสนับสนุนงานบริหาร', 'support', 1],
    [10, 'งานวิชาการ', 'บุคลากรสายสนับสนุนงานวิชาการ', 'support', 2],
    [11, 'งานกิจการนักเรียน', 'บุคลากรสายสนับสนุนงานกิจการนักเรียน', 'support', 3],
    [12, 'งานนโยบายและแผน', 'บุคลากรสายสนับสนุนงานนโยบายและแผน', 'support', 4],
    [13, 'ห้องปฏิบัติการทางวิทยาศาสตร์', 'บุคลากรสายสนับสนุนห้องปฏิบัติการทางวิทยาศาสตร์', 'support', 5],
    [14, 'ห้องสมุด', 'บุคลากรสายสนับสนุนห้องสมุด', 'support', 6],
    
    // Primary education departments (ประถมศึกษา)
    [15, 'ประถมศึกษาปีที่ 1', 'ครูประจำชั้นประถมศึกษาปีที่ 1', 'primary', 1],
    [16, 'ประถมศึกษาปีที่ 2', 'ครูประจำชั้นประถมศึกษาปีที่ 2', 'primary', 2],
    [17, 'ประถมศึกษาปีที่ 3', 'ครูประจำชั้นประถมศึกษาปีที่ 3', 'primary', 3],
    [18, 'ประถมศึกษาปีที่ 4', 'ครูประจำชั้นประถมศึกษาปีที่ 4', 'primary', 4],
    [19, 'ประถมศึกษาปีที่ 5', 'ครูประจำชั้นประถมศึกษาปีที่ 5', 'primary', 5],
    [20, 'ประถมศึกษาปีที่ 6', 'ครูประจำชั้นประถมศึกษาปีที่ 6', 'primary', 6],
    [21, 'ครูพิเศษประถมศึกษา', 'ครูผู้สอนวิชาพิเศษระดับประถมศึกษา', 'primary', 7]
];

foreach ($departments as $dept) {
    $stmt = $conn->prepare("INSERT IGNORE INTO departments (id, name, description, type, order_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $dept[0], $dept[1], $dept[2], $dept[3], $dept[4]);
    $stmt->execute();
}

// Check if admin user exists, if not create default admin
$stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin01'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create default admin user as PR Officer
    $admin_username = 'admin01';
    $admin_password = password_hash('1234', PASSWORD_DEFAULT);
    $admin_role = 'admin';
    $user_type = 'pr_officer';
    $full_name = 'นักประชาสัมพันธ์';
    $position = 'เจ้าหน้าที่ประชาสัมพันธ์';
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, user_type, full_name, position) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $admin_username, $admin_password, $admin_role, $user_type, $full_name, $position);
    
    if (!$stmt->execute()) {
        die("Error creating admin user: " . $stmt->error);
    }
}

// Return connection object
return $conn;
?>
