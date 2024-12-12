<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi database sudah diimpor

header('Content-Type: application/json'); // Pastikan output berupa JSON

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
    exit;
}

// Ambil data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

// Debugging optional: gunakan var_dump untuk memastikan request jika perlu
// var_dump($_REQUEST); die;

// Gunakan $_REQUEST untuk menangani permintaan data
$rentalId = isset($_REQUEST['rentalId']) ? $_REQUEST['rentalId'] : null;
$startDate = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : null;
$endDate = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : null;
$bookingNotes = isset($_REQUEST['bookingNotes']) ? $_REQUEST['bookingNotes'] : '';
$userId = $_SESSION['user_id'];

// Validasi input
if (empty($rentalId) || empty($startDate) || empty($endDate)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap!']);
    exit;
}

// Masukkan data ke database
try {
    $stmt = $pdo->prepare("
        INSERT INTO reservations (user_id, rental_id, reservation_date, start_time, end_time, booking_notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$userId, $rentalId, $startDate, $startDate, $endDate, $bookingNotes]);

    echo json_encode(['success' => true, 'message' => 'Reservation successful!']);
} catch (Exception $e) {
    // Tangkap pesan kesalahan dan kembalikan ke frontend
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>