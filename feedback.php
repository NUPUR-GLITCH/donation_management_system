<?php
session_start();
// Assume donor is logged in; set mock session data
if (!isset($_SESSION['donor'])) {
    $_SESSION['donor'] = true;
    $_SESSION['donor_id'] = 1; // Mock donor ID
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Management System - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #6a1b9a, #ab47bc);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding: 20px 0;
            z-index: 3;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }
        .sidebar h3 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
            color: #e1bee7;
            text-transform: uppercase;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidebar ul li:hover, .sidebar ul li.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .sidebar ul li i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            backdrop-filter: blur(5px);
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .dashboard-container h2 {
            color: #6a1b9a;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }
        .feedback-form {
            margin-top: 20px;
            text-align: left;
        }
        .feedback-form label {
            color: #4a148c;
            font-weight: 500;
        }
        .feedback-form textarea {
            width: 100%;
            border-radius: 10px;
            border: 2px solid #ce93d8;
            background: #f3e5f5;
            padding: 10px;
            resize: vertical;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ffeb3b;
            color: #333;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 4;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        .contact-info {
            margin-top: 40px;
            background: #f3e5f5;
            padding: 15px;
            border-radius: 15px;
            color: #4a148c;
            font-size: 1rem;
            line-height: 1.6;
        }
        .contact-info i {
            margin-right: 8px;
            color: #6a1b9a;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Donor Panel</h3>
    <ul>
        <li data-section="donor_dashboard"><i class="fas fa-home"></i> Dashboard</li>
        <li data-section="history"><i class="fas fa-history"></i> History</li>
        <li data-section="goals"><i class="fas fa-chart-line"></i> Goals</li>
        <li class="active" data-section="feedback"><i class="fas fa-comment"></i> Feedback</li>
        <li data-section="profile"><i class="fas fa-user"></i> Profile</li>
        <li><a href="logout.php" class="btn-donor"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="dashboard-container">
        <h2>Feedback</h2>
        <div class="feedback-form">
            <h5>Share Your Feedback</h5>
            <label for="feedback">Your Thoughts:</label>
            <textarea id="feedback" rows="4" placeholder="Enter your feedback here..."></textarea>
            <button type="button" class="btn btn-primary w-100 mt-2" id="submitFeedback">Submit Feedback</button>
        </div>

        <div class="contact-info">
            <h5 class="mt-4"><i class="fas fa-headset"></i> Contact Support</h5>
            <p><i class="fab fa-whatsapp"></i> WhatsApp: <a href="https://wa.me/919876543210" target="_blank">+91 98765 43210</a></p>
            <p><i class="fas fa-envelope"></i> Email: <a href="mailto:support@donationportal.org">support@donationportal.org</a></p>
        </div>
    </div>
</div>

<div class="notification" id="notification"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showNotification(message) {
    const notification = $('#notification');
    notification.text(message).fadeIn(500).delay(3000).fadeOut(500);
}

$('#submitFeedback').on('click', function() {
    const feedback = $('#feedback').val();
    if (feedback.trim()) {
        showNotification('Thank you for your feedback!');
        $('#feedback').val('');
    } else {
        showNotification('Please enter your feedback.');
    }
});

function showSection(section) {
    window.location.href = section + '.php';
}

$('.sidebar ul li[data-section]').on('click', function() {
    const section = $(this).data('section');
    showSection(section);
});
</script>

</body>
</html>
