<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .navbar-nav .nav-link.active {
            font-weight: bold; /* Menjadikan tulisan lebih gelap */
        }
        /* Tambahkan margin atau padding pada bagian atas container */
        .container {
            margin-top: 80px; /* Sesuaikan dengan tinggi navbar */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- Tambahkan tombol atau link ke halaman penilaian -->
        <h1>Halaman Penilaian</h1>
        <a href="create_penilaian.php" class="btn btn-primary">Lakukan Penilaian</a>
    </div>
</body>
</html>
