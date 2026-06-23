<?php
session_start();
include '../includes/db_connect.php';
include 'upload_prescription.php';

// Redirect if the user somehow reached this page without a form submission
if (!isset($_POST['place_order'])) {
    header("Location: ../index.php");
    exit();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=Please login to place an order");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = mysqli_real_escape_string($conn, $_POST['total_amount']);
$address = mysqli_real_escape_string($conn, $_POST['shipping_address'] ?? '');
$delivery_method = mysqli_real_escape_string($conn, $_POST['delivery_method'] ?? 'delivery');

// Check if any product in the cart requires a prescription
$requires_prescription = false;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (!empty($item['requires_prescription'])) {
            $requires_prescription = true;
            break;
        }
    }
}

// Handle prescription upload
$prescription_filename = uploadPrescription($_FILES['prescription']);

// Enforce prescription gating
if ($requires_prescription && empty($prescription_filename)) {
    header("Location: ../checkout.php?error=Prescription upload is required for Rx medicines");
    exit();
}

// Status: Rx orders go to 'pending_review' (Pending Pharmacist Review).
// OTC-only orders are automatically 'approved' and ready for packing.
$status = $requires_prescription ? 'pending_review' : 'approved';

// Insert the main order into the database
$sql = "INSERT INTO orders (user_id, total_amount, shipping_address, status, prescription_path, delivery_method) 
        VALUES ('$user_id', '$total', '$address', '$status', '$prescription_filename', '$delivery_method')";

if (mysqli_query($conn, $sql)) {
    $order_id = mysqli_insert_id($conn); // Get the ID of the order we just created

    // Loop through the Session Cart and add items to 'order_items' table, updating stock
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $product_id = (int)$product_id;
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];

            // Insert order item details
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_time_of_purchase) 
                         VALUES ($order_id, $product_id, $qty, $price)";
            mysqli_query($conn, $item_sql);

            // Deduct from product stock
            $update_stock = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - $qty) WHERE id = $product_id";
            mysqli_query($conn, $update_stock);
        }
    }

    // Clear the session cart
    unset($_SESSION['cart']);

    // Redirect to success page
    header("Location: ../order-success.php?order_id=" . $order_id);
    exit();
} else {
    // Database error handling
    echo "Error placing order: " . mysqli_error($conn);
}
?>