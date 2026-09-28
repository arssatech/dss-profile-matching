<?php
include('config.php');

function hitungGapDanBobot($nilai, $target) {
    // Hitung gap
    $gap = $target - $nilai;

    // Ubah gap menjadi bobot berdasarkan pemetaan yang telah ditentukan
    switch ($gap) {
        case 0:
        case 1:
        case 2:
        case 3:
        case 4:
            $bobot = 5;
            break;
        case -1:
            $bobot = 4;
            break;
        case -2:
            $bobot = 3;
            break;
        case -3:
            $bobot = 2;
            break;
        case -4:
            $bobot = 1;
            break;
        default:
            $bobot = 0; // Default jika gap tidak sesuai dengan daftar
            break;
    }

    return array('gap' => $gap, 'bobot' => $bobot);
}

if(isset($_POST['nilai_kriteria'])) {
    // Ambil data penilaian dari form
    $nilai_kriteria = $_POST['nilai_kriteria'];

    // Inisialisasi array untuk menyimpan bobot kriteria
    $bobot_kriteria = array();

    // Proses perhitungan gap dan bobot
    foreach ($nilai_kriteria as $id_alternatif => $kriteria) {
        foreach ($kriteria as $id_kriteria => $nilai) {
            // Ambil nilai target, jenis factor, dan nama kriteria dari database
            $queryInfo = $conn->query("SELECT nilai_target, jenis_factor, id_aspek, nama_kriteria FROM kriteria WHERE id_kriteria = $id_kriteria");
            if($queryInfo) {
                $info_data = $queryInfo->fetch_assoc();
                $target = $info_data['nilai_target'];
                $jenis_factor = $info_data['jenis_factor'];
                $id_aspek = $info_data['id_aspek'];
                $nama_kriteria = $info_data['nama_kriteria'];

                // Hitung gap dan bobot
                $result = hitungGapDanBobot($nilai, $target);
                $gap = $result['gap'];
                $bobot = $result['bobot'];

                // Menyimpan informasi jenis factor (CF atau SF) ke dalam array bobot
                $bobot_kriteria[$id_aspek][$id_kriteria][] = array(
                    'alternatif' => $id_alternatif,
                    'gap' => $gap,
                    'bobot' => $bobot,
                    'jenis_factor' => $jenis_factor,
                    'nama_kriteria' => $nama_kriteria,
                    'target' => $target
                );
            } else {
                // Handle jika query tidak berhasil dieksekusi
                echo "Error: " . $conn->error;
            }
        }
    }

    // Output perhitungan gap dan bobot per aspek dan alternatif
    echo "<h2>Gap dan Bobot per Aspek dan Alternatif</h2>";
    foreach ($bobot_kriteria as $id_aspek => $aspek) {
        echo "<h3>Aspek $id_aspek</h3>";
        echo "<table border='1'><tr><th>Alternatif</th><th>Kriteria</th><th>Nilai Target</th><th>Gap</th><th>Bobot</th><th>Jenis Factor</th></tr>";
        foreach ($aspek as $id_kriteria => $kriteria) {
            foreach ($kriteria as $info) {
                echo "<tr><td>Alternatif " . $info['alternatif'] . "</td><td>" . $info['nama_kriteria'] . "</td><td>" . $info['target'] . "</td><td>" . $info['gap'] . "</td><td>" . $info['bobot'] . "</td><td>" . $info['jenis_factor'] . "</td></tr>";
            }
        }
        echo "</table><hr>";
    }

    // Hitung nilai CF dan SF per aspek untuk setiap alternatif
    $nilaiCF = array(); // Array untuk menyimpan nilai CF
    $nilaiSF = array(); // Array untuk menyimpan nilai SF
    foreach ($bobot_kriteria as $id_aspek => $aspek) {
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
    }

    // Output nilai CF dan SF per aspek dan alternatif
    echo "<h2>Nilai CF dan SF per Aspek dan Alternatif</h2>";
    foreach ($nilaiCF as $id_aspek => $aspek) {
        echo "<h3>Aspek $id_aspek</h3>";
        echo "<table border='1'><tr><th>Alternatif</th><th>CF</th><th>SF</th></tr>";
        foreach ($aspek as $alternatif => $cf) {
            $sf = isset($nilaiSF[$id_aspek][$alternatif]) ? $nilaiSF[$id_aspek][$alternatif] : 0;
            echo "<tr><td>Alternatif $alternatif</td><td>$cf</td><td>$sf</td></tr>";
        }
        echo "</table><hr>";
    }

    
    // Ambil jumlah CF dan SF di setiap aspek dari database
    $queryJumlahCF = $conn->query("SELECT id_aspek, COUNT(*) as jumlah_cf FROM kriteria WHERE jenis_factor = 'core' GROUP BY id_aspek");
    $queryJumlahSF = $conn->query("SELECT id_aspek, COUNT(*) as jumlah_sf FROM kriteria WHERE jenis_factor = 'secondary' GROUP BY id_aspek");

    // Buat array untuk menyimpan jumlah CF dan SF per aspek
    $jumlahCFPerAspek = array();
    $jumlahSFPerAspek = array();

    // Ambil jumlah CF per aspek
    if ($queryJumlahCF) {
    while ($row = $queryJumlahCF->fetch_assoc()) {
        $jumlahCFPerAspek[$row['id_aspek']] = $row['jumlah_cf'];
        }
    }

    // Ambil jumlah SF per aspek
    if ($queryJumlahSF) {
    while ($row = $queryJumlahSF->fetch_assoc()) {
        $jumlahSFPerAspek[$row['id_aspek']] = $row['jumlah_sf'];
        }
    }

    // Bagi nilai CF dengan jumlah CF alternatif di setiap aspek
    foreach ($nilaiCF as $id_aspek => $nilai_cf_per_aspek) {
        $jumlah_cf_aspek = isset($jumlahCFPerAspek[$id_aspek]) ? $jumlahCFPerAspek[$id_aspek] : 1;
    foreach ($nilai_cf_per_aspek as $alternatif => $nilai_cf) {
        $nilaiCF[$id_aspek][$alternatif] /= $jumlah_cf_aspek;
        }
    }

    // Bagi nilai SF dengan jumlah SF alternatif di setiap aspek
    foreach ($nilaiSF as $id_aspek => $nilai_sf_per_aspek) {
        $jumlah_sf_aspek = isset($jumlahSFPerAspek[$id_aspek]) ? $jumlahSFPerAspek[$id_aspek] : 1;
    foreach ($nilai_sf_per_aspek as $alternatif => $nilai_sf) {
        $nilaiSF[$id_aspek][$alternatif] /= $jumlah_sf_aspek;
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

    // Output hasil akhir per aspek dan alternatif
    echo "<h2>Hasil Akhir per Aspek dan Alternatif</h2>";
    foreach ($hasilAkhir as $id_aspek => $aspek) {
        echo "<h3>Aspek $id_aspek</h3>";
        echo "<table border='1'><tr><th>Alternatif</th><th>Hasil Akhir</th></tr>";
        foreach ($aspek as $alternatif => $hasil) {
            echo "<tr><td>Alternatif $alternatif</td><td>$hasil</td></tr>";
        }
        echo "</table><hr>";
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

        // Output nilai aspek dalam desimal
        echo "<h2>Nilai Aspek dalam Desimal</h2>";
        foreach ($nilaiAspek as $id_aspek => $nilai) {
            echo "Aspek $id_aspek: $nilai<br>";
        }
        echo "<hr>";

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
        
        arsort($perankingan);

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