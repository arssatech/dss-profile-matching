<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aspek = $_POST['id_aspek'];
    $nama_aspek = $_POST['nama_aspek'];
    $bobot = $_POST['bobot'];

    $query = "UPDATE aspek SET nama_aspek = '$nama_aspek', bobot = '$bobot' WHERE id_aspek = $id_aspek";

    if ($conn->query($query) === TRUE) {
        echo "Data aspek berhasil diperbarui.";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

if (isset($_GET['id'])) {
    $id_aspek = $_GET['id'];

    $query = "SELECT * FROM aspek WHERE id_aspek = $id_aspek";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nama_aspek = $row['nama_aspek'];
        $bobot = $row['bobot'];
    } else {
        echo "Data aspek tidak ditemukan.";
        exit();
    }
} else {
    echo "ID aspek tidak tersedia.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Aspek</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit Data Aspek</h1>
        
        <form method="POST" action="">
            <input type="hidden" name="id_aspek" value="<?php echo $id_aspek; ?>">
            
            <div class="form-group">
                <label>Nama Aspek:</label>
                <input type="text" name="nama_aspek" value="<?php echo $nama_aspek; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Bobot:</label>
                <input type="number" name="bobot" value="<?php echo $bobot; ?>" class="form-control" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Perbarui Data</button>
        </form>

        <a href="data_aspek.php" class="btn btn-secondary mt-3">Kembali ke Data Aspek</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
