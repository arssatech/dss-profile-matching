<?php
session_start();

// Pengecekan Autentikasi Sederhana
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Services/ProfileMatchingService.php';

use App\Services\ProfileMatchingService;

try {
    $db = Database::getConnection();
    $pmService = new ProfileMatchingService($db);
    $rankings = $pmService->processAll();
} catch (Exception $e) {
    error_log("Error pada hasil.php: " . $e->getMessage());
    $error = "Gagal memproses data perhitungan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perankingan - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Hasil Perankingan (Profile Matching)</h2>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col" style="width: 100px;">Peringkat</th>
                            <th scope="col">Nama Alternatif</th>
                            <th scope="col" style="width: 200px;">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rankings)): ?>
                            <?php foreach ($rankings as $index => $row): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= $index === 0 ? 'success' : 'secondary' ?> fs-6">
                                            #<?= $index + 1 ?>
                                        </span>
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['nama_alternatif']) ?></strong></td>
                                    <td><?= number_format($row['nilai_akhir'], 4) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data alternatif atau penilaian yang dapat dihitung.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>