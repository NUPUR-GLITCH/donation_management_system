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
    <title>Donation Management System - Goals</title>
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
        .impact-stats, .goal-progress {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }
        .stat-item h4, .goal-item h4 {
            color: #6a1b9a;
            margin: 0;
            font-size: 1.2rem;
        }
        .stat-item p {
            color: #4a148c;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0;
        }
        .progress {
            height: 20px;
            background-color: #e1bee7;
        }
        .progress-bar {
            background-color: #8e24aa;
        }
        .goal-item {
            flex: 1 1 30%;
            margin: 10px;
            padding: 10px;
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
        <li data-section="history"><i class="fas fa-history"></i> History</li>
        <li class="active" data-section="goals"><i class="fas fa-chart-line"></i> Goals</li>
        <li data-section="feedback"><i class="fas fa-comment"></i> Feedback</li>
        <li data-section="profile"><i class="fas fa-user"></i> Profile</li>
        <li><a href="logout.php" class="btn-donor"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="dashboard-container">
        <h2>Donation Goals & Milestones</h2>

        <div class="impact-stats">
            <div class="stat-item">
                <h4>Students Helped</h4>
                <p id="studentsHelped">0</p>
            </div>
            <div class="stat-item">
                <h4>Families Assisted</h4>
                <p id="familiesAssisted">0</p>
            </div>
            <div class="stat-item">
                <h4>Relief Kits</h4>
                <p id="reliefKits">0</p>
            </div>
        </div>

        <div class="goal-progress mt-4">
            <div class="goal-item">
                <h4>$500 Milestone</h4>
                <div class="progress">
                    <div id="goal500" class="progress-bar" role="progressbar"></div>
                </div>
            </div>
            <div class="goal-item">
                <h4>$1000 Milestone</h4>
                <div class="progress">
                    <div id="goal1000" class="progress-bar" role="progressbar"></div>
                </div>
            </div>
            <div class="goal-item">
                <h4>$2000 Milestone</h4>
                <div class="progress">
                    <div id="goal2000" class="progress-bar" role="progressbar"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="notification" id="notification"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let mockDonations = JSON.parse(localStorage.getItem('donations')) || [];
let totalDonated = mockDonations.reduce((sum, d) => sum + d.amount, 0);

function updateImpactStats() {
    $('#studentsHelped').text(Math.floor(totalDonated / 10));
    $('#familiesAssisted').text(Math.floor(totalDonated / 20));
    $('#reliefKits').text(Math.floor(totalDonated / 50));
}

function updateGoalBars() {
    const milestones = [500, 1000, 2000];
    milestones.forEach(goal => {
        const percent = Math.min((totalDonated / goal) * 100, 100);
        $(`#goal${goal}`).css('width', `${percent}%`).text(`${percent.toFixed(0)}%`);
    });
}

function showSection(section) {
    window.location.href = section + '.php';
}

$('.sidebar ul li[data-section]').on('click', function() {
    const section = $(this).data('section');
    showSection(section);
});

$(document).ready(function() {
    updateImpactStats();
    updateGoalBars();
});
</script>

</body>
</html>
