<?php
include('config.php');

// Periksa apakah ada parameter ID kriteria yang dikirimkan melalui URL
if (isset($_GET['id'])) {
    $id_kriteria = $_GET['id'];

    // Query untuk mengambil data kriteria berdasarkan ID
    $query = "SELECT * FROM kriteria WHERE id_kriteria = $id_kriteria";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kode_kriteria = $row['kode_kriteria'];
        $id_aspek = $row['id_aspek'];
        $nama_kriteria = $row['nama_kriteria'];
        $jenis_factor = $row['jenis_factor'];
        $nilai_target = $row['nilai_target'];
    } else {
        echo "Data kriteria tidak ditemukan.";
        exit();
    }
} else {
    echo "ID kriteria tidak diberikan.";
    exit();
}

// Query untuk mengambil data aspek
$query_aspek = "SELECT * FROM aspek";
$result_aspek = $conn->query($query_aspek);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $id_aspek = $_POST['id_aspek'];
    $nama_kriteria = $_POST['nama_kriteria'];
    $jenis_factor = $_POST['jenis_factor'];
    $nilai_target = $_POST['nilai_target'];

    // Query untuk melakukan update data kriteria
    $query_update = "UPDATE kriteria 
                     SET kode_kriteria = '$kode_kriteria', id_aspek = $id_aspek, 
                         nama_kriteria = '$nama_kriteria', jenis_factor = '$jenis_factor', 
                         nilai_target = $nilai_target 
                     WHERE id_kriteria = $id_kriteria";

    if ($conn->query($query_update) === TRUE) {
        echo "Data kriteria berhasil diperbarui.";
    } else {
        echo "Error: " . $query_update . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kriteria</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit Data Kriteria</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="kode_kriteria">Kode Kriteria:</label>
                <input type="text" name="kode_kriteria" value="<?php echo $kode_kriteria; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="id_aspek">Nama Aspek:</label>
                <select name="id_aspek" class="form-control" required>
                    <?php 
                    // Memeriksa apakah ada data aspek
                    if ($result_aspek->num_rows > 0) {
                        // Menampilkan pilihan untuk setiap aspek
                        while ($row_aspek = $result_aspek->fetch_assoc()) {
                            $selected = ($row_aspek['id_aspek'] == $id_aspek) ? 'selected' : '';
                            echo "<option value='" . $row_aspek['id_aspek'] . "' $selected>" . $row_aspek['nama_aspek'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada data aspek</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nama_kriteria">Nama Kriteria:</label>
                <input type="text" name="nama_kriteria" value="<?php echo $nama_kriteria; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="jenis_factor">Jenis Factor:</label>
                <select name="jenis_factor" class="form-control" required>
                    <option value="core" <?php if ($jenis_factor == 'core') echo 'selected'; ?>>Core Factor</option>
                    <option value="secondary" <?php if ($jenis_factor == 'secondary') echo 'selected'; ?>>Secondary Factor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nilai_target">Nilai Target:</label>
                <input type="number" name="nilai_target" value="<?php echo $nilai_target; ?>" step="0.01" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Perbarui Data</button>
        </form>

        <a href="data_kriteria.php" class="btn btn-secondary mt-3">Kembali ke Data Kriteria</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
