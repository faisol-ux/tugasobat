<?php
include 'koneksi.php';

// Proses Hapus Data
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM dokter WHERE id_dokter='$id'");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Rumah Sakit - Data Dokter</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === RESET & BASE STYLE === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        :root {
            /* Palet Warna Tema Gelap */
            --bg-body: #0b1120;      /* Latar belakang halaman paling gelap */
            --bg-card: #1e293b;      /* Latar belakang form/tabel (Biru Gelap) */
            --teal: #14b8a6;         /* Hijau Tosca Aksen */
            --teal-hover: #0f766e;   /* Tosca Gelap */
            --text-light: #f8fafc;   /* Teks Putih Terang */
            --text-muted: #94a3b8;   /* Teks Abu-abu */
            --border-color: #334155; /* Garis batas antar elemen */
            --input-bg: #0f172a;     /* Warna input lebih gelap dari card */
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-light);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* === HEADER STYLE === */
        .page-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            background: var(--bg-card); /* Warna Gelap */
            padding: 20px 25px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3);
            border-left: 6px solid var(--teal); 
        }

        .page-header i {
            font-size: 24px;
            color: #ffffff;
            margin-right: 15px;
            background: linear-gradient(135deg, var(--teal), var(--teal-hover));
            padding: 14px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(20, 184, 166, 0.3);
        }

        .page-header h2 {
            color: var(--text-light); /* Teks Putih */
            font-weight: 700;
            font-size: 22px;
        }

        .page-header p {
            font-size: 13px;
            color: var(--text-muted); /* Teks Abu-abu */
            margin-top: 4px;
        }

        /* === CARD STYLE (Form & Tabel) === */
        .card {
            background: var(--bg-card); /* Warna Gelap menggantikan Putih */
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            padding: 35px;
            margin-bottom: 35px;
            border: 1px solid var(--border-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-light); /* Label Putih */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Styling untuk Input dan Select */
        .form-control {
            padding: 14px 15px;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-light);
            background-color: var(--input-bg); /* Background Input Gelap */
            transition: all 0.3s ease;
            outline: none;
            width: 100%;
            appearance: none;
        }

        /* Ikon panah custom untuk Select */
        .select-wrapper {
            position: relative;
        }
        
        .select-wrapper::after {
            content: '\f107';
            font-family: 'FontAwesome';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        /* Warna Fokus Input */
        .form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.15);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        /* Memperbaiki warna ikon kalender di input date agar terlihat di background gelap */
        .form-control::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        /* Tombol Simpan */
        .form-action {
            grid-column: 1 / -1;
            margin-top: 15px;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, var(--teal), var(--teal-hover));
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }

        /* === TABLE STYLE === */
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
        }

        /* Header Tabel */
        th {
            background-color: var(--bg-body); /* Warna paling gelap */
            color: var(--text-light);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:not(:last-child) td {
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.03); /* Efek hover halus */
        }

        td {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Teks Nama & ID di Tabel dibuat lebih terang */
        .td-id { color: var(--text-light); font-weight: bold; }
        .td-nama { color: var(--text-light); font-weight: 600; }

        /* Badge Jenis Kelamin */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-laki { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .badge-perempuan { background: rgba(20, 184, 166, 0.15); color: var(--teal); }

        /* === BUTTON AKSI (Edit/Hapus) === */
        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-hapus {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .btn-edit:hover { background-color: rgba(245, 158, 11, 0.3); }

        .btn-hapus {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        .btn-hapus:hover { background-color: rgba(239, 68, 68, 0.3); }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- HEADER -->
        <div class="page-header">
            <i class="fas fa-user-md"></i>
            <div>
                <h2>Manajemen Data Dokter</h2>
                <p>Tambahkan dan kelola data dokter rumah sakit dengan mudah.</p>
            </div>
        </div>

        <!-- BAGIAN FORM INPUT -->
        <div class="card">
            <form action="proses.php?aksi=tambah" method="POST">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Buat username..." required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Buat password..." required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap Dokter</label>
                        <input type="text" name="nama_dokter" class="form-control" placeholder="Contoh: dr. Budi Santoso" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap..." required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" required>
                    </div>

                    <!-- JENIS KELAMIN DROPDOWN -->
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <div class="select-wrapper">
                            <select name="jenis" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Spesialis</label>
                        <input type="text" name="spesialis" class="form-control" placeholder="Contoh: Spesialis Anak, Spesialis Bedah..." required>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit">
                            <i class="fas fa-save"></i> Simpan Data Dokter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- BAGIAN TABEL DATA -->
        <div class="card">
            <h3 style="margin-bottom: 20px; font-size: 18px; color: var(--text-light);">Daftar Dokter Terdaftar</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Dokter</th>
                            <th>Alamat</th>
                            <th>Tgl Lahir</th>
                            <th>Jenis Kelamin</th>
                            <th>Spesialis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY id_dokter DESC");
                        
                        if(mysqli_num_rows($query) == 0){
                            echo "<tr><td colspan='7' style='text-align:center; padding: 30px; color:var(--text-muted);'><i class='fas fa-folder-open' style='font-size:24px;margin-bottom:10px;display:block;'></i> Belum ada data dokter.</td></tr>";
                        }

                        while ($data = mysqli_fetch_array($query)) {
                            // Tentukan warna badge berdasarkan jenis kelamin
                            $badgeClass = ($data['Jenis'] == 'Laki-laki') ? 'badge-laki' : 'badge-perempuan';
                        ?>
                        <tr>
                            <td class="td-id">#<?php echo $data['id_dokter']; ?></td>
                            <td>
                                <div class="td-nama"><?php echo $data['nama_dokter']; ?></div>
                                <div style="font-size: 12px; color:var(--text-muted); margin-top: 2px;">@<?php echo $data['username']; ?></div>
                            </td>
                            <td><?php echo $data['alamat']; ?></td>
                            <td><?php echo date('d M Y', strtotime($data['tgl_lahir'])); ?></td>
                            <td>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $data['Jenis']; ?>
                                </span>
                            </td>
                            <td><?php echo $data['Spesialis']; ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $data['id_dokter']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?hapus=<?php echo $data['id_dokter']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus data dokter ini?')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

</body>
</html>