<?php
// 1. STATS CALCULATIONS
$rev_query = mysqli_query($conn, "SELECT SUM(total_price) as total_earnings FROM bookings WHERE status IN ('Approved', 'Completed')");
$rev_data = mysqli_fetch_assoc($rev_query);
$total_ksh = $rev_data['total_earnings'] ?? 0;

$active_query = mysqli_query($conn, "SELECT COUNT(*) as active_count FROM bookings WHERE status = 'Approved'");
$active_data = mysqli_fetch_assoc($active_query);
$active_rentals = $active_data['active_count'] ?? 0;

$user_count_query = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total_users FROM bookings");
$user_data = mysqli_fetch_assoc($user_count_query);
$total_customers = $user_data['total_users'] ?? 0;

// 2. FETCH LIVE INVENTORY (All cars and their current status)
$inventory_query = mysqli_query($conn, "SELECT * FROM cars ORDER BY status ASC, brand ASC");
?>

<div class="glass-card" style="margin-bottom: 25px;">
    <h2 style="margin-bottom: 25px;">Business <span style="color: #0066FF;">Analytics</span></h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        
        <div style="background: rgba(40, 167, 69, 0.05); border: 1px solid rgba(40, 167, 69, 0.3); padding: 20px; border-radius: 15px;">
            <p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">TOTAL REVENUE</p>
            <h1 style="color: #28a745; margin: 10px 0;">Ksh <?= number_format($total_ksh) ?></h1>
        </div>

        <div style="background: rgba(0, 102, 255, 0.05); border: 1px solid rgba(0, 102, 255, 0.3); padding: 20px; border-radius: 15px;">
            <p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">ACTIVE RENTALS</p>
            <h1 style="color: #0066FF; margin: 10px 0;"><?= $active_rentals ?></h1>
        </div>

        <div style="background: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.3); padding: 20px; border-radius: 15px;">
            <p style="color: #888; font-size: 11px; margin: 0; font-weight: bold;">TOTAL CUSTOMERS</p>
            <h1 style="color: #ffc107; margin: 10px 0;"><?= $total_customers ?></h1>
        </div>
    </div>
</div>

<div class="glass-card">
    <h3 style="margin-bottom: 20px;">Live <span style="color: #0066FF;">Fleet Inventory</span></h3>
    <table style="width: 100%; border-collapse: collapse; color: white; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 2px solid #333; text-align: left; color: #888;">
                <th style="padding: 12px;">Vehicle</th>
                <th style="padding: 12px;">Daily Rate</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Action Required</th>
            </tr>
        </thead>
        <tbody>
            <?php while($car = mysqli_fetch_assoc($inventory_query)): ?>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 12px;">
                    <strong><?= htmlspecialchars($car['brand']) ?></strong> <?= htmlspecialchars($car['model']) ?>
                </td>
                <td style="padding: 12px;">Ksh <?= number_format($car['price_per_day']) ?></td>
                <td style="padding: 12px;">
                    <?php 
                        $is_avail = ($car['status'] == 'available');
                        $color = $is_avail ? '#28a745' : '#dc3545';
                    ?>
                    <span style="color: <?= $color ?>; font-weight: bold;">
                        ● <?= ucfirst($car['status']) ?>
                    </span>
                </td>
                <td style="padding: 12px;">
                    <?php if (!$is_avail): ?>
                        <a href="?page=all_bookings" style="color: #0066FF; text-decoration: none; font-size: 12px;">View Rental Detail →</a>
                    <?php else: ?>
                        <span style="color: #444; font-size: 12px;">Ready for Hire</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>