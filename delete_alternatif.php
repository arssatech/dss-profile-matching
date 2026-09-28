<?php

// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Periksa apakah parameter ID alternatif diberikan dalam URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Ambil ID alternatif dari parameter URL
    $id_alternatif = $_GET['id'];

    // Query untuk menghapus data alternatif dari database
    $query = "DELETE FROM alternatif WHERE id_alternatif = $id_alternatif";

    // Eksekusi query
    if ($conn->query($query) === TRUE) {
        // Jika berhasil dihapus, redirect ke halaman data alternatif
        header("Location: data_alternatif.php");
        exit;
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error deleting record: " . $conn->error;
    }
}

// Tutup koneksi database
$conn->close();
?>
