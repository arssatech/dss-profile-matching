<?php
require_once __DIR__ . '/config/database.php';

echo "Mulai mengisi Data Seed...\n";

try {
    $db = Database::getConnection();

    $check = $db->query("SELECT COUNT(*) FROM aspek")->fetchColumn();
    if ($check > 0) {
        die("Data sudah terisi sebelumnya.\n");
    }

    // Insert Aspek (Total Bobot = 100%)
    $db->exec("INSERT INTO aspek (id_aspek, nama_aspek, bobot) VALUES 
        (1, 'Aspek Intelektual', 60),
        (2, 'Aspek Sikap Kerja', 40);
    ");

    // Insert Kriteria (type: 'core' / 'secondary')
    $db->exec("INSERT INTO kriteria (id_aspek, nama_kriteria, target, type) VALUES 
        (1, 'Sistematika Berpikir', 4, 'core'),
        (1, 'Penalaran Verbal', 3, 'secondary'),
        (1, 'Kemampuan Analisis', 4, 'core'),
        (2, 'Kedisiplinan', 5, 'core'),
        (2, 'Kerjasama Tim', 3, 'secondary');
    ");

    // Insert Alternatif Contoh
    $db->exec("INSERT INTO alternatif (nama_alternatif) VALUES 
        ('Ahmad Fauzi'),
        ('Budi Santoso');
    ");

    echo "🎉 Seeding Berhasil!\n";

} catch (PDOException $e) {
    die("Gagal Seeding: " . $e->getMessage() . "\n");
}