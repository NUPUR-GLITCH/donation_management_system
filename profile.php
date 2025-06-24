<?php
session_start();

// Mock data for demo purposes
$_SESSION['donor'] = true;
$_SESSION['donor_id'] = 1;
$donor = [
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
    'phone' => '+91 9876543210',
    'joined' => '2024-03-15',
    'photo' => 'uploads/profile_placeholder.png'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Profile</title>
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
            display: flex;
            overflow: hidden;
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
            color: #e1bee7;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .sidebar ul li:hover, .sidebar ul li.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar ul li i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }
        .profile-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ce93d8;
        }
        .profile-header h4 {
            color: #6a1b9a;
        }
        .form-control:focus {
            border-color: #ab47bc;
            box-shadow: 0 0 0 0.25rem rgba(171, 71, 188, 0.25);
        }
        .btn-update {
            background-color: #8e24aa;
            color: #fff;
            border-radius: 25px;
            padding: 10px 30px;
            border: none;
        }
        .btn-update:hover {
            background-color: #6a1b9a;
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
        <li data-section="feedback"><i class="fas fa-comment"></i> Feedback</li>
        <li class="active" data-section="profile"><i class="fas fa-user"></i> Profile</li>
        <li><a href="logout.php" class="btn-donor"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="profile-container">
        <div class="profile-header">
            <img id="profilePic" src="<?= $donor['photo'] ?>" alt="Profile Photo">
            <div>
                <h4><?= $donor['name'] ?></h4>
                <p class="text-muted">Joined on <?= $donor['joined'] ?></p>
            </div>
        </div>

        <form>
            <div class="mb-3">
                <label for="photoUpload" class="form-label">Change Profile Picture</label>
                <input class="form-control" type="file" id="photoUpload" accept="image/*" onchange="previewPhoto(event)">
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" value="<?= $donor['name'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="<?= $donor['email'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" value="<?= $donor['phone'] ?>">
            </div>
            <button type="submit" class="btn btn-update mt-2">Update Profile</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('profilePic').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function showSection(section) {
    window.location.href = section + '.php';
}

$('.sidebar ul li[data-section]').on('click', function () {
    const section = $(this).data('section');
    showSection(section);
});
</script>

</body>
</html>
