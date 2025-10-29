<?php
/**
 * Import slideshow records from legacy static slideshow definitions.
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in and have slideshow management permission
requireLogin();
if (!canManageSlideshow()) {
    header("Location: ../index.php");
    exit;
}

// Define default slides based on the old static_slideshow.php content
$default_slides = [
    [
        'title' => 'ยินดีต้อนรับ',
        'description' => 'สร้างคนดี มีความรู้ สู่สังคมอย่างมีคุณภาพ',
        'image_path' => 'images/slideshow/slideshow1.jpg',
        'link' => 'about-history.php',
        'display_order' => 1,
        'active' => 1,
    ],
    [
        'title' => 'การศึกษาที่มีคุณภาพ',
        'description' => 'ด้วยเทคโนโลยีและนวัตกรรมที่ทันสมัย',
        'image_path' => 'images/slideshow/slideshow2.jpg',
        'link' => 'academic-curriculum.php',
        'display_order' => 2,
        'active' => 1,
    ],
    [
        'title' => 'กิจกรรมหลากหลาย',
        'description' => 'ส่งเสริมการเรียนรู้ในทุกมิติ',
        'image_path' => 'images/slideshow/slideshow3.jpg',
        'link' => 'student-activities.php',
        'display_order' => 3,
        'active' => 1,
    ],
];

// Collect existing image paths to avoid duplicates
$existing_paths = [];
$result = $conn->query("SELECT image_path FROM slideshow");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $existing_paths[] = $row['image_path'];
    }
}

$inserted = 0;
$skipped = 0;
$missing_files = [];

$project_root = realpath(__DIR__ . '/../../');

$stmt = $conn->prepare("INSERT INTO slideshow (title, description, image_path, link, display_order, active) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($default_slides as $slide) {
    $image_rel_path = $slide['image_path'];
    $image_full_path = $project_root !== false
        ? $project_root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image_rel_path)
        : null;

    if ($image_full_path === null || !file_exists($image_full_path)) {
        $missing_files[] = $image_rel_path;
        continue;
    }

    if (in_array($image_rel_path, $existing_paths, true)) {
        $skipped++;
        continue;
    }

    $stmt->bind_param(
        'ssssii',
        $slide['title'],
        $slide['description'],
        $slide['image_path'],
        $slide['link'],
        $slide['display_order'],
        $slide['active']
    );

    if ($stmt->execute()) {
        $inserted++;
    } else {
        // If insertion fails, treat as skipped to prevent duplicate error
        $skipped++;
    }
}

$stmt->close();

$missing_count = count($missing_files);
$query = [
    'imported' => 1,
    'inserted' => $inserted,
    'skipped' => $skipped,
    'missing' => $missing_count,
];

if ($missing_count > 0) {
    $query['missing_files'] = base64_encode(json_encode($missing_files));
}

$redirect_url = 'index.php?' . http_build_query($query);

header("Location: $redirect_url");
exit;

