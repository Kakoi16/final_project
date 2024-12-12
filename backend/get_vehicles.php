<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "rental_vehicle");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mendapatkan parameter filter
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

// Query untuk mendapatkan kendaraan
$query = "SELECT rental_id, car_name, type_car, car_image FROM rentals";

if ($typeFilter) {
    $query .= " WHERE type_car = ?";
}

$stmt = $conn->prepare($query);

if ($typeFilter) {
    $stmt->bind_param("s", $typeFilter);
}

$stmt->execute();
$result = $stmt->get_result();

$upload_dir = 'uploads/'; // Path folder upload
$vehicles = [];
while ($row = $result->fetch_assoc()) {
    $row['car_image'] = $row['car_image'] ? $upload_dir . $row['car_image'] : null; // Tambahkan path lengkap
    $vehicles[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($vehicles);
?>
