# Sistem Pendukung Keputusan (SPK) Penilaian Karyawan - Profile Matching

Sistem Pendukung Keputusan (SPK) berbasis web yang dibangun menggunakan **PHP Native** dan **MySQL**. Aplikasi ini digunakan untuk melakukan penilaian serta perankingan karyawan/pegawai berdasarkan kriteria dan aspek tertentu menggunakan metode **Profile Matching** (Pencocokan Profil).

---

## 🛠️ Fitur Utama

- **Autentikasi & Hak Akses:** Keamanan login menggunakan `password_verify` dengan pemisahan peran antara Admin dan User biasa.
- **Manajemen Master Data (CRUD):**
  - **Data Pengguna:** Pengelolaan akun pengguna dan otorisasi sistem.
  - **Data Aspek:** Pengaturan aspek-aspek penilaian beserta bobot persentasenya.
  - **Data Kriteria:** Pengaturan kriteria untuk setiap aspek, nilai target ideal, serta pengelompokan jenis faktor (*Core Factor* / *Secondary Factor*).
  - **Data Alternatif:** Pengelolaan data karyawan/pegawai yang akan dinilai.
- **Penilaian Interaktif:** Form pengisian nilai kriteria berbasis AJAX/jQuery per aspek secara terstruktur.
- **Kalkulasi Otomatis:** Pemrosesan otomatis mencakup kalkulasi *Gap*, pemetaan bobot, perhitungan *Core Factor* & *Secondary Factor*, hingga perolehan *Final Ranking*.

---

## 📂 Struktur Direktori

```text
.
├── algo1.php                 # Skrip pengujian awal algoritma Profile Matching
├── algo2_perhitungan.php     # Skrip modul eksperimen kalkulasi
├── algo3.php                 # Skrip kalkulasi alternatif Profile Matching
├── auth.php                  # Middleware autentikasi & otorisasi hak akses
├── config.php                # File konfigurasi database MySQL
├── create_alternatif.php     # Form tambah data karyawan/alternatif
├── create_aspek.php          # Form tambah data aspek penilaian
├── create_kriteria.php       # Form tambah data kriteria penilaian
├── create_penilaian.php      # Form input nilai kriteria karyawan
├── data_alternatif.php       # Tabel dan pengelolaan data karyawan
├── data_aspek.php            # Tabel dan pengelolaan data aspek
├── data_kriteria.php         # Tabel dan pengelolaan data kriteria
├── data_pengguna.php         # Tabel dan pengelolaan data pengguna
├── delete_*.php              # Skrip modul penghapusan data
├── edit_*.php                # Skrip modul pembaruan data
├── index.php                 # Dashboard / Halaman utama
├── login.php                 # Halaman autentikasi masuk
├── logout.php                # Skrip terminasi sesi user
├── perhitungan.php           # Modul pemrosesan utama & tabel hasil perankingan
├── register.php              # Halaman pendaftaran pengguna baru
└── includes/                 # Komponen antarmuka (Header, Footer, Sidebar, Navbar)
```

---

## 📚 Landasan Teori: Metode Profile Matching

**Profile Matching** (Pencocokan Profil) adalah metode pengambilan keputusan yang berasumsi bahwa terdapat tingkat efektivitas ideal yang harus dipenuhi oleh setiap alternatif (karyawan), bukan sekadar tingkat kompetensi minimal yang harus dicapai.

Dalam prosesnya, metode ini membandingkan antara profil karyawan dengan profil jabatan/target yang dibutuhkan.

### Tahapan Perhitungan

#### 1. Perhitungan Selisih (Gap)
Menghitung selisih antara nilai kriteria yang dimiliki oleh alternatif ($N_{\text{kriteria}}$) dengan nilai target kriteria ($N_{\text{target}}$) yang ditetapkan:

$$\text{Gap} = N_{\text{target}} - N_{\text{kriteria}}$$

