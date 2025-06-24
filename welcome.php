<?php
// Contact form handling
$contactMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contactSubmit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = "your-email@example.com"; // Replace with your actual email
    $subject = "New Contact Form Message";
    $body = "From: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        $contactMsg = "Your message has been sent successfully!";
    } else {
        $contactMsg = "Failed to send message. Try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome | Donation Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
      background: url('uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg') no-repeat center center fixed;
      background-size: cover;
      background-color: #3e1f72;
    }
    .hero {
      color: white;
      height: 100vh;
      text-align: center;
      padding-top: 130px;
      background: rgba(0,0,0,0.5);
    }
    .logo {
      position: fixed;
      top: 15px;
      left: 30px;
      z-index: 10;
    }
    .logo img {
      height: 45px;
      filter: brightness(0) invert(1);
    }
    .nav {
      position: fixed;
      top: 20px;
      right: 30px;
      z-index: 10;
    }
    .nav a {
      color: white;
      background-color: rgba(255, 255, 255, 0.2);
      padding: 10px 18px;
      margin-left: 12px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }
    .nav a:hover {
      background-color: rgba(255, 255, 255, 0.4);
      transform: scale(1.05);
    }
    .title-box {
      background: rgba(255, 255, 255, 0.95);
      color: #6a1b9a;
      padding: 12px 35px;
      display: inline-block;
      border-radius: 12px;
      font-weight: 800;
      font-size: 1.8rem;
      margin-bottom: 25px;
      font-family: 'Trebuchet MS', sans-serif;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .welcome-box {
      max-width: 800px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 50px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    }
    .welcome-text h1 {
      font-size: 4.5rem;
      font-weight: 900;
      letter-spacing: 2px;
      color: #ffffff;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }
    .btn-start {
      background: #ffffff;
      color: #6a1b9a;
      border-radius: 30px;
      padding: 12px 25px;
      font-weight: 600;
      border: none;
      margin-top: 25px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .btn-start:hover {
      background: #f1f1f1;
    }
    .terms {
      margin-top: 20px;
      font-size: 0.9rem;
    }
    .popup {
      display: none;
      position: fixed;
      top: 10%;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      background: linear-gradient(to right, #6a1b9a, #3e1f72);
      color: white;
      padding: 30px;
      border-radius: 15px;
      width: 80%;
      max-width: 800px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      animation: slideDown 0.5s ease;
    }
    @keyframes slideDown {
      from { top: -100px; opacity: 0; }
      to { top: 10%; opacity: 1; }
    }
    .popup h2 { margin-bottom: 20px; }
    .popup .close-btn {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 24px;
      cursor: pointer;
      color: #fff;
    }
    footer {
      padding: 20px;
      background: #0d1a2d;
      color: white;
      text-align: center;
    }
  </style>
</head>
<body>

<!-- Logo -->
<div class="logo">
  <img src="https://i.ibb.co/kXNSdNd/logo.png" alt="Logo">
</div>

<!-- Navigation -->
<div class="nav">
  <a onclick="openPopup('about')">About Us</a>
  <a onclick="openPopup('services')">Services</a>
  <a onclick="openPopup('contact')">Contact</a>
</div>

<!-- Home Section -->
<section id="home" class="hero">
  <div class="title-box">DONATION MANAGEMENT SYSTEM</div>
  <div class="welcome-box">
    <div class="welcome-text">
      <h1>WELCOME</h1>
      <h4>to our Donation Management Community</h4>
      <p class="mt-3">A place to manage donations, donors, and community impact efficiently.</p>
      <form method="get" action="login.php" onsubmit="return checkTerms()">
        <div class="terms text-white">
          <input type="checkbox" id="agree" required>
          <label for="agree">
            I agree to the <a href="#" onclick="alert('Terms and Conditions: By using this platform, you agree to responsibly manage donor data, respect privacy, and comply with all applicable laws. Misuse will result in revoked access.');" style="color:#ccc;">Terms and Conditions</a>
          </label>
        </div>
        <button type="submit" class="btn btn-start">Join Us Now</button>
      </form>
    </div>
  </div>
</section>

<!-- Popups -->
<div id="popup-about" class="popup">
  <div class="close-btn" onclick="closePopup()">&times;</div>
  <h2>About Us</h2>
  <p><strong>Donation Management System</strong> supports NGOs and nonprofits in managing donations and donors effectively. With automation, real-time reporting, and easy dashboards, we simplify donation tracking and engagement while maintaining transparency and security.</p>
</div>

<div id="popup-services" class="popup">
  <div class="close-btn" onclick="closePopup()">&times;</div>
  <h2>Our Services</h2>
  <ul>
    <li>✅ Real-time donation monitoring</li>
    <li>✅ Donor and volunteer insights</li>
    <li>✅ Secure login system for admins</li>
    <li>✅ Custom dashboards and reporting</li>
    <li>✅ Event coordination & follow-up tools</li>
  </ul>
</div>

<div id="popup-contact" class="popup">
  <div class="close-btn" onclick="closePopup()">&times;</div>
  <h2>Contact Us</h2>
  <?php if ($contactMsg): ?>
    <div class="alert alert-info"><?= $contactMsg ?></div>
  <?php endif; ?>
  <p><strong>Email:</strong> support@donationsystem.org<br><strong>Phone:</strong> +91-98765-43210</p>
  <form method="post">
    <input type="text" name="name" class="form-control mb-2" placeholder="Your Name" required>
    <input type="email" name="email" class="form-control mb-2" placeholder="Your Email" required>
    <textarea name="message" class="form-control mb-2" placeholder="Your Message" rows="4" required></textarea>
    <button name="contactSubmit" class="btn btn-light">Send Message</button>
  </form>
</div>

<!-- Footer -->
<footer>
  Designed by OpenAI | © <?= date('Y') ?> Donation Management
</footer>

<!-- Scripts -->
<script>
function checkTerms() {
  const checkbox = document.getElementById('agree');
  if (!checkbox.checked) {
    alert("You must agree to the terms and conditions.");
    return false;
  }
  return true;
}
function openPopup(id) {
  closePopup();
  document.getElementById('popup-' + id).style.display = 'block';
}
function closePopup() {
  const popups = document.querySelectorAll('.popup');
  popups.forEach(p => p.style.display = 'none');
}
</script>

</body>
</html>
