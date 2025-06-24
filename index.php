<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');

    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }

    if (isset($_POST['fetch_data'])) {
        // Fetch updated data for table and cards
        $approved = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='approved'")->fetch_assoc()['total'] ?? 0;
        $pending = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
        $rejected = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='rejected'")->fetch_assoc()['total'] ?? 0;
        $donations = [];
        $result = $conn->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $donations[] = [
                    'id' => $row['id'],
                    'donor_name' => htmlspecialchars($row['donor_name']),
                    'amount' => number_format($row['amount'], 2),
                    'status' => htmlspecialchars($row['status']),
                    'created_at' => date('d/m/Y H:i', strtotime($row['created_at']))
                ];
            }
        } else {
            error_log('Fetch data query failed: ' . $conn->error);
        }
        echo json_encode([
            'success' => true,
            'approved' => number_format($approved, 2),
            'pending' => number_format($pending, 2),
            'rejected' => number_format($rejected, 2),
            'donations' => $donations
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            padding: 30px 20px;
            position: relative;
            z-index: 2;
        }
        .sidebar h4 {
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #e1bee7;
        }
        .sidebar a {
            color: #e1bee7;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #6a1b9a;
            color: #fff;
        }
        .sidebar hr {
            border-color: #ab47bc;
            margin: 20px 0;
        }
        .sidebar a.logout {
            color: #f8bbd0;
        }
        .sidebar a.logout:hover {
            background-color: #b71c1c;
        }
        .main {
            flex: 1;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 2;
            border-radius: 10px;
            margin: 20px;
            backdrop-filter: blur(5px);
        }
        .main h2 {
            color: #6a1b9a;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .main h4 {
            color: #6a1b9a;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .bg-approved {
            background-color: #66bb6a !important;
            color: #fff;
        }
        .bg-pending {
            background-color: #ffab40 !important;
            color: #fff;
        }
        .bg-rejected {
            background-color: #ef5350 !important;
            color: #fff;
        }
        .table-container {
            overflow-x: auto;
            overflow-y: auto; /* Enable vertical scrolling */
            max-height: 400px; /* Fixed height for scrolling */
        }
        .table {
            background-color: #ffffff;
            border: 2px solid #ab47bc;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            table-layout: fixed;
        }
        .table thead {
            background-color: #4a148c;
            color: #fff;
        }
        .table th, .table td {
            padding: 10px;
            vertical-align: middle;
            border: 1px solid #ab47bc;
            color: #4a148c;
        }
        .table tbody tr:hover {
            background-color: #f3e5f5;
        }
        .table th:nth-child(1), .table td:nth-child(1) { width: 25%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 20%; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 20%; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 25%; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 30%; text-align: center; }
        .btn-edit, .btn-delete {
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.9rem;
            margin-right: 5px;
            text-decoration: none;
        }
        .btn-edit {
            background-color: #8e24aa;
            color: #fff;
            border: none;
        }
        .btn-edit:hover {
            background-color: #6a1b9a;
        }
        .btn-delete {
            background-color: #ef5350;
            color: #fff;
            border: none;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        .modal-content {
            background-color: #f3e5f5;
            border-radius: 15px;
            border: none;
        }
        .modal-header {
            background-color: #8e24aa;
            color: #fff;
            border: none;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .modal-body {
            color: #4a148c;
        }
        .modal-footer {
            border: none;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .btn-close-white {
            filter: invert(1);
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
    <h2>Donation Overview</h2>
    <div class="row" id="summaryCards">
        <?php
        $approved = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='approved'")->fetch_assoc()['total'] ?? 0;
        $pending = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
        $rejected = $conn->query("SELECT SUM(amount) as total FROM donations WHERE status='rejected'")->fetch_assoc()['total'] ?? 0;
        ?>
        <div class="col-md-4"><div class="card text-white bg-approved p-3"><h4>$<?= number_format($approved, 2) ?> Approved</h4></div></div>
        <div class="col-md-4"><div class="card text-white bg-pending p-3"><h4>$<?= number_format($pending, 2) ?> Pending</h4></div></div>
        <div class="col-md-4"><div class="card text-white bg-rejected p-3"><h4>$<?= number_format($rejected, 2) ?> Rejected</h4></div></div>
    </div>

    <h4>Latest Donations</h4>
    <div class="table-container">
        <table class="table table-striped mt-2" id="donationsTable">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 10");
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['donor_name']) . "</td>
                            <td>$" . number_format($row['amount'], 2) . "</td>
                            <td>" . htmlspecialchars($row['status']) . "</td>
                            <td>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>
                            <td>
                                <a href='edit_donation.php?id={$row['id']}' class='btn-edit'>Edit</a>
                                <button class='btn-delete' data-id='{$row['id']}' data-donor='" . htmlspecialchars($row['donor_name']) . "' data-amount='" . number_format($row['amount'], 2) . "'>Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No donations found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-delete" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Delete functionality
const deleteButtons = document.querySelectorAll('.btn-delete');
const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
const deleteConfirmMessage = document.getElementById('deleteConfirmMessage');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

// Named function for event listener
function handleDeleteClick(event) {
    const button = event.target;
    const id = button.dataset.id;
    const donor = button.dataset.donor;
    const amount = button.dataset.amount;
    console.log('Opening modal for ID:', id);
    deleteConfirmMessage.textContent = `Are you sure you want to delete ${donor}'s donation of $${amount}?`;
    confirmDeleteBtn.dataset.id = id;
    deleteModal.show();
}

// Attach delete button listeners
function attachDeleteListeners() {
    deleteButtons.forEach(button => {
        button.removeEventListener('click', handleDeleteClick); // Remove existing listeners
        button.addEventListener('click', handleDeleteClick);
    });
}

// Initial attachment
attachDeleteListeners();

confirmDeleteBtn.addEventListener('click', () => {
    const id = confirmDeleteBtn.dataset.id;
    console.log('Sending delete request for ID:', id);
    fetch('delete.php', { // Target delete.php
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delete_id=${encodeURIComponent(id)}`
    })
    .then(response => {
        console.log('Delete response status:', response.status);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('Delete response data:', data);
        if (data.success) {
            // Fetch updated data from index.php without refresh
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'fetch_data=1'
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Updated data:', data);
                if (data.success) {
                    // Update summary cards
                    document.querySelector('#summaryCards .bg-approved h4').textContent = `$${data.approved} Approved`;
                    document.querySelector('#summaryCards .bg-pending h4').textContent = `$${data.pending} Pending`;
                    document.querySelector('#summaryCards .bg-rejected h4').textContent = `$${data.rejected} Rejected`;

                    // Update table
                    const tbody = document.querySelector('#donationsTable tbody');
                    tbody.innerHTML = data.donations.length > 0 ? data.donations.map(d => `
                        <tr>
                            <td>${d.donor_name}</td>
                            <td>$${d.amount}</td>
                            <td>${d.status}</td>
                            <td>${d.created_at}</td>
                            <td>
                                <a href="edit_donation.php?id=${d.id}" class="btn-edit">Edit</a>
                                <button class="btn-delete" data-id="${d.id}" data-donor="${d.donor_name}" data-amount="${d.amount}">Delete</button>
                            </td>
                        </tr>
                    `).join('') : '<tr><td colspan="5" class="text-center">No donations found.</td></tr>';

                    // Reattach delete listeners
                    attachDeleteListeners();
                }
                deleteModal.hide();
            })
            .catch(error => {
                console.error('Error fetching updated data:', error);
                deleteModal.hide(); // Hide modal on failure without alert
            });
        } else {
            deleteModal.hide(); // Hide modal on deletion failure without alert
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        deleteModal.hide(); // Hide modal on network failure without alert
    });
});
</script>

</body>
</html>