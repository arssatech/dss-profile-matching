<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aspek = filter_input(INPUT_POST, 'id_aspek', FILTER_VALIDATE_INT);
    $nama_aspek = trim(filter_input(INPUT_POST, 'nama_aspek', FILTER_SANITIZE_SPECIAL_CHARS));
    $bobot = filter_input(INPUT_POST, 'bobot', FILTER_VALIDATE_FLOAT);

    if ($id_aspek && !empty($nama_aspek) && $bobot !== false) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE aspek SET nama_aspek = :nama, bobot = :bobot WHERE id_aspek = :id");
            $stmt->execute([
                'nama' => $nama_aspek,
                'bobot' => $bobot,
                'id' => $id_aspek
            ]);

            header("Location: aspek.php?status=success_update");
            exit();
        } catch (PDOException $e) {
            error_log("Error Edit Aspek: " . $e->getMessage());
            header("Location: aspek.php?status=error");
            exit();
        }
    } else {
        header("Location: aspek.php?status=invalid_input");
        exit();
    }
}