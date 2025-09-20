<?php
/**
 * Logout Page
 */

// Include authentication functions
require_once 'includes/auth_functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Logout user
logoutUser();
?>