<?php
require_once __DIR__ . '/config.php';

function formatPrice($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

function clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function isCartEmpty() {
    return empty($_SESSION['cart']);
}

function getCartCount() {
    if (isset($_SESSION['cart'])) {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    return 0;
}

function getWhatsAppLink($text) {
    $phone = "923001234567"; // Standard Pakistani format for WhatsApp Business
    return "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . urlencode($text);
}
?>
