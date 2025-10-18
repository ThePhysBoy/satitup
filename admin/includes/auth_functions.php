<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is an admin
 * 
 * @return bool True if user is an admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Check if user is a PR officer
 * 
 * @return bool True if user is a PR officer, false otherwise
 */
function isPrOfficer() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'pr_officer';
}

/**
 * Check if user can manage slideshow
 * 
 * @return bool True if user can manage slideshow, false otherwise
 */
function canManageSlideshow() {
    return isAdmin() || isPrOfficer();
}

/**
 * Check if user can manage rankings
 * 
 * @return bool True if user can manage rankings, false otherwise
 */
function canManageRankings() {
    return isAdmin() || isPrOfficer();
}

/**
 * Check if user can manage news
 * 
 * @return bool True if user can manage news, false otherwise
 */
function canManageNews() {
    return isAdmin() || isPrOfficer();
}

/**
 * Check if user can manage staff
 * 
 * @return bool True if user can manage staff, false otherwise
 */
function canManageStaff() {
    return isAdmin() || isPrOfficer();
}

/**
 * Authenticate user with username and password
 * 
 * @param string $username Username
 * @param string $password Password
 * @param mysqli $conn Database connection
 * @return array|bool User data if authentication successful, false otherwise
 */
function authenticateUser($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, username, password, user_type, full_name, position FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['position'] = $user['position'];
            
            return $user;
        }
    }
    
    return false;
}

/**
 * Log out user
 */
function logoutUser() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // If it's desired to kill the session, also delete the session cookie.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Require user to be logged in
 * Redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . getBaseUrl() . "admin/login.php");
        exit;
    }
}

/**
 * Require user to be an admin
 * Redirect to dashboard if not an admin
 */
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ เฉพาะผู้ดูแลระบบเท่านั้น";
        header("Location: " . getBaseUrl() . "admin/index.php");
        exit;
    }
}

/**
 * Require user to have slideshow access
 * Redirect to dashboard if not authorized
 */
function requireSlideshowAccess() {
    requireLogin();
    
    if (!canManageSlideshow()) {
        $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนการจัดการสไลด์โชว์";
        header("Location: " . getBaseUrl() . "admin/index.php");
        exit;
    }
}

/**
 * Require user to have rankings access
 * Redirect to dashboard if not authorized
 */
function requireRankingsAccess() {
    requireLogin();
    
    if (!canManageRankings()) {
        $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนการจัดการอันดับมหาวิทยาลัย";
        header("Location: " . getBaseUrl() . "admin/index.php");
        exit;
    }
}

/**
 * Require user to have news access
 * Redirect to dashboard if not authorized
 */
function requireNewsAccess() {
    requireLogin();
    
    if (!canManageNews()) {
        $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนนี้";
        header("Location: " . getBaseUrl() . "admin/index.php");
        exit;
    }
}

/**
 * Require user to have staff access
 * Redirect to dashboard if not authorized
 */
function requireStaffAccess() {
    requireLogin();
    
    if (!canManageStaff()) {
        $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนการจัดการบุคลากร";
        header("Location: " . getBaseUrl() . "admin/index.php");
        exit;
    }
}

/**
 * Get base URL for the application
 * 
 * @return string Base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    
    // Remove 'admin' from path if present
    $path = str_replace('/admin', '', $path);
    
    // Ensure path ends with a slash
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }
    
    return $protocol . $host . $path;
}
?>