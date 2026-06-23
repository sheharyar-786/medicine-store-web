<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Fetch items for the specified order
$query = "SELECT order_items.product_id, order_items.quantity, products.name, products.price, products.image_url, products.generic_name, products.requires_prescription 
          FROM order_items 
          JOIN products ON order_items.product_id = products.id 
          WHERE order_items.order_id = $order_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Clear current shopping cart
    $_SESSION['cart'] = [];

    // Load order items into cart session
    while ($item = mysqli_fetch_assoc($result)) {
        $product_id = $item['product_id'];
        $_SESSION['cart'][$product_id] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'image' => $item['image_url'],
            'generic_name' => $item['generic_name'],
            'requires_prescription' => $item['requires_prescription'],
            'quantity' => $item['quantity']
        ];
    }

    // Redirect to checkout with a success parameter
    header("Location: checkout.php?reorder=success");
    exit();
} else {
    header("Location: index.php?error=Unable to process reorder. Products may no longer be available.");
    exit();
}
?>
