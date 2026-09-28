<?php
require_once __DIR__ . '/config/database.php';

$id_aspek = filter_input(INPUT_GET, 'id_aspek', FILTER_VALIDATE_INT);

if ($id_aspek) {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM aspek WHERE id_aspek = :id");
        $stmt->execute(['id' => $id_aspek]);

        header("Location: aspek.php?status=success_delete");
        exit();
    } catch (PDOException $e) {
        error_log("Error Delete Aspek: " . $e->getMessage());
        header("Location: aspek.php?status=error");
        exit();
    }
} else {
    header("Location: aspek.php?status=invalid_id");
    exit();
}