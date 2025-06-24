<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Existing queries
$total = $conn->query("SELECT SUM(amount) as total FROM donations")->fetch_assoc()['total'] ?? 0;
$count = $conn->query("SELECT COUNT(*) as total FROM donations")->fetch_assoc()['total'] ?? 0;

// Donation analysis queries
$status_breakdown = $conn->query("SELECT status, SUM(amount) as total, COUNT(*) as count FROM donations GROUP BY status");
$avg_amount = $conn->query("SELECT AVG(amount) as avg_amount FROM donations")->fetch_assoc()['avg_amount'] ?? 0;
$top_donor = $conn->query("SELECT donor_name, SUM(amount) as total FROM donations GROUP BY donor_name ORDER BY total DESC LIMIT 1")->fetch_assoc();

// Additional queries for collapsible details
$status_details = [];
$statuses = ['approved', 'pending', 'rejected'];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT donor_name, amount, created_at FROM donations WHERE status = ? ORDER BY amount DESC LIMIT 3");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $status_details[$status] = [];
    while ($row = $result->fetch_assoc()) {
        $status_details[$status][] = $row;
    }
    $stmt->close();
}
$min_max = $conn->query("SELECT MIN(amount) as min_amount, MAX(amount) as max_amount FROM donations")->fetch_assoc();
$top_donor_details = [];
if ($top_donor) {
    $stmt = $conn->prepare("SELECT amount, status, created_at FROM donations WHERE donor_name = ? ORDER BY created_at DESC LIMIT 3");
    $stmt->bind_param("s", $top_donor['donor_name']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $top_donor_details[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-image: url('Uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            font-family: 'Segoe UI', sans-serif;
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
            background: rgba(74, 20, 140, 0.9);
            color: white;
            padding: 20px;
            position: relative;
            z-index: 2;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar h4 {
            margin-bottom: 25px;
            font-weight: 700;
            color: #e1bee7;
            letter-spacing: 1px;
        }
        .sidebar a {
            color: #e1bee7;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #6a1b9a;
            color: #fff;
        }
        .sidebar hr {
            border-color: #ab47bc;
            margin: 10px 0;
        }
        .sidebar a.logout {
            color: #f8bbd0;
        }
        .sidebar a.logout:hover {
            background-color: #b71c1c;
            color: #fff;
        }
        .main {
            flex: 1;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 2;
            border-radius: 10px;
            margin: 20px;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .main h3, .main h4 {
            color: #6a1b9a;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
        }
        .alert-info {
            background-color: #e1bee7;
            color: #4a148c;
            border-radius: 15px;
            border: none;
            margin-top: 1rem;
            text-align: center;
        }
        .card {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
            color: #fff;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-header {
            border-radius: 10px 10px 0 0;
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .card-body {
            padding: 15px;
            color: #fff;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
        }
        .card-body p, .card-body ul {
            margin: 0;
            line-height: 1.6;
        }
        .card-body ul {
            padding-left: 20px;
        }
        .card-body li {
            margin-bottom: 10px;
        }
        .bg-approved { background-color: #66bb6a !important; }
        .bg-pending { background-color: #ffab40 !important; }
        .bg-rejected { background-color: #ef5350 !important; }
        .bg-average { background-color: #8e24aa !important; }
        .bg-top-donor { background-color: #4a148c !important; }
        .bi-chevron-right {
            transition: transform 0.3s ease;
        }
        .bi-chevron-right.rotate-90 {
            transform: rotate(90deg);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4>Admin Panel</h4>
    <a href="index.php">Dashboard</a>
    <a href="add_donation.php">Add Donation</a>
    <a href="manage_donors.php">Manage Donors</a>
    <a href="report.php">Reports</a>
    <a href="terms_and_conditions.php">Terms and Conditions</a>
    <hr>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main">
    <h3>Donation Reports</h3>
    <div class="alert alert-info">
        <strong>Total Donations:</strong> $<?= number_format($total, 2) ?><br>
        <strong>Number of Donations:</strong> <?= htmlspecialchars($count) ?>
    </div>
    <h4 class="mt-4">Donation Analysis</h4>
    <?php if ($count > 0): ?>
        <div class="row">
            <?php while ($row = $status_breakdown->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card bg-<?= htmlspecialchars($row['status']) ?>">
                        <div class="card-header" id="heading<?= ucfirst($row['status']) ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?= ucfirst($row['status']) ?>" aria-expanded="false" aria-controls="collapse<?= ucfirst($row['status']) ?>">
                            <h5>Total Amount of <?= ucfirst(htmlspecialchars($row['status'])) ?> Donations <?= $row['status'] === 'pending' ? 'Awaiting Review' : ($row['status'] === 'rejected' ? 'Declined' : 'Received') ?></h5>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <div id="collapse<?= ucfirst($row['status']) ?>" class="collapse" aria-labelledby="heading<?= ucfirst($row['status']) ?>" data-bs-parent="#donationAnalysisAccordion">
                            <div class="card-body">
                                <p>$<?= number_format($row['total'], 2) ?> (<?= htmlspecialchars($row['count']) ?> donations)</p>
                                <?php if (!empty($status_details[$row['status']])): ?>
                                    <p>Top <?= min(3, count($status_details[$row['status']])) ?> Donations:</p>
                                    <ul>
                                        <?php foreach ($status_details[$row['status']] as $detail): ?>
                                            <li><?= htmlspecialchars($detail['donor_name']) ?>: $<?= number_format($detail['amount'], 2) ?> on <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No individual donations available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="col-md-4">
                <div class="card bg-average">
                    <div class="card-header" id="headingAverage" data-bs-toggle="collapse" data-bs-target="#collapseAverage" aria-expanded="false" aria-controls="collapseAverage">
                        <h5>Average Donation Amount Across All Contributions</h5>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div id="collapseAverage" class="collapse" aria-labelledby="headingAverage" data-bs-parent="#donationAnalysisAccordion">
                        <div class="card-body">
                            <p>$<?= number_format($avg_amount, 2) ?></p>
                            <p>Additional Details:</p>
                            <ul>
                                <li>Minimum Donation: $<?= number_format($min_max['min_amount'] ?? 0, 2) ?></li>
                                <li>Maximum Donation: $<?= number_format($min_max['max_amount'] ?? 0, 2) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($top_donor): ?>
                <div class="col-md-4">
                    <div class="card bg-top-donor">
                        <div class="card-header" id="headingTopDonor" data-bs-toggle="collapse" data-bs-target="#collapseTopDonor" aria-expanded="false" aria-controls="collapseTopDonor">
                            <h5>Top Donor by Total Contribution Amount</h5>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                        <div id="collapseTopDonor" class="collapse" aria-labelledby="headingTopDonor" data-bs-parent="#donationAnalysisAccordion">
                            <div class="card-body">
                                <p><?= htmlspecialchars($top_donor['donor_name']) ?> ($<?= number_format($top_donor['total'], 2) ?>)</p>
                                <?php if (!empty($top_donor_details)): ?>
                                    <p>Last <?= min(3, count($top_donor_details)) ?> Donations:</p>
                                    <ul>
                                        <?php foreach ($top_donor_details as $detail): ?>
                                            <li>$<?= number_format($detail['amount'], 2) ?> (<?= ucfirst(htmlspecialchars($detail['status'])) ?>) on <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No recent donations available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No donations available for analysis. <a href="add_donation.php">Add a donation</a> to get started.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Rotate arrow on collapse toggle
    document.querySelectorAll('.card-header').forEach(header => {
        header.addEventListener('click', () => {
            const arrow = header.querySelector('.bi-chevron-right');
            const collapse = document.querySelector(header.dataset.bsTarget);
            if (collapse.classList.contains('show')) {
                arrow.classList.remove('rotate-90');
            } else {
                arrow.classList.add('rotate-90');
            }
        });
    });
</script>
</body>
</html>