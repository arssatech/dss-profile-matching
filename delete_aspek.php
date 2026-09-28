<?php
// Mulai sesi PHP
session_start();

// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Periksa apakah parameter ID aspek diberikan dalam URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Ambil ID aspek dari parameter URL
    $id_aspek = $_GET['id'];

    // Query untuk menghapus data aspek dari database
    $query = "DELETE FROM aspek WHERE id_aspek = $id_aspek";

    // Eksekusi query
    if ($conn->query($query) === TRUE) {
        // Jika berhasil dihapus, redirect ke halaman data aspek
        header("Location: data_aspek.php");
        exit;
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error deleting record: " . $conn->error;
    }
}

// Tutup koneksi database
$conn->close();
?>
