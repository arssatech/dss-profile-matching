<?php
// Mulai sesi PHP
session_start();



// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Periksa apakah parameter ID kriteria diberikan dalam URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Ambil ID kriteria dari parameter URL
    $id_kriteria = $_GET['id'];

    // Query untuk menghapus data kriteria dari database
    $query = "DELETE FROM kriteria WHERE id_kriteria = $id_kriteria";

    // Eksekusi query
    if ($conn->query($query) === TRUE) {
        // Jika berhasil dihapus, redirect ke halaman data kriteria
        header("Location: data_kriteria.php");
        exit;
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error deleting record: " . $conn->error;
    }
}

// Tutup koneksi database
$conn->close();
?>
