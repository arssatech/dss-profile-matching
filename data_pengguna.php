<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Proteksi Halaman Khusus Admin
AuthMiddleware::checkAdmin();

$csrfToken = CsrfMiddleware::generateToken();
$db = Database::getConnection();

// Fetch Data Pengguna
$stmt = $db->query("SELECT id_pengguna, username, role FROM pengguna ORDER BY id_pengguna ASC");
$penggunaList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - SPK Profile Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Manajemen Pengguna System</h3>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">Dashboard</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    + Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Username</th>
                            <th style="width: 180px;">Role</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penggunaList)): ?>
                            <?php foreach ($penggunaList as $row): ?>
                                <tr>
                                    <td><?= $row['id_pengguna'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?= $row['role'] === 'admin' ? 'danger' : 'success' ?>">
                                            <?= strtoupper(htmlspecialchars($row['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="delete_pengguna.php?id_pengguna=<?= $row['id_pengguna'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada data pengguna.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="create_pengguna.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengguna Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role Akses</label>
                            <select class="form-select" name="role" id="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User / Evaluator</option>
                            </select>
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