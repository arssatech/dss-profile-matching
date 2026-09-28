<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Middleware\CsrfMiddleware;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke dashboard jika sudah login
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$csrfToken = CsrfMiddleware::generateToken();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">SPK Login</h4>
                        
                        <?php if ($error === 'invalid_credentials'): ?>
                            <div class="alert alert-danger">Username atau password salah!</div>
                        <?php elseif ($error === 'system_error'): ?>
                            <div class="alert alert-danger">Terjadi kesalahan sistem. Silakan coba lagi.</div>
                        <?php endif; ?>

                        <form action="auth.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required autocomplete="off">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>