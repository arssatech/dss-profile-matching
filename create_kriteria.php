<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aspek = filter_input(INPUT_POST, 'id_aspek', FILTER_VALIDATE_INT);
    $nama_kriteria = trim(filter_input(INPUT_POST, 'nama_kriteria', FILTER_SANITIZE_SPECIAL_CHARS));
    $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_INT);
    $type = trim(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS)); // 'core' / 'secondary'

    if ($id_aspek && !empty($nama_kriteria) && $target !== false && !empty($type)) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO kriteria (id_aspek, nama_kriteria, target, type) VALUES (:id_aspek, :nama, :target, :type)");
            $stmt->execute([
                'id_aspek' => $id_aspek,
                'nama'     => $nama_kriteria,
                'target'   => $target,
                'type'     => $type
            ]);

            header("Location: kriteria.php?status=success_create");
            exit();
        } catch (PDOException $e) {
            error_log("Error Create Kriteria: " . $e->getMessage());
            header("Location: kriteria.php?status=error");
            exit();
        }
    } else {
        header("Location: kriteria.php?status=invalid_input");
        exit();
    }
}