<?php
$page_title = 'Resep Obat (Tabel: pemeriksaan_obat)';
$page_subtitle = 'Data relasi peresepan antara pemeriksaan klinis dan jenis obat yang diberikan';
include __DIR__ . '/header.php';
require_staff();

// Filter
$filter_obat = clean($_GET['obat'] ?? '');
$filter_pasien = clean($_GET['pasien'] ?? '');

$where_clauses = [];
if (!empty($filter_obat)) {
    $where_clauses[] = "po.id_id_obat = " . (int)$filter_obat;
}
if (!empty($filter_pasien)) {
    $where_clauses[] = "p.id_pasien = " . (int)$filter_pasien;
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Query join pemeriksaan_obat + pemeriksaan + obat + pasien + dokter
$query = "
    SELECT 
        po.id AS id_pemeriksaan_obat,
        po.id_pemeriksaan,
        po.id_id_obat,
        po.aturan_pakai,
        o.kode_obat,
        o.nama_obat,
        p.tgl_periksa,
        p.tensi_darah,
        pas.id AS id_pasien,
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
$result = mysqli_query($koneksi, $query);

// Data untuk filter dropdown & modal
$obat_options = mysqli_query($koneksi, "SELECT id, kode_obat, nama_obat FROM obat ORDER BY nama_obat ASC");
$pasien_options = mysqli_query($koneksi, "SELECT id, nama_pasien, no_registrasi FROM pasien ORDER BY nama_pasien ASC");
$periksa_options = mysqli_query($koneksi, "
    SELECT p.id, p.tgl_periksa, pas.nama_pasien 
    FROM pemeriksaan p 
    JOIN pasien pas ON p.id_pasien = pas.id 
    ORDER BY p.id DESC
");
?>

<div class="space-y-6">

    <!-- TOP TOOLBAR -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="resep.php" class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Filter Obat -->
            <select name="obat" onchange="this.form.submit()"
                    class="py-2.5 px-3 bg-slate-800/90 border border-slate-700 rounded-xl text-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <option value="">Semua Jenis Obat</option>
                <?php while ($o = mysqli_fetch_assoc($obat_options)): ?>
                    <option value="<?= $o['id'] ?>" <?= ($filter_obat == $o['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($o['nama_obat']) ?> (<?= htmlspecialchars($o['kode_obat']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Filter Pasien -->
            <select name="pasien" onchange="this.form.submit()"
                    class="py-2.5 px-3 bg-slate-800/90 border border-slate-700 rounded-xl text-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <option value="">Semua Pasien</option>
                <?php while ($ps = mysqli_fetch_assoc($pasien_options)): ?>
                    <option value="<?= $ps['id'] ?>" <?= ($filter_pasien == $ps['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ps['nama_pasien']) ?> (<?= htmlspecialchars($ps['no_registrasi']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <?php if (!empty($filter_obat) || !empty($filter_pasien)): ?>
                <a href="resep.php" class="px-3 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                </a>
            <?php endif; ?>
        </form>

        <!-- Tambah Obat ke Pemeriksaan -->
        <button onclick="openModalTambahResep()" 
                class="px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 text-xs flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Tambah Resep Obat</span>
        </button>
    </div>

    <!-- TABEL DATA PEMERIKSAAN_OBAT -->
    <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-prescription-bottle-medical text-purple-400"></i>
                    <span>Tabel Resep Obat (pemeriksaan_obat)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Relasi kunci antara `pemeriksaan.id` dan `obat.id` (Dapat diedit teks aturan pakai)</p>
            </div>
            <span class="text-xs bg-purple-500/10 text-purple-300 border border-purple-500/20 px-3 py-1 rounded-full font-mono">
                <?= mysqli_num_rows($result) ?> Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/90 border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                        <th class="py-3 px-4 w-12 text-center">ID</th>
                        <th class="py-3 px-4">Pemeriksaan Ref</th>
                        <th class="py-3 px-4">Pasien Penerima</th>
                        <th class="py-3 px-4">Dokter Peresep</th>
                        <th class="py-3 px-4">Obat Diberikan</th>
                        <th class="py-3 px-4">Aturan Pakai (Edit Text)</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php if (mysqli_num_rows($result) > 0): 
                        while ($row = mysqli_fetch_assoc($result)): 
                            $aturan_txt = !empty($row['aturan_pakai']) ? $row['aturan_pakai'] : '3x1 Sehari Sesudah Makan';
                    ?>
                        <tr class="hover:bg-slate-750 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-slate-400">#<?= $row['id_pemeriksaan_obat'] ?></td>
                            <td class="py-3 px-4">
                                <a href="pemeriksaan_detail.php?id=<?= $row['id_pemeriksaan'] ?>" class="text-teal-400 hover:underline font-mono font-bold">
                                    #<?= str_pad($row['id_pemeriksaan'], 4, '0', STR_PAD_LEFT) ?>
                                </a>
                                <div class="text-[10px] text-slate-400"><?= tgl_indo($row['tgl_periksa']) ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white"><?= htmlspecialchars($row['nama_pasien']) ?></div>
                                <span class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($row['no_registrasi']) ?></span>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <?= htmlspecialchars($row['nama_dokter']) ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-pills text-teal-400 text-xs"></i>
                                    <span><?= htmlspecialchars($row['nama_obat']) ?></span>
                                </div>
                                <span class="text-[10px] text-amber-400 font-mono"><?= htmlspecialchars($row['kode_obat']) ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <button type="button" 
                                        onclick="openModalEditResep(<?= htmlspecialchars(json_encode([
                                            'id' => $row['id_pemeriksaan_obat'],
                                            'id_pemeriksaan' => $row['id_pemeriksaan'],
                                            'id_id_obat' => $row['id_id_obat'],
                                            'aturan_pakai' => $aturan_txt,
                                            'nama_pasien' => $row['nama_pasien'],
                                            'nama_obat' => $row['nama_obat']
                                        ])) ?>)"
                                        class="text-left group flex items-center gap-2 p-1.5 rounded-lg bg-slate-900 border border-slate-700 hover:border-purple-500 transition-all cursor-pointer">
                                    <span class="text-purple-300 font-mono text-xs"><?= htmlspecialchars($aturan_txt) ?></span>
                                    <i class="fa-solid fa-pen text-[10px] text-slate-500 group-hover:text-purple-400 ml-1"></i>
                                </button>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Edit Resep (Edit Text) -->
                                    <button onclick="openModalEditResep(<?= htmlspecialchars(json_encode([
                                                'id' => $row['id_pemeriksaan_obat'],
                                                'id_pemeriksaan' => $row['id_pemeriksaan'],
                                                'id_id_obat' => $row['id_id_obat'],
                                                'aturan_pakai' => $aturan_txt,
                                                'nama_pasien' => $row['nama_pasien'],
                                                'nama_obat' => $row['nama_obat']
                                            ])) ?>)"
                                            class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30" 
                                            title="Edit Resep & Aturan Pakai">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <button onclick="confirmDelete('resep_action.php?aksi=hapus&id=<?= $row['id_pemeriksaan_obat'] ?>', 'Resep <?= htmlspecialchars($row['nama_obat']) ?>')"
                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30" 
                                            title="Hapus Resep">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-4xl mb-3 text-slate-600 block"></i>
                                Tidak ada data resep obat ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL TAMBAH RESEP ================= -->
