<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kriteria = filter_input(INPUT_POST, 'id_kriteria', FILTER_VALIDATE_INT);
    $id_aspek = filter_input(INPUT_POST, 'id_aspek', FILTER_VALIDATE_INT);
    $nama_kriteria = trim(filter_input(INPUT_POST, 'nama_kriteria', FILTER_SANITIZE_SPECIAL_CHARS));
    $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_INT);
    $type = trim(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS));

    if ($id_kriteria && $id_aspek && !empty($nama_kriteria) && $target !== false && !empty($type)) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE kriteria SET id_aspek = :id_aspek, nama_kriteria = :nama, target = :target, type = :type WHERE id_kriteria = :id");
            $stmt->execute([
                'id_aspek' => $id_aspek,
                'nama'     => $nama_kriteria,
                'target'   => $target,
                'type'     => $type,
                'id'       => $id_kriteria
            ]);

            header("Location: kriteria.php?status=success_update");
            exit();
        } catch (PDOException $e) {
            error_log("Error Edit Kriteria: " . $e->getMessage());
            header("Location: kriteria.php?status=error");
            exit();
        }
    } else {
        header("Location: kriteria.php?status=invalid_input");
        exit();
    }
}