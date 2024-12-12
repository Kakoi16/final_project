<?php
include './backend/koneksi.php'; // Koneksi database

// Ambil data penyewaan dari database
$rentalData = array();
$query = "SELECT * FROM reservations";
$stmt = $pdo->prepare($query);s
$stmt->execute();
$rentalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Cash</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-header {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .chat-body {
            padding: 10px;
            overflow-y: auto;
            max-height: 300px;
        }
        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        #chat-log {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #chat-log li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        #chat-log li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Chat dengan Admin</h3>
                <div class="chat-box">
                    <div class="chat-header">
                        <h4>Chat dengan aAdmin</h4>
                    </div>
                    <div class="chat-body">
                        <ul id="chat-log">
                            <!-- Chat log akan ditampilkan di sini -->
                        </ul>
                    </div>
                    <div class="chat-footer">
                        <input type="text" id="chat-input" placeholder="Tulis pesan...">
                        <button id="send-btn">Kirim</button>
                        <button id="send-image-btn">Kirim Gambar</button>
                        <input type="file" id="image-input" accept="image/*">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Riwayat Penyewaan</h3>
                <table class="table table-striped">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Kendaraan</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Akhir</th>
            <th>Catatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rentalData as $rental) { ?>
            <tr>
                <td><?php echo $rental['reservation_id']; ?></td>
                <td><?php echo $rental['rental_id']; ?></td>
                <td><?php echo $rental['start_time']; ?></td>
                <td><?php echo $rental['end_time']; ?></td>
                <td><?php echo $rental['booking_notes']; ?></td>
                <td><?php echo $rental['status']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        const chatLog = document.getElementById('chat-log');
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        const sendImageBtn = document.getElementById('send-image-btn');
        const imageInput = document.getElementById('image-input');

        // Fungsi untuk mengirim pesan
        function sendMessage() {
            const message = chatInput.value;
            if (message.trim() !== '') {
                // Kirim pesan ke server
                $.ajax({
                    type: 'POST',
                    url: '/send-message',
                    data: { message: message },
                    success: function(data) {
                        // Tampilkan pesan di chat log
                        const messageElement = document.createElement('li');
                        messageElement.textContent = `Anda: ${message}`;
                        chatLog.appendChild(messageElement);
                        chatInput.value = '';
                    }
                });
            }
        }

        // Fungsi untuk mengirim gambar
        function sendImage() {
            const image = imageInput.files[0];
            if (image) {
                // Kirim gambar ke server
                const formData = new FormData();
                formData.append('image', image);
                $.ajax({
                    type: 'POST',
                    url: '/send-image',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        // Tampilkan gambar di chat log
                        const imageElement = document.createElement('li');
                        imageElement.innerHTML = `<img src="${data.url}" alt="Gambar">`;
                        chatLog.appendChild(imageElement);
                        imageInput.value = '';
                    }
                });
            }
        }

        // Tambahkan event listener pada tombol kirim
        sendBtn.addEventListener('click', sendMessage);
        sendImageBtn.addEventListener('click', sendImage);
    </script>
</body>
</html>