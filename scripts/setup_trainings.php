<?php
$mysqli = new mysqli('localhost', 'root', '', 'satitup');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `training_announcements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `training_topic` VARCHAR(255) DEFAULT NULL,
    `reference_number` VARCHAR(100) DEFAULT NULL,
    `host_department` VARCHAR(150) DEFAULT NULL,
    `training_type` VARCHAR(100) DEFAULT NULL,
    `target_audience` VARCHAR(255) DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `training_dates` TEXT DEFAULT NULL,
    `registration_deadline` DATE DEFAULT NULL,
    `price` VARCHAR(100) DEFAULT NULL,
    `agenda` TEXT DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `document_pdf` VARCHAR(255) NOT NULL,
    `additional_files` TEXT DEFAULT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `contact_phone` VARCHAR(50) DEFAULT NULL,
    `contact_email` VARCHAR(150) DEFAULT NULL,
    `status` ENUM('draft','open','closed','cancelled') DEFAULT 'draft',
    `notes` TEXT DEFAULT NULL,
    `published_date` DATE NOT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_published` (`published_date`),
    INDEX `idx_deadline` (`registration_deadline`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$mysqli->query($sql)) {
    die('Failed to create training_announcements table: ' . $mysqli->error);
}

$result = $mysqli->query("SELECT COUNT(*) as cnt FROM training_announcements");
$row = $result ? $result->fetch_assoc() : ['cnt' => 0];

if ((int)$row['cnt'] === 0) {
    $insert = "INSERT INTO training_announcements (
        title, training_topic, reference_number, host_department, training_type, target_audience, location,
        training_dates, registration_deadline, price, agenda, description, document_pdf, contact_person,
        contact_phone, contact_email, status, published_date
    ) VALUES
    (
        'ประกาศเชิญเข้าร่วมอบรมการใช้เทคโนโลยีดิจิทัลเพื่อการเรียนรู้',
        'อบรมเชิงปฏิบัติการ: Digital Classroom Tools 2025',
        'สทภ-อบรม 01/2568',
        'กลุ่มงานเทคโนโลยีสารสนเทศ',
        'อบรมภายใน',
        'ครูและบุคลากรสายวิชาการ',
        'ห้องประชุมอาคารเฉลิมพระเกียรติ ชั้น 3',
        'วันที่ 5 - 6 พฤศจิกายน 2568 เวลา 09.00-16.00 น.',
        DATE_ADD(CURDATE(), INTERVAL 20 DAY),
        'ไม่มีค่าใช้จ่าย',
        'วันที่ 1: เวิร์กช็อปการใช้ระบบจัดการเรียนรู้ออนไลน์\nวันที่ 2: การสร้างสื่ออินเตอร์แอคทีฟ',
        'เสริมสร้างทักษะด้านเทคโนโลยีการศึกษาให้กับครู เพื่อเตรียมพร้อมการเรียนการสอนยุคดิจิทัล',
        'documents/trainings/sample_digital_classroom.pdf',
        'นายศุภกรณ์ นิลธร',
        '054-466666 ต่อ 1402',
        'training@satitup.ac.th',
        'open',
        DATE_SUB(CURDATE(), INTERVAL 3 DAY)
    ),
    (
        'ประกาศผลผู้ผ่านการอบรมโครงการพัฒนาครูที่ปรึกษา',
        'โครงการพัฒนาครูที่ปรึกษาและกิจการนักเรียน',
        'สทภ-อบรม 02/2568',
        'กลุ่มกิจการนักเรียน',
        'สรุปผลการอบรม',
        'ครูที่ปรึกษา ระดับมัธยมศึกษาตอนต้น',
        'ศูนย์ประชุมมหาวิทยาลัยพะเยา',
        'อบรมเมื่อวันที่ 10-12 ตุลาคม 2568',
        DATE_SUB(CURDATE(), INTERVAL 10 DAY),
        'ไม่มีค่าใช้จ่าย',
        'เนื้อหาครอบคลุมการให้คำปรึกษาและกิจกรรมเสริมสร้างทักษะชีวิต',
        'ประกาศรายชื่อผู้ผ่านการอบรมและแนวทางการติดตามผลหลังอบรม',
        'documents/trainings/sample_guidance_training.pdf',
        'นางสาวปภาวดี อินทอง',
        '054-466666 ต่อ 1405',
        'studentaffairs@satitup.ac.th',
        'closed',
        DATE_SUB(CURDATE(), INTERVAL 25 DAY)
    );";

    if (!$mysqli->query($insert)) {
        die('Failed to insert sample training data: ' . $mysqli->error);
    }
}

$mysqli->close();
echo "Training announcements table ready.";
