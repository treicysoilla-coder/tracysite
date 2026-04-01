<?php
session_start();
require_once('db.php');

// Security Check: Only staff should be able to process returns
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    die("Unauthorized access.");
}

// Check if we have the necessary IDs from the URL
if (isset($_GET['id']) && isset($_GET['booking_id'])) {
    $car_id = intval($_GET['id']);
    $booking_id = intval($_GET['booking_id']);

    // 1. Update the Car status back to 'Available'
    $update_car = "UPDATE cars SET status = 'Available' WHERE id = $car_id";
    
    // 2. Update the Booking status to 'Returned'
    $update_booking = "UPDATE bookings SET status = 'Returned' WHERE id = $booking_id";

    // Execute both queries
    if (mysqli_query($conn, $update_car) && mysqli_query($conn, $update_booking)) {
        // Redirect back to the bookings page with a success message
        header("Location: Dashboard.php?page=bookings&status=success");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid Request.";
}
?>