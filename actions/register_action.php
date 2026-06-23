<?php
include '../includes/db_connect.php';

if (isset($_POST['register'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? 'customer');

    // Secure the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password, phone, address, role) 
            VALUES ('$full_name', '$email', '$hashed_password', '$phone', '$address', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../login.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>