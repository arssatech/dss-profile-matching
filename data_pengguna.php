<?php

include('config.php');

include('auth.php'); // Add a semicolon here

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_create'])) {
    // Only proceed if the user is logged in as admin
    if (isAdmin()) {
        $nama_pengguna = $_POST['nama_pengguna'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role']; // Ambil peran dari formulir

        // Enkripsi password sebelum menyimpannya ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk menyimpan data pengguna ke dalam database
        $query = "INSERT INTO pengguna (nama_pengguna, username, password, role) VALUES ('$nama_pengguna', '$username', '$hashed_password', '$role')";

        if ($conn->query($query) === TRUE) {
            echo "Data pengguna berhasil ditambahkan.";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    } else {
        echo "Anda tidak memiliki izin untuk menambahkan pengguna.";
    }
}

// READ
$query = "SELECT * FROM pengguna";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Data Pengguna</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_pengguna'] . "</td>
                                <td>" . $row['nama_pengguna'] . "</td>
                                <td>" . $row['username'] . "</td>
                                <td>";
                        // Check if the user is logged in as admin
                        if (isAdmin()) {
                            echo "<a href='edit_pengguna.php?id=" . $row['id_pengguna'] . "' class='btn btn-primary'>Edit</a>
                                    <a href='delete_pengguna.php?id=" . $row['id_pengguna'] . "' class='btn btn-danger'>Delete</a>";
                        } else {
                            echo "Anda tidak memiliki izin untuk mengedit atau menghapus pengguna.";
                        }
                        echo "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data pengguna.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <?php if (isAdmin()): ?>
        <h2>Tambah Data Pengguna</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_pengguna">Nama Pengguna:</label>
                <input type="text" name="nama_pengguna" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" class="form-control" required>
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <!-- Only show the submit button if the user is logged in as admin -->
            
                <button type="submit" name="submit_create" class="btn btn-success">Tambah Data</button>
            <?php endif; ?>
        </form>

        <a href="dashboard.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
