# 🎯 Decision Support System (SPK) - Profile Matching Engine

Sistem Pendukung Keputusan (SPK) berbasis Web modular yang dibangun menggunakan **PHP Native** dan **MySQL/SQLite**. Aplikasi ini dirancang untuk melakukan proses penilaian, kalkulasi gap, penentuan bobot, hingga perangkingan alternatif secara objektif, terotomatisasi, dan presisi 100%.

## 📌 Problem Statement & Objective

* **Problem**: Proses seleksi kandidat atau evaluasi kinerja secara manual rentan terhadap keterlambatan, kecenderungan subjektif, dan kekeliruan kalkulasi matematis (*human-error*) saat mengolah data dalam jumlah besar di spreadsheet.

* **Objective**: Membangun engine SPK berbasis web yang menerapkan kalkulasi matematis Profile Matching secara presisi, akurat, serta aman dari ancaman keamanan web dasar (SQL Injection & CSRF).

## ✨ Fitur Utama & Keunggulan

* **Autentikasi & Hak Akses:**
  * Login aman dengan proteksi sesi `auth.php` dan `unauthorize.php`.
  * Pendaftaran pengguna baru melalui `register.php`.

* **Manajemen Master Data (CRUD):**
  * **Data Pengguna (`data_pengguna.php`):** Pengelolaan akun dan otorisasi sistem.
  * **Data Aspek (`data_aspek.php`):** Pengaturan aspek penilaian beserta bobot persentasenya.
  * **Data Kriteria (`data_kriteria.php`):** Pengaturan kriteria per aspek, nilai target ideal, serta pengelompokan jenis faktor (*Core Factor* / *Secondary Factor*).
  * **Data Alternatif (`data_alternatif.php`):** Pengelolaan data kandidat/karyawan yang akan dinilai.
  * **Data Penilaian (`data_penilaian.php`):** Form pengisian nilai kriteria alternatif per aspek.

* **Kalkulasi & Perangkingan Otomatis:**
  * Pemrosesan *Gap*, pemetaan bobot, perhitungan *Core Factor* (CF) & *Secondary Factor* (SF), hingga laporan perangkingan akhir pada `perhitungan.php`, `hasil.php`, dan `data_hasil_akhir.php`.

* **Security-First Architecture:**
  * **PDO Prepared Statements:** Proteksi penuh terhadap SQL Injection.
  * **CSRF Token Middleware:** Proteksi form dari Cross-Site Request Forgery pada handler `create_*.php`, `edit_*.php`, dan `delete_*.php`.

* **Zero-XAMPP Portability:**
  * Dilengkapi script `migrate.php` & `seed.php` berbasis PHP CLI untuk inisialisasi database instan.

## 📚 Landasan Teori & Formula Profile Matching

**Profile Matching** (Pencocokan Profil) adalah metode pengambilan keputusan yang membandingkan antara profil alternatif dengan profil target yang dibutuhkan.

### 1. Perhitungan Selisih (Gap)

Menghitung selisih antara nilai alternatif ($N_{\text{alternatif}}$) dengan nilai target kriteria ($N_{\text{target}}$):

$$
\text{Gap} = N_{\text{alternatif}} - N_{\text{target}}
$$

### 2. Pembobotan Nilai Gap

Nilai *gap* dikonversikan ke dalam bobot nilai standar:

| Selisih (Gap) | Bobot Nilai | Keterangan |
| :---: | :---: | :--- |
| $0$ | $5.0$ | Tidak ada selisih (Sesuai target) |
| $1$ | $4.5$ | Kelebihan 1 tingkat |
| $-1$ | $4.0$ | Kekurangan 1 tingkat |
| $2$ | $3.5$ | Kelebihan 2 tingkat |
| $-2$ | $3.0$ | Kekurangan 2 tingkat |
| $3$ | $2.5$ | Kelebihan 3 tingkat |
| $-3$ | $2.0$ | Kekurangan 3 tingkat |
| $4$ | $1.5$ | Kelebihan 4 tingkat |
| $-4$ | $1.0$ | Kekurangan 4 tingkat |

### 3. Perhitungan Core Factor (NCF) dan Secondary Factor (NSF)

$$
\text{NCF} = \frac{\sum \text{Bobot Nilai Gap (Core Factor)}}{\sum \text{Jumlah Kriteria Core Factor}}
$$

$$
\text{NSF} = \frac{\sum \text{Bobot Nilai Gap (Secondary Factor)}}{\sum \text{Jumlah Kriteria Secondary Factor}}
$$

### 4. Perhitungan Nilai Total Aspek

$$
N_{\text{Aspek}} = (\text{NCF} \times 60\%) + (\text{NSF} \times 40\%)
$$

