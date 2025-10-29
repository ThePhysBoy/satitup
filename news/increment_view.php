<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = $_POST;
}

$newsId = isset($data['news_id']) ? (int)$data['news_id'] : 0;

if ($newsId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid news id'
    ]);
    exit;
}

require_once '../db_connect.php';

if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้'
    ]);
    exit;
}

$updateStmt = $conn->prepare('UPDATE news SET views = COALESCE(views, 0) + 1 WHERE id = ?');

if (!$updateStmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเตรียมคำสั่งอัปเดตได้'
    ]);
    exit;
}

$updateStmt->bind_param('i', $newsId);

if (!$updateStmt->execute()) {
    $updateStmt->close();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถบันทึกข้อมูลได้'
    ]);
    exit;
}

$updateStmt->close();

$selectStmt = $conn->prepare('SELECT COALESCE(views, 0) AS views FROM news WHERE id = ?');

if (!$selectStmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเตรียมคำสั่งดึงข้อมูลได้'
    ]);
    exit;
}

$selectStmt->bind_param('i', $newsId);
$selectStmt->execute();
$selectStmt->bind_result($views);
$selectStmt->fetch();
$selectStmt->close();

$views = (int) $views;

echo json_encode([
    'success' => true,
    'views' => $views
]);

if ($conn instanceof mysqli) {
    $conn->close();
}


