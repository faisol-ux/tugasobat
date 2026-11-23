<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($koneksi, "
    SELECT 
        ro.*,
        p.tgl_periksa,
        p.keluhan,
        p.tensi_darah,
        pas.nama_pasien,
        pas.no_registrasi,
        dok.nama_dokter,
        o.nama_obat,
        o.kode_obat
    FROM resep_obat ro
    LEFT JOIN pemeriksaan p ON ro.id_periksa = p.id
    LEFT JOIN pasien pas ON p.id_pasien = pas.id
    LEFT JOIN dokter dok ON p.id_dokter = dok.id
    LEFT JOIN obat o ON ro.id_obat = o.id
    WHERE ro.id_resep_obat = $id
");
$data = mysqli_fetch_assoc($query);
if (!$data) {
    die("Data resep tidak ditemukan. <a href='index.php'>Kembali</a>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resep Obat #<?= $data['id_resep_obat'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f4f1ea; padding:30px; }
        .card { max-width:650px; margin:0 auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); border-top:5px solid #8b1c24; }
        .header { text-align:center; border-bottom:2px solid #8b1c24; padding-bottom:15px; margin-bottom:20px; }
        .header h2 { margin:0; color:#8b1c24; }
        .header p { margin:4px 0 0; color:#555; font-size:13px; }
        .row { display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; }
        .rx { font-size:32px; font-family:serif; font-weight:bold; color:#8b1c24; margin:15px 0 5px; }
        .table-obat { width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:25px; }
        .table-obat th, .table-obat td { border:1px solid #ddd; padding:10px; font-size:13px; }
        .table-obat th { background:#8b1c24; color:#fff; }
        .sign { text-align:right; margin-top:30px; }
        .btn-print { background:#8b1c24; color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-weight:bold; }
        @media print {
            body { background:#fff; padding:0; }
            .no-print { display:none; }
            .card { box-shadow:none; border:none; max-width:100%; }
        }
    </style>
</head>
<body>
    <div style="max-width:650px; margin:0 auto 15px; display:flex; justify-content:space-between;" class="no-print">
        <a href="index.php" style="color:#555; text-decoration:none; font-weight:bold;">&larr; Kembali</a>
        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Lembar Resep</button>
    </div>
    <div class="card">
        <div class="header">
            <h2>RUMAH SAKIT MEDIKA UTAMA</h2>
            <p>Jl. Pelayanan Sehat No. 100 &bull; Telp: (031) 8899-7766</p>
            <p style="font-weight:bold; color:#1e3a5f;">LEMBAR RESEP DOKTER</p>
        </div>
        <div class="row">
            <div><strong>No. Resep:</strong> #RSP-<?= $data['id_resep_obat'] ?></div>
            <div><strong>Tanggal:</strong> <?= htmlspecialchars($data['tgl_periksa'] ?? date('Y-m-d')) ?></div>
        </div>
        <div class="row">
            <div><strong>Dokter:</strong> <?= htmlspecialchars($data['nama_dokter'] ?? 'Dokter Jaga') ?></div>
            <div><strong>No. Pemeriksaan:</strong> #<?= $data['id_periksa'] ?></div>
        </div>
        <div class="row">
            <div><strong>Pasien:</strong> <?= htmlspecialchars($data['nama_pasien'] ?? 'Pasien Umum') ?> (<?= htmlspecialchars($data['no_registrasi'] ?? '-') ?>)</div>
        </div>
        <div class="rx">R/</div>
        <table class="table-obat">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Aturan Pakai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($data['kode_obat'] ?? '-') ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_obat'] ?? '-') ?></strong></td>
                    <td style="color:#8b1c24; font-weight:bold;"><?= htmlspecialchars($data['aturan_pakai'] ?? '3x1 Sehari Sesudah Makan') ?></td>
                </tr>
            </tbody>
        </table>
        <div class="sign">
            <p>Dokter Pemeriksa,</p>
            <br><br>
            <p><strong><?= htmlspecialchars($data['nama_dokter'] ?? 'dr. Dokter Jaga') ?></strong></p>
        </div>
    </div>
</body>
</html>
