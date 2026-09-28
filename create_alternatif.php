<?php
include('config.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil ID terakhir yang dimasukkan ke dalam tabel alternatif
    $query_last_id = "SELECT MAX(id_alternatif) AS last_id FROM alternatif";
    $result_last_id = $conn->query($query_last_id);
    if ($result_last_id && $result_last_id->num_rows > 0) {
        $last_id_row = $result_last_id->fetch_assoc();
        $last_id = $last_id_row['last_id'];
        // Menghitung ID berikutnya dengan menambahkan 1
        $next_id = $last_id + 1;
    } else {
        // Jika tidak ada data di tabel, ID berikutnya diatur sebagai 1
        $next_id = 1;
    }

    // Mendapatkan nilai dari form
    $kode_pegawai = $_POST['kode_pegawai'];
    $nama_pegawai = $_POST['nama_pegawai'];
    $tahun_masuk = $_POST['tahun_masuk'];

    // Menyisipkan data dengan ID yang sudah ditentukan
    $query = "INSERT INTO alternatif (id_alternatif, kode_pegawai, nama_pegawai, tahun_masuk) 
              VALUES ($next_id, '$kode_pegawai', '$nama_pegawai', $tahun_masuk)";

    if ($conn->query($query) === TRUE) {
        $message = "Data alternatif berhasil ditambahkan.";
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
    <title>Create Data Alternatif</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Create Data Alternatif</h1>
        
        <?php if (!empty($message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $message; ?>
            <a href="dashboard.php" class="btn btn-primary ml-2">Kembali ke Dashboard</a>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="kode_pegawai">Kode Pegawai:</label>
                <input type="text" class="form-control" id="kode_pegawai" name="kode_pegawai" required>
            </div>

            <div class="form-group">
                <label for="nama_pegawai">Nama Pegawai:</label>
                <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" required>
            </div>

            <div class="form-group">
                <label for="tahun_masuk">Tahun Masuk:</label>
                <input type="number" class="form-control" id="tahun_masuk" name="tahun_masuk" required>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>

        <div class="mt-3">
            <a href="data_alternatif.php" class="btn btn-secondary">Lihat Data Alternatif</a>
        </div>
    </div>
</body>
</html>
