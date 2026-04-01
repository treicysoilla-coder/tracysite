<?php
// 1. STAFF ACTION LOGIC
if ($role == 'staff') {
    // Handle Approval
    if (isset($_GET['approve_id'])) {
        $b_id = mysqli_real_escape_string($conn, $_GET['approve_id']);
        mysqli_query($conn, "UPDATE bookings SET status = 'Approved' WHERE id = '$b_id'");
        
        $res = mysqli_query($conn, "SELECT car_id FROM bookings WHERE id = '$b_id'");
        if ($booking_data = mysqli_fetch_assoc($res)) {
            $car_id = $booking_data['car_id'];
            mysqli_query($conn, "UPDATE cars SET status = 'rented' WHERE id = '$car_id'");
        }
        echo "<script>window.location.href='?page=all_bookings';</script>";
        exit();
    }

    // Handle Return
    if (isset($_GET['return_id'])) {
        $b_id = mysqli_real_escape_string($conn, $_GET['return_id']);
        mysqli_query($conn, "UPDATE bookings SET status = 'Completed' WHERE id = '$b_id'");
        
        $res = mysqli_query($conn, "SELECT car_id FROM bookings WHERE id = '$b_id'");
        if ($booking_data = mysqli_fetch_assoc($res)) {
            $car_id = $booking_data['car_id'];
            mysqli_query($conn, "UPDATE cars SET status = 'available' WHERE id = '$car_id'");
        }
        echo "<script>window.location.href='?page=all_bookings';</script>";
        exit();
    }

    // Handle Rejection
    if (isset($_GET['reject_id'])) {
        $b_id = mysqli_real_escape_string($conn, $_GET['reject_id']);
        mysqli_query($conn, "UPDATE bookings SET status = 'Rejected' WHERE id = '$b_id'");
        echo "<script>window.location.href='?page=all_bookings';</script>";
        exit();
    }

    // Handle Deletion
    if (isset($_GET['del_booking'])) {
        $b_id = mysqli_real_escape_string($conn, $_GET['del_booking']);
        mysqli_query($conn, "DELETE FROM bookings WHERE id = '$b_id'");
        echo "<script>window.location.href='?page=all_bookings';</script>";
        exit();
    }
}

// 2. FETCH DATA (Ensuring all date columns are included) [cite: 2026-03-02]
$query = "SELECT b.*, u.username, c.brand, c.model 
          FROM bookings b 
          JOIN users u ON b.user_id = u.id 
          JOIN cars c ON b.car_id = c.id 
          ORDER BY b.id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Rental <span style="color: #0066FF;">History & Management</span></h2>
        <p style="font-size: 12px; color: #888;">Total Records: <?= mysqli_num_rows($result) ?></p>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; color: white;">
        <tr style="border-bottom: 2px solid #0066FF; text-align: left; background: rgba(0, 102, 255, 0.1);">
            <th style="padding: 15px;">User</th>
            <th style="padding: 15px;">Vehicle</th>
            <th style="padding: 15px;">Rental Period</th>
            <th style="padding: 15px;">Status</th>
            <th style="padding: 15px; text-align: center;">Actions</th>
        </tr>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr style="border-bottom: 1px solid #333;">
                <td style="padding: 15px;">
                    <strong style="color: #0066FF;"><?= htmlspecialchars($row['username'] ?? 'N/A') ?></strong>
                </td>
                <td style="padding: 15px;"><?= htmlspecialchars($row['brand'] ?? '') ?> <?= htmlspecialchars($row['model'] ?? '') ?></td>
                <td style="padding: 15px;">
                    <div style="font-size: 13px;">
                        <?php 
                        // FIXED: Changed pickup_date/return_date to start_date/end_date [cite: 2026-03-02]
                        $p_date = (!empty($row['start_date']) && $row['start_date'] != '0000-00-00') ? date('M d, Y', strtotime($row['start_date'])) : 'N/A';
                        $r_date = (!empty($row['end_date']) && $row['end_date'] != '0000-00-00') ? date('M d, Y', strtotime($row['end_date'])) : 'N/A';
                        echo "📅 $p_date <br> <span style='color:#666'>to</span> $r_date";
                        ?>
                    </div>
                </td>
                <td style="padding: 15px;">
                    <?php 
                        $status = $row['status'] ?? 'Pending';
                        $color = '#ffc107'; 
                        if($status == 'Approved' || $status == 'Paid') $color = '#28a745';
                        if($status == 'Completed') $color = '#0066FF';
                        if($status == 'Rejected') $color = '#dc3545';
                    ?>
                    <span style="background: <?= $color ?>22; color: <?= $color ?>; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid <?= $color ?>44;">
                        ● <?= htmlspecialchars($status) ?>
                    </span>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                        <?php if ($status == 'Pending' || $status == 'Paid'): ?>
                            <a href="?page=all_bookings&approve_id=<?= $row['id'] ?>" style="color: #28a745; text-decoration: none; border: 1px solid #28a745; padding: 4px 8px; border-radius: 5px; font-size: 11px;">Approve</a>
                            <a href="?page=all_bookings&reject_id=<?= $row['id'] ?>" style="color: #dc3545; text-decoration: none; border: 1px solid #dc3545; padding: 4px 8px; border-radius: 5px; font-size: 11px;">Reject</a>
                        
                        <?php elseif ($status == 'Approved'): ?>
                            <a href="?page=all_bookings&return_id=<?= $row['id'] ?>" 
                               style="background: #0066FF; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: bold;"
                               onclick="return confirm('Mark this vehicle as returned and available?')">
                               Mark Returned
                            </a>

                        <?php elseif ($status == 'Completed'): ?>
                            <a href="?page=invoice&id=<?= $row['id'] ?>" target="_blank"
                               style="background: #28a745; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: bold;">
                               📄 Receipt
                            </a>

                        <?php else: ?>
                            <span style="color: #555; font-size: 11px;">Closed</span>
                        <?php endif; ?>
                        
                        <a href="?page=all_bookings&del_booking=<?= $row['id'] ?>" 
                           style="color: #444; text-decoration: none; font-size: 14px; margin-left: 5px;"
                           onclick="return confirm('Delete this record forever?')">
                            🗑️
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="padding: 40px; text-align: center; color: #666;">No rental records found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>