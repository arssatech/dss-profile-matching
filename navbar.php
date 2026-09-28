<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-pink fixed-top" style="background-color: lightpink;">
        <!-- Tombol Toggle Navbar -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Bagian Kiri Navbar -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_aspek.php' ? 'active' : ''; ?>" href="data_aspek.php">Data Aspek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_kriteria.php' ? 'active' : ''; ?>" href="data_kriteria.php">Data Kriteria</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_alternatif.php' ? 'active' : ''; ?>" href="data_alternatif.php">Data Alternatif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_hasil_akhir.php' ? 'active' : ''; ?>" href="data_hasil_akhir.php">Data Hasil Akhir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data_pengguna.php' ? 'active' : ''; ?>" href="data_pengguna.php">Data Pengguna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Logo Perusahaan -->
        <a class="navbar-brand ml-auto" href="dashboard.php">
            <img src="img/logo_perusahaan.png" alt="Logo Perusahaan" width="140" height="50">
        </a>
    </nav>
</div>
