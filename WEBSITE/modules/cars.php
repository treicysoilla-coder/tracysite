<?php
$role = $_SESSION['role'] ?? 'client'; 
$user_id = $_SESSION['user_id'] ?? 0;

// 1. Staff Logic: Add & Delete
if ($role == 'staff') {
    if (isset($_GET['delete_id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
        
        // Delete image file if it exists
        $img_res = mysqli_query($conn, "SELECT car_image FROM cars WHERE id = '$id'");
        if ($img_data = mysqli_fetch_assoc($img_res)) {
            if ($img_data['car_image'] != 'default_car.jpg' && file_exists("car_images/" . $img_data['car_image'])) {
                unlink("car_images/" . $img_data['car_image']);
            }
        }
        
        mysqli_query($conn, "DELETE FROM bookings WHERE car_id = '$id'");
        mysqli_query($conn, "DELETE FROM cars WHERE id = '$id'");
        echo "<script>window.location.href='?page=dashboard';</script>";
        exit();
    }

    if (isset($_POST['add_car'])) {
        $brand = mysqli_real_escape_string($conn, $_POST['brand']);
        $model = mysqli_real_escape_string($conn, $_POST['model']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $image_name = 'default_car.jpg';

        if (!empty($_FILES['car_image']['name'])) {
            $image_name = time() . '_' . basename($_FILES["car_image"]["name"]);
            move_uploaded_file($_FILES["car_image"]["tmp_name"], "car_images/" . $image_name);
        }

        mysqli_query($conn, "INSERT INTO cars (brand, model, price_per_day, status, car_image) 
                            VALUES ('$brand', '$model', '$price', 'available', '$image_name')");
        echo "<script>window.location.href='?page=dashboard';</script>"; 
        exit();
    }
}

$result = mysqli_query($conn, "SELECT * FROM cars");
?>

<style>
    .cars-page { background: url('bus.jpg') no-repeat center center/cover; min-height: 100vh; padding: 30px; position: relative; color: white; font-family: 'Montserrat', sans-serif; }
    .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1; }
    .relative-content { position: relative; z-index: 2; }
    .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; display: flex; flex-direction: column; }
    .car-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
    .btn { padding: 12px; border-radius: 8px; font-weight: bold; display: block; text-align: center; border: none; cursor: pointer; text-decoration: none; transition: 0.3s; }
    .btn-add { background: #28a745; color: white; }
    .btn-delete { background: #dc3545; color: white; margin-top: 10px; font-size: 13px; }
    /* Style for the new Book Button */
    .btn-book { background: #0066FF; color: white; margin-top: 15px; }
    .btn-book:hover { background: #0052cc; box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3); }
    input { background: rgba(0,0,0,0.5); border: 1px solid #444; color: white; padding: 10px; border-radius: 5px; }
</style>

<div class="cars-page">
    <div class="overlay"></div>
    <div class="relative-content">
        <h2>Vehicle <span style="color: #0066FF;">Fleet</span></h2>

        <?php if ($role == 'staff'): ?>
            <div class="glass-card" style="margin-bottom: 30px;">
                <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="brand" placeholder="Brand" required>
                    <input type="text" name="model" placeholder="Model" required>
                    <input type="number" name="price" placeholder="Price/Day" required>
                    <input type="file" name="car_image" accept="image/*" style="width: 200px;">
                    <button type="submit" name="add_car" class="btn btn-add">Add Car</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="car-grid">
            <?php while($car = mysqli_fetch_assoc($result)): ?>
                <div class="glass-card">
                    <?php 
                        $img = (!empty($car['car_image']) && file_exists("car_images/".$car['car_image'])) 
                               ? "car_images/".$car['car_image'] : "assets/default_car.jpg";
                    ?>
                    <img src="<?= $img ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px; margin-bottom: 10px;">
                    
                    <h3 style="margin: 0;"><?= htmlspecialchars($car['brand'] ?? 'Unknown') ?> <?= htmlspecialchars($car['model'] ?? '') ?></h3>
                    <p style="color: #0066FF; font-weight: bold;">Ksh <?= number_format($car['price_per_day'] ?? 0) ?> / day</p>
                    <p style="font-size: 12px;">Status: <span style="color: <?= ($car['status'] == 'available') ? '#28a745' : '#dc3545' ?>;"><?= ucfirst($car['status'] ?? 'N/A') ?></span></p>

                    <?php if ($role == 'staff'): ?>
                        <a href="?page=dashboard&delete_id=<?= $car['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete vehicle?')">Delete Vehicle</a>
                    <?php else: ?>
                        <a href="?page=book_car&id=<?= $car['id'] ?>" class="btn btn-book">BOOK</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>