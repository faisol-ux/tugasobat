<?php
include 'koneksi.php'; // Sesuaikan path jika file koneksi ada di luar folder

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM resep_obat WHERE id_resep_obat='$id'");
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
    <title>Edit Resep Obat - Rumah Sakit</title>
    
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
            max-width: 600px; /* Lebih kecil karena form edit hanya butuh 2 input */
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
            font-size: 24px;
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
            font-size: 22px;
        }

        /* === CARD STYLE === */
        .card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05), 0 2px 5px rgba(0,0,0,0.03);
            padding: 30px;
            margin-bottom: 35px;
            border-top: 5px solid var(--navy); 
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
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

        /* Warna Fokus diubah ke Marun */
        .form-control:focus {
            border-color: var(--maroon);
            background-color: var(--white);
            box-shadow: 0 0 0 4px rgba(139, 28, 36, 0.1);
        }

        .form-control:disabled {
            background-color: #e2e8f0;
            cursor: not-allowed;
            color: #a0aec0;
        }

        /* Grup Tombol */
        .action-btns {
            display: flex;
            gap: 15px;
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
            flex: 1; /* Tombol ambil sebagian besar ruang */
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

        .btn-batal {
            background-color: transparent;
            color: var(--text-gray);
            border: 1.5px solid var(--border);
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            flex: 0.5;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-batal:hover {
            background-color: #edf2f7;
            color: var(--text-dark);
            border-color: #cbd5e0;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- HEADER -->
        <div class="page-header">
            <i class="fas fa-edit"></i>
            <h2>Edit Resep Obat</h2>
        </div>

        <!-- FORM EDIT -->
        <div class="card">
            <form action="proses.php?aksi=update" method="POST">
                
                <!-- ID Resep Tersembunyi (Tidak diubah, hanya dikirim ke proses) -->
                <input type="hidden" name="id_resep_obat" value="<?php echo $data['id_resep_obat']; ?>">

                <div class="form-group">
                    <label>ID Resep (Tidak dapat diubah)</label>
                    <!-- Input disabled agar user tahu ID-nya, tapi tidak bisa diedit -->
                    <input type="number" class="form-control" value="<?php echo $data['id_resep_obat']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>ID Pemeriksaan</label>
                    <input type="number" name="id_periksa" class="form-control" value="<?php echo $data['id_periksa']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>ID Obat</label>
                    <input type="number" name="id_obat" class="form-control" value="<?php echo $data['id_obat']; ?>" required>
                </div>
                
                <div class="action-btns">
                    <button type="submit">
                        <i class="fas fa-save"></i> Update Resep
                    </button>
                    <a href="index.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </div>
        
    </div>

</body>
</html>