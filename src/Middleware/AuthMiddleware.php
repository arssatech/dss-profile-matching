<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function checkAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: login.php?status=unauthorized");
            exit();
        }
    }

    public static function checkAdmin(): void
    {
        self::checkAuth();

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            header("Location: index.php?status=forbidden");
            exit();
        }
    }
}