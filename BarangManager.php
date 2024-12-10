<?php
class BarangManager {
    private $filePath = 'data.json'; // Lokasi file JSON

    public function getBarang(): array {
        // Periksa apakah file JSON ada
        if (file_exists($this->filePath)) {
            $data = file_get_contents($this->filePath); // Baca isi file JSON
            return json_decode($data, true) ?? []; // Decode JSON menjadi array
        }
        // Jika file tidak ada, buat file baru kosong
        file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }

    public function updateBarang(array $barangList): void {
        // Encode array ke JSON dan simpan ke file
        file_put_contents($this->filePath, json_encode($barangList, JSON_PRETTY_PRINT));
    }
}
?>
