<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$mode = $_GET['mode'] ?? 'single';
$id   = (int)($_GET['id'] ?? 0);
$id_periksa = (int)($_GET['id_periksa'] ?? 0);

// ========================================================
// MODE 1: REKAPITULASI / LAPORAN SEMUA RESEP OBAT
// ========================================================
if ($mode === 'rekap') {
    $filter_obat   = (int)($_GET['filter_obat'] ?? 0);
    $filter_pasien = (int)($_GET['filter_pasien'] ?? 0);

    $where_clauses = [];
    if ($filter_obat > 0) {
        $where_clauses[] = "po.id_id_obat = $filter_obat";
    }
    if ($filter_pasien > 0) {
        $where_clauses[] = "p.id_pasien = $filter_pasien";
    }

    $where_sql = "";
    if (count($where_clauses) > 0) {
        $where_sql = "WHERE " . implode(" AND ", $where_clauses);
    }

    $query_rekap = "
        SELECT 
            po.id AS id_pemeriksaan_obat,
            po.id_pemeriksaan,
            po.aturan_pakai,
            o.kode_obat,
            o.nama_obat,
            p.tgl_periksa,
            pas.nama_pasien,
            pas.no_registrasi,
            dok.nama_dokter
        FROM pemeriksaan_obat po
        JOIN pemeriksaan p ON po.id_pemeriksaan = p.id
        JOIN obat o ON po.id_id_obat = o.id
        JOIN pasien pas ON p.id_pasien = pas.id
        JOIN dokter dok ON p.id_dokter = dok.id
        $where_sql
        ORDER BY po.id DESC
    ";
    $result_rekap = mysqli_query($koneksi, $query_rekap);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Resep Obat - SIMRS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-6 text-slate-800">
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="resep.php" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700">
            &larr; Kembali ke Daftar Resep
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-600/30 flex items-center gap-2">
            <span>🖨️</span> Cetak / Simpan PDF
        </button>
    </div>

    <div class="print-card max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
        <!-- KOP LAPORAN -->
        <div class="border-b-2 border-slate-900 pb-4 mb-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-600 text-white flex items-center justify-center text-2xl font-black">
                    +
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">RUMAH SAKIT MEDIKA UTAMA</h1>
                    <p class="text-xs text-slate-600">Instalasi Farmasi & Pelayanan Resep Obat &bull; Telp: (031) 8899-7766</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200">
                    LAPORAN RESEP OBAT
                </span>
                <p class="text-[11px] text-slate-500 mt-1">Dicetak: <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">REKAPITULASI PERESEPAN OBAT KLINIS</h2>
            <p class="text-xs text-slate-500">Total <?= mysqli_num_rows($result_rekap) ?> resep obat tercatat</p>
        </div>

        <table class="w-full text-xs text-left border border-slate-300 rounded-lg overflow-hidden mb-6">
            <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 uppercase text-[10px] font-bold">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">No</th>
                    <th class="py-2.5 px-3">Tgl Periksa</th>
                    <th class="py-2.5 px-3">Pasien</th>
                    <th class="py-2.5 px-3">Dokter Peresep</th>
                    <th class="py-2.5 px-3">Obat Diberikan</th>
                    <th class="py-2.5 px-3">Aturan Pakai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (mysqli_num_rows($result_rekap) > 0): 
                    $no = 1;
                    while ($r = mysqli_fetch_assoc($result_rekap)):
                ?>
                    <tr>
                        <td class="py-2.5 px-3 text-center"><?= $no++ ?></td>
                        <td class="py-2.5 px-3 font-mono"><?= tgl_indo($r['tgl_periksa']) ?></td>
                        <td class="py-2.5 px-3 font-bold text-slate-900">
                            <?= htmlspecialchars($r['nama_pasien']) ?>
                            <div class="text-[10px] text-slate-500 font-normal font-mono"><?= htmlspecialchars($r['no_registrasi']) ?></div>
                        </td>
                        <td class="py-2.5 px-3 text-slate-700"><?= htmlspecialchars($r['nama_dokter']) ?></td>
                        <td class="py-2.5 px-3">
                            <span class="font-bold text-slate-900"><?= htmlspecialchars($r['nama_obat']) ?></span>
                            <span class="text-[10px] text-amber-700 font-mono ml-1">(<?= htmlspecialchars($r['kode_obat']) ?>)</span>
                        </td>
                        <td class="py-2.5 px-3 font-medium text-slate-800"><?= htmlspecialchars($r['aturan_pakai'] ?: '3x1 Sehari Sesudah Makan') ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="py-6 text-center text-slate-400">Belum ada data resep obat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TANDA TANGAN LAPORAN -->
        <div class="grid grid-cols-2 text-xs pt-6 border-t border-slate-200">
            <div class="text-slate-500 text-[11px]">
                Dokumen ini merupakan salinan rekapitulasi data resep resmi yang terdaftar pada sistem SIMRS.
            </div>
            <div class="text-center ml-auto w-48">
                <p class="text-slate-600">Surabaya, <?= tgl_indo(date('Y-m-d')) ?></p>
                <p class="font-semibold text-slate-700 mt-1">Kepala Instalasi Farmasi,</p>
                <div class="h-14"></div>
                <p class="font-bold text-slate-900 underline">Apt. Farmasis Utama, S.Farm</p>
                <p class="text-[10px] text-slate-500">SIPA: 19850212/STRA/<?= date('Y') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// ========================================================
// MODE 2: CETAK LEMBAR RESEP DOKTER (SINGLE / PER PEMERIKSAAN)
// ========================================================

// 1. Cari data resep berdasarkan id pemeriksaan_obat atau id_pemeriksaan
$where_detail = "";
if ($id > 0) {
    $where_detail = "po.id = $id";
} elseif ($id_periksa > 0) {
    $where_detail = "po.id_pemeriksaan = $id_periksa";
} else {
    die("Parameter ID resep tidak valid. <a href='resep.php'>Kembali</a>");
}

$query_single = "
    SELECT 
        po.id AS id_pemeriksaan_obat,
        po.id_pemeriksaan,
        po.id_id_obat,
        po.aturan_pakai,
        o.kode_obat,
        o.nama_obat,
        p.id AS no_pemeriksaan,
        p.tgl_periksa,
        p.keluhan,
        p.tensi_darah,
        pas.id AS id_pasien,
        pas.nama_pasien,
        pas.no_registrasi,
        pas.alamat AS alamat_pasien,
        pas.tgl_lahir AS tgl_lahir_pasien,
        dok.nama_dokter,
        dok.spesialis,
        dok.alamat AS alamat_dokter
    FROM pemeriksaan_obat po
    JOIN pemeriksaan p ON po.id_pemeriksaan = p.id
    JOIN obat o ON po.id_id_obat = o.id
    JOIN pasien pas ON p.id_pasien = pas.id
    JOIN dokter dok ON p.id_dokter = dok.id
    WHERE $where_detail
    LIMIT 1
";
$res_single = mysqli_query($koneksi, $query_single);

if (!$res_single || mysqli_num_rows($res_single) == 0) {
    die("Data resep obat tidak ditemukan. <a href='resep.php'>Kembali</a>");
}

$data = mysqli_fetch_assoc($res_single);
$current_periksa_id = (int)$data['id_pemeriksaan'];

// Ambil seluruh obat yang ada pada nomor periksa tersebut agar resep utuh
$query_all_obat = "
    SELECT 
        po.id AS id_po,
        po.id_id_obat,
        o.kode_obat,
        o.nama_obat,
        po.aturan_pakai
    FROM pemeriksaan_obat po
    JOIN obat o ON po.id_id_obat = o.id
    WHERE po.id_pemeriksaan = $current_periksa_id
    ORDER BY po.id ASC
";
$res_all_obat = mysqli_query($koneksi, $query_all_obat);

$tgl_lahir = !empty($data['tgl_lahir_pasien']) ? new DateTime($data['tgl_lahir_pasien']) : null;
$today     = new DateTime($data['tgl_periksa']);
$usia      = $tgl_lahir ? $tgl_lahir->diff($today)->y : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Resep Dokter #<?= $data['id_pemeriksaan_obat'] ?> - <?= htmlspecialchars($data['nama_pasien']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,600;1,700&display=swap" rel="stylesheet">
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

    <!-- Print Control Bar (Non-Printable) -->
    <div class="max-w-2xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="resep.php" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 flex items-center gap-1.5 transition-colors">
            <span>&larr;</span> Kembali ke Resep
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-600/30 flex items-center gap-2 transition-all">
                <span>🖨️</span> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Prescription Paper Card -->
    <div class="print-card max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
        
        <!-- KOP RESEP DOKTER / RUMAH SAKIT -->
        <div class="border-b-2 border-slate-900 pb-4 mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-teal-600 text-white flex items-center justify-center text-2xl font-black shadow-md">
                    +
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-slate-900 uppercase">RUMAH SAKIT MEDIKA UTAMA</h1>
                    <p class="text-[11px] text-slate-600">Jl. Pelayanan Sehat No. 100 &bull; Telp: (031) 8899-7766 &bull; Surabaya</p>
                    <p class="text-[10px] text-teal-700 font-bold uppercase tracking-wider">Instalasi Farmasi & Pelayanan Resep Dokter</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-mono font-bold text-slate-500 uppercase">No. Resep</div>
                <div class="text-base font-mono font-black text-teal-700">#RSP-<?= str_pad($data['id_pemeriksaan_obat'], 4, '0', STR_PAD_LEFT) ?></div>
                <div class="text-[10px] font-mono text-slate-400">Ref: #MED-<?= str_pad($data['no_pemeriksaan'], 4, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <!-- JUDUL LEMBAR RESEP -->
        <div class="text-center mb-5">
            <h2 class="text-sm font-extrabold tracking-widest uppercase text-slate-900 underline">SALINAN RESEP DOKTER</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tanggal Peresepan: <?= tgl_indo($data['tgl_periksa']) ?></p>
        </div>

        <!-- IDENTITAS DOKTER & PASIEN -->
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 text-xs">
            <div>
                <div class="font-bold text-slate-500 uppercase text-[10px] tracking-wider mb-1.5 flex items-center gap-1">
                    <span>👨‍⚕️</span> Dokter Peresep:
                </div>
                <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($data['nama_dokter']) ?></div>
                <div class="text-[11px] text-teal-700 font-medium"><?= htmlspecialchars($data['spesialis']) ?></div>
                <?php if (!empty($data['tensi_darah'])): ?>
                    <div class="text-[11px] text-slate-600 mt-1">Tensi Pasien: <span class="font-bold text-rose-700"><?= htmlspecialchars($data['tensi_darah']) ?></span></div>
                <?php endif; ?>
            </div>

            <div>
                <div class="font-bold text-slate-500 uppercase text-[10px] tracking-wider mb-1.5 flex items-center gap-1">
                    <span>🧑</span> Pasien Penerima:
                </div>
                <table class="w-full text-xs">
                    <tr><td class="w-20 text-slate-500 py-0.5">Nama</td><td class="font-bold text-slate-900">: <?= htmlspecialchars($data['nama_pasien']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">No. RM</td><td class="font-mono font-bold">: <?= htmlspecialchars($data['no_registrasi']) ?></td></tr>
                    <tr><td class="text-slate-500 py-0.5">Usia</td><td>: <?= $usia ?> Tahun</td></tr>
                    <tr><td class="text-slate-500 py-0.5">Alamat</td><td class="truncate max-w-[140px]">: <?= htmlspecialchars($data['alamat_pasien'] ?: '-') ?></td></tr>
                </table>
            </div>
        </div>

        <!-- KELUHAN / DIAGNOSA KLINIS -->
        <?php if (!empty($data['keluhan'])): ?>
        <div class="mb-5 text-xs">
            <div class="font-bold text-slate-700 uppercase text-[10px] tracking-wider mb-1">Diagnosa / Catatan Medis:</div>
            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-slate-700 text-[11px]">
                <?= nl2br(htmlspecialchars($data['keluhan'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- BAGIAN R/ (RECIPE - RESEP OBAT) -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2.5">
                <span class="text-2xl font-serif italic font-black text-slate-900">R/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">(Formularium Resep Obat Diberikan)</span>
            </div>

            <table class="w-full text-xs text-left border border-slate-300 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                        <th class="py-2.5 px-3">Kode Obat</th>
                        <th class="py-2.5 px-3">Nama Obat & Sediaan</th>
                        <th class="py-2.5 px-3 text-right">Aturan Pakai / Signa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php 
                    $no = 1;
                    while ($ob = mysqli_fetch_assoc($res_all_obat)): 
                        $is_current = ($ob['id_po'] == $data['id_pemeriksaan_obat']);
                    ?>
                        <tr class="<?= $is_current ? 'bg-amber-50/70 font-semibold' : '' ?>">
                            <td class="py-2.5 px-3 text-center"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800"><?= htmlspecialchars($ob['kode_obat']) ?></td>
                            <td class="py-2.5 px-3 font-bold text-slate-900">
                                <?= htmlspecialchars($ob['nama_obat']) ?>
                                <?php if ($is_current): ?>
                                    <span class="text-[9px] font-bold text-amber-700 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded ml-1">Dipilih</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-teal-800">
                                <?= htmlspecialchars(!empty($ob['aturan_pakai']) ? $ob['aturan_pakai'] : '3x1 Sehari Sesudah Makan') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- TANDA TANGAN DOKTER & PETUGAS FARMASI -->
        <div class="grid grid-cols-2 text-xs pt-5 border-t border-slate-200">
            <div class="text-slate-500 text-[11px]">
                <p class="font-bold text-slate-700 mb-1">Ketentuan Farmasi:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <li>Obat hanya dapat ditebus di instalasi farmasi resmi.</li>
                    <li>Konsumsi obat secara teratur sesuai aturan pakai dokter.</li>
                    <li>Jauhkan dari jangkauan anak-anak.</li>
                </ul>
                <div class="mt-4 pt-3 border-t border-dashed border-slate-200 w-44">
                    <p class="text-[10px] text-slate-400">Petugas Penyerah Farmasi:</p>
                    <div class="h-8"></div>
                    <p class="text-[10px] font-semibold text-slate-600">( ............................................ )</p>
                </div>
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
