<?php
include 'koneksi.php'; // Koneksi database
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fungsi untuk menambah kendaraan
function addVehicle($pdo) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vehicle'])) {
        $car_name = $_POST['car_name'];
        $type_car = $_POST['type_car'];

        $stmt = $pdo->prepare("INSERT INTO rentals (car_name, type_car) VALUES (:car_name, :type_car)");
        $stmt->execute(['car_name' => $car_name, 'type_car' => $type_car]);
    }
}

// Fungsi untuk menghapus kendaraan
function deleteVehicle($pdo) {
    if (isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM rentals WHERE rental_id = :id");
        $stmt->execute(['id' => $id]);
    }
}

// Fungsi untuk mendapatkan semua kendaraan
function getVehicles($pdo) {
    $stmt = $pdo->query("SELECT * FROM rentals");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Eksekusi fungsi
addVehicle($pdo);
deleteVehicle($pdo);
$vehicles = getVehicles($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kelola Kendaraan</title>
    <link rel="stylesheet" href="../css/styledash.css"> <!-- Pastikan untuk menggunakan styledash.css -->
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="../index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="manage_rentals.php">Manage Rentals</a></li>
            <li><a href="manage_reservations.php">Manage Reservations</a></li>
            <li><a href="analisis.php">Laporan dan Analisis</a></li> <!-- Menu baru untuk laporan dan analisis -->
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="admin-dashboard">
        <h1>Kelola Kendaraan</h1>

        <!-- Form untuk menambah kendaraan -->
        <h2>Tambah Kendaraan</h2>
        <form method="POST">
            <input type="text" name="car_name" placeholder="Nama Kendaraan" required>
            <select name="type_car" required>
                <option value="lgcc">LGCC</option>
                <option value="suv">SUV</option>
                <option value="sedan">Sedan</option>
            </select>
            <button type="submit" name="add_vehicle">Tambah Kendaraan</button>
        </form>

        <!-- Tabel daftar kendaraan -->
        <h2>Daftar Kendaraan</h2>
        <table>
            <tr>
                <th>Nama Kendaraan</th>
                <th>Tipe</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($vehicles as $rental): ?>
            <tr>
                <td><?= htmlspecialchars($rental['car_name']) ?></td>
                <td><?= htmlspecialchars($rental['type_car']) ?></td>
                <td>
                    <a href="?delete_id=<?= $rental['rental_id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>