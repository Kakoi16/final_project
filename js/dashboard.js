dashboard.js

// Fungsi untuk mengambil data kendaraan dari server
async function fetchVehicles() {
    try {
        const response = await fetch('backend/get_vehicles.php'); // Memanggil API
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const vehicles = await response.json(); // Mengubah respons menjadi format JSON
        displayVehicles(vehicles); // Menampilkan kendaraan di UI
    } catch (error) {
        console.error("Error fetching vehicles:", error);
        alert("Gagal memuat kendaraan. Silakan coba lagi.");
    }
}

// Fungsi untuk menampilkan kendaraan di halaman
function displayVehicles(vehicles) {
    const vehicleList = document.getElementById('vehicleList');
    vehicleList.innerHTML = ''; // Kosongkan daftar kendaraan terlebih dahulu

    if (vehicles.length === 0) {
        vehicleList.innerHTML = '<p>Tidak ada kendaraan tersedia.</p>';
        return;
    }

    vehicles.forEach(vehicle => {
        const vehicleDiv = document.createElement('div');
        vehicleDiv.className = 'vehicle-card';

        vehicleDiv.innerHTML = `
            <img src="${vehicle.car_image || 'assets/default_image.jpg'}" alt="${vehicle.car_name}" class="vehicle-image" onerror="this.src='assets/default_image.jpg';">
            <h3>${vehicle.car_name}</h3>
            <p>Tipe: ${vehicle.type_car}</p>
            <button onclick="showRentalPopup(${vehicle.rental_id})">Pesan</button>
        `;
        vehicleList.appendChild(vehicleDiv);
    });
}

// Fungsi untuk menampilkan popup pemesanan
function showRentalPopup(rentalId) {
    document.getElementById('rentalVehicleId').value = rentalId;
    document.getElementById('rentalPopup').style.display = 'flex';
}

// Fungsi untuk menutup popup
function closePopup() {
    document.getElementById('rentalPopup').style.display = 'none';
}

// Event listener untuk menangani form pemesanan kendaraan
document.getElementById('rentVehicleForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const rentalId = document.getElementById('rentalVehicleId').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const bookingNotes = document.getElementById('bookingNotes').value;

    try {
        const response = await fetch('backend/rent_vehicle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                rentalId: rentalId,
                reservation_date: new Date().toISOString().split('T')[0],
                start_time: startDate,
                end_time: endDate,
                bookingNotes: bookingNotes
            })
        });

        const result = await response.json();
        alert(result.message);
        closePopup(); // Menutup pop-up pemesanan
        showPaymentPopup(); // Menampilkan pop-up pembayaran
        fetchVehicles(); // Refresh daftar kendaraan
    } catch (error) {
        console.error("Error renting vehicle:", error);
        alert("Gagal menyewa kendaraan. Silakan coba lagi.");
    }
});


// Fetch vehicles on page load
fetchVehicles();


// Fungsi untuk menampilkan popup pembayaran
function showPaymentPopup() {
    document.getElementById('paymentPopup').style.display = 'flex';
}

// Fungsi untuk menutup popup pembayaran
function closePaymentPopup() {
    document.getElementById('paymentPopup').style.display = 'none';
}

// Fungsi untuk menangani pembayaran
function confirmPayment() {
    alert('Payment processing...');
    closePaymentPopup();
}


// Load history when the button is clicked
document.getElementById('loadHistory').addEventListener('click', async function () {
    try {
        const response = await fetch('backend/history.php'); // Memanggil API untuk mendapatkan riwayat
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const historyData = await response.json(); // Mengubah respons menjadi format JSON
        displayHistory(historyData); // Menampilkan riwayat di UI
    } catch (error) {
        console.error('Error fetching history:', error);
        document.getElementById('historyContent').innerHTML = '<p>Gagal memuat riwayat penyewaan.</p>'; // Menampilkan pesan error
    }
});

// Fungsi untuk menampilkan riwayat penyewaan
function displayHistory(history) {
    const historyContent = document.getElementById('historyContent');
    historyContent.innerHTML = ''; // Kosongkan konten sebelumnya

    if (history.length === 0) {
        historyContent.innerHTML = '<p>Tidak ada riwayat penyewaan.</p>'; // Menampilkan pesan jika tidak ada riwayat
        return;
    }

    const table = document.createElement('table');
    table.innerHTML = `
        <thead>
            <tr>
                <th>ID Reservasi</th>
                <th>Nama Kendaraan</th>
                <th>Jenis Kendaraan</th>
                <th>Tanggal Reservasi</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Total Harga</th>
                <th>Status Reservasi</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    `;

    history.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.reservation_id}</td>
            <td>${item.car_name}</td>
            <td>${item.type_car}</td>
            <td>${item.reservation_date}</td>
            <td>${item.start_time}</td>
            <td>${item.end_time}</td>
            <td>Rp${item.total_price.toLocaleString()}</td>
            <td>${item.reservation_status}</td>
            <td>${item.payment_method || '-'}</td>
            <td>${item.payment_status || '-'}</td>
        `;
        table.querySelector('tbody').appendChild(row); // Menambahkan baris ke tabel
    });

    historyContent.appendChild(table); // Menambahkan tabel ke konten riwayat
}
