<?php
/**
 * Staff Management Functions
 * Helper functions for staff management
 */

/**
 * Upload a staff photo
 * 
 * @param array $file The uploaded file array ($_FILES['field'])
 * @return array Result array with success status, path and error message
 */
function uploadStaffPhoto($file) {
    // Define allowed file types and max file size
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check if file is valid
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'ไม่พบไฟล์ที่อัพโหลด'];
    }
    
    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'ประเภทไฟล์ไม่ถูกต้อง กรุณาอัพโหลดไฟล์รูปภาพเท่านั้น (JPEG, PNG, WEBP)'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'ไฟล์มีขนาดใหญ่เกินไป กรุณาอัพโหลดไฟล์ขนาดไม่เกิน 5MB'];
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../../uploads/staff';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid('staff_') . '_' . basename($file['name']);
    $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename); // Remove special characters
    $upload_path = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return [
            'success' => true,
            'path' => 'uploads/staff/' . $filename
        ];
    } else {
        return ['success' => false, 'error' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
}

/**
 * Get staff by ID
 * 
 * @param int $id The staff ID
 * @param mysqli $conn Database connection
 * @return array|null The staff data or null if not found
 */
function getStaffById($id, $conn) {
    $stmt = $conn->prepare("SELECT s.*, d.name as department_name, d.type as department_type 
                           FROM staff s 
                           LEFT JOIN departments d ON s.department_id = d.id 
                           WHERE s.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get staff list by department
 * 
 * @param int $department_id The department ID
 * @param mysqli $conn Database connection
 * @return array The staff list
 */
function getStaffByDepartment($department_id, $conn) {
    $stmt = $conn->prepare("SELECT s.*, d.name as department_name, d.type as department_type 
                           FROM staff s 
                           LEFT JOIN departments d ON s.department_id = d.id 
                           WHERE s.department_id = ? AND s.status = 'active'
                           ORDER BY s.is_head DESC, s.order_number, s.first_name");
    $stmt->bind_param('i', $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get all departments
 * 
 * @param string $type The department type (academic, service, or null for all)
 * @param mysqli $conn Database connection
 * @return array The departments
 */
function getDepartments($type = null, $conn) {
    $sql = "SELECT * FROM departments";
    if ($type !== null) {
        $sql .= " WHERE type = ?";
        $stmt = $conn->prepare($sql . " ORDER BY order_number, name");
        $stmt->bind_param('s', $type);
    } else {
        $stmt = $conn->prepare($sql . " ORDER BY type, order_number, name");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get department by ID
 * 
 * @param int $id The department ID
 * @param mysqli $conn Database connection
 * @return array|null The department data or null if not found
 */
function getDepartmentById($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get staff additional positions
 * 
 * @param int $staff_id The staff ID
 * @param mysqli $conn Database connection
 * @return array The staff positions
 */
function getStaffPositions($staff_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM staff_positions WHERE staff_id = ? ORDER BY is_primary DESC");
    $stmt->bind_param('i', $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Format Thai name with title
 * 
 * @param string $title Title (นาย, นาง, นางสาว, etc.)
 * @param string $first_name First name
 * @param string $last_name Last name
 * @return string Formatted name
 */
function formatThaiName($title, $first_name, $last_name) {
    return $title . ' ' . $first_name . ' ' . $last_name;
}

/**
 * Count staff by department
 * 
 * @param int $department_id The department ID
 * @param mysqli $conn Database connection
 * @return int The staff count
 */
function countStaffByDepartment($department_id, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM staff WHERE department_id = ? AND status = 'active'");
    $stmt->bind_param('i', $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

/**
 * Get staff by type (academic or service)
 * 
 * @param string $type The department type (academic or service)
 * @param mysqli $conn Database connection
 * @return array The staff list grouped by department
 */
function getStaffByType($type, $conn) {
    $departments = getDepartments($type, $conn);
    $result = [];
    
    foreach ($departments as $department) {
        $staff = getStaffByDepartment($department['id'], $conn);
        $result[] = [
            'department' => $department,
            'staff' => $staff
        ];
    }
    
    return $result;
}

/**
 * Delete a staff image
 * 
 * @param string $image_path The image path to delete
 * @return bool True if successful, false otherwise
 */
function deleteStaffImage($image_path) {
    $full_path = '../../' . $image_path;
    
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    
    return false;
}
