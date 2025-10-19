<?php
$conn = new mysqli('localhost', 'root', '', 'satitup');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$alterSql = [
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_organization` varchar(255) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_year` varchar(10) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_category` varchar(255) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_position` varchar(50) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_score` decimal(10,2) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `ranking_criteria` text DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `achievement_highlights` text DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `publication_date` date DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `featured` tinyint(1) DEFAULT 0",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `color_theme` varchar(50) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `logo_path` varchar(255) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `additional_links` text DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `created_by` int(11) DEFAULT NULL",
    "ALTER TABLE `university_rankings` ADD COLUMN IF NOT EXISTS `updated_by` int(11) DEFAULT NULL"
];

foreach ($alterSql as $sql) {
    if (!$conn->query($sql)) {
        echo "Failed: {$conn->error}\n";
    }
}

$stmt = $conn->prepare("UPDATE university_rankings SET title=?, ranking_organization=?, ranking_year=?, ranking_category=?, ranking_position=?, ranking_score=?, ranking_criteria=?, achievement_highlights=?, publication_date=?, description=?, image_path=?, link=?, additional_links=?, active=1 WHERE id=?");

$title = 'โรงเรียนสาธิตมหาวิทยาลัยพะเยา คว้ารางวัลโครงการนวัตกรรมระดับนานาชาติ';
$org = 'QS World Innovation Awards';
$year = '2025';
$category = 'Innovation in Education';
$position = 'ชนะเลิศระดับนานาชาติ';
$score = 95.50;
$criteria = 'การบูรณาการเทคโนโลยีกับการเรียนการสอน, ความคิดสร้างสรรค์, ผลงานนักเรียน';
$highlights = 'ผลงานนักเรียนช่วงชั้นมัธยมปลายได้รับรางวัลเหรียญทอง พร้อมสิทธิเข้าร่วมค่ายนานาชาติที่สิงคโปร์';
$publication = '2025-06-01';
$description = 'โรงเรียนสาธิตมหาวิทยาลัยพะเยาได้รับรางวัลชนะเลิศจากโครงการนวัตกรรมการศึกษาระดับนานาชาติ ด้วยผลงานระบบผู้ช่วยการเรียนรู้ด้วยปัญญาประดิษฐ์';
$image = 'images/rankings/sample_innovation.jpg';
$link = 'https://www.up.ac.th/news/innovation-award-2025';
$additional = json_encode([
    'บทสัมภาษณ์ทีมงาน' => 'https://www.up.ac.th/news/interview-innovation',
    'รายละเอียดโครงการ' => 'https://www.up.ac.th/news/innovation-details'
]);
$id = 7;

$stmt->bind_param(
    'sssssdsssssssi',
    $title,
    $org,
    $year,
    $category,
    $position,
    $score,
    $criteria,
    $highlights,
    $publication,
    $description,
    $image,
    $link,
    $additional,
    $id
);

if ($stmt->execute()) {
    echo "Updated ranking ID {$id}\n";
} else {
    echo "Update failed: {$stmt->error}\n";
}

$stmt->close();
$conn->close();

