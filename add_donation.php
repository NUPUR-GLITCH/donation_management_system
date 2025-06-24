<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$errors = [];
$success = '';

if (isset($_POST['submit'])) {
    $donor_name = trim($_POST['donor_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Server-side validation
    if (empty($donor_name)) {
        $errors['donor_name'] = "Donor name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]{1,100}$/', $donor_name)) {
        $errors['donor_name'] = "Donor name must be letters and spaces, up to 100 characters.";
    }
    if (empty($amount)) {
        $errors['amount'] = "Amount is required.";
    } elseif (!is_numeric($amount) || $amount <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
        $errors['amount'] = "Amount must be a positive number with up to 2 decimal places.";
    }
    if (!in_array($status, ['approved', 'pending', 'rejected'])) {
        $errors['status'] = "Invalid status selected.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, amount, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $donor_name, $amount, $status);
        if ($stmt->execute()) {
            $success = "Donation added!";
        } else {
            $errors['general'] = "Failed to add donation: " . $conn->error;
            error_log("Donation insertion failed: " . $conn->error);
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .main h3 {
            color: #6a1b9a;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
        }
        .form-control {
            border-radius: 25px;
            border: 2px solid #ce93d8;
            background-color: #f3e5f5;
            color: #4a148c;
            padding: 10px 15px;
            margin-bottom: 0.5rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #7b1fa2;
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25);
        }
        .btn-primary {
            border-radius: 25px;
            padding: 10px 20px;
            background-color: #8e24aa;
            border: none;
            color: #fff;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #6a1b9a;
        }
        .alert-success {
            background-color: #e1bee7;
            color: #4a148c;
            border-radius: 15px;
            border: none;
            margin-top: 1rem;
            text-align: center;
        }
        .alert-danger {
            background-color: #f8bbd0;
            color: #b71c1c;
            border-radius: 15px;
            border: none;
            margin-top: 1rem;
            text-align: center;
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
    <hr>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main">
    <h3>Add New Donation</h3>
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-2">
            <input name="donor_name" class="form-control" placeholder="Donor Name" value="<?= isset($_POST['donor_name']) ? htmlspecialchars($_POST['donor_name']) : '' ?>" required>
            <?php if (!empty($errors['donor_name'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['donor_name']) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <input name="amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="Amount" value="<?= isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '' ?>" required>
            <?php if (!empty($errors['amount'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['amount']) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <select name="status" class="form-control">
                <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= (isset($_POST['status']) && $_POST['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= (isset($_POST['status']) && $_POST['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
            <?php if (!empty($errors['status'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['status']) ?></div>
            <?php endif; ?>
        </div>
        <button name="submit" class="btn btn-primary">Add Donation</button>
    </form>
</div>

</body>
</html>