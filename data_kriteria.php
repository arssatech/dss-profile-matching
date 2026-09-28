<?php

// Menggunakan __DIR__ untuk path yang relatif
include('config.php');

// Sertakan file auth.php
include('auth.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kriteria</title>
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
    <h1>Data Kriteria</h1>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode Kriteria</th>
                <th scope="col">Aspek</th>
                <th scope="col">Kriteria</th>
                <th scope="col">Jenis Factor</th>
                <th scope="col">Nilai Target</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil data kriteria dan informasi aspek yang terkait dari database
            $query = "SELECT k.id_kriteria, k.kode_kriteria, a.nama_aspek, k.nama_kriteria, k.jenis_factor, k.nilai_target 
                      FROM kriteria k
                      INNER JOIN aspek a ON k.id_aspek = a.id_aspek";
            $result = $conn->query($query);

            // Periksa apakah query berhasil dieksekusi dengan benar
            if ($result) {
                // Periksa apakah ada data kriteria
                if ($result->num_rows > 0) {
                    // Iterasi untuk menampilkan data kriteria
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $no++ . "</td>
                                <td>" . $row['kode_kriteria'] . "</td>
                                <td>" . $row['nama_aspek'] . "</td>
                                <td>" . $row['nama_kriteria'] . "</td>
                                <td>" . $row['jenis_factor'] . "</td>
                                <td>" . $row['nilai_target'] . "</td>
                                <td>";
                        // Tampilkan tombol edit dan delete hanya jika pengguna adalah admin
                        if (isAdmin()) {
                            echo "<a href='edit_kriteria.php?id={$row['id_kriteria']}' class='btn btn-sm btn-primary'>Edit</a>
                                  <a href='delete_kriteria.php?id={$row['id_kriteria']}' class='btn btn-sm btn-danger'>Delete</a>";
                        }
                        echo "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data kriteria.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Error: " . $conn->error . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
    // Tampilkan tombol tambah hanya jika pengguna adalah admin
    if (isAdmin()) {
        echo "<a href='create_kriteria.php' class='btn btn-success'>Tambah Kriteria</a>";
    }
    ?>
</div>

</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>
