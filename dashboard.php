<?php
include 'backend/koneksi.php'; // Koneksi database
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location:backend/login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AutoRental</title>
    <link rel="stylesheet" href="css/styledash.css"> <!-- Link ke file CSS -->
    <style>
        /* Tambahkan CSS untuk vehicle-gallery */
        .vehicle-gallery {
            display: grid; /* Menggunakan grid untuk tata letak */
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* 3-4 kolom tergantung ukuran */
            gap: 20px; /* Jarak antar gambar */
            margin: 20px; /* Margin di sekitar gallery */
        }

        /* Modifikasi vehicle-card jika perlu */
        .vehicle-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Gambar dalam vehicle-card */
        .vehicle-card img {
            width: 100%; /* Memastikan gambar memenuhi lebar card */
            height: auto; /* Menjaga rasio aspek gambar */
            border-radius: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.html">Logo</a>
        </div>
        <ul class="menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="backend/logout.php">Logout</a></li>
            <li><a href="backend/history.php" id="historyLink">Riwayat Penyewaan</a></li>
        </ul>
    </header>

    <main class="admin-dashboard">
        <h1>Dashboard</h1>
        <h2>Available Vehicles</h2>
        <input type="text" id="search" placeholder="Search by vehicle name...">
        <select id="typeFilter">
            <option value="">All Types</option>
            <option value="lgcc">LGCC</option>
            <option value="suv">SUV</option>
            <option value="sedan">Sedan</option>
        </select>
        
        <!-- Container untuk daftar kendaraan -->
        <div id="vehicleList" class="vehicle-gallery">
            <!-- Contoh vehicle-card -->
            <div class="vehicle-card">
                <img src="path/to/image1.jpg" alt="Vehicle 1">
                <h3>Vehicle 1</h3>
                <p>Description of Vehicle 1</p>
                <button>View Details</button>
            </div>
            <div class="vehicle-card">
                <img src="path/to/image2.jpg" alt="Vehicle 2">
                <h3>Vehicle 2</h3>
                <p>Description of Vehicle 2</p>
                <button>View Details</button>
            </div>
             <div class="vehicle-card">
                <img src="path/to/image3.jpg" alt="Vehicle 3">
                <h3>Vehicle 3</h3>
                <p>Description of Vehicle 3</p>
                <button>View Details</button>
            </div>
            <!-- Tambahkan lebih banyak vehicle-card sesuai kebutuhan -->
        </div>

        <!-- Popup Rental Form -->
        <div id="rentalPopup" class="popup hidden">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <h2>Rent Vehicle</h2>
                <form id="rentVehicleForm">
                    <input type="hidden" id="rentalVehicleId">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" required>
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" required>
                    <label for="bookingNotes">Booking Notes:</label>
                    <textarea id="bookingNotes"></textarea>
                    <button type="submit">Confirm Rental</button>
                </form>
            </div>
        </div>
        
        <!-- Popup Payment Form -->
    <div id="paymentPopup" class="popup hidden">
        <div class="popup-content">
        <span class="close-btn" onclick="closePaymentPopup()">&times;</span>
        <h2>Payment Confirmation</h2>
        <p>Your reservation has been confirmed. Please proceed with the payment.</p>
        <button onclick="Confirm Rental()">Pay Now</button>
        </div>
    </div>

        <button id="loadHistory">Load Rental History</button>
        <div id="historyContent"></div>
    </main>

    <script src="js/dashboard.js"></script> <!-- Link ke file JavaScript -->
</body>
</html>