<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Halaman
AuthMiddleware::checkAuth();

$csrfToken = CsrfMiddleware::generateToken();
$db = Database::getConnection();

// Fetch Data Alternatif
$stmt = $db->query("SELECT * FROM alternatif ORDER BY id_alternatif ASC");
$alternatifList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alternatif - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Data Alternatif / Kandidat</h3>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">Dashboard</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    + Tambah Alternatif
                </button>
            </div>
        </div>

        <!-- Notifikasi Status -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'created'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data alternatif berhasil ditambahkan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data alternatif berhasil dihapus.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] === 'invalid'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Inputan tidak valid atau nama alternatif kosong.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Terjadi kesalahan pada database.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 80px;" class="text-center">No</th>
                                <th>Nama Alternatif</th>
                                <th style="width: 150px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($alternatifList)): ?>
                                <?php foreach ($alternatifList as $index => $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_alternatif']) ?></strong></td>
                                        <td class="text-center">
                                            <a href="delete_alternatif.php?id_alternatif=<?= $row['id_alternatif'] ?>&csrf_token=<?= urlencode($csrfToken) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus data alternatif ini? Seluruh data nilai terkait akan terhapus.')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada data alternatif.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Alternatif -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="create_alternatif.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Alternatif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <div class="mb-3">
                            <label for="nama_alternatif" class="form-label">Nama Alternatif / Kandidat</label>
                            <input type="text" class="form-control" name="nama_alternatif" id="nama_alternatif" required autocomplete="off" placeholder="Misal: Ahmad Fauzi">
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