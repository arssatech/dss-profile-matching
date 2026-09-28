<?php
require_once __DIR__ . '/config/database.php';

$id_kriteria = filter_input(INPUT_GET, 'id_kriteria', FILTER_VALIDATE_INT);

if ($id_kriteria) {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM kriteria WHERE id_kriteria = :id");
        $stmt->execute(['id' => $id_kriteria]);

        header("Location: kriteria.php?status=success_delete");
        exit();
    } catch (PDOException $e) {
        error_log("Error Delete Kriteria: " . $e->getMessage());
        header("Location: kriteria.php?status=error");
        exit();
    }
} else {
    header("Location: kriteria.php?status=invalid_id");
    exit();
}