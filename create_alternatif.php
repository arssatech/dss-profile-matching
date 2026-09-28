<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alternatif = filter_input(INPUT_POST, 'id_alternatif', FILTER_VALIDATE_INT);
    $nama_alternatif = trim(filter_input(INPUT_POST, 'nama_alternatif', FILTER_SANITIZE_SPECIAL_CHARS));

    if ($id_alternatif && !empty($nama_alternatif)) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE alternatif SET nama_alternatif = :nama WHERE id_alternatif = :id");
            $stmt->execute([
                'nama' => $nama_alternatif,
                'id' => $id_alternatif
            ]);

            header("Location: alternatif.php?status=success_update");
            exit();
        } catch (PDOException $e) {
            error_log("Error Edit Alternatif: " . $e->getMessage());
            header("Location: alternatif.php?status=error");
            exit();
        }
    } else {
        header("Location: alternatif.php?status=invalid_input");
        exit();
    }
}