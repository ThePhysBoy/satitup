<?php
/**
 * Reset Admin User Script
 * This script resets the admin user password or recreates the admin user if it doesn't exist
 */

// Include database connection
require_once 'admin/includes/db_config.php';

// Create connection to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create admin user or update password
$admin_username = 'admin01';
$admin_password = password_hash('1234', PASSWORD_DEFAULT);
$admin_role = 'admin';
$user_type = 'pr_officer';
$full_name = 'นักประชาสัมพันธ์';
$position = 'เจ้าหน้าที่ประชาสัมพันธ์';

// Check if admin user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing admin user
    $user_id = $result->fetch_assoc()['id'];
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = ?, user_type = ?, full_name = ?, position = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $admin_password, $admin_role, $user_type, $full_name, $position, $user_id);
    
    if ($stmt->execute()) {
        echo "Admin user updated successfully.<br>";
        echo "Username: admin01<br>";
        echo "Password: 1234<br>";
    } else {
        echo "Error updating admin user: " . $stmt->error;
    }
} else {
    // Create new admin user
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, user_type, full_name, position) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $admin_username, $admin_password, $admin_role, $user_type, $full_name, $position);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully.<br>";
        echo "Username: admin01<br>";
        echo "Password: 1234<br>";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
}

// Close connection
$conn->close();

// Add link to login page
echo "<br><a href='admin/login.php'>Go to login page</a>";
?>
