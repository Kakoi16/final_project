<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat penyewaan pengguna beserta status review
$stmt = $pdo->prepare("
    SELECT r.reservation_id, rt.car_name, r.reservation_date, r.status, r.total_price,
           (SELECT COUNT(*) FROM reviews rv WHERE rv.reservation_id = r.reservation_id) AS has_review
    FROM reservations r
    JOIN rentals rt ON r.rental_id = rt.rental_id
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Penyewaan</title>
    <link rel="stylesheet" href="../css/styledash.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="backend/logout.php">Logout</a></li>
            <li><a href="#" class="active">Riwayat Penyewaan</a></li>
        </ul>
    </div>

    <div class="admin-dashboard">
        <h1>History Penyewaan</h1>
        <table>
            <tr>
                <th>ID Reservasi</th>
                <th>Nama Kendaraan</th>
                <th>Tanggal Penyewaan</th>
                <th>Status</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= htmlspecialchars($reservation['reservation_id']) ?></td>
                <td><?= htmlspecialchars($reservation['car_name']) ?></td>
                <td><?= htmlspecialchars($reservation['reservation_date']) ?></td>
                <td><?= htmlspecialchars($reservation['status']) ?></td>
                <td>Rp <?= number_format($reservation['total_price'], 2) ?></td>
                <td>
                    <?php if ($reservation['status'] == 'completed' && $reservation['has_review'] == 0): ?>
                        <a href="review.php?id=<?= $reservation['reservation_id'] ?>">Beri Review</a>
                    <?php elseif ($reservation['has_review'] > 0): ?>
                        Sudah Direview
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
