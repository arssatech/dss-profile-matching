<?php
// Sertakan file konfigurasi database
include(__DIR__ . '/config.php');

// Query untuk mengambil data alternatif dari database
$query = "SELECT * FROM alternatif";
$result = $conn->query($query);

// Array untuk menyimpan data alternatif
$alternatif = array();

// Periksa apakah ada data alternatif
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tambahkan data alternatif ke dalam array
        $alternatif[] = $row;
    }
}

// Ubah array menjadi format JSON dan kirimkan ke client
echo json_encode($alternatif);

// Tutup koneksi database
$conn->close();
?>