#### 2. Pembobotan Nilai Gap
Nilai *gap* yang diperoleh dikonversikan ke dalam bobot nilai standar sesuai dengan ketetapan berikut:

| Selisih (Gap) | Bobot Nilai | Keterangan |
| :---: | :---: | :--- |
| $0$ | $5.0$ | Tidak ada selisih (Kompetensi sesuai target) |
| $1$ | $4.5$ | Kompetensi individu kelebihan 1 tingkat |
| $-1$ | $4.0$ | Kompetensi individu kekurangan 1 tingkat |
| $2$ | $3.5$ | Kompetensi individu kelebihan 2 tingkat |
| $-2$ | $3.0$ | Kompetensi individu kekurangan 2 tingkat |
| $3$ | $2.5$ | Kompetensi individu kelebihan 3 tingkat |
| $-3$ | $2.0$ | Kompetensi individu kekurangan 3 tingkat |
| $4$ | $1.5$ | Kompetensi individu kelebihan 4 tingkat |
| $-4$ | $1.0$ | Kompetensi individu kekurangan 4 tingkat |

#### 3. Perhitungan Core Factor (CF) dan Secondary Factor (SF)
Kriteria penilaian dibagi menjadi dua kelompok:
- **Core Factor (CF):** Kriteria utama/vital yang paling menentukan performa.
- **Secondary Factor (SF):** Kriteria pendukung/pelengkap.

Rumus perhitungan nilai rata-rata tiap faktor per aspek:

$$N_{CF} = \frac{\sum N_{CF}}{\sum i_{CF}}$$

$$N_{SF} = \frac{\sum N_{SF}}{\sum i_{SF}}$$

*Keterangan:*
- $N_{CF}, N_{SF}$: Nilai rata-rata Core Factor dan Secondary Factor.
- $\sum N_{CF}, \sum N_{SF}$: Jumlah total bobot nilai Gap pada Core Factor dan Secondary Factor.
- $\sum i_{CF}, \sum i_{SF}$: Jumlah total item kriteria Core Factor dan Secondary Factor.

#### 4. Perhitungan Nilai Total Aspek
Nilai akhir tiap aspek dihitung dengan mengombinasikan proporsi *Core Factor* ($55\%$) dan *Secondary Factor* ($45\%$):

$$N_{\text{Aspek}} = (N_{CF} \times 55\%) + (N_{SF} \times 45\%)$$

#### 5. Perhitungan Nilai Akhir & Perankingan (Total Rank)
Nilai akhir seluruh alternatif diperoleh dari akumulasi perkalian nilai total aspek dengan bobot persentase dari masing-masing aspek ($W_{\text{Aspek}}$):

$$\text{Total Nilai} = \sum \left(N_{\text{Aspek}} \times W_{\text{Aspek}}\right)$$

Alternatif kemudian diurutkan (*ranking*) berdasarkan nilai tertinggi hingga terendah.

---

## 🚀 Panduan Instalasi

### Prasyarat System
- Web Server (XAMPP / Laragon / Apache / Nginx)
- PHP versi 7.4 atau lebih baru
- Database Server MySQL / MariaDB

### Langkah-Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/spk-profile-matching.git
   cd spk-profile-matching
   ```

2. **Pengaturan Database**
   - Buat database baru di MySQL (misalnya: `spk_profile_matching`).
   - *Import* skema file database (berformat `.sql`) ke dalam database baru tersebut.
   - Buka dan ubah konfigurasi koneksi pada berkas `config.php`:
     ```php
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "spk_profile_matching";
     ```

3. **Menjalankan Aplikasi**
   - Pindahkan folder proyek ke dalam direktori server web (`htdocs` pada XAMPP atau `www` pada Laragon).
   - Akses aplikasi melalui browser di alamat:
     ```text
     http://localhost/spk-profile-matching
     ```

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Bootstrap 4, FontAwesome
- **Interaktivitas:** JavaScript, jQuery, AJAX