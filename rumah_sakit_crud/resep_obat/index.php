<?php
include '../koneksi.php'; // Sesuaikan path jika file koneksi ada di luar folder

// Proses Hapus Data
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM resep_obat WHERE id_resep_obat='$id'");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Resep Obat - Rumah Sakit</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === PALET WARNA BERDASARKAN LOGO === */
        :root {
            --bg-paper: #f4f1ea;      /* Warna krem seperti kertas */
            --maroon: #8b1c24;        /* Merah Marun Logo */
            --maroon-hover: #6a131a;  /* Marun Gelap untuk Hover */
            --navy: #1e3a5f;          /* Biru Dongker Logo */
            --white: #ffffff;
            --text-dark: #2d3748;
            --text-gray: #718096;
            --border: #e2e8f0;
            --input-bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-paper);
            /* Efek tekstur kertas halus */
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100' height='100' filter='url(%23noise)' opacity='0.05'/%3E%3C/svg%3E");
            color: var(--text-dark);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* === HEADER === */
        .page-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 3px solid var(--maroon);
            padding-bottom: 15px;
        }

        .page-header i {
            font-size: 28px;
            color: var(--white);
            background-color: var(--maroon);
            padding: 12px;
            border-radius: 50%;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(139, 28, 36, 0.3);
        }

        .page-header h2 {
            color: var(--maroon);
            font-weight: 700;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        /* === CARD STYLE (Kotak Putih) === */
        .card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05), 0 2px 5px rgba(0,0,0,0.03);
            padding: 30px;
            margin-bottom: 35px;
            border-top: 5px solid var(--navy); /* Aksen garis biru di atas */
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .form-control {
            padding: 12px 15px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-dark);
            background-color: var(--input-bg);
            transition: all 0.3s ease;
            outline: none;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--maroon);
            background-color: var(--white);
            box-shadow: 0 0 0 4px rgba(139, 28, 36, 0.1);
        }

        /* Tombol Simpan */
        .form-action {
            grid-column: 1 / -1;
            margin-top: 10px;
        }

        button[type="submit"] {
            background-color: var(--maroon);
            color: var(--white);
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(139, 28, 36, 0.2);
        }

        button[type="submit"]:hover {
            background-color: var(--maroon-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(139, 28, 36, 0.3);
        }

        /* === TABLE STYLE === */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
        }

        th, td {
            padding: 16px;
            text-align: center; /* Teks di tengah agar rapi */
            font-size: 14px;
        }

        /* Header tabel warna Marun */
        th {
            background-color: var(--maroon);
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }

        tr:not(:last-child) td {
            border-bottom: 1px solid var(--border);
        }

        tr:hover td {
            background-color: #fdfbf7;
        }

        td {
            color: var(--text-gray);
            font-weight: 500;
        }

        /* Angka ID dibuat menonjol */
        .td-id {
            color: var(--navy);
            font-weight: 700;
        }

        /* === BUTTON AKSI === */
        .action-btns {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-edit, .btn-hapus {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background-color: #fffaf0;
            color: #dd6b20; /* Warna oranye keemasan */
            border: 1px solid #fbd38d;
        }
        .btn-edit:hover { background-color: #feebc8; }

        .btn-hapus {
            background-color: #fff5f5;
            color: #e53e3e; /* Warna merah */
            border: 1px solid #feb2b2;
        }
        .btn-hapus:hover { background-color: #fed7d7; }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            th, td { padding: 12px; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- HEADER -->
        <div class="page-header">
            <i class="fas fa-prescription-bottle-medical"></i>
            <h2>Manajemen Resep Obat</h2>
        </div>

        <!-- BAGIAN FORM TAMBAH DATA -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-plus-circle"></i> Tambah Resep Baru
            </div>
            <form action="proses.php?aksi=tambah" method="POST">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>ID Pemeriksaan</label>
                        <input type="number" name="id_periksa" class="form-control" placeholder="Masukkan ID Pemeriksaan..." required>
                    </div>
                    
                    <div class="form-group">
                        <label>ID Obat</label>
                        <input type="number" name="id_obat" class="form-control" placeholder="Masukkan ID Obat..." required>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit">
                            <i class="fas fa-save"></i> Simpan Resep
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- BAGIAN TABEL DATA -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-list-alt"></i> Daftar Resep Obat
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Resep</th>
                            <th>ID Pemeriksaan</th>
                            <th>ID Obat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Pastikan query benar (cek nama tabel di database Anda)
                        $query = mysqli_query($koneksi, "SELECT * FROM resep_obat ORDER BY id_resep_obat DESC");
                        
                        if(!$query) {
                            echo "<tr><td colspan='4'>Query Error: " . mysqli_error($koneksi) . "</td></tr>";
                        } else if(mysqli_num_rows($query) == 0){
                            echo "<tr><td colspan='4' style='padding: 30px; color:var(--text-gray);'><i class='fas fa-folder-open' style='font-size:24px;margin-bottom:10px;display:block;color:#cbd5e0;'></i> Belum ada data resep obat.</td></tr>";
                        }

                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td class="td-id">#<?php echo $data['id_resep_obat']; ?></td>
                            <td><?php echo $data['id_periksa']; ?></td>
                            <td><?php echo $data['id_obat']; ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $data['id_resep_obat']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?hapus=<?php echo $data['id_resep_obat']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus resep ini?')">
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