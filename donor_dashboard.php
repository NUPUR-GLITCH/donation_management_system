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
    <title>Donation Management System - Donor Dashboard</title>
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
            overflow-y: auto;
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
        .form-control {
            border-radius: 25px;
            border: 2px solid #ce93d8;
            background-color: #f3e5f5;
            color: #4a148c;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #7b1fa2;
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25);
        }
        .btn-donate {
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 1rem;
            background: linear-gradient(90deg, #8e24aa, #6a1b9a);
            border: none;
            color: #fff;
            transition: transform 0.3s ease, background 0.3s ease;
            margin: 5px;
        }
        .btn-donate:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #6a1b9a, #8e24aa);
        }
        .btn-donor {
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            background: linear-gradient(90deg, #ab47bc, #ce93d8);
            border: none;
            color: #fff;
            text-decoration: none;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn-donor:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #ce93d8, #ab47bc);
        }
        .funds-box {
            position: fixed;
            bottom: 20px;
            left: 270px;
            width: calc(100% - 270px);
            max-width: 800px;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
            color: #fff;
            font-size: 0.95rem;
            display: flex;
            gap: 15px;
        }
        .funds-box .funds-toggle {
            background: linear-gradient(90deg, #ab47bc, #ce93d8);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: #fff;
            cursor: pointer;
            transition: transform 0.3s ease, background 0.3s ease;
            flex: 1;
            min-width: 0;
        }
        .funds-box .funds-toggle:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #ce93d8, #ab47bc);
        }
        .funds-box .funds-content {
            flex: 3;
            display: none;
            padding: 10px;
            border-radius: 10px;
            overflow-y: auto;
            max-height: 200px;
        }
        .funds-box .funds-content.active {
            display: block;
        }
        .funds-box .funds-content h4 {
            margin-top: 0;
            font-size: 1.1rem;
        }
        .funds-box .funds-content .donate-buttons {
            margin-top: 10px;
        }
        .funds-box .education-bg { background: linear-gradient(135deg, #4caf50, #81c784); }
        .funds-box .disaster-relief-bg { background: linear-gradient(135deg, #f44336, #ef9a9a); }
        .funds-box .health-bg { background: linear-gradient(135deg, #2196f3, #64b5f6); }
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
        <li class="active" data-section="dashboard"><i class="fas fa-home"></i> Dashboard</li>
        <li data-section="history"><i class="fas fa-history"></i> History</li>
        <li data-section="goals"><i class="fas fa-chart-line"></i> Goals</li>
        <li data-section="feedback"><i class="fas fa-comment"></i> Feedback</li>
        <li data-section="profile"><i class="fas fa-user"></i> Profile</li>
        <li><a href="logout.php" class="btn-donor"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="dashboard-container">
        <h2>Donor Dashboard</h2>

        <form id="donationForm">
            <input type="number" id="donationAmount" class="form-control mb-3" placeholder="Enter Donation Amount" step="0.01" required>
            <div class="mb-3">
                <label>
                    <input type="checkbox" id="recurringCheckbox"> Enable Recurring Donation (Monthly)
                </label>
            </div>
            <button type="submit" class="btn-donate w-100">Donate Now</button>
        </form>
    </div>
</div>

<div class="funds-box">
    <button class="funds-toggle" onclick="toggleFunds('education')">Education</button>
    <button class="funds-toggle" onclick="toggleFunds('disaster-relief')">Disaster Relief</button>
    <button class="funds-toggle" onclick="toggleFunds('health')">Health</button>

    <div class="funds-content education-bg">
        <h4>Education</h4>
        <p>50% of funds support education initiatives, providing scholarships, school supplies, and digital tools. Over 1,000 students have continued their education this year.</p>
        <div class="donate-buttons">
            <button class="btn-donate" onclick="donate('education', 'stationery')">Donate for Stationery</button>
            <button class="btn-donate" onclick="donate('education', 'books')">Donate for Books</button>
            <button class="btn-donate" onclick="donate('education', 'digital-tools')">Donate for Digital Tools</button>
            <button class="btn-donate" onclick="donate('education', 'relief-kits')">Donate for Relief Kits</button>
        </div>
    </div>
    <div class="funds-content disaster-relief-bg">
        <h4>Disaster Relief</h4>
        <p>20% is allocated for disaster relief, offering food, water, and shelter during crises. Recently, 200 households were aided after floods.</p>
        <div class="donate-buttons">
            <button class="btn-donate" onclick="donate('disaster-relief', 'food')">Donate for Food</button>
            <button class="btn-donate" onclick="donate('disaster-relief', 'clothing')">Donate for Clothing</button>
            <button class="btn-donate" onclick="donate('disaster-relief', 'relief-kits')">Donate for Relief Kits</button>
        </div>
    </div>
    <div class="funds-content health-bg">
        <h4>Health</h4>
        <p>30% funds healthcare programs, including medical camps and essential medicines. Last month, 500 families received free check-ups.</p>
        <div class="donate-buttons">
            <button class="btn-donate" onclick="donate('health', 'medical-supplies')">Donate for Medical Supplies</button>
            <button class="btn-donate" onclick="donate('health', 'medicines')">Donate for Medicines</button>
        </div>
    </div>
</div>

<div class="notification" id="notification"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let mockDonations = JSON.parse(localStorage.getItem('donations')) || [];
let totalDonated = mockDonations.reduce((sum, d) => sum + d.amount, 0);
let activeFund = null;

function showNotification(message) {
    const notification = $('#notification');
    notification.text(message).fadeIn(500).delay(3000).fadeOut(500);
}

function showSection(section) {
    window.location.href = section + '.php';
}

function toggleFunds(category) {
    $('.funds-content').removeClass('active');
    const $content = $(`.funds-content.${category}-bg`);
    $content.toggleClass('active');
    activeFund = $content.hasClass('active') ? category : null;
}

function donate(category, item) {
    let amount = prompt(`Enter donation amount for ${item} under ${category}:`);
    amount = parseFloat(amount);
    if (isNaN(amount) || amount <= 0) {
        showNotification('Please enter a valid amount greater than 0.');
        return;
    }
    const recurring = confirm('Enable recurring donation (Monthly)?');
    const date = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
    mockDonations.unshift({ amount, date, recurring, category, item });
    totalDonated += amount;
    localStorage.setItem('donations', JSON.stringify(mockDonations));
    showNotification(`Donation of $${amount.toFixed(2)} for ${item} under ${category} processed successfully!`);
}

$('#donationForm').on('submit', function(e) {
    e.preventDefault();
    let amount = parseFloat($('#donationAmount').val());
    if (isNaN(amount) || amount <= 0) {
        showNotification('Please enter a valid amount greater than 0.');
        return;
    }
    const recurring = $('#recurringCheckbox').is(':checked');
    const date = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
    const category = activeFund || 'general';
    const item = 'general-donation';
    mockDonations.unshift({ amount, date, recurring, category, item });
    totalDonated += amount;
    localStorage.setItem('donations', JSON.stringify(mockDonations));
    $('#donationAmount').val('');
    showNotification(`Donation of $${amount.toFixed(2)} ${activeFund ? `for ${activeFund}` : ''} processed successfully!`);
});

$('#recurringCheckbox').on('change', function() {
    localStorage.setItem('recurring', $(this).is(':checked') ? '1' : '0');
    showNotification('Recurring donation preference updated!');
});

$('.sidebar ul li[data-section]').on('click', function() {
    const section = $(this).data('section');
    showSection(section);
});

$(document).ready(function() {
    $('#recurringCheckbox').prop('checked', localStorage.getItem('recurring') === '1');
    $('.funds-content').removeClass('active');
});
</script>
</body>
</html>