<?php
// 1. Handle Approval or Rejection (Now includes 'Confirmed' check)
if (isset($_GET['action']) && isset($_GET['booking_id'])) {
    $b_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    
    mysqli_query($conn, "UPDATE bookings SET status = '$status' WHERE id = '$b_id'");
    echo "<script>window.location.href='?page=all_bookings';</script>";
}

// 2. Query remains the same
$query = "SELECT bookings.*, cars.brand, cars.model, users.username 
          FROM bookings 
          JOIN cars ON bookings.car_id = cars.id 
          JOIN users ON bookings.user_id = users.id 
          ORDER BY bookings.id DESC";
$result = mysqli_query($conn, $query);
?>

<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; color: white; }
    .admin-table th { background: rgba(0, 102, 255, 0.2); padding: 15px; text-align: left; border-bottom: 2px solid #0066FF; }
    .admin-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); }
    
    .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    
    /* ADDED: Styles for every possible status [cite: 2026-03-02] */
    .status-pending { background: #ffc107; color: black; }
    .status-confirmed { background: #dc3545; color: white; } /* Matches your Red Theme */
    .status-approved { background: #28a745; color: white; }
    .status-rejected { background: #6c757d; color: white; }

    .action-btn { text-decoration: none; padding: 5px 10px; border-radius: 5px; font-size: 11px; margin-right: 5px; color: white; font-weight: bold; }
    .btn-approve { border: 1px solid #28a745; color: #28a745; }
    .btn-reject { border: 1px solid #dc3545; color: #dc3545; }
    .btn-approve:hover { background: #28a745; color: white; }
</style>

<div class="glass-card">
    <h2>Total <span style="color: #0066FF;">Rental History</span></h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Vehicle</th>
                <th>M-Pesa Code</th> <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <?php $clean_status = strtolower(trim($row['status'])); ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?></td>
                
                <td style="color: #0066FF; font-family: monospace; font-weight: bold;">
                    <?= $row['payment_method'] ? $row['payment_method'] : '<span style="color:#444">No Payment</span>' ?>
                </td>

                <td>
                    <span class="status-pill status-<?= $clean_status ?>">
                        <?= $row['status'] ? $row['status'] : 'Pending' ?>
                    </span>
                </td>
                
                <td>
                    <?php if($clean_status == 'pending' || $clean_status == 'confirmed'): ?>
                        <a href="?page=all_bookings&action=approve&booking_id=<?= $row['id'] ?>" class="action-btn btn-approve">Approve</a>
                        <a href="?page=all_bookings&action=reject&booking_id=<?= $row['id'] ?>" class="action-btn btn-reject">Reject</a>
                    <?php else: ?>
                        <span style="color: #555;">Closed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>