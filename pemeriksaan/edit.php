<?php
require_once 'admin_auth.php';
require_admin();
include 'koneksi.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM Pemeriksaan WHERE id_periksa='$id'");
$data = mysqli_fetch_array($query);

if (!$data) { echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>"; exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pemeriksaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-dark: #0f172a; --bg-card: #1e293b; --teal: #dd329b; --text-light: #f8fafc; --border: #334155; --input: #0f172a; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-dark); color: var(--text-light); padding: 40px 20px; }
        .container { max-width: 700px; margin: 0 auto; }
        .page-header { display: flex; align-items: center; margin-bottom: 25px; background: var(--bg-card); padding: 20px; border-radius: 16px; border-left: 6px solid #f59e0b; }
        .page-header i { font-size: 24px; color: #fff; margin-right: 15px; background: #f59e0b; padding: 12px; border-radius: 12px; }
        .card { background: var(--bg-card); border-radius: 16px; padding: 30px; border: 1px solid var(--border); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; margin-bottom: 8px; color: #94a3b8; }
        .form-control { width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 8px; background: var(--input); color: #fff; outline: none; }
        .form-control:focus { border-color: #f59e0b; }
        .btn-group { display: flex; gap: 15px; margin-top: 30px; }
        button { flex: 1; padding: 14px; border: none; border-radius: 8px; background: #f59e0b; color: #fff; font-weight: bold; cursor: pointer; }
        .btn-batal { flex: 0.5; padding: 14px; border-radius: 8px; border: 1.5px solid var(--border); text-align: center; text-decoration: none; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <i class="fas fa-edit"></i>
            <h2>Edit Data Pemeriksaan</h2>
        </div>
        <div class="card">
            <form action="proses.php?aksi=update" method="POST">
                <input type="hidden" name="id_periksa" value="<?php echo $data['id_periksa']; ?>">
                
                <div class="form-group">
                    <label>ID Pasien</label>
                    <input type="number" name="id_pasien" class="form-control" value="<?php echo $data['id_pasien']; ?>" required>
                </div>
                <div class="form-group">
                    <label>ID Dokter</label>
                    <input type="number" name="id_dokter" class="form-control" value="<?php echo $data['id_dokter']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Periksa</label>
                    <input type="date" name="tgl_periksa" class="form-control" value="<?php echo $data['tgl_periksa']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Keluhan</label>
                    <input type="text" name="keluhan" class="form-control" value="<?php echo $data['keluhan']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Tensi Darah</label>
                    <input type="text" name="tensi_darah" class="form-control" value="<?php echo $data['tensi_darah']; ?>" required>
                </div>

                <div class="btn-group">
                    <button type="submit"><i class="fas fa-save"></i> Update Data</button>
                    <a href="index.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>