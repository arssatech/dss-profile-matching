<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Halaman
AuthMiddleware::checkAuth();

$csrfToken = CsrfMiddleware::generateToken();
$db = Database::getConnection();

// Fetch Data Aspek
$stmt = $db->query("SELECT * FROM aspek ORDER BY id_aspek ASC");
$aspekList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Aspek - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Data Aspek Penilaian</h3>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">Dashboard</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    + Tambah Aspek
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th>Nama Aspek</th>
                            <th style="width: 150px;">Bobot (%)</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($aspekList)): ?>
                            <?php foreach ($aspekList as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($row['nama_aspek']) ?></td>
                                    <td><?= number_format($row['bobot'], 2) ?>%</td>
                                    <td>
                                        <a href="delete_aspek.php?id_aspek=<?= $row['id_aspek'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin ingin menghapus aspek ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada data aspek.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Aspek -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="create_aspek.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Aspek</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <div class="mb-3">
                            <label for="nama_aspek" class="form-label">Nama Aspek</label>
                            <input type="text" class="form-control" name="nama_aspek" id="nama_aspek" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="bobot" class="form-label">Bobot (%)</label>
                            <input type="number" step="0.01" class="form-control" name="bobot" id="bobot" required min="0" max="100">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>