<?php
include 'koneksi.php'; // Koneksi database
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fungsi untuk mendapatkan semua reservasi
function getReservations($pdo) {
    $stmt = $pdo->query("SELECT 
                            r.reservation_id, 
                            u.nama_lengkap, 
                            rt.car_name, 
                            r.reservation_date, 
                            DATE_ADD(r.reservation_date, INTERVAL TIME_TO_SEC(r.duration_time) SECOND) AS end_date,
                            r.status 
                         FROM reservations r 
                         JOIN users u ON r.user_id = u.user_id 
                         JOIN rentals rt ON r.rental_id = rt.rental_id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Eksekusi fungsi
$reservations = getReservations($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
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
            <li><a href="manage_reservations.php" class="active">Manage Reservations</a></li>
            <li><a href="analisis.php">Laporan dan Analisis</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="admin-dashboard">
        <h1>Kelola Reservasi</h1>

        <!-- Tabel daftar reservasi -->
<table>
    <tr>
        <th>ID Reservasi</th>
        <th>Nama Pengguna</th>
        <th>Nama Kendaraan</th>
        <th>Tanggal Pemesanan</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th> <!-- Kolom untuk tombol edit -->
    </tr>
    <?php foreach ($reservations as $reservation): ?>
    <tr>
        <td><?= htmlspecialchars($reservation['reservation_id']) ?></td>
        <td><?= htmlspecialchars($reservation['nama_lengkap']) ?></td>
        <td><?= htmlspecialchars($reservation['car_name']) ?></td>
        <td><?= htmlspecialchars($reservation['reservation_date']) ?></td>
        <td><?= htmlspecialchars($reservation['end_date']) ?></td>
        <td><?= htmlspecialchars($reservation['status']) ?></td>
        <td>
            <a href="edit_reservation.php?id=<?= $reservation['reservation_id'] ?>" class="btn-edit">Edit</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

    </div>
</body>
</html>