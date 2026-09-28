<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_aspek = trim(filter_input(INPUT_POST, 'nama_aspek', FILTER_SANITIZE_SPECIAL_CHARS));
    $bobot = filter_input(INPUT_POST, 'bobot', FILTER_VALIDATE_FLOAT);

    if (!empty($nama_aspek) && $bobot !== false) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO aspek (nama_aspek, bobot) VALUES (:nama, :bobot)");
            $stmt->execute([
                'nama' => $nama_aspek,
                'bobot' => $bobot
            ]);

            header("Location: aspek.php?status=success_create");
            exit();
        } catch (PDOException $e) {
            error_log("Error Create Aspek: " . $e->getMessage());
            header("Location: aspek.php?status=error");
            exit();
        }
    } else {
        header("Location: aspek.php?status=invalid_input");
        exit();
    }
}