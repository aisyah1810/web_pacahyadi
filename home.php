<?php
// home.php
session_start();
require_once 'BarangManager.php'; // Pastikan file BarangManager.php sudah ada dan sesuai

$barangManager = new BarangManager();
$barangList = $barangManager->getBarang(); // Mendapatkan data barang dari JSON

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h1>TOKO DIY</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="customer.php">Customer</a></li>
                <li><a href="index.php">Stok</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <center>
            <br><br><br>
        <h1>Selamat datang di Toko kami!</h1>
        <p>Silakan pilih menu untuk barang impianmu.</p>
        <br><br><br>
        <img src="assets/bajup.jpg" alt="Deskripsi Gambar" width="400" height="460" style="border-radius: 15px;">
        </center>
    </main>
    <br><br><br>
    <center>
    <font color=white><div class="stok"><a href="index.php">Stok Barang</a></div></g
    </center>
   <br><br><br>
    <footer>
        <p>&copy; 2024 Aisyah Suci Maisyaro.</p>
    </footer>
</body>
</html>
