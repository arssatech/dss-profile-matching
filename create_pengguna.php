<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = $_POST['password'] ?? '';
    $role     = trim(filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS));

    if (!empty($username) && !empty($password) && !empty($role)) {
        try {
            $db = Database::getConnection();
            
            // Password hashing menggunakan BCRYPT
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $db->prepare("INSERT INTO pengguna (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute([
                'username' => $username,
                'password' => $hashedPassword,
                'role'     => $role
            ]);

            header("Location: pengguna.php?status=success_create");
            exit();
        } catch (PDOException $e) {
            error_log("Error Create Pengguna: " . $e->getMessage());
            header("Location: pengguna.php?status=error");
            exit();
        }
    } else {
        header("Location: pengguna.php?status=invalid_input");
        exit();
    }
}