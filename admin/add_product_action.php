<?php
include '../includes/db_connect.php';

if (isset($_POST['submit_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $generic = mysqli_real_escape_string($conn, $_POST['generic_name']);
    $cat = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $req_pres = isset($_POST['requires_prescription']) ? 1 : 0;

    // Handle Image Upload
    $image_name = $_FILES['image']['name'];
    $target = "../assets/uploads/products/" . basename($image_name);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (name, generic_name, category, price, stock_quantity, image_url, description, requires_prescription) 
                VALUES ('$name', '$generic', '$cat', '$price', '$stock', '$image_name', '$desc', '$req_pres')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage-products.php?success=1");
        } else {
            echo "DB Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>