<?php
require_once __DIR__ . '/config/database.php';

echo "========================================\n";
echo "   Mulai Migrasi Database SPK...        \n";
echo "========================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Tabel Aspek (Kolom: bobot)
    $db->exec("
        CREATE TABLE IF NOT EXISTS aspek (
            id_aspek INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_aspek TEXT NOT NULL,
            bobot REAL NOT NULL
        );
    ");
    echo "[✔] Tabel 'aspek' siap.\n";

    // 2. Tabel Kriteria (Kolom: type ['core', 'secondary'])
    $db->exec("
        CREATE TABLE IF NOT EXISTS kriteria (
            id_kriteria INTEGER PRIMARY KEY AUTOINCREMENT,
            id_aspek INTEGER NOT NULL,
            nama_kriteria TEXT NOT NULL,
            target INTEGER NOT NULL DEFAULT 3,
            type TEXT CHECK(type IN ('core', 'secondary')) NOT NULL DEFAULT 'core',
            FOREIGN KEY (id_aspek) REFERENCES aspek(id_aspek) ON DELETE CASCADE
        );
    ");
    echo "[✔] Tabel 'kriteria' siap.\n";

    // 3. Tabel Alternatif
    $db->exec("
        CREATE TABLE IF NOT EXISTS alternatif (
            id_alternatif INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_alternatif TEXT NOT NULL
        );
    ");
    echo "[✔] Tabel 'alternatif' siap.\n";

    // 4. Tabel Penilaian
    $db->exec("
        CREATE TABLE IF NOT EXISTS penilaian (
            id_penilaian INTEGER PRIMARY KEY AUTOINCREMENT,
            id_alternatif INTEGER NOT NULL,
            id_kriteria INTEGER NOT NULL,
            nilai INTEGER NOT NULL DEFAULT 0,
            UNIQUE (id_alternatif, id_kriteria),
            FOREIGN KEY (id_alternatif) REFERENCES alternatif(id_alternatif) ON DELETE CASCADE,
            FOREIGN KEY (id_kriteria) REFERENCES kriteria(id_kriteria) ON DELETE CASCADE
        );
    ");
    echo "[✔] Tabel 'penilaian' siap.\n";

    // 5. Tabel Users
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id_user INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            nama_lengkap TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    echo "[✔] Tabel 'users' siap.\n";

    // Insert Default Admin
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $db->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES ('admin', :pass, 'Administrator SPK')")
           ->execute([':pass' => $defaultPassword]);
        echo "[+] Default admin user dibuat (User: admin | Pass: admin123)\n";
    }

    echo "\n----------------------------------------\n";
    echo "🎉 Migrasi Database BERHASIL!\n";
    echo "----------------------------------------\n";

} catch (PDOException $e) {
    die("\n[✘] Gagal Melakukan Migrasi: " . $e->getMessage() . "\n");
}