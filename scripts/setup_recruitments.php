<?php
$mysqli = new mysqli('localhost', 'root', '', 'satitup');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `recruitment_announcements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `position_title` VARCHAR(255) DEFAULT NULL,
    `reference_number` VARCHAR(100) DEFAULT NULL,
    `department` VARCHAR(150) DEFAULT NULL,
    `employment_type` VARCHAR(100) DEFAULT NULL,
    `number_of_positions` INT DEFAULT 1,
    `responsibilities` TEXT DEFAULT NULL,
    `qualifications` TEXT DEFAULT NULL,
    `benefits` TEXT DEFAULT NULL,
    `application_process` TEXT DEFAULT NULL,
    `salary_range` VARCHAR(150) DEFAULT NULL,
    `published_date` DATE NOT NULL,
    `application_deadline` DATE NOT NULL,
    `interview_date` DATE DEFAULT NULL,
    `document_pdf` VARCHAR(255) NOT NULL,
    `additional_files` TEXT DEFAULT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `contact_phone` VARCHAR(50) DEFAULT NULL,
    `contact_email` VARCHAR(150) DEFAULT NULL,
    `status` ENUM('draft','open','closed','cancelled') DEFAULT 'draft',
    `notes` TEXT DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_published_date` (`published_date`),
    INDEX `idx_deadline` (`application_deadline`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$mysqli->query($sql)) {
    die('Failed to create recruitment_announcements table: ' . $mysqli->error);
}

$result = $mysqli->query("SELECT COUNT(*) AS cnt FROM recruitment_announcements");
$row = $result ? $result->fetch_assoc() : ['cnt' => 0];

if ((int)$row['cnt'] === 0) {
    $insert = "INSERT INTO recruitment_announcements (
        title, position_title, reference_number, department, employment_type, number_of_positions,
        responsibilities, qualifications, benefits, application_process, salary_range,
        published_date, application_deadline, interview_date, document_pdf, contact_person,
        contact_phone, contact_email, status
    ) VALUES
    (
        'ประกาศรับสมัครครูผู้สอนวิชาวิทยาศาสตร์ระดับมัธยมศึกษาตอนปลาย',
        'ครูวิทยาศาสตร์ (ฟิสิกส์/เคมี)',
        'สทภ-รับ 01/2568',
        'กลุ่มงานวิชาการ',
        'สัญญาจ้าง 1 ปี (ต่อสัญญาได้)',
        2,
        'สอนวิชาฟิสิกส์และเคมีระดับมัธยมศึกษาตอนปลาย จัดทำแผนการสอนและสื่อประกอบ รวมถึงกิจกรรมพัฒนาผู้เรียน',
        'ปริญญาตรีขึ้นไป สาขาฟิสิกส์ เคมี หรือการศึกษาวิทยาศาสตร์ มีใบประกอบวิชาชีพครู หากมีประสบการณ์จะพิจารณาเป็นพิเศษ',
        'ประกันสังคม เครื่องแบบ และอบรมพัฒนาศักยภาพ',
        'ส่งเอกสารสมัครผ่านระบบออนไลน์ภายในวันที่กำหนด พร้อมแนบเอกสารประกอบครบถ้วน',
        'เริ่มต้น 22,000 - 28,000 บาท ตามคุณวุฒิและประสบการณ์',
        DATE_SUB(CURDATE(), INTERVAL 2 DAY),
        DATE_ADD(CURDATE(), INTERVAL 12 DAY),
        DATE_ADD(CURDATE(), INTERVAL 20 DAY),
        'documents/recruitments/sample_teacher.pdf',
        'นางสาวจุฑารัตน์ สายทอง',
        '054-466666 ต่อ 1302',
        'recruitment@satitup.ac.th',
        'open'
    ),
    (
        'ประกาศรายชื่อผู้ผ่านการคัดเลือกตำแหน่งเจ้าหน้าที่กิจการนักศึกษา',
        'เจ้าหน้าที่กิจการนักศึกษา',
        'สทภ-รับ 02/2568',
        'กลุ่มกิจการนักเรียน',
        'พนักงานประจำ',
        1,
        'ดูแลกิจกรรมนักเรียน ติดต่อผู้ปกครอง และจัดทำรายงานกิจการนักเรียนประจำเดือน',
        'ปริญญาตรี สาขาการศึกษา สังคมศาสตร์ หรือสาขาที่เกี่ยวข้อง มีทักษะการสื่อสารและการจัดกิจกรรม',
        'ประกันสังคม, กองทุนสำรองเลี้ยงชีพ, สิทธิลาพักผ่อนประจำปี',
        'รายงานตัวและรับเอกสารเพิ่มเติมภายในวันที่กำหนด',
        'เริ่มต้น 18,000 บาท ตามประสบการณ์',
        DATE_SUB(CURDATE(), INTERVAL 18 DAY),
        DATE_SUB(CURDATE(), INTERVAL 8 DAY),
        DATE_SUB(CURDATE(), INTERVAL 4 DAY),
        'documents/recruitments/sample_officer.pdf',
        'นายสมชาย วัฒนะ',
        '054-466666 ต่อ 1305',
        'hr@satitup.ac.th',
        'closed'
    );";

    if (!$mysqli->query($insert)) {
        die('Failed to insert sample recruitment data: ' . $mysqli->error);
    }
}

$mysqli->close();
echo "Recruitment announcements table ready.";
