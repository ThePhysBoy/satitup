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

$assignmentId = isset($data['assignment_id']) ? (int)$data['assignment_id'] : 0;

if ($assignmentId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid assignment id'
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

$updateStmt = $conn->prepare('UPDATE international_assignments SET likes = COALESCE(likes, 0) + 1 WHERE id = ?');

if (!$updateStmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเตรียมคำสั่งอัปเดตได้'
    ]);
    exit;
}

$updateStmt->bind_param('i', $assignmentId);

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

$selectStmt = $conn->prepare('SELECT COALESCE(likes, 0) AS likes FROM international_assignments WHERE id = ?');

if (!$selectStmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเตรียมคำสั่งดึงข้อมูลได้'
    ]);
    exit;
}

$selectStmt->bind_param('i', $assignmentId);
$selectStmt->execute();
$selectStmt->bind_result($likes);
$selectStmt->fetch();
$selectStmt->close();

$likes = (int)$likes;

echo json_encode([
    'success' => true,
    'likes' => $likes
]);

if ($conn instanceof mysqli) {
    $conn->close();
}


