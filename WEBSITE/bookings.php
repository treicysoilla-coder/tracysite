<?php
/**
 * FastCar - Client Bookings Module
 * FIXED: Syntax Error on Line 86 and Status Badge logic [cite: 2026-03-02]
 */

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
$u_id = $_SESSION['user_id'];

// Fetch Bookings
$query = "SELECT b.*, c.brand, c.model, c.car_image 
          FROM bookings b 
          JOIN cars c ON b.car_id = c.id 
          WHERE b.user_id = '$u_id' 
          ORDER BY b.id DESC";

$result = mysqli_query($conn, $query);
?>

<div style="color: white; padding: 20px; font-family: 'Segoe UI', sans-serif;">
    <h2 style="margin-bottom: 20px;">My <span style="color: #0066FF;">Bookings</span></h2>

    <div style="background: rgba(0,0,0,0.4); border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: rgba(0, 102, 255, 0.1); color: #888; font-size: 12px; border-bottom: 1px solid #333;">
                    <th style="padding: 20px;">VEHICLE</th>
                    <th style="padding: 20px;">RENTAL DATES</th>
                    <th style="padding: 20px;">TOTAL PRICE</th>
                    <th style="padding: 20px;">STATUS</th>
                    <th style="padding: 20px;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                            <img src="car_images/<?= htmlspecialchars($row['car_image']) ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div style="font-weight: bold; color: #fff;"><?= htmlspecialchars($row['brand']) ?></div>
                                <div style="font-size: 12px; color: #777;"><?= htmlspecialchars($row['model']) ?></div>
                            </div>
                        </td>
                        <td style="padding: 15px; font-size: 13px; color: #ccc;">
                            <?= date('M d', strtotime($row['start_date'])) ?> — <?= date('M d, Y', strtotime($row['end_date'])) ?>
                        </td>
                        <td style="padding: 15px; font-weight: bold; color: #0066FF;">
                            Ksh <?= number_format($row['total_price']) ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php 
                                // Logic to fix the Orange Dot [cite: 2026-03-02]
                                $s = strtolower(trim($row['status']));
                                
                                // If status is empty but they paid, it's Confirmed
                                if (empty($s) && !empty($row['payment_method'])) {
                                    $s = 'confirmed';
                                }

                                if ($s == 'pending' || empty($s)) {
                                    $color = '#ffcc00'; 
                                    $text = 'Pending';
                                } elseif ($s == 'confirmed') {
                                    $color = '#dc3545'; // Matches your Red Badge
                                    $text = 'Confirmed';
                                } elseif ($s == 'approved') {
                                    $color = '#28a745'; 
                                    $text = 'Approved';
                                } else {
                                    $color = '#444'; 
                                    $text = $row['status'];
                                }
                            ?>
                            <span style="background: <?= $color ?>; color: white; padding: 6px 14px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; display: inline-flex; justify-content: center; min-width: 95px;">
                                <?= htmlspecialchars($text) ?>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <?php if($s == 'pending' || empty($s)): ?>
                                <a href="?page=payment&id=<?= $row['id'] ?>" style="background: #0066FF; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold;">PAY NOW</a>
                            <?php else: ?>
                                <span style="color: #666; font-size: 11px; font-weight: bold;">PAID</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="padding: 50px; text-align: center; color: #555;">No current bookings.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>