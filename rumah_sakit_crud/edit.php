<?php
include 'koneksi.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM dokter WHERE id_dokter='$id'");
$data = mysqli_fetch_array($query);

// Jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Dokter</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Tema Gelap (Sama persis dengan index.php) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        :root {
            --bg-body: #0b1120; --bg-card: #1e293b; --teal: #14b8a6; 
            --teal-hover: #0f766e; --text-light: #f8fafc; --text-muted: #94a3b8; 
            --border-color: #334155; --input-bg: #0f172a;
        }
        body { background-color: var(--bg-body); color: var(--text-light); padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .page-header { display: flex; align-items: center; margin-bottom: 25px; background: var(--bg-card); padding: 20px 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); border-left: 6px solid #f59e0b; }
        .page-header i { font-size: 24px; color: #ffffff; margin-right: 15px; background: linear-gradient(135deg, #f59e0b, #d97706); padding: 14px; border-radius: 12px; }
        .page-header h2 { color: var(--text-light); font-weight: 700; font-size: 22px; }

        .card { background: var(--bg-card); border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); padding: 35px; margin-bottom: 35px; border: 1px solid var(--border-color); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .form-group { display: flex; flex-direction: column; position: relative; }
        .form-group label { font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-light); text-transform: uppercase; }
        
        .form-control { padding: 14px 15px; border: 1.5px solid var(--border-color); border-radius: 10px; font-size: 14px; color: var(--text-light); background-color: var(--input-bg); outline: none; width: 100%; appearance: none; transition: all 0.3s ease; }
        .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15); }
        
        .select-wrapper { position: relative; }
        .select-wrapper::after { content: '\f107'; font-family: 'FontAwesome'; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
        
        .form-action { grid-column: 1 / -1; margin-top: 15px; display: flex; gap: 15px; }
        
        button[type="submit"] { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; padding: 16px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; width: 70%; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3); display: flex; justify-content: center; align-items: center; gap: 10px; transition: all 0.3s; }
        button[type="submit"]:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
        
        .btn-batal { background-color: transparent; color: var(--text-light); border: 1.5px solid var(--border-color); padding: 16px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; text-decoration: none; width: 30%; display: flex; justify-content: center; align-items: center; transition: all 0.3s; }
        .btn-batal:hover { background-color: var(--border-color); }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
            .form-action { flex-direction: column; }
            button[type="submit"], .btn-batal { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="page-header">
            <i class="fas fa-user-edit"></i>
            <div>
                <h2>Edit Data Dokter</h2>
            </div>
        </div>

        <div class="card">
            <form action="proses.php?aksi=update" method="POST">
                <!-- Kirim ID Tersembunyi -->
                <input type="hidden" name="id_dokter" value="<?php echo $data['id_dokter']; ?>">

                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" value="<?php echo $data['password']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap Dokter</label>
                        <input type="text" name="nama_dokter" class="form-control" value="<?php echo $data['nama_dokter']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?php echo $data['alamat']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" value="<?php echo $data['tgl_lahir']; ?>" required>
                    </div>

                    <!-- Dropdown Jenis Kelamin dengan Auto-Select -->
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <div class="select-wrapper">
                            <select name="jenis" class="form-control" required>
                                <option value="Laki-laki" <?php if($data['Jenis'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php if($data['Jenis'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Spesialis</label>
                        <input type="text" name="spesialis" class="form-control" value="<?php echo $data['Spesialis']; ?>" required>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit">
                            <i class="fas fa-save"></i> Update Data
                        </button>
                        <a href="index.php" class="btn-batal">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>