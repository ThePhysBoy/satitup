<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h2>Video System Access Test</h2>";

// Test 1: Check if auth_functions.php can be loaded
echo "<h3>1. Loading auth_functions.php:</h3>";
try {
    if (file_exists('../includes/auth_functions.php')) {
        echo "✅ File exists at: ../includes/auth_functions.php<br>";
        require_once '../includes/auth_functions.php';
        echo "✅ Successfully loaded auth_functions.php<br><br>";
    } else {
        echo "❌ File NOT found at: ../includes/auth_functions.php<br>";
        echo "Current directory: " . __DIR__ . "<br>";
        echo "Looking for: " . realpath('../includes/auth_functions.php') . "<br><br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading auth_functions.php: " . $e->getMessage() . "<br><br>";
}

// Test 2: Check login status
echo "<h3>2. Login Status:</h3>";
if (function_exists('isLoggedIn')) {
    if (isLoggedIn()) {
        echo "✅ User is logged in<br>";
        echo "User ID: " . $_SESSION['user_id'] . "<br>";
        echo "Username: " . ($_SESSION['username'] ?? 'N/A') . "<br><br>";
    } else {
        echo "⚠️ User is NOT logged in<br>";
        echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";
        echo "<a href='../login.php'>Go to Login Page</a><br><br>";
    }
} else {
    echo "❌ Function isLoggedIn() not found<br><br>";
}

// Test 3: Direct access to simple_video_manager.php
echo "<h3>3. Attempting to access simple_video_manager.php:</h3>";
if (isLoggedIn()) {
    echo "✅ Access should be granted<br>";
    echo "<a href='simple_video_manager.php'>Click here to go to Simple Video Manager</a><br><br>";
} else {
    echo "❌ Access will be denied (not logged in)<br>";
    echo "You will be redirected to login page<br><br>";
    
    // Create a test login session
    echo "<h3>4. Creating Test Session:</h3>";
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_type'] = 'admin';
    $_SESSION['full_name'] = 'Administrator';
    
    echo "✅ Test session created<br>";
    echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";
    echo "<a href='simple_video_manager.php' style='background: green; color: white; padding: 10px; text-decoration: none; display: inline-block; margin-top: 10px;'>Now try accessing Simple Video Manager</a><br><br>";
}

// Show all errors if any
echo "<h3>PHP Errors (if any):</h3>";
$error = error_get_last();
if ($error) {
    echo "<pre>" . print_r($error, true) . "</pre>";
} else {
    echo "No PHP errors detected<br>";
}
?>
