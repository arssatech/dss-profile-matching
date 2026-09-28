<?php
// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_pengguna = $_POST['nama_pengguna'];
    $role = 0; // Mengatur peran sebagai pengguna biasa secara default

    // Menggunakan prepared statement untuk mencegah SQL injection
    $query = "INSERT INTO pengguna (username, password, nama_pengguna, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Menggunakan password_hash untuk mengamankan kata sandi
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('sssi', $username, $hashedPassword, $nama_pengguna, $role); // Mengikat peran ke parameter
        $stmt->execute();

        echo "Registrasi berhasil. Silakan login <a href='login.php'>di sini</a>.";
    } else {
        echo "Error: Gagal melakukan registrasi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Registrasi Pengguna</h1>

        <!-- Form registrasi -->
        <form method="POST" action="" class="mt-3">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nama Pengguna:</label>
                <input type="text" name="nama_pengguna" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrasi</button>
        </form>

        <div class="mt-3">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>
