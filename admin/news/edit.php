<?php
/**
 * Edit News - Redirect to Modern Template
 * This file redirects to the new edit_new.php page which uses the modern template
 */

// Get the ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Preserve any success parameter
$success = isset($_GET['success']) ? '&success=1' : '';

// Redirect to the new edit page
header("Location: edit_new.php?id=$id$success");
exit;
?>