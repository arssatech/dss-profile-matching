<?php

include('config.php');

$hasilAkhir = [];

if(isset($_POST['nilai_kriteria'])) {
    
    $nilai_kriteria = $_POST['nilai_kriteria'];

    $bobot_kriteria = array();
    foreach ($nilai_kriteria as $id_alternatif => $kriteria) {
        foreach ($kriteria as $id_kriteria => $nilai) {
            
            if(!empty($nilai)) {
                $queryInfo = $conn->query("SELECT nilai_target, jenis_factor, id_aspek FROM kriteria WHERE id_kriteria = $id_kriteria");
                if($queryInfo) {
                    $info_data = $queryInfo->fetch_assoc();
                    $target = $info_data['nilai_target'];
                    $jenis_factor = $info_data['jenis_factor'];
                    $id_aspek = $info_data['id_aspek'];
                    
                    $gap = $target - $nilai;

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
                            $bobot = 0; 
                            break;
                    }

                    
                    $bobot_kriteria[$id_aspek][$id_kriteria][] = array(
                        'alternatif' => $id_alternatif,
                        'gap' => $gap,
                        'bobot' => $bobot,
                        'jenis_factor' => $jenis_factor
                    );
                } else {
                   
                    echo "Error: " . $conn->error;
                }
            }
        }
    }


    $nilaiCF = array(); 
    $nilaiSF = array(); 
    foreach ($bobot_kriteria as $id_aspek => $aspek) {
        foreach ($aspek as $id_kriteria => $kriteria) {
            foreach ($kriteria as $info) {
                if ($info['jenis_factor'] == 'core') {
                    if (!isset($nilaiCF[$id_aspek][$info['alternatif']])) {
                        $nilaiCF[$id_aspek][$info['alternatif']] = 0;
                    }
                    $nilaiCF[$id_aspek][$info['alternatif']] += $info['bobot'];
                } elseif ($info['jenis_factor'] == 'secondary') {
                    if (!isset($nilaiSF[$id_aspek][$info['alternatif']])) {
                        $nilaiSF[$id_aspek][$info['alternatif']] = 0;
                    }
                    $nilaiSF[$id_aspek][$info['alternatif']] += $info['bobot'];
                }
            }
        }
      
    }

   
   $queryJumlahCF = $conn->query("SELECT id_aspek, COUNT(*) as jumlah_cf FROM kriteria WHERE jenis_factor = 'core' GROUP BY id_aspek");
   $queryJumlahSF = $conn->query("SELECT id_aspek, COUNT(*) as jumlah_sf FROM kriteria WHERE jenis_factor = 'secondary' GROUP BY id_aspek");

   $jumlahCFPerAspek = array();
   $jumlahSFPerAspek = array();

   if ($queryJumlahCF) {
   while ($row = $queryJumlahCF->fetch_assoc()) {
       $jumlahCFPerAspek[$row['id_aspek']] = $row['jumlah_cf'];
       }
   }

   if ($queryJumlahSF) {
   while ($row = $queryJumlahSF->fetch_assoc()) {
       $jumlahSFPerAspek[$row['id_aspek']] = $row['jumlah_sf'];
       }
   }

   
   foreach ($nilaiCF as $id_aspek => $nilai_cf_per_aspek) {
       $jumlah_cf_aspek = isset($jumlahCFPerAspek[$id_aspek]) ? $jumlahCFPerAspek[$id_aspek] : 1;
   foreach ($nilai_cf_per_aspek as $alternatif => $nilai_cf) {
       $nilaiCF[$id_aspek][$alternatif] /= $jumlah_cf_aspek;
       }
   }

   foreach ($nilaiSF as $id_aspek => $nilai_sf_per_aspek) {
       $jumlah_sf_aspek = isset($jumlahSFPerAspek[$id_aspek]) ? $jumlahSFPerAspek[$id_aspek] : 1;
   foreach ($nilai_sf_per_aspek as $alternatif => $nilai_sf) {
       $nilaiSF[$id_aspek][$alternatif] /= $jumlah_sf_aspek;
       }
   }


   
    $hasilAkhir = array();
    foreach ($nilaiCF as $id_aspek => $nilai_cf_per_aspek) {
        foreach ($nilai_cf_per_aspek as $alternatif => $nilai_cf) {
            $hasilAkhir[$id_aspek][$alternatif] = ($nilai_cf * 0.55) + ($nilaiSF[$id_aspek][$alternatif] * 0.45);
        }
    }

    // Ambil nilai aspek dari database dan ubah ke desimal
    $queryAspek = $conn->query("SELECT id_aspek, bobot FROM aspek");
    if($queryAspek) {
        $aspek_data = $queryAspek->fetch_all(MYSQLI_ASSOC);
        $total_bobot = array_sum(array_column($aspek_data, 'bobot'));
        $nilaiAspek = array();
        foreach ($aspek_data as $data) {
            $nilaiAspek[$data['id_aspek']] = $data['bobot'] / $total_bobot; // Ubah bobot menjadi desimal
        }

       
        $perankingan = array();
        foreach ($hasilAkhir as $id_aspek => $hasil_per_aspek) {
            foreach ($hasil_per_aspek as $alternatif => $hasil) {
                if (!isset($perankingan[$alternatif])) {
                    $perankingan[$alternatif] = 0;
                }
                $perankingan[$alternatif] += $hasil * $nilaiAspek[$id_aspek];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .btn-container {
            margin-top: 20px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Hasil Perhitungan</h1>

        <!-- Tabel hasil akhir -->
        <?php if(!empty($hasilAkhir)): ?>
            <h2>Peringkat Hasil Akhir</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Nama Pegawai</th>
                        <th>Hasil Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sorting $perankingan by value (descending order)
                    arsort($perankingan);

                    // Counter for ranking
                    $ranking = 1;

                    // Display each result
                    foreach ($perankingan as $alternatif => $hasil) {
                        // Retrieve employee name from the database based on alternative ID
                        $queryNamaPegawai = $conn->query("SELECT nama_pegawai FROM alternatif WHERE id_alternatif = $alternatif");
                        if($queryNamaPegawai && $queryNamaPegawai->num_rows > 0) {
                            $nama_pegawai = $queryNamaPegawai->fetch_assoc()['nama_pegawai'];
                            // Display the result
                            echo "<tr>";
                            echo "<td>$ranking</td>";
                            echo "<td>$nama_pegawai</td>";
                            echo "<td>$hasil</td>";
                            echo "</tr>";
                            $ranking++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info" role="alert">Belum ada hasil perhitungan yang tersedia.</div>
        <?php endif; ?>
    </div>

    <!-- Button Container -->
    <div class="container btn-container">
        <!-- Tombol kembali ke dashboard -->
        <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>

        <!-- Button untuk menyimpan tabel ke PDF -->
        <button id="savePDF" class="btn btn-success">Simpan ke PDF</button>
    </div>

    <!-- Script untuk menyimpan tabel ke PDF -->
    <script>
    document.getElementById('savePDF').addEventListener('click', function() {
        saveToPDF();
    });

    function saveToPDF() {
        // Initialize jsPDF
        const doc = new jsPDF();

        // Add table content
        const table = document.querySelector('table');
        const tableData = doc.autoTableHtmlToJson(table);
        doc.autoTable(tableData.columns, tableData.rows);

        // Save the PDF
        doc.save('hasil_perhitungan.pdf');
    }
    </script>
</body>
</html>