### 5. Perhitungan Nilai Akhir Total (Ranking)

$$
\text{Total Nilai} = \sum \left(N_{\text{Aspek}} \times \text{Bobot Persen Aspek}\right)
$$

## 📐 Arsitektur & Diagram Sistem

### 1. Flowchart Sistem

```text
[ Start ] ──► [ Login / Register ] ──► [ Dashboard ]
                                             │
    ┌────────────────────────────────────────┼────────────────────────────────────────┐
    ▼                                        ▼                                        ▼
[ Data Aspek & Kriteria ]          [ Data Alternatif ]                   [ Input Penilaian ]
    │                                        │                                        │
    └────────────────────────────────────────┴────────────────────────────────────────┘
                                             │
                                             ▼
                                [ Engine Profile Matching ]
                               (Gap ➔ NCF/NSF ➔ Total Score)
                                             │
                                             ▼
                                [ Perhitungan & Hasil Akhir ]
```

### 2. Entity Relationship Diagram (ERD)

```text
+------------------+         +------------------+
|      aspek       |         |     kriteria     |
+------------------+         +------------------+
| PK id_aspek      |1       N| PK id_kriteria   |
|    nama_aspek    |<--------| FK id_aspek      |
|    bobot         |         |    nama_kriteria |
+------------------+         |    target        |
                             |    type (core/sec|
                             +--------+---------+
                                      | 1
+------------------+                  |
|    alternatif    |                  |
+------------------+                  | N
| PK id_alternatif |1        N+-------+----------+
|    nama_alt      |<--------|    penilaian     |
+------------------+         +------------------+
                             | PK id_penilaian  |
                             | FK id_alternatif |
                             | FK id_kriteria   |
                             |    nilai         |
                             +------------------+
```

## 📂 Struktur Direktori Proyek

```text
.
├── config/                  # Konfigurasi Database (Singleton PDO)
├── config.php               # System Environment & Global Settings
├── src/                     # Core Business Logic & Security Middleware
├── vendor/                  # Composer Autoload Dependencies
│
├── index.php                # Landing Page / Redirect Handler
├── login.php / logout.php   # Autentikasi Login & Logout
├── register.php             # Form Registrasi User
├── auth.php                 # Middleware Otorisasi Hak Akses
├── unauthorize.php          # Halaman Error Akses Ditolak
├── dashboard.php            # Control Panel Utama
├── navbar.php               # Komponen Layout Navigasi Header
│
├── data_aspek.php           # Interface Kelola Master Aspek
├── data_kriteria.php        # Interface Kelola Master Kriteria
├── data_alternatif.php      # Interface Kelola Master Alternatif
├── data_penilaian.php       # Form Input Nilai Alternatif
├── data_pengguna.php        # Interface Kelola Data User
├── data_hasil_akhir.php     # Ringkasan Laporan Penilaian
├── perhitungan.php          # Modul Breakdown Math Profile Matching
├── hasil.php                # Laporan Perangkingan Akhir
│
├── create_*.php             # Action Handlers Tambah Data (Insert)
├── edit_*.php               # Action Handlers Ubah Data (Update)
├── delete_*.php             # Action Handlers Hapus Data (Delete)
├── get_*.php                # AJAX & Data Fetching Endpoint Handlers
│
├── migrate.php              # Script Migrasi Database Otomatis (PHP CLI)
├── seed.php                 # Script Seeder Data Awal
└── README.md                # Dokumentasi Utama Repositori
```

## ⚡ Cara Jalankan Proyek

### **Metode 1: CLI Server (Tanpa XAMPP / Menggunakan SQLite)**

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/username/spk-profile-matching.git
   cd spk-profile-matching
   ```

2. **Install Autoload Dependencies:**
   ```bash
   composer install
   ```

3. **Jalankan Migrasi Database & Seeder:**
   ```bash
   php migrate.php
   php seed.php
   ```

4. **Jalankan Development Server:**
   ```bash
   php -S localhost:8000
   ```
   Akses via browser di `http://localhost:8000`.

### **Metode 2: Web Server Konvensional (XAMPP / Laragon + MySQL)**

1. Pastikan Service **MySQL** di Control Panel XAMPP / Laragon sudah **Start/Aktif**.
2. Pindahkan folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
3. Buat database di phpMyAdmin (misal: `spk_profile_matching`).
4. Sesuaikan kredensial database di `config/database.php` atau `config.php`.
5. Jalankan `php migrate.php` atau import file `.sql` ke database MySQL tersebut.
6. Akses melalui browser di `http://localhost/spk-profile-matching`.

## 🔑 Akun Default Login

| Username | Password | Hak Akses |
| :--- | :--- | :--- |
| `admin` | `admin123` | Administrator |