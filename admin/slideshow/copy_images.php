<?php
/**
 * Copy Images Script
 * This script copies images from admin/slideshow/uploads to images/slideshow
 */

// Include database connection
$conn = require_once '../includes/db_config.php';

// Get all slideshow items
$stmt = $conn->prepare("SELECT id, image_path FROM slideshow");
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Copying Images</h1>";
echo "<pre>";

// Process each item
while ($slide = $result->fetch_assoc()) {
    $id = $slide['id'];
    $old_path = $slide['image_path'];
    
    // Check if the file exists
    if (file_exists($old_path)) {
        // Extract filename
        $filename = basename($old_path);
        $new_path = '../../images/slideshow/' . $filename;
        
        // Copy file
        if (copy($old_path, $new_path)) {
            // Update path in database
            $db_path = 'images/slideshow/' . $filename;
            $update_stmt = $conn->prepare("UPDATE slideshow SET image_path = ? WHERE id = ?");
            $update_stmt->bind_param("si", $db_path, $id);
            
            if ($update_stmt->execute()) {
                echo "Copied and updated image for slide ID $id: $old_path -> $db_path\n";
            } else {
                echo "Error updating database for slide ID $id: " . $update_stmt->error . "\n";
            }
        } else {
            echo "Error copying file for slide ID $id: $old_path -> $new_path\n";
        }
    } else {
        echo "File not found for slide ID $id: $old_path\n";
    }
}

echo "</pre>";
echo "<p>Image copying completed. <a href='index.php'>Go back to slideshow management</a></p>";
?>
