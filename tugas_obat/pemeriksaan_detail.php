<?php
$page_title = 'Detail Rekam Medis Pasien';
$page_subtitle = 'Informasi lengkap hasil pemeriksaan fisik dan rincian resep obat';
include __DIR__ . '/header.php';
require_staff();

$id = (int)($_GET['id'] ?? 0);

// Query data pemeriksaan + pasien + dokter
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
    set_flash('danger', 'Data pemeriksaan tidak ditemukan.');
    header("Location: pemeriksaan.php");
    exit;
}

$data = mysqli_fetch_assoc($res);

// Query resep obat dari tabel pemeriksaan_obat
$query_obat = "
    SELECT 
        po.id AS id_pemeriksaan_obat,
        po.aturan_pakai,
        o.id AS id_obat,
        o.kode_obat,
        o.nama_obat
    FROM pemeriksaan_obat po
    JOIN obat o ON po.id_id_obat = o.id
    WHERE po.id_pemeriksaan = $id
    ORDER BY o.nama_obat ASC
";
$res_obat = mysqli_query($koneksi, $query_obat);
$total_obat = mysqli_num_rows($res_obat);

// Hitung usia pasien
$tgl_lahir = new DateTime($data['tgl_lahir_pasien']);
$today = new DateTime($data['tgl_periksa']);
$usia = $tgl_lahir->diff($today)->y;
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <a href="pemeriksaan.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Pemeriksaan</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="pemeriksaan_cetak.php?id=<?= $id ?>" target="_blank"
               class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold text-xs shadow-lg shadow-sky-500/20 flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Lembar Resep</span>
            </a>
            <a href="pemeriksaan_edit.php?id=<?= $id ?>" 
               class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i class="fa-regular fa-pen-to-square"></i>
                <span>Edit Pemeriksaan</span>
            </a>
        </div>
    </div>

    <!-- MAIN MEDICAL CARD -->
    <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Header Sheet -->
        <div class="p-6 md:p-8 bg-gradient-to-r from-slate-900 to-slate-850 border-b border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-2.5 py-1 rounded bg-teal-500/10 border border-teal-500/30 text-teal-300 font-mono text-xs font-bold mb-2">
                    REKAM MEDIS #<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?>
                </span>
                <h2 class="text-xl md:text-2xl font-black text-white"><?= htmlspecialchars($data['nama_pasien']) ?></h2>
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                    <span class="font-mono text-teal-400"><?= htmlspecialchars($data['no_registrasi']) ?></span>
                    <span>&bull;</span>
                    <span>Usia: <?= $usia ?> Tahun</span>
                    <span>&bull;</span>
                    <span><?= htmlspecialchars($data['alamat_pasien']) ?></span>
                </p>
            </div>

            <div class="text-left md:text-right">
                <div class="text-xs text-slate-400">Tanggal Pemeriksaan:</div>
                <div class="text-sm font-bold text-white"><?= tgl_indo($data['tgl_periksa']) ?></div>
                <div class="text-xs text-teal-400 mt-1">Dokter: <?= htmlspecialchars($data['nama_dokter']) ?></div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            <!-- Grid Observasi Fisik -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tensi Darah Card -->
                <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-heart-pulse text-rose-400"></i>
                        <span>Tekanan / Tensi Darah</span>
                    </div>
                    <div class="text-2xl font-extrabold text-white font-mono"><?= htmlspecialchars($data['tensi_darah']) ?></div>
                    <div class="text-[11px] text-slate-400 mt-1">Standar normal dewasa: 120/80 mmHg</div>
                </div>

                <!-- Info Dokter Pemeriksa -->
                <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-user-doctor text-emerald-400"></i>
                        <span>Dokter yang Memeriksa</span>
                    </div>
                    <div class="text-sm font-bold text-white"><?= htmlspecialchars($data['nama_dokter']) ?></div>
                    <div class="text-xs text-teal-400 mt-0.5">Spesialisasi: <?= htmlspecialchars($data['spesialis']) ?></div>
                </div>
            </div>

            <!-- Keluhan / Anamnesa -->
            <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-700/60">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-notes-medical text-teal-400"></i>
                    <span>Keluhan Utama & Hasil Anamnesa</span>
                </div>
                <p class="text-sm text-slate-200 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($data['keluhan']) ?></p>
            </div>

            <!-- Rincian Resep Obat (pemeriksaan_obat) -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-prescription-bottle-medical text-amber-400"></i>
                        <span>Resep Obat yang Diberikan</span>
                    </h3>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 font-semibold">
                        <?= $total_obat ?> Item Obat
                    </span>
                </div>

                <div class="rounded-xl border border-slate-700 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-900/80 text-slate-400 uppercase text-[11px] font-semibold border-b border-slate-700">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Kode Obat</th>
                                <th class="py-3 px-4">Nama Obat & Sediaan</th>
                                <th class="py-3 px-4">Aturan Pakai</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50 bg-slate-900/40">
                            <?php if ($total_obat > 0): 
                                $no_o = 1;
                                while ($ob = mysqli_fetch_assoc($res_obat)): 
                            ?>
                                <tr class="hover:bg-slate-800/40">
                                    <td class="py-3 px-4 text-center text-slate-400"><?= $no_o++ ?></td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-amber-400 font-mono font-bold text-[11px] border border-slate-700">
                                            <?= htmlspecialchars($ob['kode_obat']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-white"><?= htmlspecialchars($ob['nama_obat']) ?></div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded bg-purple-500/10 border border-purple-500/30 text-purple-300 font-mono text-[11px]">
                                            <i class="fa-solid fa-clock text-[9px] mr-1"></i>
                                            <?= htmlspecialchars(!empty($ob['aturan_pakai']) ? $ob['aturan_pakai'] : '3x1 Sehari Sesudah Makan') ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-[10px] font-semibold">
                                            Tersedia & Terverifikasi
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 italic">
                                        Tidak ada obat yang diresepkan pada pemeriksaan ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . '/footer.php'; ?>

