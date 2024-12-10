<?php
session_start();
require_once 'BarangManager.php'; // Memanggil class BarangManager

$barangManager = new BarangManager();
$barangList = $barangManager->getBarang(); // Mendapatkan data barang dari JSON

// Inisialisasi data pelanggan jika belum ada
if (!isset($_SESSION['customers'])) {
    $_SESSION['customers'] = [];
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $namaPelanggan = htmlspecialchars($_POST['nama_pelanggan']);
    $alamatPengiriman = htmlspecialchars($_POST['alamat']);
    $kontakPelanggan = htmlspecialchars($_POST['kontak']); // Ambil nilai kontak dari form
    $produkDipilih = htmlspecialchars($_POST['produk']);
    $jumlahDipilih = (int)htmlspecialchars($_POST['jumlah']);

    if (!empty($namaPelanggan) && !empty($alamatPengiriman) && !empty($kontakPelanggan) && !empty($produkDipilih) && $jumlahDipilih > 0) {
        foreach ($barangList as &$barang) {
            if ($barang['nama'] === $produkDipilih) {
                if ($barang['stok'] >= $jumlahDipilih) {
                    // Kurangi stok barang
                    $barang['stok'] -= $jumlahDipilih;
                    $barangManager->updateBarang($barangList);

                    // Tambahkan ke daftar pelanggan
                    $_SESSION['customers'][] = [
                        'name' => $namaPelanggan,
                        'contact' => $kontakPelanggan, // Masukkan kontak pelanggan
                        'alamat' => $alamatPengiriman,
                        'produk' => $produkDipilih,
                        'jumlah' => $jumlahDipilih
                    ];
                    $success = "Checkout berhasil! Data pelanggan ditambahkan.";
                } else {
                    $error = "Stok tidak mencukupi untuk produk ini.";
                }
                break;
            }
        }
    } else {
        $error = "Semua bidang harus diisi dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style3.css">
    <title>Checkout Produk</title>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_pelanggan">Nama Pelanggan:</label>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>

            <label for="kontak">Kontak</label>
            <input type="text" id="kontak" name="kontak" required>


            <label for="alamat">Alamat Pengiriman:</label>
            <textarea id="alamat" name="alamat" required></textarea>

            <label for="produk">Produk:</label>
            <select id="produk" name="produk" required>
                <?php foreach ($barangList as $barang): ?>
                    <option value="<?= htmlspecialchars($barang['nama']) ?>">
                        <?= htmlspecialchars($barang['nama']) ?> (Stok: <?= $barang['stok'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="jumlah">Jumlah Produk:</label>
            <input type="number" id="jumlah" name="jumlah" min="1" required>

            <button type="submit" name="checkout">Proses Pembayaran</button>
        </form>
    </div>
</body>
</html>
