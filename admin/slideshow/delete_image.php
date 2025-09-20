<?php
/**
 * Delete Slideshow Image
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slide_id'])) {
    $slide_id = (int)$_POST['slide_id'];
    
    // Get slideshow item
    $stmt = $conn->prepare("SELECT image_path FROM slideshow WHERE id = ?");
    $stmt->bind_param("i", $slide_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $slide = $result->fetch_assoc();
        $image_path = $slide['image_path'];
        
        // Delete the physical file
        $full_path = '../../' . $image_path;
        $deleted = false;
        
        if (file_exists($full_path)) {
            if (unlink($full_path)) {
                $deleted = true;
            }
        } else {
            // File doesn't exist, consider it deleted
            $deleted = true;
        }
        
        if ($deleted) {
            // Update database to remove image path
            $stmt = $conn->prepare("UPDATE slideshow SET image_path = '' WHERE id = ?");
            $stmt->bind_param("i", $slide_id);
            
            if ($stmt->execute()) {
                header("Location: edit.php?id=" . $slide_id . "&image_deleted=1");
                exit;
            }
        }
    }
}

// If we get here, something went wrong
header("Location: edit.php?id=" . $slide_id . "&image_deleted=0");
exit;
?>