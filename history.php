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
    <title>Donation Management System - History</title>
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
            transition: transform 0.3s ease;
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
        .donation-history table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .donation-history th, .donation-history td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #4a148c;
        }
        .donation-history th {
            background: linear-gradient(90deg, #ce93d8, #ab47bc);
            color: #fff;
            font-weight: 600;
        }
        .funds-explanation-box {
            position: fixed;
            top: 50%;
            left: 280px;
            transform: translateY(-50%);
            width: 300px;
            background: rgba(171, 71, 188, 0.9);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
            text-align: center;
            color: #fff;
            font-size: 0.95rem;
            display: none;
            max-height: 80vh;
            overflow-y: auto;
        }
        .toggle-button {
            margin-top: 20px;
            background: linear-gradient(90deg, #ab47bc, #ce93d8);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .toggle-button:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #ce93d8, #ab47bc);
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
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Donor Panel</h3>
    <ul>
        <li data-section="donor_dashboard"><i class="fas fa-home"></i> Dashboard</li>
        <li class="active" data-section="history"><i class="fas fa-history"></i> History</li>
        <li data-section="goals"><i class="fas fa-chart-line"></i> Goals</li>
        <li data-section="feedback"><i class="fas fa-comment"></i> Feedback</li>
        <li data-section="profile"><i class="fas fa-user"></i> Profile</li>
        <li><a href="logout.php" class="btn-donor"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="dashboard-container">
        <h2>Donation History</h2>
        <div class="donation-history">
            <table id="donationTable">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <button class="toggle-button mt-3" onclick="toggleFundsBox()">Show Funds Info</button>
    </div>
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

<div class="notification" id="notification"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
let mockDonations = JSON.parse(localStorage.getItem('donations')) || [];
let totalDonated = mockDonations.reduce((sum, d) => sum + d.amount, 0);

function showNotification(message) {
    const notification = $('#notification');
    notification.text(message).fadeIn(500).delay(3000).fadeOut(500);
}

function updateDonationHistory() {
    const tbody = $('#donationTable tbody').empty();
    mockDonations.slice(0, 5).forEach(donation => {
        tbody.append(`
            <tr>
                <td>$${donation.amount.toFixed(2)}</td>
                <td>${donation.date}</td>
                <td>${donation.recurring ? 'Recurring' : 'One-time'}</td>
            </tr>
        `);
    });
}

function toggleFundsBox() {
    const box = document.getElementById('fundsExplanationBox');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
}

function showSection(section) {
    window.location.href = section + '.php';
}

$('.sidebar ul li[data-section]').on('click', function() {
    const section = $(this).data('section');
    showSection(section);
});

$(document).ready(function() {
    updateDonationHistory();
});
</script>

</body>
</html>