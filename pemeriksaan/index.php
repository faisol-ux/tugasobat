<?php
require_once 'admin_auth.php';
require_admin();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemeriksaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a; --bg-card: #1e293b; --teal: #d83bcb;
            --teal-hover: #ac3078; --text-light: #f8fafc; --text-muted: #94a3b8;
            --border-color: #334155; --input-bg: #0f172a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-light); padding: 40px 20px; }
        .container { max-width: 1100px; margin: 0 auto; }

        /* HEADER */
        .page-header { display: flex; align-items: center; margin-bottom: 25px; background: var(--bg-card); padding: 20px 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); border-left: 6px solid var(--teal); }
        .page-header i { font-size: 24px; color: #fff; margin-right: 15px; background: linear-gradient(135deg, var(--teal), var(--teal-hover)); padding: 14px; border-radius: 12px; }
        .page-header h2 { color: var(--text-light); font-size: 22px; }

        /* CARD */
        .card { background: var(--bg-card); border-radius: 16px; padding: 30px; margin-bottom: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 15px rgba(0,0,0,0.2); }
        .card h3 { margin-bottom: 20px; color: var(--teal); font-size: 18px; }

        /* FORM */
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); }
        .form-control { padding: 12px 15px; border: 1.5px solid var(--border-color); border-radius: 8px; color: var(--text-light); background-color: var(--input-bg); outline: none; transition: 0.3s; width: 100%; }
        .form-control:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2); }
        /* Perbaikan warna icon kalender */
        .form-control::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

        .form-action { grid-column: 1 / -1; margin-top: 10px; }
        button[type="submit"] { background: linear-gradient(135deg, var(--teal), var(--teal-hover)); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s; }
        button[type="submit"]:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(20, 184, 166, 0.3); }

        .header-link { margin-left: auto; padding: 10px 14px; border-radius: 8px; background: var(--teal); color: white; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; }
        .header-link:hover { background: var(--teal-hover); }
        .header-link.logout { margin-left: 10px; background: #475569; }
        .header-link.logout:hover { background: #334155; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .page-header h2 { font-size: 18px; }
            .header-link { padding: 9px 10px; font-size: 12px; }
            .header-link.logout { margin-left: 6px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <i class="fas fa-stethoscope"></i>
            <h2>Manajemen Data Pemeriksaan</h2>
            <a href="daftar.php" class="header-link"><i class="fas fa-list"></i> Daftar Pemeriksaan</a>
            <a href="logout.php" class="header-link logout"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </div>

        <!-- FORM TAMBAH -->
        <div class="card">
            <h3><i class="fas fa-plus-circle"></i> Tambah Data Pemeriksaan</h3>
            <form action="proses.php?aksi=tambah" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>ID Pasien</label>
                        <input type="number" name="id_pasien" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>ID Dokter</label>
                        <input type="number" name="id_dokter" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Periksa</label>
                        <input type="date" name="tgl_periksa" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keluhan</label>
                        <input type="text" name="keluhan" class="form-control" placeholder="Contoh: Demam, Batuk" required>
                    </div>
                    <div class="form-group">
                        <label>Tensi Darah</label>
                        <input type="text" name="tensi_darah" class="form-control" placeholder="Contoh: 120/80" required>
                    </div>
                    <div class="form-action">
                        <button type="submit"><i class="fas fa-save"></i> Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</body>
</html>