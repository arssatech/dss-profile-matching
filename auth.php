<?php
session_start();

// Fungsi untuk memeriksa apakah pengguna telah login
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Jika pengguna belum login, redirect ke halaman login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin'; // Memeriksa apakah peran adalah 'admin'
}
?>

