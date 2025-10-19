<?php
$mysqli = new mysqli('localhost', 'root', '', 'satitup');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `international_assignments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `person_name` VARCHAR(255) NOT NULL,
    `role` VARCHAR(150) DEFAULT NULL,
    `affiliation` VARCHAR(150) DEFAULT NULL,
    `country` VARCHAR(150) NOT NULL,
    `city` VARCHAR(150) DEFAULT NULL,
    `purpose` VARCHAR(255) DEFAULT NULL,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `duration_text` VARCHAR(150) DEFAULT NULL,
    `event_name` VARCHAR(255) DEFAULT NULL,
    `achievement` TEXT DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `cover_image` VARCHAR(255) NOT NULL,
    `gallery_images` TEXT DEFAULT NULL,
    `document_pdf` VARCHAR(255) DEFAULT NULL,
    `video_url` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('draft','published') DEFAULT 'published',
    `featured` TINYINT(1) DEFAULT 0,
    `published_date` DATE DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_published` (`published_date`),
    INDEX `idx_country` (`country`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$mysqli->query($sql)) {
    die('Failed to create international_assignments table: ' . $mysqli->error);
}

$result = $mysqli->query("SELECT COUNT(*) as cnt FROM international_assignments");
$row = $result ? $result->fetch_assoc() : ['cnt' => 0];

if ((int)$row['cnt'] === 0) {
    $insert = "INSERT INTO international_assignments (
        title, person_name, role, affiliation, country, city, purpose, start_date, end_date,
        duration_text, event_name, achievement, description, cover_image, gallery_images,
        document_pdf, video_url, status, featured, published_date
    ) VALUES
    (
        'นักเรียนโครงการ วมว. ตัวแทนประเทศไทยแข่งขันวิจัยนานาชาติ',
        'นางสาวณัฐธิดา คำสร้อย',
        'นักเรียนชั้น ม.5 โครงการ วมว.มพ.',
        'โรงเรียนสาธิตมหาวิทยาลัยพะเยา',
        'สหรัฐอเมริกา',
        'ซานฟรานซิสโก',
        'นำเสนองานวิจัยระดับนานาชาติ',
        DATE_SUB(CURDATE(), INTERVAL 30 DAY),
        DATE_SUB(CURDATE(), INTERVAL 25 DAY),
        '5 วัน',
        'International Science Fair 2025',
        'ได้รับรางวัลชนะเลิศสาขาวิทยาศาสตร์สิ่งแวดล้อม',
        'นักเรียนโครงการ วมว. คว้ารางวัลชนะเลิศจากการนำเสนองานวิจัยเกี่ยวกับการจัดการน้ำในชุมชน โดยได้รับคำชื่นชมจากผู้เชี่ยวชาญหลายประเทศ',
        'images/international/sample_student.jpg',
        JSON_ARRAY('images/international/gallery_student1.jpg','images/international/gallery_student2.jpg'),
        'documents/international/sample_student_paper.pdf',
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'published',
        1,
        DATE_SUB(CURDATE(), INTERVAL 20 DAY)
    ),
    (
        'คณาจารย์ร่วมโครงการวิจัยระยะสั้นมหาวิทยาลัยโตเกียว',
        'ผศ.ดร.วีระชัย สายทอง',
        'คณาจารย์กลุ่มสาระวิทยาศาสตร์',
        'โรงเรียนสาธิตมหาวิทยาลัยพะเยา',
        'ญี่ปุ่น',
        'โตเกียว',
        'แลกเปลี่ยนองค์ความรู้และทำวิจัยร่วม',
        DATE_SUB(CURDATE(), INTERVAL 60 DAY),
        DATE_SUB(CURDATE(), INTERVAL 45 DAY),
        '15 วัน',
        'Short-term Research Fellowship 2025',
        'นำผลวิจัยกลับมาพัฒนาหลักสูตร STEM ของโรงเรียน',
        'คณาจารย์ได้รับเชิญเข้าร่วมโครงการวิจัยร่วมกับมหาวิทยาลัยโตเกียว พร้อมทั้งอบรมเชิงลึกด้านการจัดการเรียนรู้ STEM',
        'images/international/sample_teacher.jpg',
        JSON_ARRAY('images/international/gallery_teacher1.jpg','images/international/gallery_teacher2.jpg'),
        'documents/international/sample_teacher_report.pdf',
        NULL,
        'published',
        0,
        DATE_SUB(CURDATE(), INTERVAL 40 DAY)
    );";

    if (!$mysqli->query($insert)) {
        die('Failed to insert sample international data: ' . $mysqli->error);
    }
}

$mysqli->close();
echo "International assignments table ready.";
