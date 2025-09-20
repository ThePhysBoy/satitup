<?php
/**
 * Fix Image Paths in Database
 * This script updates image paths in the database to use the correct path
 */

// Include database connection
$conn = require_once 'includes/db_config.php';

// Get all slideshow items
$stmt = $conn->prepare("SELECT id, image_path FROM slideshow");
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Fixing Image Paths</h1>";
echo "<pre>";

// Update each item
while ($slide = $result->fetch_assoc()) {
    $id = $slide['id'];
    $old_path = $slide['image_path'];
    
    // Check if the path needs to be updated
    if (strpos($old_path, 'admin/slideshow/uploads/') === false && strpos($old_path, 'uploads/') === 0) {
        // Update path
        $new_path = 'admin/slideshow/' . $old_path;
        
        // Update in database
        $update_stmt = $conn->prepare("UPDATE slideshow SET image_path = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_path, $id);
        
        if ($update_stmt->execute()) {
            echo "Updated image path for slide ID $id: $old_path -> $new_path\n";
        } else {
            echo "Error updating image path for slide ID $id: " . $update_stmt->error . "\n";
        }
    } else {
        echo "No update needed for slide ID $id: $old_path\n";
    }
}

echo "</pre>";
echo "<p>Image path fixing completed. <a href='slideshow/index.php'>Go back to slideshow management</a></p>";
?>
