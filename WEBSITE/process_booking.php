<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect data from the form
    $user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing
    $car_id = $_POST['car_id'];
    $pickup = $_POST['pickup_date'];
    $return = $_POST['return_date'];
    $status = 'Pending';

    // Insert into bookings table
    // Note: I used start_date/end_date to match your phpMyAdmin structure
    $sql = "INSERT INTO bookings (user_id, car_id, start_date, end_date, status) 
            VALUES ('$user_id', '$car_id', '$pickup', '$return', '$status')";

    if (mysqli_query($conn, $sql)) {
        // Success: Redirect back to bookings page
        header("Location: Dashboard.php?page=bookings&status=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>