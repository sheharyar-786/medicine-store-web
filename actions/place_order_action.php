<?php
session_start();
include '../includes/db_connect.php';
include 'upload_prescription.php'; // Include the tool we created above

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
$address = mysqli_real_escape_string($conn, $_POST['shipping_address']);

// 1. Call the modular upload function
// It returns the filename if successful, otherwise an empty string
$prescription_filename = uploadPrescription($_FILES['prescription']);

// 2. Insert the main order into the database
// We store the prescription filename in the 'prescription_path' column
$sql = "INSERT INTO orders (user_id, total_amount, status, prescription_path) 
        VALUES ('$user_id', '$total', 'pending', '$prescription_filename')";

if (mysqli_query($conn, $sql)) {
    $order_id = mysqli_insert_id($conn); // Get the ID of the order we just created

    // 3. Optional: Loop through the Session Cart and add items to an 'order_items' table
    // For now, we will just clear the cart
    unset($_SESSION['cart']);

    // 4. Redirect to success page
    header("Location: ../order-success.php?order_id=" . $order_id);
    exit();
} else {
    // Database error handling
    echo "Error placing order: " . mysqli_error($conn);
}
?>