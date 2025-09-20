<?php
/**
 * Update Users Table Structure
 * This script updates the users table to add new columns for user type, full name, and position
 */

// Include database connection configuration
require_once 'admin/includes/db_config.php';

// Create connection to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Array to track operations
$operations = [];

// Check and add user_type column if it doesn't exist
if (!columnExists($conn, 'users', 'user_type')) {
    $sql = "ALTER TABLE users ADD COLUMN user_type VARCHAR(50) DEFAULT 'general' AFTER role";
    if ($conn->query($sql) === TRUE) {
        $operations[] = "Added user_type column to users table.";
    } else {
        $operations[] = "Error adding user_type column: " . $conn->error;
    }
}

// Check and add full_name column if it doesn't exist
if (!columnExists($conn, 'users', 'full_name')) {
    $sql = "ALTER TABLE users ADD COLUMN full_name VARCHAR(100) AFTER user_type";
    if ($conn->query($sql) === TRUE) {
        $operations[] = "Added full_name column to users table.";
    } else {
        $operations[] = "Error adding full_name column: " . $conn->error;
    }
}

// Check and add position column if it doesn't exist
if (!columnExists($conn, 'users', 'position')) {
    $sql = "ALTER TABLE users ADD COLUMN position VARCHAR(100) AFTER full_name";
    if ($conn->query($sql) === TRUE) {
        $operations[] = "Added position column to users table.";
    } else {
        $operations[] = "Error adding position column: " . $conn->error;
    }
}

// Update admin01 user with PR Officer information if it exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin01'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_id = $result->fetch_assoc()['id'];
    $user_type = 'pr_officer';
    $full_name = 'นักประชาสัมพันธ์';
    $position = 'เจ้าหน้าที่ประชาสัมพันธ์';
    
    $stmt = $conn->prepare("UPDATE users SET user_type = ?, full_name = ?, position = ? WHERE id = ?");
    $stmt->bind_param("sssi", $user_type, $full_name, $position, $user_id);
    
    if ($stmt->execute()) {
        $operations[] = "Updated admin01 user information.";
    } else {
        $operations[] = "Error updating admin01 user: " . $stmt->error;
    }
}

// Close connection
$conn->close();

// Display results
echo "<h1>Users Table Update</h1>";
echo "<ul>";
foreach ($operations as $operation) {
    echo "<li>" . $operation . "</li>";
}
echo "</ul>";

if (count($operations) === 0) {
    echo "<p>No changes were needed. The users table is already up to date.</p>";
}

echo "<p><a href='admin/login.php'>Go to login page</a></p>";
?>
