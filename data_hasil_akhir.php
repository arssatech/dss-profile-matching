<?php


// Menggunakan __DIR__ untuk path yang relatif
include(__DIR__ . '/config.php');

// Periksa apakah pengguna telah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login
    header('Location: login.php');
    exit();
}

// Query untuk mengambil data hasil akhir dari database
$query = "SELECT * FROM hasil_akhir";
$result = $conn->query($query);

// Periksa apakah ada data hasil akhir
if ($result->num_rows > 0) {
    // Tampilkan tabel data hasil akhir
    echo "<table border='1'>
            <tr>
                <th>No</th>
                <th>ID Penilaian</th>
                <th>Nilai Kriteria</th>
                <th>Pemetaan GAP</th>
                <th>Pembobotan Nilai GAP</th>
                <th>Perhitungan Factor</th>
                <th>Hasil Profil Matching</th>
            </tr>";

    // Iterasi untuk menampilkan data hasil akhir
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . $row['id_penilaian'] . "</td>
                <td>" . $row['nilai_kriteria'] . "</td>
                <td>" . $row['pemetaan_gap'] . "</td>
                <td>" . $row['pembobotan_nilai_gap'] . "</td>
                <td>" . $row['perhitungan_factor'] . "</td>
                <td>" . $row['hasil_profil_matching'] . "</td>
            </tr>";
    }

    echo "</table>";
} else {
    echo "Tidak ada data hasil akhir.";
}

// Tutup koneksi database
$conn->close();
?>
