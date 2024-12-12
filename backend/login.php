<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mengambil data pengguna berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['PASSWORD'])) {
        // Set sesi berdasarkan data pengguna
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        // Cek peran dan arahkan ke halaman yang sesuai
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location:../dashboard.php');
        }
        exit;
    } else {
        echo "<p style='color:red;'>Invalid login credentials!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/styledash.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="../index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="../index.html">Home</a></li>
            <li><a href="signup.php">Sign Up</a></li>
            <li><a href="login.php" class="active">Login</a></li>
        </ul>
    </div>
    
    <div class="wrapper">
        <h1>Login</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>