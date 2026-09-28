<?php
include('config.php');

// Query untuk mengambil data aspek
$query_aspek = "SELECT * FROM aspek";
$result_aspek = $conn->query($query_aspek);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $id_aspek = $_POST['id_aspek'];
    $nama_kriteria = $_POST['nama_kriteria'];
    $jenis_factor = $_POST['jenis_factor'];
    $nilai_target = $_POST['nilai_target'];

    $query = "INSERT INTO kriteria (kode_kriteria, id_aspek, nama_kriteria, jenis_factor, nilai_target) 
              VALUES ('$kode_kriteria', $id_aspek, '$nama_kriteria', '$jenis_factor', $nilai_target)";

    if ($conn->query($query) === TRUE) {
        $message = "Data kriteria berhasil ditambahkan.";
    } else {
        $message = "Error: " . $query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Data Kriteria</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Create Data Kriteria</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="kode_kriteria">Kode Kriteria:</label>
                <input type="text" class="form-control" id="kode_kriteria" name="kode_kriteria" required>
            </div>

            <div class="form-group">
                <label for="id_aspek">Nama Aspek:</label>
                <select class="form-control" id="id_aspek" name="id_aspek" required>
                    <?php 
                    // Memeriksa apakah ada data aspek
                    if ($result_aspek->num_rows > 0) {
                        // Menampilkan pilihan untuk setiap aspek
                        while ($row_aspek = $result_aspek->fetch_assoc()) {
                            echo "<option value='" . $row_aspek['id_aspek'] . "'>" . $row_aspek['nama_aspek'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada data aspek</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nama_kriteria">Nama Kriteria:</label>
                <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" required>
            </div>

            <div class="form-group">
                <label for="jenis_factor">Jenis Factor:</label>
                <select class="form-control" id="jenis_factor" name="jenis_factor" required>
                    <option value="core">Core Factor</option>
                    <option value="secondary">Secondary Factor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nilai_target">Nilai Target:</label>
                <input type="number" class="form-control" id="nilai_target" name="nilai_target" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>

        <!-- Navigasi untuk kembali ke halaman data_kriteria.php -->
        <a href="data_kriteria.php" class="btn btn-secondary mt-3">Kembali ke Data Kriteria</a>
        
        <!-- Tampilkan pesan -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-info mt-3" role="alert">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
