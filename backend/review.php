<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'] ?? null;

// Ambil data reservasi untuk memastikan validitas
$stmt = $pdo->prepare("
    SELECT r.*, rt.car_name 
    FROM reservations r
    JOIN rentals rt ON r.rental_id = rt.rental_id
    WHERE r.reservation_id = ? AND r.user_id = ?
");
$stmt->execute([$reservation_id, $user_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "Reservasi tidak valid!";
    exit();
}

// Proses submit review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    $stmt = $pdo->prepare("INSERT INTO reviews (reservation_id, rating, review_text) VALUES (?, ?, ?)");
    $stmt->execute([$reservation_id, $rating, $review_text]);

    header('Location: history.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Review</title>
    <link rel="stylesheet" href="../css/styledash.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Beri Review untuk <?= htmlspecialchars($reservation['car_name']) ?></h1>
        <form method="POST">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" required>
                <option value="1">1 - Sangat Buruk</option>
                <option value="2">2 - Buruk</option>
                <option value="3">3 - Cukup</option>
                <option value="4">4 - Baik</option>
                <option value="5">5 - Sangat Baik</option>
            </select>

            <label for="review_text">Komentar:</label>
            <textarea name="review_text" required></textarea>

            <button type="submit">Kirim Review</button>
            <a href="history.php">Batal</a>
        </form>
    </div>
</body>
</html>