<div id="modalTambahResep" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-purple-400"></i>
                <span>Tambah Resep Obat</span>
            </h3>
            <button onclick="closeModalTambahResep()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="resep_action.php?aksi=tambah" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Rekam Pemeriksaan</label>
                <select name="id_pemeriksaan" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <option value="">-- Pilih Rekam Pemeriksaan --</option>
                    <?php 
                    mysqli_data_seek($periksa_options, 0);
                    while ($po = mysqli_fetch_assoc($periksa_options)): 
                    ?>
                        <option value="<?= $po['id'] ?>">
                            #<?= $po['id'] ?> - <?= htmlspecialchars($po['nama_pasien']) ?> (<?= tgl_indo($po['tgl_periksa']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Obat (id_id_obat)</label>
                <select name="id_id_obat" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <option value="">-- Pilih Jenis Obat --</option>
                    <?php 
                    mysqli_data_seek($obat_options, 0);
                    while ($o = mysqli_fetch_assoc($obat_options)): 
                    ?>
                        <option value="<?= $o['id'] ?>">
                            <?= htmlspecialchars($o['nama_obat']) ?> (<?= htmlspecialchars($o['kode_obat']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- EDIT TEXT ATURAN PAKAI / DOSIS -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                    Aturan Pakai / Catatan Dosis (Edit Text) <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="aturan_pakai" id="tambah_aturan_pakai" 
                       value="3x1 Sehari Sesudah Makan" required
                       placeholder="Contoh: 3x1 sehari sesudah makan"
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none">
                
                <!-- Quick Preset Aturan Pakai -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2 text-[11px]">
                    <span class="text-slate-400">Pilihan cepat:</span>
                    <button type="button" onclick="setTambahAturan('3x1 Sesudah Makan')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-purple-300 hover:border-purple-500">3x1 Sesudah Makan</button>
                    <button type="button" onclick="setTambahAturan('2x1 Sebelum Makan')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-purple-300 hover:border-purple-500">2x1 Sebelum Makan</button>
                    <button type="button" onclick="setTambahAturan('1x1 Malam Hari')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-purple-300 hover:border-purple-500">1x1 Malam</button>
                    <button type="button" onclick="setTambahAturan('Bila Perlu / Nyeri')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-purple-300 hover:border-purple-500">Bila Perlu</button>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalTambahResep()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold shadow-lg shadow-purple-500/20">
                    Tambahkan Obat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT RESEP (EDIT TEXT ATURAN PAKAI & OBAT) ================= -->
<div id="modalEditResep" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-pen-to-square text-amber-400"></i>
                <span>Edit Resep Obat (pemeriksaan_obat)</span>
            </h3>
            <button onclick="closeModalEditResep()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="resep_action.php?aksi=edit" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="edit_resep_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">ID Resep (pemeriksaan_obat.id)</label>
                <input type="text" id="edit_resep_display_id" disabled
                       class="w-full px-3.5 py-2 bg-slate-900/60 border border-slate-700 rounded-xl text-slate-400 font-mono text-xs cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Rekam Pemeriksaan</label>
                <select name="id_pemeriksaan" id="edit_id_pemeriksaan" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <?php 
                    mysqli_data_seek($periksa_options, 0);
                    while ($po = mysqli_fetch_assoc($periksa_options)): 
                    ?>
                        <option value="<?= $po['id'] ?>">
                            #<?= $po['id'] ?> - <?= htmlspecialchars($po['nama_pasien']) ?> (<?= tgl_indo($po['tgl_periksa']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Obat (id_id_obat)</label>
                <select name="id_id_obat" id="edit_id_id_obat" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <?php 
                    mysqli_data_seek($obat_options, 0);
                    while ($o = mysqli_fetch_assoc($obat_options)): 
                    ?>
                        <option value="<?= $o['id'] ?>">
                            <?= htmlspecialchars($o['nama_obat']) ?> (<?= htmlspecialchars($o['kode_obat']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- EDIT TEXT ATURAN PAKAI / DOSIS -->
            <div>
                <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1">
                    Aturan Pakai / Catatan Resep (Edit Text) <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="aturan_pakai" id="edit_aturan_pakai" required
                       placeholder="Contoh: 3x1 sehari sesudah makan"
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                
                <!-- Quick Preset Aturan Pakai -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2 text-[11px]">
                    <span class="text-slate-400">Pilihan cepat:</span>
                    <button type="button" onclick="setEditAturan('3x1 Sesudah Makan')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">3x1 Sesudah Makan</button>
                    <button type="button" onclick="setEditAturan('2x1 Sebelum Makan')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">2x1 Sebelum Makan</button>
                    <button type="button" onclick="setEditAturan('1x1 Malam Hari')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">1x1 Malam</button>
                    <button type="button" onclick="setEditAturan('Bila Perlu / Nyeri')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">Bila Perlu</button>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalEditResep()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20">
                    Simpan Perubahan Resep
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahResep() {
        document.getElementById('modalTambahResep').classList.remove('hidden');
    }
    function closeModalTambahResep() {
        document.getElementById('modalTambahResep').classList.add('hidden');
    }
    function setTambahAturan(val) {
        document.getElementById('tambah_aturan_pakai').value = val;
    }

    function openModalEditResep(data) {
        document.getElementById('edit_resep_id').value = data.id;
        document.getElementById('edit_resep_display_id').value = '#' + data.id + ' (' + data.nama_pasien + ' - ' + data.nama_obat + ')';
        document.getElementById('edit_id_pemeriksaan').value = data.id_pemeriksaan;
        document.getElementById('edit_id_id_obat').value = data.id_id_obat;
        document.getElementById('edit_aturan_pakai').value = data.aturan_pakai || '3x1 Sehari Sesudah Makan';
        document.getElementById('modalEditResep').classList.remove('hidden');
        document.getElementById('edit_aturan_pakai').focus();
    }
    function closeModalEditResep() {
        document.getElementById('modalEditResep').classList.add('hidden');
    }
    function setEditAturan(val) {
        document.getElementById('edit_aturan_pakai').value = val;
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>
