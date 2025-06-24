<?php
session_start();
include 'db.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donation Management System - Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background-image: url('uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      position: relative;
    }
    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5); /* Dark overlay for better text readability */
      z-index: 1;
    }
    .social-media-box {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(106, 27, 154, 0.9); /* Deep purple */
      padding: 10px 20px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 3;
      text-align: center;
    }
    .social-media-box a {
      color: #fff;
      margin: 0 15px;
      text-decoration: none;
      font-size: 1.2rem;
      transition: color 0.3s ease;
    }
    .social-media-box a:hover {
      color: #e1bee7;
    }
    .funds-explanation-box {
      position: fixed;
      top: 50%;
      left: 20px;
      transform: translateY(-50%);
      width: 300px;
      background: rgba(171, 71, 188, 0.9); /* Lighter purple */
      padding: 15px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 2;
      text-align: center;
      color: #fff;
      font-size: 0.95rem;
      display: none; /* Hidden by default */
      max-height: 80vh; /* Limit height to 80% of viewport */
      overflow-y: auto; /* Enable scrolling if content overflows */
    }
    .toggle-button {
      position: relative;
      margin-top: 20px;
      background-color: #ab47bc;
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 10px 20px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .toggle-button:hover {
      background-color: #8e24aa;
    }
    .login-container {
      width: 400px;
      padding: 2.5rem 2rem;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      text-align: center;
      position: relative;
      z-index: 2;
      backdrop-filter: blur(5px);
      margin-top: 60px; /* Adjusted for social box only */
    }
    .login-container h2 {
      color: #6a1b9a; /* Deep purple */
      font-weight: 700;
      margin-bottom: 1rem;
    }
    .login-container h5 {
      color: #ab47bc; /* Lighter purple */
      margin-bottom: 1.5rem;
    }
    .form-control {
      border-radius: 25px;
      border: 2px solid #ce93d8; /* Purple border */
      background-color: #f3e5f5; /* Light purple background */
      color: #4a148c; /* Dark purple text */
    }
    .form-control:focus {
      border-color: #7b1fa2; /* Vibrant purple on focus */
      box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25); /* Purple shadow */
    }
    .btn-login {
      border-radius: 25px;
      padding: 12px 30px;
      font-size: 1.1rem;
      background-color: #8e24aa; /* Bright purple */
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #6a1b9a; /* Darker purple on hover */
    }
    .btn-donor {
      border-radius: 25px;
      padding: 10px 20px;
      font-size: 1rem;
      background-color: #ab47bc; /* Lighter purple */
      border: none;
      color: #fff;
      text-decoration: none;
      display: inline-block;
      margin-top: 1rem;
      transition: background-color 0.3s ease;
    }
    .btn-donor:hover {
      background-color: #8e24aa; /* Bright purple on hover */
    }
    .alert-danger {
      background-color: #f8bbd0; /* Light pink for error */
      color: #b71c1c;
      border-radius: 15px;
      border: none;
    }
    /* Donation Counter Styles */
    .donation-counter {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 300px;
      background: linear-gradient(135deg, #ab47bc, #6a1b9a); /* Gradient background */
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      z-index: 3;
      padding: 20px;
      text-align: center;
      backdrop-filter: blur(5px);
      color: #fff;
      transition: transform 0.3s ease;
    }
    .donation-counter:hover {
      transform: scale(1.05); /* Slight scale on hover for interactivity */
    }
    .donation-counter h4 {
      color: #fff;
      margin-bottom: 15px;
      font-weight: 700;
      text-transform: uppercase;
    }
    .donation-counter p {
      color: #e1bee7;
      font-size: 1.3rem;
      margin: 5px 0;
      font-weight: 600;
    }
    .funds-allocation {
      margin-top: 15px;
      font-size: 0.9rem;
    }
    .funds-allocation p {
      margin: 5px 0;
      color: #fff;
    }
    .progress-bar-container {
      margin-top: 15px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      height: 20px;
      overflow: hidden;
    }
    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #ffeb3b, #ff9800); /* Yellow to orange gradient */
      width: 0%;
      transition: width 0.5s ease;
      border-radius: 10px;
      text-align: right;
      padding-right: 10px;
      color: #333;
      font-size: 0.9rem;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="social-media-box">
  <a href="https://twitter.com/DonationSys2025" target="_blank"><i class="fab fa-twitter"></i> @DonationSys2025</a>
  <a href="https://facebook.com/DonationManagementSystem" target="_blank"><i class="fab fa-facebook-f"></i> DonationMgmt</a>
  <a href="https://instagram.com/donationsystem2025" target="_blank"><i class="fab fa-instagram"></i> @donationsystem2025</a>
</div>

<div class="funds-explanation-box" id="fundsExplanationBox">
  <p><strong>Your Donations at Work</strong></p>
  <p>Your generous contributions are making a significant impact across multiple sectors. We allocate 50% of funds to education initiatives, supporting underprivileged students with scholarships, school supplies, and digital learning tools. This has enabled over 1,000 students to continue their education in the past year alone.</p>
  <p>Another 30% goes to healthcare programs, funding medical camps, providing essential medicines, and improving rural clinic infrastructure. Last month, we assisted 500 families with free health check-ups and distributed critical supplies to remote areas.</p>
  <p>The remaining 20% is dedicated to disaster relief efforts, offering immediate aid such as food, water, and shelter during natural calamities. Recently, our team provided relief to 200 households affected by floods, ensuring their safety and recovery.</p>
  <p><strong>Our Impact</strong></p>
  <p>Every dollar you donate helps us expand these efforts. We partner with local organizations to ensure funds reach those in need efficiently. Our transparent process allows donors to see the real change their support creates, from building schools to saving lives.</p>
  <p><strong>Call to Action</strong></p>
  <p>Join us in making a difference! Log in as a donor to contribute or spread the word through our social media channels. Your support can transform communities and provide hope where it’s needed most. Together, we can achieve more!</p>
</div>

<div class="login-container">
    <h2>Donation Management System</h2>
    <h5>Admin Login</h5>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button type="submit" name="login" class="btn btn-login w-100">Login</button>
    </form>

    <a href="donor_login.php" class="btn-donor">Donor Login</a>
    <button class="toggle-button" onclick="toggleFundsBox()">Show Funds Info</button>
</div>

<!-- Donation Counter -->
<div class="donation-counter" id="donationCounter">
  <h4>Total Donations</h4>
  <p id="donationTotal">$0.00</p>
  <p id="lastUpdated">Last updated: Loading...</p>
  <div class="funds-allocation">
    <p><strong>Where Funds Go:</strong></p>
    <p>50% Education</p>
    <p>30% Healthcare</p>
    <p>20% Disaster Relief</p>
  </div>
  <div class="progress-bar-container">
    <div class="progress-bar" id="progressBar">0%</div>
  </div>
</div>

<script>
  function updateDonationCounter() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        const data = JSON.parse(this.responseText);
        const total = parseFloat(data.total);
        document.getElementById('donationTotal').textContent = `$${total.toFixed(2)}`;
        document.getElementById('lastUpdated').textContent = `Last updated: ${data.timestamp}`;
        
        // Calculate progress (goal set to $10,000)
        const goal = 10000;
        let percentage = (total / goal) * 100;
        percentage = Math.min(100, Math.max(0, percentage)); // Cap at 100%
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = `${percentage}%`;
        progressBar.textContent = `${percentage.toFixed(1)}%`;
      }
    };
    xhttp.open("GET", "get_total_donations.php", true);
    xhttp.send();
  }

  function toggleFundsBox() {
    const box = document.getElementById('fundsExplanationBox');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
  }

  // Update every 10 seconds
  updateDonationCounter();
  setInterval(updateDonationCounter, 10000);
</script>

</body>
</html>