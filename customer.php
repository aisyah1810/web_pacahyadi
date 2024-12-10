<?php
// Memulai sesi
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
$editingIndex = null; // Menyimpan index pelanggan yang sedang diedit

// Menambahkan data pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $name = htmlspecialchars($_POST['name']);
    $contact = htmlspecialchars($_POST['contact']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $produk = htmlspecialchars($_POST['produk']);
    $jumlah = (int)htmlspecialchars($_POST['jumlah']);

    if (!empty($name) && !empty($contact) && !empty($alamat) && !empty($produk) && $jumlah > 0) {
        $barangDitemukan = false;
        foreach ($barangList as &$barang) {
            if ($barang['nama'] === $produk) {
                if ($barang['stok'] >= $jumlah) {
                    $barang['stok'] -= $jumlah;
                    $barangManager->updateBarang($barangList);

                    $_SESSION['customers'][] = [
                        'name' => $name,
                        'contact' => $contact,
                        'alamat' => $alamat,
                        'produk' => $produk,
                        'jumlah' => $jumlah,
                    ];

                    $success = "Pelanggan berhasil ditambahkan!";
                    $barangDitemukan = true;
                } else {
                    $error = "Stok produk tidak mencukupi!";
                }
                break;
            }
        }

        if (!$barangDitemukan) {
            $error = "Produk tidak ditemukan!";
        }
    } else {
        $error = "Semua bidang harus diisi dan jumlah harus valid!";
    }
}

// Menghapus data pelanggan berdasarkan index
if (isset($_POST['delete_customer'])) {
    $indexToDelete = $_POST['delete_customer'];
    $deletedCustomer = $_SESSION['customers'][$indexToDelete];

    foreach ($barangList as &$barang) {
        if ($barang['nama'] === $deletedCustomer['produk']) {
            $barang['stok'] += $deletedCustomer['jumlah'];
            $barangManager->updateBarang($barangList);
            break;
        }
    }

    array_splice($_SESSION['customers'], $indexToDelete, 1);
    $success = "Pelanggan berhasil dihapus!";
}

// Menyiapkan data untuk form edit
if (isset($_POST['edit_customer'])) {
    $editingIndex = $_POST['edit_customer'];
}

// Menyimpan perubahan data pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customer'])) {
    $indexToEdit = $_POST['save_customer'];
    $name = htmlspecialchars($_POST['name']);
    $contact = htmlspecialchars($_POST['contact']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $produkBaru = htmlspecialchars($_POST['produk']);
    $jumlahBaru = (int)htmlspecialchars($_POST['jumlah']);

    if (!empty($name) && !empty($contact) && !empty($alamat) && !empty($produkBaru) && $jumlahBaru > 0) {
        $customer = &$_SESSION['customers'][$indexToEdit];
        $produkLama = $customer['produk'];
        $jumlahLama = $customer['jumlah'];

        foreach ($barangList as &$barang) {
            if ($barang['nama'] === $produkLama) {
                $barang['stok'] += $jumlahLama; // Kembalikan stok lama
            }
            if ($barang['nama'] === $produkBaru && $barang['stok'] >= $jumlahBaru) {
                $barang['stok'] -= $jumlahBaru; // Kurangi stok baru
                $barangManager->updateBarang($barangList);

                // Perbarui data pelanggan
                $customer['name'] = $name;
                $customer['contact'] = $contact;
                $customer['alamat'] = $alamat;
                $customer['produk'] = $produkBaru;
                $customer['jumlah'] = $jumlahBaru;

                $success = "Data pelanggan berhasil diperbarui!";
                break;
            } elseif ($barang['nama'] === $produkBaru && $barang['stok'] < $jumlahBaru) {
                $error = "Stok produk baru tidak mencukupi!";
                break;
            }
        }
    } else {
        $error = "Semua bidang harus diisi dan jumlah harus valid!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="customer.php">Customer</a></li>
            <li><a href="index.php">Stok</a></li>
        </ul>
    </nav>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css"> <!-- Menautkan file CSS -->
    <title>Manajemen Pelanggan</title>
</head>
<body>
    <h1>Manajemen Pelanggan</h1>

    <!-- Pesan Error -->
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <!-- Pesan Keberhasilan -->
    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <!-- Form Tambah/Perbarui Pelanggan -->
    <form method="POST">
        <h2><?= $editingIndex !== null ? "Edit Pelanggan" : "Tambah Pelanggan" ?></h2>
        <label>Nama: <input type="text" name="name" value="<?= $editingIndex !== null ? $_SESSION['customers'][$editingIndex]['name'] : '' ?>" required></label><br>
        <label>Kontak: <input type="text" name="contact" value="<?= $editingIndex !== null ? $_SESSION['customers'][$editingIndex]['contact'] : '' ?>" required></label><br>
        <label>Alamat: <input type="text" name="alamat" value="<?= $editingIndex !== null ? $_SESSION['customers'][$editingIndex]['alamat'] : '' ?>" required></label><br>
        <label>Produk: 
            <select name="produk" required>
                <?php foreach ($barangList as $barang): ?>
                    <option value="<?= $barang['nama'] ?>" <?= $editingIndex !== null && $_SESSION['customers'][$editingIndex]['produk'] === $barang['nama'] ? 'selected' : '' ?>><?= $barang['nama'] ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Jumlah: <input type="number" name="jumlah" min="1" value="<?= $editingIndex !== null ? $_SESSION['customers'][$editingIndex]['jumlah'] : '' ?>" required></label><br>
        <button type="submit" name="<?= $editingIndex !== null ? 'save_customer' : 'add_customer' ?>" value="<?= $editingIndex !== null ? $editingIndex : '' ?>">
            <?= $editingIndex !== null ? "Simpan Perubahan" : "Tambah Pelanggan" ?>
        </button>
    </form>

    <!-- Daftar Pelanggan -->
    <h2>Daftar Pelanggan</h2>
    <?php if (!empty($_SESSION['customers'])): ?>
        <ul>
            <?php foreach ($_SESSION['customers'] as $index => $customer): ?>
                <li>
                    <?= "{$customer['name']} - {$customer['contact']} - {$customer['produk']} - {$customer['jumlah']}" ?>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="edit_customer" value="<?= $index ?>">Edit</button>
                        <button type="submit" name="delete_customer" value="<?= $index ?>">Hapus</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <form method="POST">
            <button type="submit" name="clear_customers">Hapus Semua Pelanggan</button>
        </form>
    <?php else: ?>
        <p>Tidak ada pelanggan.</p>
    <?php endif; ?>
    <footer>
        <p>&copy; 2024 Aisyah Suci Maisyaro.</p>
    </footer>
</body>
</html>
