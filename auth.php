<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\CsrfMiddleware;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!CsrfMiddleware::validateToken($token)) {
        http_response_code(403);
        die("Akses Ditolak: Invalid CSRF Token!");
    }

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM pengguna WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'] ?? 'admin';

                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: login.php?error=invalid_credentials");
                exit();
            }
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            header("Location: login.php?error=system_error");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}