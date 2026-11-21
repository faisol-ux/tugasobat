# Sistem Informasi Rumah Sakit & Pengelolaan Resep Obat (SIM Rumah Sehat)

Aplikasi web manajemen rekam medis rumah sakit / klinik yang dibangun menggunakan **PHP Native** dan **MySQL**, dirancang khusus sesuai dengan skema **Entity Relationship Diagram (ERD)** yang ditentukan.

---

## 🗄️ Struktur Basis Data (ERD)

Aplikasi mengimplementasikan secara presisi 5 tabel relasional berikut:

1. **`pasien`** (Master Data Pasien)
   - `id` (int 5, AI, PK)
   - `no_registrasi` (varchar 255)
   - `nama_pasien` (varchar 255)
   - `alamat` (varchar 255)
   - `tgl_lahir` (date)

2. **`dokter`** (Master Data Tenaga Medis & Akun Login)
   - `id` (int 5, AI, PK)
   - `username` (varchar 255, Unique)
   - `password` (varchar 255)
   - `nama_dokter` (varchar 255)
   - `alamat` (varchar 255)
   - `tgl_lahir` (date)
   - `jenis` (varchar 255 - Jenis Kelamin)
   - `spesialis` (varchar 255 - Spesialisasi)

3. **`obat`** (Master Data Katalog Farmasi)
   - `id` (int 5, AI, PK)
   - `kode_obat` (varchar 255, Unique)
   - `nama_obat` (varchar 255)

4. **`pemeriksaan`** (Transaksi Rekam Medis)
   - `id` (int 5, AI, PK)
   - `id_pasien` (int 5, FK -> `pasien.id`)
   - `id_dokter` (int 5, FK -> `dokter.id`)
   - `tgl_periksa` (date)
   - `keluhan` (varchar 255)
   - `tensi_darah` (varchar 255)

5. **`pemeriksaan_obat`** (Junction Table Resep Obat)
   - `id` (int 5, AI, PK)
   - `id_pemeriksaan` (int 5, FK -> `pemeriksaan.id`)
   - `id_id_obat` (int 5, FK -> `obat.id`) *(Sesuai persis dengan penamaan kolom pada diagram ERD)*

---

## 🚀 Cara Menjalankan di XAMPP

1. **Pastikan Apache & MySQL Berjalan:**
   - Buka **XAMPP Control Panel**.
   - Klik tombol **Start** pada modul **Apache** dan **MySQL**.

2. **Buka Aplikasi di Browser:**
   - Jalankan browser dan akses:
     ```
     http://localhost/tugas_obat/
     ```
   - Skrip koneksi (`koneksi.php`) sudah dilengkapi fitur **Auto-Installer**. Jika database `db_tugas_obat` belum ada, sistem akan otomatis membuatnya beserta seluruh tabel dan data sampelnya.
   - Atau Anda juga bisa mengimpor file `database.sql` secara manual melalui **phpMyAdmin** (`http://localhost/phpmyadmin`).

3. **Login ke Sistem:**
   Gunakan akun demo dokter yang sudah tersedia:
   | Username | Password | Nama Dokter | Spesialisasi |
   | :--- | :--- | :--- | :--- |
   | `dr_rahman` | `123` | dr. Rahman Putra, Sp.PD | Penyakit Dalam |
   | `dr_melati` | `123` | dr. Melati Kusuma, Sp.A | Anak |
   | `dr_budi` | `123` | dr. Budi Santoso | Dokter Umum |
   | `dr_anisa` | `123` | dr. Anisa Fitri, Sp.JP | Jantung & Pembuluh Darah |

---

## ✨ Fitur-Fitur Utama

- **Autentikasi Dokter:** Login dengan akun dokter terdaftar, manajemen sesi, proteksi halaman, serta tombol autofill login demo.
- **Dashboard Statistik:** Ringkasan total pasien, dokter, obat, dan pemeriksaan terkini beserta statistik obat yang paling sering diresepkan.
- **CRUD Pasien:** Tambah pasien dengan auto-generate nomor registrasi, pencarian instan, edit data, dan hapus dengan dialog konfirmasi SweetAlert2.
- **CRUD Dokter:** Kelola profil dokter, spesialisasi, username, dan keamanan kata sandi.
- **CRUD Obat:** Manajemen katalog obat farmasi dan monitoring frekuensi peresepan.
- **Rekam Pemeriksaan & Multi Resep Obat:**
  - Pilih pasien dan dokter pemeriksa.
  - Catat tanggal periksa, tensi darah (dengan shortcut preset), dan keluhan pasien.
  - Memilih **banyak obat sekaligus** (multi-resep) via checklist interaktif yang otomatis tersimpan ke tabel relasi `pemeriksaan_obat`.
- **Cetak Lembar Resep & Resume Medis:** Tampilan khusus cetak (print/PDF) lengkap dengan kop surat resmi rumah sakit dan lembar resep dokter (R/).
- **Log Resep (`pemeriksaan_obat`):** Modul rekap peresepan untuk memfilter resep per obat atau per pasien.
- **Visualisasi ERD Interaktif:** Halaman referensi visual ERD Mermaid dan struktur kolom tabel langsung di dalam aplikasi.

---

## 📂 Struktur Berkas

```
c:\xampp\htdocs\tugas_obat\
├── database.sql             # Skrip SQL skema tabel & data seed
├── koneksi.php              # Pengaturan koneksi MySQL & auto-setup
├── auth.php                 # Middleware session autentikasi
├── login.php                # Halaman login dokter
├── logout.php               # Proses logout
├── header.php               # Layout navigasi & topbar
├── sidebar.php              # Sidebar menu navigasi
├── footer.php               # Layout footer & script dialog
├── index.php                # Halaman dashboard utama
├── pasien.php               # Halaman kelola pasien
├── pasien_action.php        # Controller aksi pasien (tambah, edit, hapus)
├── dokter.php               # Halaman kelola dokter
├── dokter_action.php        # Controller aksi dokter (tambah, edit, hapus)
├── obat.php                 # Halaman kelola obat
├── obat_action.php          # Controller aksi obat (tambah, edit, hapus)
├── pemeriksaan.php          # Halaman daftar rekam pemeriksaan
├── pemeriksaan_tambah.php   # Form tambah rekam medis & resep obat
├── pemeriksaan_edit.php     # Form edit rekam medis & resep obat
├── pemeriksaan_detail.php   # Halaman detail rekam medis pasien
├── pemeriksaan_cetak.php    # Format cetak lembar resep & resume medis
├── pemeriksaan_action.php   # Controller aksi pemeriksaan & pemeriksaan_obat
├── resep.php                # Halaman log tabel pemeriksaan_obat
├── resep_action.php         # Controller aksi resep langsung
├── erd.php                  # Halaman visualisasi diagram ERD
└── README.md                # Dokumentasi petunjuk penggunaan
```

