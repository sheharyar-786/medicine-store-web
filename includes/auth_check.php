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
        header("Location: " . (function_exists('basePath') ? basePath() : '') . "/index.php?error=Unauthorized access");
        exit();
    }
}

function restrictToPharmacist() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pharmacist') {
        header("Location: " . (function_exists('basePath') ? basePath() : '') . "/index.php?error=Unauthorized access");
        exit();
    }
}

function restrictToDriver() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
        header("Location: " . (function_exists('basePath') ? basePath() : '') . "/index.php?error=Unauthorized access");
        exit();
    }
}

function restrictToStaff() {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pharmacist', 'driver'])) {
        header("Location: " . (function_exists('basePath') ? basePath() : '') . "/index.php?error=Unauthorized access");
        exit();
    }
}
?>