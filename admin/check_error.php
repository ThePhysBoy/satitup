<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h2>Error Checking Page</h2>";

// Check PHP version
echo "<h3>1. PHP Version:</h3>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Check if auth_functions.php exists
echo "<h3>2. Auth Functions File:</h3>";
$auth_file = 'includes/auth_functions.php';
if (file_exists($auth_file)) {
    echo "✅ auth_functions.php exists<br>";
    require_once $auth_file;
    echo "✅ auth_functions.php loaded successfully<br><br>";
} else {
    echo "❌ auth_functions.php NOT found<br><br>";
}

// Check database connection
echo "<h3>3. Database Connection:</h3>";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satitup";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "❌ Connection failed: " . $conn->connect_error . "<br><br>";
    } else {
        echo "✅ Database connected successfully<br>";
        echo "Database: " . $dbname . "<br><br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

// Check session
echo "<h3>4. Session Status:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User logged in<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'N/A') . "<br>";
    echo "User Type: " . ($_SESSION['user_type'] ?? 'N/A') . "<br><br>";
} else {
    echo "⚠️ User NOT logged in<br>";
    echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre><br><br>";
}

// Check video tables
echo "<h3>5. Video Tables:</h3>";
if (isset($conn) && !$conn->connect_error) {
    // Check video_categories table
    $result = $conn->query("SHOW TABLES LIKE 'video_categories'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table 'video_categories' exists<br>";
        
        $count = $conn->query("SELECT COUNT(*) as count FROM video_categories");
        if ($count) {
            $row = $count->fetch_assoc();
            echo "   - Categories count: " . $row['count'] . "<br>";
        }
    } else {
        echo "❌ Table 'video_categories' NOT found<br>";
    }
    
    // Check videos table
    $result = $conn->query("SHOW TABLES LIKE 'videos'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table 'videos' exists<br>";
        
        // Check for youtube_url column
        $check_column = $conn->query("SHOW COLUMNS FROM videos LIKE 'youtube_url'");
        if ($check_column && $check_column->num_rows > 0) {
            echo "   ✅ Column 'youtube_url' exists<br>";
        } else {
            echo "   ❌ Column 'youtube_url' NOT found<br>";
        }
        
        $count = $conn->query("SELECT COUNT(*) as count FROM videos");
        if ($count) {
            $row = $count->fetch_assoc();
            echo "   - Videos count: " . $row['count'] . "<br>";
        }
    } else {
        echo "❌ Table 'videos' NOT found<br>";
    }
}

echo "<br><hr><br>";
echo "<h3>Links to test:</h3>";
echo "<a href='login.php'>1. Login Page</a><br>";
echo "<a href='index.php'>2. Dashboard</a><br>";
echo "<a href='video_system/simple_video_manager.php'>3. Simple Video Manager</a><br>";
echo "<a href='../admin/'>4. Back to Admin</a><br>";
?>
