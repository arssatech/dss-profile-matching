<?php
session_start();

// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Sertakan file auth.php
include('auth.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Aspek</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .navbar-nav .nav-link.active {
            font-weight: bold; /* Menjadikan tulisan lebih gelap */
        }
        .container {
            margin-top: 80px; /* Sesuaikan dengan tinggi navbar */
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h1>Data Aspek</h1>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Nama Aspek</th>
                <th scope="col">Bobot</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM aspek";
            $result = $conn->query($query);
            $no = 1;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama_aspek']}</td>
                            <td>{$row['bobot']}</td>
                            <td>";
                    // Tampilkan tombol edit dan delete hanya jika pengguna adalah admin
                    if (isAdmin()) {
                        echo "<a href='edit_aspek.php?id={$row['id_aspek']}' class='btn btn-sm btn-primary'>Edit</a>
                              <a href='delete_aspek.php?id={$row['id_aspek']}' class='btn btn-sm btn-danger'>Delete</a>";
                    }
                    echo "</td>
                        </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data aspek.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
    // Tampilkan tombol tambah hanya jika pengguna adalah admin
    if (isAdmin()) {
        echo "<a href='create_aspek.php' class='btn btn-success'>Tambah Aspek</a>";
    }
    ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
