<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Akses
AuthMiddleware::checkAuth();

$csrfToken = CsrfMiddleware::generateToken();
$db = Database::getConnection();

// Fetch Data Alternatif
$alternatifList = $db->query("SELECT * FROM alternatif ORDER BY id_alternatif ASC")->fetchAll();

// Fetch Data Kriteria beserta Nama Aspek
$kriteriaList = $db->query("
    SELECT k.*, a.nama_aspek 
    FROM kriteria k 
    LEFT JOIN aspek a ON k.id_aspek = a.id_aspek 
    ORDER BY k.id_aspek ASC, k.id_kriteria ASC
")->fetchAll();

// Fetch Data Penilaian yang Sudah Ada (Matriks Nilai)
$penilaianRaw = $db->query("SELECT * FROM penilaian")->fetchAll();
$nilaiMap = [];
foreach ($penilaianRaw as $p) {
    $nilaiMap[$p['id_alternatif']][$p['id_kriteria']] = (int)$p['nilai'];
}

// Mengetahui Alternatif yang Dituju Jika Diklik Modal Edit/Input
$selectedAltId = filter_input(INPUT_GET, 'id_alternatif', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Penilaian - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Input & Kelola Penilaian Alternatif</h3>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">Dashboard</a>
                <a href="perhitungan.php" class="btn btn-success">Lihat Perhitungan</a>
            </div>
        </div>

        <!-- Notifikasi Status -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'error_input'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data inputan tidak lengkap atau salah.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] === 'db_error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal menyimpan data ke database.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Tabel Ringkasan Alternatif & Status Penilaian -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Alternatif / Kandidat</th>
                                <th style="width: 180px;" class="text-center">Status Nilai</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($alternatifList)): ?>
                                <?php foreach ($alternatifList as $index => $alt): ?>
                                    <?php 
                                        $altId = $alt['id_alternatif'];
                                        $sudahDinilai = isset($nilaiMap[$altId]) && count($nilaiMap[$altId]) > 0;
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($alt['nama_alternatif']) ?></strong></td>
                                        <td class="text-center">
                                            <?php if ($sudahDinilai): ?>
                                                <span class="badge bg-success">Sudah Dinilai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalInput<?= $altId ?>">
                                                <?= $sudahDinilai ? 'Edit Nilai' : '+ Input Nilai' ?>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Form Input/Edit Nilai per Alternatif -->
                                    <div class="modal fade" id="modalInput<?= $altId ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="create_penilaian.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Penilaian: <?= htmlspecialchars($alt['nama_alternatif']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                        <input type="hidden" name="id_alternatif" value="<?= $altId ?>">

                                                        <div class="alert alert-info py-2 fs-7">
                                                            Pilih skor nilai (1-5) untuk masing-masing kriteria di bawah ini.
                                                        </div>

                                                        <div class="row g-3">
                                                            <?php foreach ($kriteriaList as $k): ?>
                                                                <?php 
                                                                    $kId = $k['id_kriteria'];
                                                                    $existingScore = $nilaiMap[$altId][$kId] ?? 3; // Default nilai 3 jika baru
                                                                ?>
                                                                <div class="col-md-6">
                                                                    <div class="p-2 border rounded bg-white">
                                                                        <label for="score_<?= $altId ?>_<?= $kId ?>" class="form-label mb-1">
                                                                            <strong><?= htmlspecialchars($k['nama_kriteria']) ?></strong>
                                                                            <small class="text-muted d-block">(<?= htmlspecialchars($k['nama_aspek']) ?> | Target: <?= $k['target'] ?>)</small>
                                                                        </label>
                                                                        <select class="form-select form-select-sm" 
                                                                                name="scores[<?= $kId ?>]" 
                                                                                id="score_<?= $altId ?>_<?= $kId ?>" required>
                                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                <option value="<?= $i ?>" <?= $existingScore === $i ? 'selected' : '' ?>>
                                                                                    Nilai <?= $i ?>
                                                                                </option>
                                                                            <?php endfor; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada data alternatif. Silakan tambah alternatif terlebih dahulu.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>