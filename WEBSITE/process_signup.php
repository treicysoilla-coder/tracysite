<?php
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['username']); // Taking "Full Name" from form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    // Match these exactly to your phpMyAdmin column names
    $query = "INSERT INTO users (first_name, username, email, password, role) 
              VALUES ('$first_name', '$first_name', '$email', '$password', '$role')";

    if (mysqli_query($conn, $query)) {
        header("Location: login.php?signup=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>