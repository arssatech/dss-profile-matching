<?php

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alternatif = $_POST['id_alternatif'];
    $kode_pegawai = $_POST['kode_pegawai'];
    $nama_pegawai = $_POST['nama_pegawai'];
    $tahun_masuk = $_POST['tahun_masuk'];

    $query = "UPDATE alternatif SET kode_pegawai = '$kode_pegawai', nama_pegawai = '$nama_pegawai', tahun_masuk = '$tahun_masuk' WHERE id_alternatif = $id_alternatif";

    if ($conn->query($query) === TRUE) {
        echo "Data alternatif berhasil diupdate.";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

// Ambil data alternatif berdasarkan ID
if (isset($_GET['id'])) {
    $id_alternatif = $_GET['id'];
    $query = "SELECT * FROM alternatif WHERE id_alternatif = $id_alternatif";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kode_pegawai = $row['kode_pegawai'];
        $nama_pegawai = $row['nama_pegawai'];
        $tahun_masuk = $row['tahun_masuk'];
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "ID tidak tersedia.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Alternatif</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit Data Alternatif</h1>
        
        <form method="POST" action="">
            <input type="hidden" name="id_alternatif" value="<?php echo $id_alternatif; ?>">
            
            <div class="form-group">
                <label>Kode Pegawai:</label>
                <input type="text" name="kode_pegawai" value="<?php echo $kode_pegawai; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nama Pegawai:</label>
                <input type="text" name="nama_pegawai" value="<?php echo $nama_pegawai; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tahun Masuk:</label>
                <input type="number" name="tahun_masuk" value="<?php echo $tahun_masuk; ?>" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Data</button>
        </form>

        <a href="data_alternatif.php" class="btn btn-secondary mt-3">Kembali ke Data Alternatif</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
