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
    <title>Daftar Pemeriksaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a; --bg-card: #1e293b; --teal: #d83bcb;
            --teal-hover: #ac3078; --text-light: #f8fafc; --text-muted: #94a3b8;
            --border-color: #334155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-light); padding: 40px 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .page-header { display: flex; align-items: center; margin-bottom: 25px; background: var(--bg-card); padding: 20px 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); border-left: 6px solid var(--teal); }
        .page-header i { font-size: 24px; color: #fff; margin-right: 15px; background: linear-gradient(135deg, var(--teal), var(--teal-hover)); padding: 14px; border-radius: 12px; }
        .page-header h2 { color: var(--text-light); font-size: 22px; }
        .header-link { margin-left: auto; padding: 10px 14px; border-radius: 8px; background: var(--teal); color: white; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; }
        .header-link:hover { background: var(--teal-hover); }
        .header-link.logout { margin-left: 10px; background: #475569; }
        .header-link.logout:hover { background: #334155; }
        .card { background: var(--bg-card); border-radius: 16px; padding: 30px; border: 1px solid var(--border-color); box-shadow: 0 10px 15px rgba(0,0,0,0.2); }
        .card h3 { margin-bottom: 20px; color: var(--teal); font-size: 18px; }
        .table-responsive { overflow-x: auto; border-radius: 8px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px 20px; text-align: left; font-size: 14px; }
        th { background-color: var(--bg-dark); color: var(--text-light); font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr:not(:last-child) td { border-bottom: 1px solid var(--border-color); }
        td { color: var(--text-muted); }
        tr:hover td { background-color: rgba(255,255,255,0.02); }
        .action-btns { display: flex; gap: 8px; }
        .btn-edit, .btn-hapus { text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .btn-edit { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .btn-edit:hover { background: rgba(245, 158, 11, 0.3); }
        .btn-hapus { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
        .btn-hapus:hover { background: rgba(239, 68, 68, 0.3); }
        @media (max-width: 768px) {
            body { padding: 20px 12px; }
            .page-header { padding: 16px; }
            .page-header h2 { font-size: 18px; }
            .page-header > i { margin-right: 10px; }
            .header-link { padding: 9px 10px; font-size: 12px; }
            .header-link.logout { margin-left: 6px; }
            .card { padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <i class="fas fa-stethoscope"></i>
            <h2>Daftar Pemeriksaan</h2>
            <a href="index.php" class="header-link"><i class="fas fa-plus"></i> Tambah Pemeriksaan</a>
            <a href="logout.php" class="header-link logout"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </div>

        <div class="card">
            <h3><i class="fas fa-list-alt"></i> Data Pemeriksaan</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Periksa</th>
                            <th>ID Pasien</th>
                            <th>ID Dokter</th>
                            <th>Tgl Periksa</th>
                            <th>Keluhan</th>
                            <th>Tensi Darah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM pemeriksaan ORDER BY id_periksa DESC");

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pemeriksaan.</td></tr>";
                        }

                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><b>#<?php echo $data['id_periksa']; ?></b></td>
                            <td><?php echo $data['id_pasien']; ?></td>
                            <td><?php echo $data['id_dokter']; ?></td>
                            <td><?php echo date('d M Y', strtotime($data['tgl_periksa'])); ?></td>
                            <td><?php echo $data['keluhan']; ?></td>
                            <td><?php echo $data['tensi_darah']; ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $data['id_periksa']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete.php?id=<?php echo $data['id_periksa']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
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