<?php
include 'koneksi.php';
session_start();

// Cek apakah user memiliki peran admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil data kendaraan berdasarkan ID
if (isset($_GET['id'])) {
    $rental_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM rentals WHERE rental_id = ?");
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch();

    // Redirect jika data tidak ditemukan
    if (!$rental) {
        header('Location: manage_rentals.php');
        exit();
    }
}

// Direktori upload gambar
$upload_dir = "../uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Proses update kendaraan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $car_name = $_POST['car_name'];
    $type_car = $_POST['type_car'];
    $car_image = $_FILES['car_image'];
    $image_name = $rental['car_image']; // Default ke gambar lama jika tidak ada gambar baru

    // Proses upload gambar jika ada file baru
    if ($car_image['error'] === 0) {
        // Hapus gambar lama jika ada
        if ($image_name && file_exists($upload_dir . $image_name)) {
            unlink($upload_dir . $image_name);
        }

        // Simpan gambar baru
        $image_name = time() . '_' . basename($car_image['name']);
        $destination = $upload_dir . $image_name;

        if (!move_uploaded_file($car_image['tmp_name'], $destination)) {
            echo "Gagal mengunggah gambar!";
            exit();
        }
    }

    // Update data kendaraan di database
    $stmt = $pdo->prepare("UPDATE rentals SET car_name = ?, type_car = ?, car_image = ? WHERE rental_id = ?");
    $stmt->execute([$car_name, $type_car, $image_name, $rental_id]);

    // Redirect ke halaman manage_rentals.php setelah update
    header('Location: manage_rentals.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kendaraan</title>
    <link rel="stylesheet" href="../css/styledash.css">
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
        <h1>Edit Kendaraan</h1>
        <form method="post" enctype="multipart/form-data">
            <div>
                <label for="car_name">Nama Kendaraan</label>
                <input type="text" name="car_name" id="car_name" value="<?= htmlspecialchars($rental['car_name']) ?>" required>
            </div>

            <div>
                <label for="type_car">Tipe Kendaraan</label>
                <select name="type_car" id="type_car" required>
                    <option value="lgcc" <?= $rental['type_car'] == 'lgcc' ? 'selected' : '' ?>>LGCC</option>
                    <option value="suv" <?= $rental['type_car'] == 'suv' ? 'selected' : '' ?>>SUV</option>
                    <option value="sedan" <?= $rental['type_car'] == 'sedan' ? 'selected' : '' ?>>Sedan</option>
                </select>
            </div>

            <div>
                <label for="car_image">Ganti Gambar Kendaraan</label>
                <input type="file" name="car_image" id="car_image" accept="image/*">
                <?php if ($rental['car_image']): ?>
                    <p>Gambar saat ini:</p>
                    <img src="<?= htmlspecialchars($upload_dir . $rental['car_image']) ?>" alt="Car Image" style="max-width: 150px;">
                <?php endif; ?>
            </div>

            <button type="submit" name="update">Update Kendaraan</button>
        </form>

        <a href="manage_rentals.php">Kembali ke Daftar Kendaraan</a>
    </div>
</body>
</html>
