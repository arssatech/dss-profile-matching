<?php
require_once __DIR__ . '/config/database.php';

$id_alternatif = filter_input(INPUT_GET, 'id_alternatif', FILTER_VALIDATE_INT);

if ($id_alternatif) {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM alternatif WHERE id_alternatif = :id");
        $stmt->execute(['id' => $id_alternatif]);

        header("Location: alternatif.php?status=success_delete");
        exit();
    } catch (PDOException $e) {
        error_log("Error Delete Alternatif: " . $e->getMessage());
        header("Location: alternatif.php?status=error");
        exit();
    }
} else {
    header("Location: alternatif.php?status=invalid_id");
    exit();
}