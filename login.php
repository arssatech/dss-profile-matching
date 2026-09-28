<?php
session_start();

// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Cek apakah pengguna sudah login, jika ya, arahkan ke dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifikasi login menggunakan prepared statement
    $query = "SELECT * FROM pengguna WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verifikasi kata sandi menggunakan password_verify
        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Simpan peran pengguna di dalam sesi
            header('Location: dashboard.php');
            exit();
        } else {
            // Login gagal
            echo "<div class='alert alert-danger' role='alert'>Login gagal. Periksa kembali username dan password.</div>";
        }

        // Tutup pernyataan
        $stmt->close();
    } else {
        // Handle kesalahan pernyataan
        echo "<div class='alert alert-danger' role='alert'>Error in statement preparation.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title text-center">Login</h1>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                        Belum punya akun? <a href="register.php">Daftar sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
