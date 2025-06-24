<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Initialize variables
$error = '';
$success = '';
$donor_name = '';
$amount = '';
$status = '';
$created_at = '';

// Get donation ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'];

// Fetch donation details
$stmt = $conn->prepare("SELECT * FROM donations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$donation = $result->fetch_assoc();
$donor_name = $donation['donor_name'];
$amount = $donation['amount'];
$status = $donation['status'];
$created_at = $donation['created_at'];

// Handle form submission
if (isset($_POST['update'])) {
    $donor_name = trim($_POST['donor_name']);
    $amount = trim($_POST['amount']);
    $status = trim($_POST['status']);
    $created_at = trim($_POST['created_at']);

    // Basic validation
    if (empty($donor_name) || empty($amount) || empty($status) || empty($created_at)) {
        $error = "All fields are required!";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = "Amount must be a positive number!";
    } elseif (!in_array($status, ['approved', 'pending', 'rejected'])) {
        $error = "Invalid status!";
    } else {
        // Update donation
        $stmt = $conn->prepare("UPDATE donations SET donor_name = ?, amount = ?, status = ?, created_at = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $donor_name, $amount, $status, $created_at, $id);
        if ($stmt->execute()) {
            $success = "Donation updated successfully!";
            // Redirect to index.php after 2 seconds
            header("Refresh: 2; URL=index.php");
        } else {
            $error = "Failed to update donation. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Donation - Donation Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for readability */
            z-index: 1;
        }
        .edit-container {
            width: 500px;
            padding: 2.5rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(5px);
        }
        .edit-container h2 {
            color: #6a1b9a; /* Deep purple */
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 25px;
            border: 2px solid #ce93d8; /* Purple border */
            background-color: #f3e5f5; /* Light purple background */
            color: #4a148c; /* Dark purple text */
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #7b1fa2; /* Vibrant purple on focus */
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25); /* Purple shadow */
        }
        .form-select {
            border-radius: 25px;
            border: 2px solid #ce93d8;
            background-color: #f3e5f5;
            color: #4a148c;
            margin-bottom: 1rem;
        }
        .form-select:focus {
            border-color: #7b1fa2;
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25);
        }
        .btn-update {
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 1.1rem;
            background-color: #8e24aa; /* Bright purple */
            border: none;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-update:hover {
            background-color: #6a1b9a; /* Darker purple on hover */
        }
        .btn-back {
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 1.1rem;
            background-color: #ab47bc; /* Light purple */
            border: none;
            color: #fff;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }
        .btn-back:hover {
            background-color: #8e24aa; /* Bright purple on hover */
        }
        .alert-danger {
            background-color: #f8bbd0; /* Light pink for error */
            color: #b71c1c;
            border-radius: 15px;
            border: none;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #e1bee7; /* Light purple for success */
            color: #4a148c;
            border-radius: 15px;
            border: none;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Donation</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="donor_name" class="form-control" placeholder="Donor Name" value="<?= htmlspecialchars($donor_name) ?>" required>
        <input type="number" name="amount" class="form-control" placeholder="Amount" step="0.01" min="0" value="<?= htmlspecialchars($amount) ?>" required>
        <select name="status" class="form-select" required>
            <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="rejected" <?= $status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <input type="datetime-local" name="created_at" class="form-control" value="<?= htmlspecialchars(str_replace(' ', 'T', $created_at)) ?>" required>
        <button type="submit" name="update" class="btn btn-update">Update Donation</button>
        <a href="index.php" class="btn btn-back">Back to Dashboard</a>
    </form>
</div>

</body>
</html>