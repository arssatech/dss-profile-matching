<?php
include('config.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_aspek = $_POST['nama_aspek'];
    $bobot = $_POST['bobot'];

    $query = "INSERT INTO aspek (nama_aspek, bobot) VALUES ('$nama_aspek', '$bobot')";

    if ($conn->query($query) === TRUE) {
        // Mendapatkan ID aspek yang baru ditambahkan
        $last_insert_id = $conn->insert_id;
        $message = "Data aspek berhasil ditambahkan";
    } else {
        $message = "Error: " . $query . "<br>" . $conn->error;
    }
}

// Mengambil total bobot aspek yang sudah ada di dalam database
$query_total_bobot = "SELECT SUM(bobot) AS total_bobot FROM aspek";
$result_total_bobot = $conn->query($query_total_bobot);
$row_total_bobot = $result_total_bobot->fetch_assoc();
$total_bobot_terpakai = $row_total_bobot['total_bobot'];

// Menentukan persentase bobot yang sudah terpakai
$persentase_terpakai = ($total_bobot_terpakai / 100) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Data Aspek</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Create Data Aspek</h1>
        
        <!-- Tampilkan informasi persentase bobot yang sudah terpakai -->
        <p>Total bobot yang sudah terpakai: <?php echo $persentase_terpakai; ?>%</p>
        
        <!-- Formulir untuk menambah data aspek -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_aspek">Nama Aspek:</label>
                <input type="text" class="form-control" id="nama_aspek" name="nama_aspek" required>
            </div>
            
            <div class="form-group">
                <label for="bobot">Bobot:</label>
                <input type="number" class="form-control" id="bobot" name="bobot" step="0.01" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>
        
        <!-- Navigasi untuk kembali ke halaman data_aspek.php -->
        <a href="data_aspek.php" class="btn btn-secondary mt-3">Kembali ke Data Aspek</a>
        
        <!-- Tampilkan pesan -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-info mt-3" role="alert">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
