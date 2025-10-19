<?php
$mysqli = new mysqli('localhost', 'root', '', 'satitup');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$tableSql = "CREATE TABLE IF NOT EXISTS `procurement_announcements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `reference_number` VARCHAR(100) DEFAULT NULL,
    `procurement_method` VARCHAR(150) DEFAULT NULL,
    `department` VARCHAR(150) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `budget_amount` DECIMAL(15,2) DEFAULT NULL,
    `currency` VARCHAR(10) DEFAULT 'THB',
    `published_date` DATE NOT NULL,
    `closing_date` DATE NOT NULL,
    `document_pdf` VARCHAR(255) NOT NULL,
    `additional_files` TEXT DEFAULT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `contact_phone` VARCHAR(50) DEFAULT NULL,
    `contact_email` VARCHAR(150) DEFAULT NULL,
    `status` ENUM('draft','published','closed','cancelled') DEFAULT 'draft',
    `featured` TINYINT(1) DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_published` (`published_date`),
    INDEX `idx_closing` (`closing_date`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$mysqli->query($tableSql)) {
    die('Failed to create table: ' . $mysqli->error);
}

$sampleCheck = $mysqli->query("SELECT COUNT(*) AS cnt FROM procurement_announcements");
$cnt = 0;
if ($sampleCheck) {
    $row = $sampleCheck->fetch_assoc();
    $cnt = (int)$row['cnt'];
}

if ($cnt === 0) {
    $insertSql = "INSERT INTO procurement_announcements 
        (title, reference_number, procurement_method, department, description, budget_amount, currency, published_date, closing_date, document_pdf, additional_files, contact_person, contact_phone, contact_email, status)
        VALUES
        (
            'ประกาศเชิญชวนเสนอราคาจัดซื้อคอมพิวเตอร์เพื่อการเรียนรู้',
            'สทภ-กพ 01/2568',
            'e-bidding',
            'กลุ่มงานเทคโนโลยีสารสนเทศ',
            'จัดซื้อคอมพิวเตอร์ตั้งโต๊ะพร้อมอุปกรณ์ประกอบ จำนวน 25 ชุด เพื่อสนับสนุนการเรียนการสอนภายในโรงเรียน',
            1250000.00,
            'THB',
            DATE_SUB(CURDATE(), INTERVAL 5 DAY),
            DATE_ADD(CURDATE(), INTERVAL 10 DAY),
            'documents/procurements/sample_procurement.pdf',
            JSON_OBJECT('แบบฟอร์มเสนอราคา', 'documents/procurements/sample_bid_form.pdf'),
            'นางสาวจุฑารัตน์ สายทอง',
            '054-466666 ต่อ 1205',
            'procurement@satitup.ac.th',
            'published'
        ),
        (
            'ประกาศผลผู้ชนะการจัดซื้อจัดจ้างประจำเดือนสิงหาคม 2568',
            'สทภ-กพ 02/2568',
            'ประกาศผลจัดซื้อจัดจ้าง',
            'กลุ่มงานพัสดุและจัดซื้อจัดจ้าง',
            'ประกาศรายชื่อผู้ชนะการจัดซื้อจัดจ้างโครงการซ่อมแซมห้องปฏิบัติการวิทยาศาสตร์',
            685000.00,
            'THB',
            DATE_SUB(CURDATE(), INTERVAL 25 DAY),
            DATE_SUB(CURDATE(), INTERVAL 5 DAY),
            'documents/procurements/sample_award.pdf',
            NULL,
            'นายสมชาย วัฒนะ',
            '054-466666 ต่อ 1210',
            'award@satitup.ac.th',
            'closed'
        );";

    if (!$mysqli->query($insertSql)) {
        die('Failed to insert sample data: ' . $mysqli->error);
    }
}

$mysqli->close();

echo "Procurement announcements table ready.";
