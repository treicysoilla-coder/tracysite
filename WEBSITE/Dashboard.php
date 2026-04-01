<?php
session_start();
include('db_connection.php');

// 1. Role & Identity Check
$role = $_SESSION['role'] ?? 'client'; 
$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? 0;

// --- 2. ACTION HANDLER (Processes Approvals & Returns) ---
if (isset($_GET['approve_id']) && $role == 'staff') {
    $id = intval($_GET['approve_id']);
    mysqli_query($conn, "UPDATE bookings SET status = 'Approved' WHERE id = $id");
    header("Location: Dashboard.php?page=all_bookings");
    exit();
}

if (isset($_GET['return_id']) && $role == 'staff') {
    $id = intval($_GET['return_id']);
    // Update booking and optionally set car back to 'available'
    mysqli_query($conn, "UPDATE bookings SET status = 'Returned' WHERE id = $id");
    header("Location: Dashboard.php?page=all_bookings");
    exit();
}

// 3. Fetch Message Count (Staff Only)
$msg_count = 0;
if ($role == 'staff') {
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages");
    if ($count_result) {
        $msg_data = mysqli_fetch_assoc($count_result);
        $msg_count = $msg_data['total'];
    }
}

// 4. Page Routing
$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FASTCAR | <?= ucfirst($role) ?> Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: #0b0b0b; display: flex; color: white; height: 100vh; overflow: hidden; }

        /* Sidebar Styling */
        .sidebar { width: 260px; background: #121212; display: flex; flex-direction: column; padding: 30px 20px; border-right: 1px solid #222; }
        .logo { display: flex; align-items: center; gap: 10px; color: white; font-size: 20px; font-weight: bold; margin-bottom: 50px; text-decoration: none; }
        .nav-links { list-style: none; flex-grow: 1; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: #888; text-decoration: none; display: flex; align-items: center; gap: 15px; padding: 12px; border-radius: 10px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: #0066FF; color: white; }
        .badge { background: #ff4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 50%; font-weight: bold; margin-left: auto; }

        /* Main Content */
        .main-content { flex-grow: 1; position: relative; overflow-y: auto; }
        .top-header { padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; background: rgba(11, 11, 11, 0.8); backdrop-filter: blur(10px); }
        .search-bar { background: #1e1e1e; border: none; padding: 10px 20px; border-radius: 8px; color: white; width: 350px; }
        .user-profile { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .profile-icon { width: 35px; height: 35px; background: #0066FF; border-radius: 50%; display: grid; place-items: center; font-weight: bold; }

        /* Dynamic View Styling */
        .view-section { position: relative; min-height: calc(100vh - 80px); padding: 40px; background: url('bus.jpg') no-repeat center center/cover; }
        .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1; }
        .inner-content { position: relative; z-index: 2; width: 100%; }

        /* Tables & UI Components */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
        .data-table th { background: #0066FF; color: white; padding: 15px; text-align: left; font-size: 12px; text-transform: uppercase; }
        .data-table td { padding: 15px; border-bottom: 1px solid #222; font-size: 14px; }
        
        .btn-action { padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; color: white; transition: 0.3s; }
        .btn-approve { background: #28a745; }
        .btn-return { background: #dc3545; }
        .status-pill { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="?page=dashboard" class="logo"><span style="color: #0066FF;">🚗</span> FASTCAR</a>
        <ul class="nav-links">
            <li><a href="?page=dashboard" class="<?= ($page == 'dashboard') ? 'active' : '' ?>">
                <?= ($role == 'staff') ? 'Manage Fleet' : 'Available Cars' ?>
            </a></li>

            <?php if ($role == 'staff'): ?>
                <li><a href="?page=all_bookings" class="<?= $page == 'all_bookings' ? 'active' : '' ?>">Rental History</a></li>
                <li><a href="?page=revenue" class="<?= $page == 'revenue' ? 'active' : '' ?>">Revenue & Stats</a></li>
                <li><a href="?page=messages" class="<?= $page == 'messages' ? 'active' : '' ?>">Messages <?= $msg_count > 0 ? "<span class='badge'>$msg_count</span>" : "" ?></a></li>
            <?php else: ?>
                <li><a href="?page=bookings" class="<?= ($page == 'bookings' || $page == 'payment') ? 'active' : '' ?>">My Bookings</a></li>
            <?php endif; ?>

            <li><a href="?page=profile" class="<?= $page == 'profile' ? 'active' : '' ?>">Profile</a></li>
        </ul>
        <a href="logout.php" style="color: #ff4444; margin-top: auto; padding: 12px; text-decoration: none; font-weight: bold;">Logout</a>
    </div>

    <div class="main-content">
        <div class="top-header">
            <input type="text" class="search-bar" placeholder="Search brands or models...">
            <div class="user-profile">
                <span style="color: #888;"><?= ucfirst($role) ?>:</span>
                <span><?= htmlspecialchars($username) ?></span>
                <div class="profile-icon"><?= strtoupper(substr($username, 0, 1)) ?></div>
            </div>
        </div>

        <div class="view-section">
            <div class="overlay"></div>
            <div class="inner-content">
                <?php 
                switch($page) {
                    case 'all_bookings': // STAFF RENTAL HISTORY
                        if($role != 'staff') break;
                        echo "<h2>Rental History</h2>";
                        echo "<table class='data-table'>
                                <thead><tr><th>User ID</th><th>Car</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>";
                        $res = mysqli_query($conn, "SELECT * FROM bookings ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td>{$row['user_id']}</td>
                                    <td>Car ID: {$row['car_id']}</td>
                                    <td>{$row['booking_date']}</td>
                                    <td><span class='status-pill' style='background:rgba(255,187,0,0.2); color:#FFBB00;'>{$row['status']}</span></td>
                                    <td>";
                            if($row['status'] == 'Pending') {
                                echo "<a href='Dashboard.php?page=all_bookings&approve_id={$row['id']}' class='btn-action btn-approve'>Approve</a>";
                            } elseif($row['status'] == 'Approved') {
                                echo "<a href='Dashboard.php?page=all_bookings&return_id={$row['id']}' class='btn-action btn-return'>Return</a>";
                            } else {
                                echo "<span style='color:#666;'>No Actions</span>";
                            }
                            echo "</td></tr>";
                        }
                        echo "</tbody></table>";
                        break;

                    case 'bookings': // CLIENT MY BOOKINGS
                        echo "<h2>My Bookings</h2>";
                        echo "<table class='data-table'>
                                <thead><tr><th>Car Details</th><th>Booking Date</th><th>Status</th></tr></thead><tbody>";
                        $res = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id = '$user_id'");
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td>Car ID: {$row['car_id']}</td>
                                    <td>{$row['booking_date']}</td>
                                    <td><span class='status-pill'>{$row['status']}</span></td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                        break;

                    case 'dashboard':
                    default:
                        echo "<h2>Welcome to FastCar</h2><p style='color:#888;'>Select an option from the sidebar to get started.</p>";
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>