<?php
// Mulai sesi PHP
session_start();



// Tambahkan var_dump untuk memeriksa nilai $_SESSION['role']
var_dump($_SESSION['role']);


// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Periksa apakah parameter ID pengguna diberikan dalam URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Ambil ID pengguna dari parameter URL
    $id_pengguna = $_GET['id'];

    // Query untuk menghapus data pengguna dari database
    $query = "DELETE FROM pengguna WHERE id_pengguna = $id_pengguna";

    // Eksekusi query
    if ($conn->query($query) === TRUE) {
        // Jika berhasil dihapus, redirect ke halaman data pengguna
        header("Location: data_pengguna.php");
        exit;
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error deleting record: " . $conn->error;
    }
}

// Tutup koneksi database
$conn->close();
?>
