<?php
// 1. Connect to your database [cite: 2026-03-02]
include 'db_connect.php'; 
session_start();

// 2. Check if a Booking ID was sent in the URL
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // 3. Update the status to 'Approved' in the database [cite: 2026-03-02]
    $sql = "UPDATE bookings SET status = 'Approved' WHERE id = '$booking_id'";

    if (mysqli_query($conn, $sql)) {
        // 4. Redirect back to the staff dashboard after success
        echo "<script>
                alert('Booking has been officially Approved!');
                window.location.href = 'Dashboard.php?page=all_bookings'; 
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "No ID found.";
}
?>