<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai Kriteria</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .alternatif-heading {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .aspek-label {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Input Nilai Kriteria</h1>

        <!-- Form untuk memasukkan nilai kriteria untuk setiap alternatif -->
        <form id="inputForm" method="POST" action="perhitungan.php">
            <!-- Ambil data alternatif (karyawan) dari database -->
            <?php
            include('config.php');

            $queryAlternatif = $conn->query("SELECT * FROM alternatif");
            while ($alternatif = $queryAlternatif->fetch_assoc()) {
                $id_alternatif = $alternatif['id_alternatif'];
                $nama_alternatif = $alternatif['nama_pegawai'];
                echo "<div class='row alternatif-section' data-alternatif-id='$id_alternatif'>";
                echo "<div class='col-md-12 alternatif-heading'><h2>Alternatif $id_alternatif: $nama_alternatif</h2></div>";

                // Ambil data kriteria dari database berdasarkan aspek
                $queryAspek = $conn->query("SELECT DISTINCT id_aspek FROM kriteria");
                while ($aspek = $queryAspek->fetch_assoc()) {
                    $id_aspek = $aspek['id_aspek'];
                    $nama_aspek_query = $conn->query("SELECT nama_aspek FROM aspek WHERE id_aspek = $id_aspek");
                    $nama_aspek = $nama_aspek_query->fetch_assoc()['nama_aspek'];
                    echo "<div class='col-md-3'>";
                    echo "<div class='aspek-label'>$nama_aspek</div>";
                    $queryKriteria = $conn->query("SELECT * FROM kriteria WHERE id_aspek = $id_aspek");
                    while ($kriteria = $queryKriteria->fetch_assoc()) {
                        $id_kriteria = $kriteria['id_kriteria'];
                        $nama_kriteria = $kriteria['nama_kriteria'];
                        echo "<label>$nama_kriteria:</label>";
                        echo "<input type='number' name='nilai_kriteria[$id_alternatif][$id_kriteria]' class='form-control' required>";
                    }
                    echo "</div>"; // col-md-3
                }
                echo "</div>"; // row
            }
            ?>

            <!-- Tombol untuk menyimpan input -->
            <button type="submit" class="btn btn-primary">Hitung</button>
            <!-- Tombol "Next" untuk beralih ke alternatif berikutnya -->
            <button type="button" class="btn btn-secondary" id="nextButton">Next</button>
            <!-- Tombol "Before" untuk beralih ke alternatif sebelumnya -->
            <button type="button" class="btn btn-secondary" id="beforeButton">Before</button>
            <!-- Tombol kembali ke dashboard -->
            <a href="dashboard.php" class="btn btn-warning">Kembali ke Dashboard</a>
        </form>
    </div>

    <!-- Script jQuery untuk menangani navigasi antara alternatif -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Semua bagian alternatif dianggap tersembunyi awalnya
            $('.alternatif-section').hide();
            // Tampilkan bagian pertama (alternatif pertama)
            $('.alternatif-section:first').show();

            // Handler untuk tombol "Next"
            $('#nextButton').click(function() {
                // Cari bagian yang sedang ditampilkan saat ini
                var currentSection = $('.alternatif-section:visible');
                // Sembunyikan bagian yang sedang ditampilkan
                currentSection.hide();
                // Tampilkan bagian berikutnya (jika ada)
                var nextSection = currentSection.next('.alternatif-section');
                if (nextSection.length !== 0) {
                    nextSection.show();
                }
                // Disable "Next" button if there are no more sections
                if (nextSection.next('.alternatif-section').length === 0) {
                    $(this).prop('disabled', true);
                }
                // Enable "Before" button
                $('#beforeButton').prop('disabled', false);
            });

            // Handler untuk tombol "Before"
            $('#beforeButton').click(function() {
                // Cari bagian yang sedang ditampilkan saat ini
                var currentSection = $('.alternatif-section:visible');
                // Sembunyikan bagian yang sedang ditampilkan
                currentSection.hide();
                // Tampilkan bagian sebelumnya (jika ada)
                var prevSection = currentSection.prev('.alternatif-section');
                if (prevSection.length !== 0) {
                    prevSection.show();
                }
                // Disable "Before" button if there are no more sections
                if (prevSection.prev('.alternatif-section').length === 0) {
                    $(this).prop('disabled', true);
                }
                // Enable "Next" button
                $('#nextButton').prop('disabled', false);
            });

            // Disable "Before" button initially since it's the first section
            $('#beforeButton').prop('disabled', true);
        });
    </script>
</body>
</html>
