<?php
$page_title = 'Rekam Pemeriksaan Pasien';
$page_subtitle = 'Data pemeriksaan fisik, tensi darah, diagnosa keluhan, dan peresepan obat';
include __DIR__ . '/header.php';
require_staff();

// Fitur Pencarian & Filter
$search = clean($_GET['search'] ?? '');
$filter_dokter = clean($_GET['dokter'] ?? '');

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(pas.nama_pasien LIKE '%$search%' OR pas.no_registrasi LIKE '%$search%' OR p.keluhan LIKE '%$search%')";
}
if (!empty($filter_dokter)) {
    $where_clauses[] = "p.id_dokter = " . (int)$filter_dokter;
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Query join pemeriksaan + pasien + dokter + aggregate obat
$query = "
    SELECT 
        p.*,
        pas.nama_pasien,
        pas.no_registrasi,
        pas.tgl_lahir,
        dok.nama_dokter,
        dok.spesialis,
        GROUP_CONCAT(CONCAT(o.kode_obat, '||', o.nama_obat) SEPARATOR ';;') AS obat_list,
        COUNT(po.id) AS jumlah_obat
    FROM pemeriksaan p
    JOIN pasien pas ON p.id_pasien = pas.id
    JOIN dokter dok ON p.id_dokter = dok.id
    LEFT JOIN pemeriksaan_obat po ON p.id = po.id_pemeriksaan
    LEFT JOIN obat o ON po.id_id_obat = o.id
    $where_sql
    GROUP BY p.id
    ORDER BY p.tgl_periksa DESC, p.id DESC
";
$result = mysqli_query($koneksi, $query);

// Data dokter untuk dropdown filter
$dokters = mysqli_query($koneksi, "SELECT id, nama_dokter FROM dokter ORDER BY nama_dokter ASC");
?>

<div class="space-y-6">

    <!-- TOP TOOLBAR -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="pemeriksaan.php" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Cari pasien, no. reg, keluhan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 text-xs">
            </div>

            <!-- Filter Dokter -->
            <select name="dokter" onchange="this.form.submit()"
                    class="py-2.5 px-3 bg-slate-800/90 border border-slate-700 rounded-xl text-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                <option value="">Semua Dokter Pemeriksa</option>
                <?php while ($d = mysqli_fetch_assoc($dokters)): ?>
                    <option value="<?= $d['id'] ?>" <?= ($filter_dokter == $d['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nama_dokter']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <?php if (!empty($search) || !empty($filter_dokter)): ?>
                <a href="pemeriksaan.php" class="px-3 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                </a>
            <?php endif; ?>
        </form>

        <!-- Tombol Tambah Pemeriksaan -->
        <a href="pemeriksaan_tambah.php" 
           class="px-4 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-teal-500/20 text-xs flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-stethoscope"></i>
            <span>Input Pemeriksaan Baru</span>
        </a>
    </div>

    <!-- TABEL DATA PEMERIKSAAN -->
    <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-notes-medical text-teal-400"></i>
                    <span>Riwayat Rekam Medis & Pemeriksaan</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Ditemukan <?= mysqli_num_rows($result) ?> data rekam medis</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/90 border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                        <th class="py-3 px-4 w-10 text-center">ID</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Pasien</th>
                        <th class="py-3 px-4">Dokter</th>
                        <th class="py-3 px-4">Tensi Darah</th>
                        <th class="py-3 px-4">Keluhan Medis</th>
                        <th class="py-3 px-4">Resep Obat (pemeriksaan_obat)</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php if (mysqli_num_rows($result) > 0): 
                        while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr class="hover:bg-slate-750 transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">#<?= $row['id'] ?></td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-200">
                                <div class="font-semibold"><?= tgl_indo($row['tgl_periksa']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= date('l', strtotime($row['tgl_periksa'])) ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white"><?= htmlspecialchars($row['nama_pasien']) ?></div>
                                <span class="font-mono text-[10px] text-teal-400"><?= htmlspecialchars($row['no_registrasi']) ?></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-200 font-semibold"><?= htmlspecialchars($row['nama_dokter']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($row['spesialis']) ?></div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-teal-300 font-mono font-bold text-[11px] inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-heart-pulse text-rose-400 text-xs"></i>
                                    <span><?= htmlspecialchars($row['tensi_darah']) ?></span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="text-slate-300 line-clamp-2"><?= htmlspecialchars($row['keluhan']) ?></div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <?php if (!empty($row['obat_list'])): 
                                    $obat_items = explode(';;', $row['obat_list']);
                                ?>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php foreach ($obat_items as $item): 
                                            $parts = explode('||', $item);
                                            $kod = $parts[0] ?? '';
                                            $nam = $parts[1] ?? '';
                                        ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[10px] font-medium" title="<?= htmlspecialchars($nam) ?>">
                                                <i class="fa-solid fa-capsules text-[9px]"></i>
                                                <span><?= htmlspecialchars($nam) ?></span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <a href="pemeriksaan_edit.php?id=<?= $row['id'] ?>" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 text-[11px] font-semibold transition-all"
                                       title="Keluhan pasien sudah ada, klik untuk langsung meresepkan obat">
                                        <i class="fa-solid fa-pills text-teal-400"></i>
                                        <span>Beri Resep Obat</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="pemeriksaan_detail.php?id=<?= $row['id'] ?>" 
                                       class="p-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30" title="Detail Rekam Medis">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="pemeriksaan_cetak.php?id=<?= $row['id'] ?>" target="_blank"
                                       class="p-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-300 border border-sky-500/30" title="Cetak Resep & Resume">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="pemeriksaan_edit.php?id=<?= $row['id'] ?>" 
                                       class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Edit Pemeriksaan">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <button onclick="confirmDelete('pemeriksaan_action.php?aksi=hapus&id=<?= $row['id'] ?>', 'Pemeriksaan <?= htmlspecialchars($row['nama_pasien']) ?>')"
                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30" title="Hapus Pemeriksaan">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-600 block"></i>
                                Belum ada data rekam pemeriksaan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/footer.php'; ?>

