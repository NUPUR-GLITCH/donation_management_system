<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$donor_count = $conn->query("SELECT COUNT(DISTINCT donor_name) as count FROM donations")->fetch_assoc()['count'] ?? 0;
$donors = $conn->query("SELECT donor_name, SUM(amount) as total FROM donations GROUP BY donor_name");

if (isset($_GET['get_donor_details']) && !empty($_GET['donor_name'])) {
    $donor_name = trim($_GET['donor_name']);
    $stmt = $conn->prepare("SELECT amount, status, created_at FROM donations WHERE donor_name = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $donor_name);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($donations);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    $stmt->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-image: url('Uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg'); /* Replace with your image path */
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
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
            z-index: 1;
        }
        .sidebar {
            width: 250px;
            background: rgba(106, 27, 154, 0.9); /* Purple shade */
            color: #e1bee7;
            padding: 20px;
            position: relative;
            z-index: 2;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
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
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #8e24aa; /* Lighter purple on hover */
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
        .main h3 {
            color: #6a1b9a;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
        }
        .table-container {
            overflow-x: auto;
            overflow-y: auto; /* Enable vertical scrolling */
            max-height: 400px; /* Fixed height for scrolling */
        }
        .table {
            background-color: #f3e5f5;
            border-radius: 15px;
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            table-layout: fixed;
        }
        .table th {
            background-color: #8e24aa; /* Purple header */
            color: #fff;
        }
        .table td {
            color: #4a148c; /* Darker purple text */
        }
        .btn-primary {
            background-color: #8e24aa; /* Purple button */
            border: none;
        }
        .btn-primary:hover {
            background-color: #6a1b9a; /* Darker purple on hover */
        }
        .modal-header {
            background-color: #8e24aa; /* Purple modal header */
            color: #fff;
        }
        .modal-body {
            color: #4a148c; /* Darker purple text */
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
    <h3 class="mb-3">All Donors</h3>
    <div class="alert alert-info">
        <strong>Total Donors:</strong> <?= htmlspecialchars($donor_count) ?>
    </div>
    <div class="mb-3 d-flex">
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by donor name">
        <button id="export-btn" class="btn btn-primary ms-2">Export to CSV</button>
    </div>
    <div class="table-container">
        <table class="table table-bordered" id="donorsTable">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Total Donated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = $donors->fetch_assoc()): ?>
                    <tr>
                        <td class="donor-name"><?= htmlspecialchars($d['donor_name']) ?></td>
                        <td class="total">$<?= number_format($d['total'], 2) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm view-details" data-donor="<?= htmlspecialchars($d['donor_name']) ?>">View Details</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="donorDetailsModal" tabindex="-1" aria-labelledby="donorDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="donorDetailsModalLabel">Donation History</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="donorDetailsBody"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <!-- ✅ THIS BUTTON WORKS NOW -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const viewButtons = document.querySelectorAll('.view-details');
const modalEl = document.getElementById('donorDetailsModal');
const modalTitle = document.getElementById('donorDetailsModalLabel');
const modalBody = document.getElementById('donorDetailsBody');

viewButtons.forEach(button => {
    button.addEventListener('click', () => {
        const name = button.dataset.donor;
        modalTitle.textContent = `Donation History for ${name}`;
        modalBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

        fetch(`manage_donors.php?get_donor_details=1&donor_name=${encodeURIComponent(name)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    modalBody.innerHTML = '<tr><td colspan="3" class="text-center">No donations found.</td></tr>';
                } else {
                    modalBody.innerHTML = data.map(d => `
                        <tr>
                            <td>$${parseFloat(d.amount).toFixed(2)}</td>
                            <td>${d.status.charAt(0).toUpperCase() + d.status.slice(1)}</td>
                            <td>${new Date(d.created_at).toLocaleString()}</td>
                        </tr>
                    `).join('');
                }
            })
            .catch(err => {
                modalBody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">${err.message}</td></tr>`;
            });

        // ✅ Fix: properly show the modal
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
});

// Export to CSV
document.getElementById('export-btn').addEventListener('click', () => {
    const table = document.getElementById('donorsTable');
    const rows = table.querySelectorAll('tbody tr');
    const data = [];
    rows.forEach(row => {
        const donorName = row.querySelector('.donor-name').textContent;
        const total = row.querySelector('.total').textContent.replace('$', '');
        data.push(`"${donorName.replace(/"/g, '""')}",${total}`);
    });
    const csv = ['Donor Name,Total Donated', ...data].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'donors.csv';
    a.click();
    window.URL.revokeObjectURL(url);
});
</script>
</body>
</html>