<?php
$car_id = $_GET['id'] ?? 0;
$res = mysqli_query($conn, "SELECT * FROM cars WHERE id = '$car_id'");
$car = mysqli_fetch_assoc($res);

if ($car) {
?>
<div style="max-width: 400px; margin: 20px auto; background: #161616; padding: 30px; border-radius: 15px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    <h2 style="color: white; margin-bottom: 20px;">Book <span style="color: #0066FF;"><?= $car['brand'] ?></span></h2>
    
    <form method="POST">
        <label style="color: #888; font-size: 11px; display: block; margin-bottom: 5px;">PICKUP DATE</label>
        <input type="date" name="p_date" required style="width: 100%; padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; margin-bottom: 20px;">
        
        <label style="color: #888; font-size: 11px; display: block; margin-bottom: 5px;">RETURN DATE</label>
        <input type="date" name="r_date" required style="width: 100%; padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; margin-bottom: 25px;">
        
        <button type="submit" name="confirm" style="width: 100%; padding: 15px; background: #0066FF; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">CONFIRM RENTAL</button>
    </form>
</div>
<?php
    if (isset($_POST['confirm'])) {
        $start = $_POST['p_date']; $end = $_POST['r_date'];
        $days = max(1, ceil((strtotime($end) - strtotime($start)) / 86400));
        $total = $days * $car['price_per_day'];
        
        $sql = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status) VALUES ('$user_id', '$car_id', '$start', '$end', '$total', 'Pending')";
        if (mysqli_query($conn, $sql)) {
            mysqli_query($conn, "UPDATE cars SET status = 'rented' WHERE id = '$car_id'");
            echo "<script>alert('Booked!'); window.location.href='?page=bookings';</script>";
        }
    }
}
?>