<?php
session_start();
include 'db.php';

$error = '';
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = sha1($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM donors WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $donor = $result->fetch_assoc();
        $_SESSION['donor'] = $donor['donor_name'];
        header("Location: donor_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Login - Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('Uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg');
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
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .login-container {
            width: 400px;
            padding: 2.5rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(5px);
        }
        .login-container h2 {
            color: #6a1b9a;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .login-container h5 {
            color: #ab47bc;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 25px;
            border: 2px solid #ce93d8;
            background-color: #f3e5f5;
            color: #4a148c;
        }
        .form-control:focus {
            border-color: #7b1fa2;
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.25);
        }
        .btn-login {
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 1.1rem;
            background-color: #8e24aa;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #6a1b9a;
        }
        .alert-danger {
            background-color: #f8bbd0;
            color: #b71c1c;
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Donor Login</h2>
    <h5>Donate and Track Your Contributions</h5>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button type="submit" name="login" class="btn btn-login w-100">Login</button>
    </form>
</div>

</body>
</html>