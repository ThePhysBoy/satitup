<?php
/**
 * News Export
 * Export news data to CSV format
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Get export parameters
$export_type = $_GET['type'] ?? 'all';
$category_id = isset($_GET['category_id']) && !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : null;
$date_from = isset($_GET['date_from']) && !empty($_GET['date_from']) ? $_GET['date_from'] : null;
$date_to = isset($_GET['date_to']) && !empty($_GET['date_to']) ? $_GET['date_to'] : null;

// Set filename
$filename = 'news_export_' . date('Y-m-d') . '.csv';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM to fix Thai characters in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Set column headers
$headers = [
    'ID', 
    'หัวข้อ', 
    'สถานะ', 
    'หมวดหมู่', 
    'ผู้เขียน', 
    'วันที่สร้าง', 
    'วันที่เผยแพร่', 
    'จำนวนการอ่าน', 
    'ข่าวเด่น'
];
fputcsv($output, $headers);

// Build SQL query
$sql = "SELECT n.*, u.username, u.full_name
        FROM news n
        LEFT JOIN users u ON n.author_id = u.id
        WHERE 1=1";

$params = [];
$types = '';

// Add filters based on export type and parameters
if ($export_type === 'category' && $category_id !== null) {
    $sql .= " AND n.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if ($export_type === 'status' && $status !== null) {
    $sql .= " AND n.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($date_from !== null) {
    $sql .= " AND n.created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
    $types .= 's';
}

if ($date_to !== null) {
    $sql .= " AND n.created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
    $types .= 's';
}

$sql .= " ORDER BY n.created_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Output each row of data
    while ($row = $result->fetch_assoc()) {
        $status_text = '';
        switch ($row['status']) {
            case 'published':
                $status_text = 'เผยแพร่';
                break;
            case 'draft':
                $status_text = 'แบบร่าง';
                break;
            case 'pending':
                $status_text = 'รอตรวจสอบ';
                break;
        }
        
        $csv_row = [
            $row['id'],
            $row['title'],
            $status_text,
            $row['full_name'] ?? $row['username'] ?? '-',
            $row['created_at'],
            $row['published_at'] ?? '-',
            $row['views'],
            $row['is_featured'] ? 'ใช่' : 'ไม่ใช่'
        ];
        
        fputcsv($output, $csv_row);
    }
} else {
    // Handle error
    $error_row = ['Error', 'ไม่สามารถดึงข้อมูลได้'];
    fputcsv($output, $error_row);
}

// Close the file pointer
fclose($output);
exit;
?>
