<?php
/**
 * Formats a number into Pakistani Rupees (Rs.)
 */
function formatPrice($amount) {
    return "Rs. " . number_format($amount, 2);
}

/**
 * Sanitizes input to prevent XSS attacks when echoing data
 */
function clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Checks if the shopping cart has items
 */
function isCartEmpty() {
    return empty($_SESSION['cart']);
}

/**
 * Gets the total count of items in the cart for the navbar badge
 */
function getCartCount() {
    if (isset($_SESSION['cart'])) {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    return 0;
}
?>