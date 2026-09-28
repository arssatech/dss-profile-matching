<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Autentikasi
AuthMiddleware::checkAuth();

// 1. Validasi CSRF Token via GET Query Parameter
$token = $_GET['csrf_token'] ?? '';
if (!CsrfMiddleware::validateToken($token)) {
    http_response_code(403);
    die("Akses Ditolak: Invalid CSRF Token!");
}

// 2. Validasi ID Alternatif
$idAlternatif = filter_input(INPUT_GET, 'id_alternatif', FILTER_VALIDATE_INT);

if (!$idAlternatif) {
    header("Location: data_alternatif.php?status=invalid");
    exit();
}

// 3. Eksekusi Query PDO dengan Transaction (Hapus Penilaian & Alternatif)
$db = Database::getConnection();

try {
    $db->beginTransaction();

    // Hapus data penilaian terkait terlebih dahulu (mencegah Mismatched/Foreign Key Issue)
    $stmtDeletePenilaian = $db->prepare("DELETE FROM penilaian WHERE id_alternatif = :id");
    $stmtDeletePenilaian->execute([':id' => $idAlternatif]);

    // Hapus data alternatif
    $stmtDeleteAlt = $db->prepare("DELETE FROM alternatif WHERE id_alternatif = :id");
    $stmtDeleteAlt->execute([':id' => $idAlternatif]);

    $db->commit();
    header("Location: data_alternatif.php?status=deleted");
    exit();
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error delete_alternatif: " . $e->getMessage());
    header("Location: data_alternatif.php?status=error");
    exit();
}