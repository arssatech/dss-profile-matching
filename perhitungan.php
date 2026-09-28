<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Services\ProfileMatchingService;

AuthMiddleware::checkAuth();

$db = Database::getConnection();
$pmService = new ProfileMatchingService($db);

// Menjalankan kalkulasi Profile Matching
$calculationData = $pmService->calculateAll();
$rankings = $calculationData['rankings'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hasil Perhitungan & Perangkingan</h2>
            <div>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <button onclick="window.print()" class="btn btn-outline-dark me-2">Cetak Laporan</button>
            </div>
        </div>

        <!-- Tabel Rangking Akhir -->
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Rangking Hasil Akhir</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 70px;" class="text-center">Peringkat</th>
                                <th>Nama Alternatif</th>
                                <th style="width: 220px;" class="text-center">Nilai Akhir (Total)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rankings)): ?>
                                <?php foreach ($rankings as $rank => $item): ?>
                                    <tr class="<?= $rank === 0 ? 'table-success fw-bold' : '' ?>">
                                        <td class="text-center">
                                            <?php if ($rank === 0): ?>
                                                <span class="badge bg-warning text-dark">#1</span>
                                            <?php else: ?>
                                                #<?= $rank + 1 ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($item['nama_alternatif']) ?></td>
                                        <td class="text-center"><?= number_format($item['final_score'], 4) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data nilai penilaian.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rincian Nilai per Aspek -->
        <h4 class="mb-3">Rincian Perhitungan per Alternatif</h4>
        <?php foreach ($rankings as $item): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong>Alternatif: <?= htmlspecialchars($item['nama_alternatif']) ?></strong> 
                    <span class="float-end">Total Skor: <?= number_format($item['final_score'], 4) ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($item['aspek_scores'] as $aspId => $aspDetail): ?>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 bg-white h-100">
                                    <h6 class="border-bottom pb-2 text-primary"><?= htmlspecialchars($aspDetail['nama_aspek']) ?></h6>
                                    
                                    <table class="table table-sm table-borderless fs-7 mb-2">
                                        <thead>
                                            <tr class="text-muted border-bottom">
                                                <th>Kriteria</th>
                                                <th>Val</th>
                                                <th>Tgt</th>
                                                <th>Gap</th>
                                                <th>Bobot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($aspDetail['kriteria_details'] as $kd): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($kd['nama_kriteria']) ?> <small class="text-muted">(<?= strtoupper($kd['type']) ?>)</small></td>
                                                    <td><?= $kd['nilai'] ?></td>
                                                    <td><?= $kd['target'] ?></td>
                                                    <td><?= $kd['gap'] ?></td>
                                                    <td><strong><?= $kd['bobot_gap'] ?></strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <div class="bg-light p-2 rounded text-end fs-7">
                                        <small class="d-block">NCF (Core): <strong><?= number_format($aspDetail['ncf'], 2) ?></strong></small>
                                        <small class="d-block">NSF (Secondary): <strong><?= number_format($aspDetail['nsf'], 2) ?></strong></small>
                                        <small class="d-block text-primary">Nilai Aspek: <strong><?= number_format($aspDetail['nilai_total_aspek'], 4) ?></strong></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>