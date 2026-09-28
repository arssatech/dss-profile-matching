<?php
include('config.php');

// Check if POST data is set
if(isset($_POST['nilai_kriteria'])) {
    // Ambil data penilaian dari form
    $nilai_kriteria = $_POST['nilai_kriteria'];

    // Hitung nilai gap dan ubah menjadi bobot
    $bobot_kriteria = array();
    foreach ($nilai_kriteria as $id_alternatif => $kriteria) {
        echo "<h2>Perhitungan untuk Alternatif $id_alternatif:</h2>";
        foreach ($kriteria as $id_kriteria => $nilai) {
            // Ambil nilai target dan jenis factor dari database
            $queryInfo = $conn->query("SELECT nilai_target, jenis_factor, id_aspek FROM kriteria WHERE id_kriteria = $id_kriteria");
            if($queryInfo) {
                $info_data = $queryInfo->fetch_assoc();
                $target = $info_data['nilai_target'];
                $jenis_factor = $info_data['jenis_factor'];
                $id_aspek = $info_data['id_aspek'];

                // Hitung gap
                $gap = $nilai - $target;

                // Output
                echo "<p>Nilai kriteria $id_kriteria: $nilai</p>";
                echo "<p>Nilai target: $target</p>";
                echo "<p>Jenis factor: $jenis_factor</p>";
                echo "<p>Gap: $gap</p>";

                // Ubah gap menjadi bobot berdasarkan pemetaan yang telah ditentukan
                switch ($gap) {
                    case 0:
                        $bobot = 5;
                        break;
                    case 1:
                        $bobot = 5;
                        break;
                    case -1:
                        $bobot = 4;
                        break;
                    case 2:
                        $bobot = 5;
                        break;
                    case -2:
                        $bobot = 3;
                        break;
                    case 3:
                        $bobot = 5;
                        break;
                    case -3:
                        $bobot = 2;
                        break;
                    case 4:
                        $bobot = 5;
                        break;
                    case -4:
                        $bobot = 1;
                        break;
                    default:
                        $bobot = 0; // Default jika gap tidak sesuai dengan daftar
                        break;
                }

                // Output
                echo "<p>Bobot: $bobot</p>";
                echo "<hr>";

                // Menyimpan informasi jenis factor (CF atau SF) ke dalam array bobot
                $bobot_kriteria[$id_aspek][$id_kriteria][] = array(
                    'alternatif' => $id_alternatif,
                    'gap' => $gap,
                    'bobot' => $bobot,
                    'jenis_factor' => $jenis_factor
                );
            } else {
                // Handle jika query tidak berhasil dieksekusi
                echo "Error: " . $conn->error;
            }
        }
    }

    // Hitung nilai CF dan SF per aspek untuk setiap alternatif
    $nilaiCF = array(); // Array untuk menyimpan nilai CF
    $nilaiSF = array(); // Array untuk menyimpan nilai SF
    foreach ($bobot_kriteria as $id_aspek => $aspek) {
        echo "<h2>Perhitungan untuk Aspek $id_aspek:</h2>";
        foreach ($aspek as $id_kriteria => $kriteria) {
            foreach ($kriteria as $info) {
                // Tentukan jenis factor (CF atau SF)
                if ($info['jenis_factor'] == 'core') {
                    // Tambahkan nilai bobot CF
                    if (!isset($nilaiCF[$id_aspek][$info['alternatif']])) {
                        $nilaiCF[$id_aspek][$info['alternatif']] = 0;
                    }
                    $nilaiCF[$id_aspek][$info['alternatif']] += $info['bobot'];
                } elseif ($info['jenis_factor'] == 'secondary') {
                    // Tambahkan nilai bobot SF
                    if (!isset($nilaiSF[$id_aspek][$info['alternatif']])) {
                        $nilaiSF[$id_aspek][$info['alternatif']] = 0;
                    }
                    $nilaiSF[$id_aspek][$info['alternatif']] += $info['bobot'];
                }
            }
        }
        // Output
        echo "<p>Nilai CF untuk Aspek $id_aspek:</p>";
        print_r($nilaiCF[$id_aspek]);
        echo "<p>Nilai SF untuk Aspek $id_aspek:</p>";
        print_r($nilaiSF[$id_aspek]);
        echo "<hr>";
    }

    // Bagi nilai CF dengan jumlah CF alternatif di setiap aspek
    foreach ($nilaiCF as $id_aspek => $nilai_cf_per_aspek) {
        $jumlah_cf_alternatif = count($nilai_cf_per_aspek);
        foreach ($nilai_cf_per_aspek as $alternatif => $nilai_cf) {
            $nilaiCF[$id_aspek][$alternatif] /= $jumlah_cf_alternatif;
        }
    }

    // Bagi nilai SF dengan jumlah SF alternatif di setiap aspek
    foreach ($nilaiSF as $id_aspek => $nilai_sf_per_aspek) {
        $jumlah_sf_alternatif = count($nilai_sf_per_aspek);
        foreach ($nilai_sf_per_aspek as $alternatif => $nilai_sf) {
            $nilaiSF[$id_aspek][$alternatif] /= $jumlah_sf_alternatif;
        }
    }

    // Hitung hasil akhir berdasarkan bobot CF dan SF
    $hasilAkhir = array();
    foreach ($nilaiCF as $id_aspek => $nilai_cf_per_aspek) {
        foreach ($nilai_cf_per_aspek as $alternatif => $nilai_cf) {
            // Hitung hasil akhir untuk setiap alternatif di aspek tersebut
            $hasilAkhir[$id_aspek][$alternatif] = ($nilai_cf * 0.55) + ($nilaiSF[$id_aspek][$alternatif] * 0.45);
        }
    }

    // Ambil nilai aspek dari database dan ubah menjadi persentase
    $queryAspek = $conn->query("SELECT id_aspek, bobot FROM aspek");
    if($queryAspek) {
        $aspek_data = $queryAspek->fetch_all(MYSQLI_ASSOC);
        $total_bobot = array_sum(array_column($aspek_data, 'bobot'));
        $nilaiAspek = array();
        foreach ($aspek_data as $data) {
            $nilaiAspek[$data['id_aspek']] = $data['bobot'] / $total_bobot; // Ubah bobot menjadi desimal
        }

        // Hitung hasil perankingan
        $perankingan = array();
        foreach ($hasilAkhir as $id_aspek => $hasil_per_aspek) {
            foreach ($hasil_per_aspek as $alternatif => $hasil) {
                // Kalikan hasil akhir dengan nilai aspek dan jumlahkan per aspek
                if (!isset($perankingan[$alternatif])) {
                    $perankingan[$alternatif] = 0;
                }
                $perankingan[$alternatif] += $hasil * $nilaiAspek[$id_aspek];
            }
        }

        // Tampilkan hasil perankingan tiap alternatif
        echo "<h2>Perankingan Tiap Alternatif</h2>";
        echo "<table border='1'><tr><th>Alternatif</th><th>Nilai Perankingan</th></tr>";
        foreach ($perankingan as $alternatif => $hasil) {
            echo "<tr><td>Alternatif $alternatif</td><td>$hasil</td></tr>";
        }
        echo "</table>";

        // Tampilkan tombol kembali ke halaman dashboard
        echo '<a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>';
    } else {
        // Handle jika query nilai aspek tidak berhasil dieksekusi
        echo "Error: " . $conn->error;
    }

} else {
    // Handle jika POST data tidak terdefinisi atau tidak lengkap
    // Misalnya, tampilkan pesan kesalahan atau alihkan pengguna ke halaman lain
    echo "Error: Incomplete or undefined POST data.";
}
?>
