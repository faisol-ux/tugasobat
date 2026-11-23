<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$id = (int)($_GET['id'] ?? 0);

$query = "
    SELECT 
        p.*,
        pas.nama_pasien,
        pas.no_registrasi,
        pas.alamat AS alamat_pasien,
        pas.tgl_lahir AS tgl_lahir_pasien,
        dok.nama_dokter,
        dok.spesialis,
        dok.alamat AS alamat_dokter
    FROM pemeriksaan p
    JOIN pasien pas ON p.id_pasien = pas.id
    JOIN dokter dok ON p.id_dokter = dok.id
    WHERE p.id = $id
    LIMIT 1
";
$res = mysqli_query($koneksi, $query);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Data pemeriksaan tidak ditemukan.");
}

$data = mysqli_fetch_assoc($res);

// Query resep obat
$query_obat = "
    SELECT 
        o.kode_obat,
        o.nama_obat,
        po.aturan_pakai
    FROM pemeriksaan_obat po
    JOIN obat o ON po.id_id_obat = o.id
    WHERE po.id_pemeriksaan = $id
    ORDER BY o.nama_obat ASC
";
$res_obat = mysqli_query($koneksi, $query_obat);

$tgl_lahir = new DateTime($data['tgl_lahir_pasien']);
$today = new DateTime($data['tgl_periksa']);
$usia = $tgl_lahir->diff($today)->y;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep & Rekam Medis #<?= $data['id'] ?> - <?= htmlspecialchars($data['nama_pasien']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-6 text-slate-800">

    <!-- Print Control Bar -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="pemeriksaan_detail.php?id=<?= $id ?>" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-600/30">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- Printable Paper Card -->
    <div class="print-card max-w-3xl mx-auto bg-white p-10 rounded-2xl shadow-xl border border-slate-200">
        
        <!-- KOP SURAT RUMAH SAKIT -->
        <div class="border-b-2 border-slate-900 pb-5 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-teal-600 text-white flex items-center justify-center text-3xl font-bold">
                    +
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900 uppercase">RUMAH SAKIT MEDIKA UTAMA</h1>
                    <p class="text-xs text-slate-600">Jl. Pelayanan Sehat No. 100 &bull; Telp: (031) 8899-7766 &bull; Surabaya</p>
                    <p class="text-[11px] text-teal-700 font-semibold">Instalasi Rawat Jalan & Apotek Farmasi Terpadu</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[11px] font-mono font-bold text-slate-500 uppercase">No. Pemeriksaan</div>
                <div class="text-lg font-mono font-bold text-slate-900">MED-<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <!-- JUDUL LEMBAR -->
        <div class="text-center mb-6">
            <h2 class="text-base font-extrabold tracking-wider uppercase text-slate-900 underline">SURAT RESEP & RESUME MEDIS</h2>
            <p class="text-xs text-slate-500">Tanggal Periksa: <?= tgl_indo($data['tgl_periksa']) ?></p>
        </div>

        <!-- DATA PASIEN & DOKTER -->
        <div class="grid grid-cols-2 gap-6 p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 text-xs">
            <div>
                <div class="font-bold text-slate-500 uppercase text-[10px] tracking-wider mb-1">Identitas Pasien</div>
                <table class="w-full">
                    <tr><td class="w-24 text-slate-500 py-0.5">No. Registrasi</td><td class="font-mono font-bold">: <?= htmlspecialchars($data['no_registrasi']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">Nama Pasien</td><td class="font-bold text-slate-900">: <?= htmlspecialchars($data['nama_pasien']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">Usia</td><td>: <?= $usia ?> Tahun</td></tr>
                    <tr><td class="text-slate-500 py-0.5">Alamat</td><td>: <?= htmlspecialchars($data['alamat_pasien']) ?></td></tr>
                </table>
            </div>

            <div>
                <div class="font-bold text-slate-500 uppercase text-[10px] tracking-wider mb-1">Dokter Pemeriksa</div>
                <table class="w-full">
                    <tr><td class="w-24 text-slate-500 py-0.5">Nama Dokter</td><td class="font-bold text-slate-900">: <?= htmlspecialchars($data['nama_dokter']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">Spesialisasi</td><td>: <?= htmlspecialchars($data['spesialis']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">Tensi Darah</td><td class="font-bold text-rose-700">: <?= htmlspecialchars($data['tensi_darah']) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- DIAGNOSA / KELUHAN -->
        <div class="mb-6 text-xs">
            <div class="font-bold text-slate-800 uppercase text-[11px] mb-1">Anamnesa / Keluhan Pasien:</div>
            <div class="p-3 bg-white rounded-lg border border-slate-300 text-slate-700 leading-relaxed">
                <?= nl2br(htmlspecialchars($data['keluhan'])) ?>
            </div>
        </div>

        <!-- RESEP OBAT (R/) -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl font-serif italic font-bold text-slate-900">R/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">(Resep Obat yang Diberikan)</span>
            </div>

            <table class="w-full text-xs text-left border border-slate-300 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                        <th class="py-2.5 px-3">Kode Obat</th>
                        <th class="py-2.5 px-3">Nama Obat & Sediaan</th>
                        <th class="py-2.5 px-3 text-right">Aturan Pakai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (mysqli_num_rows($res_obat) > 0): 
                        $no = 1;
                        while ($ob = mysqli_fetch_assoc($res_obat)): 
                    ?>
                        <tr>
                            <td class="py-2.5 px-3 text-center"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 font-mono font-bold"><?= htmlspecialchars($ob['kode_obat']) ?></td>
                            <td class="py-2.5 px-3 font-bold text-slate-900"><?= htmlspecialchars($ob['nama_obat']) ?></td>
                            <td class="py-2.5 px-3 text-right font-medium text-slate-800"><?= htmlspecialchars(!empty($ob['aturan_pakai']) ? $ob['aturan_pakai'] : '3x1 Sehari Sesudah Makan') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-400 italic">Tidak ada resep obat tertulis.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TANDA TANGAN -->
        <div class="grid grid-cols-2 text-xs pt-6 border-t border-slate-200">
            <div class="text-slate-500">
                <p>Catatan:</p>
                <ul class="list-disc list-inside text-[11px] mt-1 space-y-0.5">
                    <li>Bawalah lembar ini ke bagian farmasi / apotek.</li>
                    <li>Simpan obat di tempat sejuk dan kering.</li>
                </ul>
            </div>
            <div class="text-center ml-auto w-48">
                <p class="text-slate-600">Surabaya, <?= tgl_indo($data['tgl_periksa']) ?></p>
                <p class="font-semibold text-slate-700 mt-1">Dokter Pemeriksa,</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline"><?= htmlspecialchars($data['nama_dokter']) ?></p>
                <p class="text-[10px] text-slate-500">SIP: <?= rand(100000, 999999) ?>/IDI/<?= date('Y') ?></p>
            </div>
        </div>

    </div>

</body>
</html>

