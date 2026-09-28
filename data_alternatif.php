<?php

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
    <title>Admin Dashboard</title>
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
        /* Tambahkan margin atau padding pada bagian atas container */
        .container {
            margin-top: 80px; /* Sesuaikan dengan tinggi navbar */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Data Alternatif</h1>
        <?php
        // Query untuk mengambil data alternatif dari database
        $query = "SELECT * FROM alternatif";
        $result = $conn->query($query);

        // HTML untuk menampilkan data tabel dan tombol tambah alternatif
        $output = "";
        if ($result->num_rows > 0) {
            // Tampilkan tabel data alternatif dengan menggunakan Bootstrap
            $output .= "<div class='table-responsive'>";
            $output .= "<table class='table table-bordered table-striped'>";
            $output .= "<thead class='thead-light'> <!-- Tambahkan gaya untuk header tabel -->";
            $output .= "<tr>
                    <th scope='col'>No</th>
                    <th scope='col'>Kode Pegawai</th>
                    <th scope='col'>Nama Pegawai</th>
                    <th scope='col'>Tahun Masuk</th>
                    <th scope='col'>Aksi</th>
                  </tr>
                  </thead>";
            $output .= "<tbody>";

            // Iterasi untuk menampilkan data alternatif
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $output .= "<tr>
                        <td>" . $no++ . "</td>
                        <td>" . $row['kode_pegawai'] . "</td>
                        <td>" . $row['nama_pegawai'] . "</td>
                        <td>" . $row['tahun_masuk'] . "</td>
                        <td>";
                // Tampilkan tombol edit dan delete hanya jika pengguna adalah admin
                if (isAdmin()) {
                    $output .= "<a href='edit_alternatif.php?id=" . $row['id_alternatif'] . "' class='btn btn-sm btn-primary'>Edit</a>
                              <a href='delete_alternatif.php?id=" . $row['id_alternatif'] . "' class='btn btn-sm btn-danger'>Delete</a>";
                }
                $output .= "</td>
                    </tr>";
            }

            $output .= "</tbody>";
            $output .= "</table>";
            $output .= "</div>";

        } else {
            $output .= "<div class='alert alert-warning' role='alert'>Tidak ada data alternatif.</div>"; // Tambahkan gaya untuk pesan peringatan
        }

        // Tambahkan tombol tambah alternatif jika pengguna memiliki izin
        if (isAdmin()) {
            $output .= "<a href='create_alternatif.php' class='btn btn-success'>Tambah Alternatif</a>";
        }

        // Menampilkan output
        echo $output;
        ?>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>
