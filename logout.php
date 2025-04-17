<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Logout user
logoutUser();

// Clear any cookies
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

// Redirect to home page
header('Location: index.php');
exit;
?>