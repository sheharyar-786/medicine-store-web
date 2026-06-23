<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirects user to login if they are not logged in.
 */
function restrictToLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=Please login to continue");
        exit();
    }
}

/**
 * Redirects user if they are not an admin.
 */
function restrictToAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../index.php?error=Unauthorized access");
        exit();
    }
}
?>