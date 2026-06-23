<?php
session_start();
include '../includes/db_connect.php';

if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Fetch details directly from database to avoid client tampering
    $query = "SELECT name, price, image_url, generic_name, requires_prescription FROM products WHERE id = $product_id LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product is already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to cart
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image_url'],
                'generic_name' => $product['generic_name'],
                'requires_prescription' => $product['requires_prescription'],
                'quantity' => $quantity
            ];
        }
    }

    header("Location: ../cart.php");
    exit();
}
?>