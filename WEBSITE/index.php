<?php
session_start();
require_once('db.php');

// 1. Handle Contact Form Submission
$message_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into contact_messages table
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$user_message')";
    
    if (mysqli_query($conn, $sql)) {
        $message_sent = true;
    }
}

// 2. Fetch Available Cars
$query = "SELECT * FROM cars WHERE status = 'available' ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$company_name = "CAR RENTAL MANAGEMENT SYSTEM";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $company_name; ?> | Premium Rentals</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #0066FF;
            --dark-bg: #000000;
            --light-bg: #f8f9fa;
            --glass: rgba(255, 255, 255, 0.05);
        }

        /* Smooth Scrolling */
        html { scroll-behavior: smooth; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: var(--light-bg); font-family: 'Poppins', sans-serif; color: #333; overflow-x: hidden; }

        /* Navigation */
        nav { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 18px 8%; background: var(--dark-bg); color: white; 
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .logo h2 { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 18px; letter-spacing: -0.5px; text-transform: uppercase; }
        .logo span { color: var(--primary-blue); }
        
        .menu { display: flex; align-items: center; gap: 30px; }
        .menu a { color: #fff; text-decoration: none; font-weight: 500; font-size: 13px; transition: 0.3s; opacity: 0.85; }
        .menu a:hover { color: var(--primary-blue); opacity: 1; }
        
        /* Updated Button Styles */
        .btn-auth { background: var(--primary-blue); color: white !important; padding: 10px 22px; border-radius: 6px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 102, 255, 0.3); }
        .btn-logout { color: #ff4d4d !important; border: 1px solid rgba(255, 77, 77, 0.3); padding: 7px 15px; border-radius: 6px; }

        /* Hero Section */
        .hero { 
            height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center; 
            text-align: center; color: white; position: relative;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.85)), url('bus.jpg') center/cover no-repeat;
        }
        .hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(2.5rem, 6vw, 4rem); font-weight: 800; margin-bottom: 10px; }
        .hero h1 span { color: var(--primary-blue); }
        .hero p { font-size: 1.2rem; max-width: 700px; opacity: 0.9; font-weight: 300; }

        /* Fleet Grid */
        .featured-cars { padding: 90px 8%; background: #fff; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-size: 2.2rem; font-family: 'Montserrat', sans-serif; text-transform: uppercase; font-weight: 700; }
        .section-title div { height: 4px; width: 60px; background: var(--primary-blue); margin: 12px auto; border-radius: 2px; }

        .cars-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 40px; max-width: 1400px; margin: 0 auto; }
        .car-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #eee; transition: 0.4s ease-in-out; }
        .car-card:hover { transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        
        .img-container { width: 100%; height: 220px; overflow: hidden; background: #f0f0f0; }
        .car-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
        .car-card:hover img { transform: scale(1.1); }
        
        .car-info { padding: 25px; }
        .car-info h3 { font-size: 1.4rem; font-weight: 700; margin-bottom: 15px; }
        .price-wrapper { display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #f5f5f5; }
        .price { color: var(--primary-blue); font-weight: 800; font-size: 1.5rem; }
        .price span { font-size: 13px; color: #888; font-weight: 400; }
        
        .btn-book { background: var(--dark-bg); color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .btn-book:hover { background: var(--primary-blue); }

        /* Contact Section */
        .contact-section { padding: 100px 8%; background: #d68585; border-top: 1px solid #eee; }
        .contact-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 60px; max-width: 1200px; margin: 0 auto; }
        
        .contact-info h3 { font-family: 'Montserrat', sans-serif; font-size: 2rem; margin-bottom: 20px; }
        .contact-info p { color: #666; margin-bottom: 40px; font-size: 1.1rem; }
        .info-item { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .info-item b { color: var(--primary-blue); font-size: 1.2rem; }

        .contact-form { background: white; padding: 45px; border-radius: 25px; box-shadow: 0 15px 50px rgba(0,0,0,0.05); }
        .contact-form input, .contact-form textarea { width: 100%; padding: 15px; margin-bottom: 20px; border: 1px solid #e0e0e0; border-radius: 10px; font-family: inherit; font-size: 15px; background: #fafafa; }
        .btn-send { width: 100%; background: var(--primary-blue); color: white; border: none; padding: 16px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-send:hover { background: #0052cc; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,102,255,0.3); }

        /* Success Message */
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; border: 1px solid #c3e6cb; }

        /* Footer */
        footer { background: var(--dark-bg); color: white; padding: 80px 8% 40px; text-align: center; }
        .footer-logo { margin-bottom: 20px; }
        .copyright { opacity: 0.4; font-size: 13px; margin-top: 50px; border-top: 1px solid #222; padding-top: 30px; }
    </style>
</head>
<body>

    <nav>
        <div class="logo"><h2>CAR RENTAL <span>SYSTEM</span></h2></div>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="#fleet">The Fleet</a>
            <a href="#contact">Contact Support</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="Dashboard.php">Dashboard</a>
                <a href="logout.php" class="btn-logout" onclick="return confirm('Logout of System?')">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signin.php" class="btn-auth">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <h1>DRIVE YOUR <span>DREAMS</span></h1>
        <p>Your journey begins with the perfect vehicle. Explore our premium selection of cars managed with professional excellence.</p>
    </header>

    <section class="featured-cars" id="fleet">
        <div class="section-title">
            <h2>Our Managed Fleet</h2>
            <div></div>
        </div>

        <div class="cars-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($car = mysqli_fetch_assoc($result)): ?>
                    <article class="car-card">
                        <div class="img-container">
                            <?php 
                                $img = $car['car_image'] ? "car_images/".$car['car_image'] : "assets/default.jpg";
                            ?>
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($car['model']) ?>">
                        </div>
                        <div class="car-info">
                            <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                            <p style="color:#777; font-size: 13px; margin-bottom: 15px;">Reliable • Fully Insured • GPS Included</p>
                            
                            <div class="price-wrapper">
                                <div class="price">Ksh <?= number_format($car['price_per_day']) ?> <span>/ day</span></div>
                                <a href="book.php?id=<?= $car['id'] ?>" class="btn-book">Details</a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px; border: 2px dashed #eee; border-radius: 20px;">
                    <p style="color:#aaa;">Our fleet is currently out on the road. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="contact-container">
            <div class="contact-info">
                <h3>Get In Touch</h3>
                <p>Have a question about a specific vehicle or a long-term rental? Send our management staff a message.</p>
                <div class="info-item"><b>📍</b> <span>101 Management Plaza, Nairobi</span></div>
                <div class="info-item"><b>📞</b> <span>+254 712 345 678</span></div>
                <div class="info-item"><b>✉</b> <span>support@carrentalsystem.com</span></div>
            </div>

            <div class="contact-form">
                <?php if ($message_sent): ?>
                    <div class="alert-success">✔ Your message was sent successfully!</div>
                <?php endif; ?>

                <form action="index.php#contact" method="POST">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <textarea name="message" rows="5" placeholder="Tell us what you need..." required></textarea>
                    <button type="submit" name="send_message" class="btn-send">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-logo"><h2>CAR RENTAL <span>SYSTEM</span></h2></div>
        <p style="opacity: 0.6; font-size: 14px; max-width: 600px; margin: 0 auto;">Dedicated to providing the best car rental management experience in Kenya. Your safety and comfort are our priority.</p>
        <div class="copyright">
            &copy; <?= date('Y') ?> <?php echo $company_name; ?>. Managed by Thuli9.
        </div>
    </footer>

</body>
</html>