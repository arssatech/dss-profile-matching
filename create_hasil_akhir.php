<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penilaian = $_POST['id_penilaian'];
    $nilai_kriteria = $_POST['nilai_kriteria'];
    $pemetaan_gap = $_POST['pemetaan_gap'];
    $pembobotan_nilai_gap = $_POST['pembobotan_nilai_gap'];
    $perhitungan_factor = $_POST['perhitungan_factor'];

    // Lakukan perhitungan profil matching (contoh sederhana)
    $hasil_profil_matching = ($nilai_kriteria + $pemetaan_gap + $pembobotan_nilai_gap) / $perhitungan_factor;

    $query = "INSERT INTO hasil_akhir (id_penilaian, nilai_kriteria, pemetaan_gap, pembobotan_nilai_gap, perhitungan_factor, hasil_profil_matching) 
              VALUES ($id_penilaian, $nilai_kriteria, $pemetaan_gap, $pembobotan_nilai_gap, $perhitungan_factor, $hasil_profil_matching)";

    if ($conn->query($query) === TRUE) {
        echo "Data hasil akhir berhasil ditambahkan.";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Data Hasil Akhir</title>
</head>
<body>
    <h1>Create Data Hasil Akhir</h1>
    
    <form method="POST" action="">
        <label>ID Penilaian:</label>
        <input type="number" name="id_penilaian" required><br>

        <label>Nilai Kriteria:</label>
        <input type="number" name="nilai_kriteria" step="0.01" required><br>

        <label>Pemetaan GAP:</label>
        <input type="number" name="pemetaan_gap" step="0.01" required><br>

        <label>Pembobotan Nilai GAP:</label>
        <input type="number" name="pembobotan_nilai_gap" step="0.01" required><br>

        <label>Perhitungan Factor:</label>
        <input type="number" name="perhitungan_factor" step="0.01" required><br>

        <button type="submit">Tambah Data</button>
    </form>
</body>
</html>
