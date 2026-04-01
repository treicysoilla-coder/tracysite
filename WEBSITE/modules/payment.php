<?php
// 1. Get the booking ID from the URL [cite: 2026-03-02]
$booking_id = $_GET['id'] ?? 0;

// 2. Fetch details so the user knows exactly what they are paying for [cite: 2026-03-02]
$res = mysqli_query($conn, "SELECT b.*, c.brand, c.model FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = '$booking_id'");
$data = mysqli_fetch_assoc($res);

if ($data):
    // 3. Process the Payment Form [cite: 2026-03-02]
    if (isset($_POST['pay'])) {
        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $payment_details = "M-Pesa ($code)";
        
        // AUTOMATED UPDATE: Sets status to 'Confirmed' so the red badge appears
        $update_query = "UPDATE bookings SET status = 'Confirmed', payment_method = '$payment_details' WHERE id = '$booking_id'";
        
        if (mysqli_query($conn, $update_query)) {
            // SUCCESS: Show alert and redirect back to the bookings dashboard [cite: 2026-03-02]
            echo "<script>
                    alert('Payment Received! Your transaction code ($code) has been submitted for verification.');
                    window.location.href = 'Dashboard.php?page=bookings';
                  </script>";
            exit(); 
        } else {
            echo "<script>alert('Error processing payment. Please try again.');</script>";
        }
    }
?>

<div style="max-width: 500px; margin: 40px auto; background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(15px); color: white; font-family: sans-serif;">
    <h2 style="text-align: center; margin-bottom: 20px;">Secure <span style="color: #0066FF;">Payment</span></h2>
    
    <div style="background: rgba(0, 102, 255, 0.1); padding: 15px; border-radius: 12px; margin-bottom: 25px; border-left: 5px solid #0066FF;">
        <p style="font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px;">Amount Due</p>
        <h2 style="margin: 5px 0; color: #fff;">Ksh <?= number_format($data['total_price']) ?></h2>
        <p style="font-size: 14px; color: #ccc;">Car: <b><?= htmlspecialchars($data['brand']) ?> <?= htmlspecialchars($data['model']) ?></b></p>
    </div>

    <form method="POST" action="">
        <div style="margin-bottom: 25px; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px;">
            <p style="font-weight: bold; margin-bottom: 5px; color: #28a745; font-size: 14px;">M-Pesa Paybill: 888999</p>
            <p style="font-size: 13px; color: #aaa; margin-bottom: 15px;">Account Name: <b style="color: white;">FAST<?= $data['id'] ?></b></p>
            
            <label style="display: block; font-size: 11px; color: #888; margin-bottom: 8px; text-transform: uppercase;">Transaction Code</label>
            <input type="text" name="code" placeholder="Example: SGR456789X" required 
                   style="width: 100%; padding: 12px; background: #000; border: 1px solid #333; color: #0066FF; border-radius: 8px; font-weight: bold; font-family: monospace; font-size: 16px; text-transform: uppercase;">
        </div>

        <button type="submit" name="pay" 
                style="width: 100%; padding: 16px; background: #0066FF; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 14px;">
            CONFIRM & SUBMIT PAYMENT
        </button>
        
        <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #555;">
            Clicking confirm will notify our staff to verify your transaction.
        </p>
    </form>
</div>

<?php 
else:
    echo "<div style='text-align:center; padding:50px; color: white;'><h3>Booking not found.</h3><a href='?page=bookings' style='color: #0066FF;'>Go Back</a></div>";
endif; 
?>