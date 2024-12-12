<?php
include 'koneksi.php';
session_start();

// Cek apakah user memiliki peran admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$upload_dir = "../uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Menambah kendaraan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $car_name = $_POST['car_name'];
    $type_car = $_POST['type_car'];
    
    $car_image = $_FILES['car_image'];
    $image_name = null;

    // Proses upload gambar
    if ($car_image['error'] === 0) {
        $image_name = time() . '_' . basename($car_image['name']);
        $destination = $upload_dir . $image_name;

        if (move_uploaded_file($car_image['tmp_name'], $destination)) {
            // Simpan data ke database
            $stmt = $pdo->prepare("INSERT INTO rentals (car_name, type_car, car_image) VALUES (?, ?, ?)");
            $stmt->execute([$car_name, $type_car, $image_name]);
        }
    }
}

// Menghapus kendaraan
if (isset($_GET['delete'])) {
    $rental_id = $_GET['delete'];
    
    // Ambil nama file gambar dari database
    $stmt = $pdo->prepare("SELECT car_image FROM rentals WHERE rental_id = ?");
    $stmt->execute([$rental_id]);
    $row = $stmt->fetch();
    
    // Hapus file gambar jika ada
    if ($row && $row['car_image']) {
        $file_path = $upload_dir . $row['car_image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Hapus data dari database
    $stmt = $pdo->prepare("DELETE FROM rentals WHERE rental_id = ?");
    $stmt->execute([$rental_id]);
}

// Ambil data kendaraan untuk ditampilkan
$rentals = $pdo->query("SELECT * FROM rentals");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kendaraan</title>
    <link rel="stylesheet" href="../css/styledash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_rentals.php" class="active">Manage Rentals</a></li>
            <li><a href="manage_reservations.php">Manage Reservations</a></li>
            <li><a href="analisis.php">Laporan dan Analisis</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="admin-dashboard">
        <h1>Kelola Kendaraan</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="car_name" placeholder="Nama Kendaraan" required>
            <select name="type_car" required>
                <option value="lgcc">LGCC</option>
                <option value="suv">SUV</option>
                <option value="sedan">Sedan</option>
            </select>
            <a href="manage_types.php" title="Kelola Tipe Kendaraan" style="margin-left: 10px; font-size: 1.2em; color:  #f9532d;">
                <i class="fas fa-wrench"></i>
            </a>
            <input type="file" name="car_image" accept="image/*" required>
            <button type="submit" name="add">Tambah Kendaraan</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nama Kendaraan</th>
                    <th>Tipe</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($rental = $rentals->fetch()): ?>
                <tr>
                    <td><?= htmlspecialchars($rental['car_name']) ?></td>
                    <td><?= htmlspecialchars($rental['type_car']) ?></td>
                    <td>
                        <?php if ($rental['car_image']): ?>
                            <img src="<?= htmlspecialchars($upload_dir . $rental['car_image']) ?>" alt="Car Image" style="max-width: 100px;">
                        <?php else: ?>
                            <span>Gambar tidak tersedia</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?= $rental['rental_id'] ?>" onclick="return confirm('Yakin ingin menghapus kendaraan ini?')">Hapus</a>
                        <a href="edit_rental.php?id=<?= $rental['rental_id'] ?>">Edit</a>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
