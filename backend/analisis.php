<?php
include 'koneksi.php'; // Koneksi database
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fungsi untuk mendapatkan analisis data
function getAnalysis($pdo) {
    $stmt = $pdo->query("SELECT COUNT(reservation_id) AS total_reservations, SUM(total_price) AS total_income 
                          FROM reservations WHERE status = 'completed'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Eksekusi fungsi
$analysis = getAnalysis($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan dan Analisis</title>
    <link rel="stylesheet" href="../css/styledash.css"> <!-- Pastikan untuk menggunakan styledash.css -->
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_rentals.php">Manage Rentals</a></li>
            <li><a href="manage_reservations.php">Manage Reservations</a></li>
            <li><a href="analisis.php" class="active">Laporan dan Analisis</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="admin-dashboard">
        <h1>Laporan dan Analisis</h1>

        <h2>Statistik Reservasi </h2>
        <p>Total Reservasi: <?= htmlspecialchars($analysis['total_reservations']) ?></p>
        <p>Total Pendapatan: <?= htmlspecialchars($analysis['total_income']) ?> IDR</p>
    </div>
</body>
</html>