<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit'])) {
    $id_pengguna = $_POST['id_pengguna'];
    $new_password = $_POST['new_password'];
    $role = $_POST['role']; // Ambil peran yang diubah dari formulir

    // Enkripsi password sebelum menyimpannya ke database
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password dan peran pengguna berdasarkan ID
    $query = "UPDATE pengguna SET password = '$hashed_password', role = '$role' WHERE id_pengguna = $id_pengguna";

    if ($conn->query($query) === TRUE) {
        echo "Password dan peran pengguna berhasil diperbarui.";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

// Ambil ID pengguna dari parameter URL
if (isset($_GET['id'])) {
    $id_pengguna = $_GET['id'];

    // Query untuk mengambil data pengguna berdasarkan ID
    $query = "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $role = $row['role']; // Simpan peran pengguna saat ini
    } else {
        echo "Data pengguna tidak ditemukan.";
        exit();
    }
} else {
    echo "ID pengguna tidak tersedia.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password dan Peran Pengguna</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit Password dan Peran Pengguna</h1>
        
        <form method="POST" action="">
            <input type="hidden" name="id_pengguna" value="<?php echo $id_pengguna; ?>">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" value="<?php echo $username; ?>" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="new_password">Password Baru:</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" class="form-control" required>
                    <option value="0" <?php if($role == 0) echo 'disabled'; ?>>User</option> <!-- Periksa dan tandai pengguna biasa -->
                    <option value="1" <?php if($role == 1) echo 'disabled'; ?>>Admin</option> <!-- Periksa dan tandai admin -->
                </select>
            </div>

            <button type="submit" name="submit_edit" class="btn btn-primary">Perbarui Password dan Peran</button>
        </form>

        <a href="data_pengguna.php" class="btn btn-secondary mt-3">Kembali ke Data Pengguna</a>
    </div>
</body>
</html>
