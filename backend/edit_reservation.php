<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Ambil data reservasi berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
    $stmt->execute([$id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo "Reservasi tidak ditemukan!";
        exit();
    }
}

// Proses update reservasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
    $stmt->execute([$status, $id]);

    header('Location: manage_reservations.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservasi</title>
    <link rel="stylesheet" href="../css/styledash.css">
</head>
<body>
<div class="navbar">
        <div class="logo">
            <a href="index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_rentals.php">Manage Rentals</a></li>
            <li><a href="manage_reservations.php" class="active">Manage Reservations</a></li>
            <li><a href="analisis.php">Laporan dan Analisis</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <h1>Edit Reservasi</h1>
    <div class="admin-dashboard">
    <form method="POST">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="pending" <?= $reservation['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $reservation['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="completed" <?= $reservation['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="canceled" <?= $reservation['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>
        <br><br>
        <button type="submit">Update</button>
        <a href="manage_reservations.php">Batal</a>
    </form>
    </div>
</body>
</html>
