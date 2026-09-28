<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Autentikasi
AuthMiddleware::checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_alternatif.php");
    exit();
}

// 1. Validasi CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!CsrfMiddleware::validateToken($token)) {
    http_response_code(403);
    die("Akses Ditolak: Invalid CSRF Token!");
}

// 2. Filter & Sanitasi Input
$namaAlternatif = trim(filter_input(INPUT_POST, 'nama_alternatif', FILTER_SANITIZE_SPECIAL_CHARS));

if (empty($namaAlternatif)) {
    header("Location: data_alternatif.php?status=invalid");
    exit();
}

// 3. Eksekusi Query PDO
try {
    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO alternatif (nama_alternatif) VALUES (:nama)");
    $stmt->execute([':nama' => $namaAlternatif]);

    header("Location: data_alternatif.php?status=created");
    exit();
} catch (PDOException $e) {
    error_log("Error create_alternatif: " . $e->getMessage());
    header("Location: data_alternatif.php?status=error");
    exit();
}