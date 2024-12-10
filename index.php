<?php
// Membaca data dari file JSON
$barangJson = file_get_contents('data.json');
$barangList = json_decode($barangJson, true);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="customer.php">Customer</a></li>
            <li><a href="index.php">Stok</a></li>
        </ul>
    </nav>

    <!-- Konten Utama -->
    <div class="container">
        <h1>Daftar Barang</h1>
        <div class="product-grid">
            <?php if (!empty($barangList)): ?>
                <?php foreach ($barangList as $barang): ?>
                    <div class="product-card">
                        <!-- Gambar produk -->
                        <img src="<?= htmlspecialchars($barang['gambar']) ?>" alt="<?= htmlspecialchars($barang['nama']) ?>">
                        <div class="product-info">
                            <h3><?= htmlspecialchars($barang['nama']) ?></h3>
                            <?php 
                                // Pastikan harga adalah angka
                                $harga = str_replace('Rp ', '', $barang['harga']); // Hapus simbol Rp jika ada
                                $harga = str_replace('.', '', $harga); // Hapus titik pemisah ribuan jika ada
                                $harga = (float)$harga; // Ubah menjadi angka
                            ?>
                            <p>Harga: Rp <?= number_format($harga, 0, ',', '.') ?></p>
                            <p>Stok: <?= htmlspecialchars($barang['stok']) ?></p>
                            <!-- Tombol beli dengan link ke halaman checkout -->
                            <button onclick="window.location.href='checkout.php?id=<?= htmlspecialchars($barang['id']) ?>'">Beli Sekarang</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada barang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Aisyah Suci Maisyaro.</p>
    </footer>
</body>
</html>
