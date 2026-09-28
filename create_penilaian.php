<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

AuthMiddleware::checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: data_penilaian.php');
    exit;
}

// Validasi CSRF Token
if (!CsrfMiddleware::validateToken($_POST['csrf_token'] ?? '')) {
    die('Akses ditolak: Token CSRF tidak valid.');
}

$idAlternatif = filter_input(INPUT_POST, 'id_alternatif', FILTER_VALIDATE_INT);
$scores = $_POST['scores'] ?? []; // Array [id_kriteria => nilai]

if (!$idAlternatif || empty($scores)) {
    header('Location: data_penilaian.php?status=error_input');
    exit;
}

$db = Database::getConnection();

try {
    $db->beginTransaction();

    // Prepared statement UPSERT (Insert or Update if exists)
    $stmt = $db->prepare("
        INSERT INTO penilaian (id_alternatif, id_kriteria, nilai) 
        VALUES (:id_alternatif, :id_kriteria, :nilai)
        ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
    ");

    foreach ($scores as $idKriteria => $nilai) {
        $stmt->execute([
            ':id_alternatif' => $idAlternatif,
            ':id_kriteria'   => (int)$idKriteria,
            ':nilai'         => (int)$nilai
        ]);
    }

    $db->commit();
    header('Location: perhitungan.php?status=success');
    exit;

} catch (\PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error Penilaian: " . $e->getMessage());
    header('Location: data_penilaian.php?status=db_error');
    exit;
}