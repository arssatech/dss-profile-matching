<?php
require_once __DIR__ . '/config/database.php';

$id_pengguna = filter_input(INPUT_GET, 'id_pengguna', FILTER_VALIDATE_INT);

if ($id_pengguna) {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM pengguna WHERE id_pengguna = :id");
        $stmt->execute(['id' => $id_pengguna]);

        header("Location: pengguna.php?status=success_delete");
        exit();
    } catch (PDOException $e) {
        error_log("Error Delete Pengguna: " . $e->getMessage());
        header("Location: pengguna.php?status=error");
        exit();
    }
} else {
    header("Location: pengguna.php?status=invalid_id");
    exit();
}