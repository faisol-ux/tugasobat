<?php
$page_title = 'Obat';
$page_subtitle = 'Kelola inventaris obat-obatan, kode obat, dan katalog farmasi';
include __DIR__ . '/header.php';
require_staff();

// Fitur Pencarian
$search = clean($_GET['search'] ?? '');
$where = "";
if (!empty($search)) {
    $where = "WHERE o.nama_obat LIKE '%$search%' OR o.kode_obat LIKE '%$search%'";
}

$query = "
    SELECT 
        o.*,
        COUNT(po.id) AS total_diresepkan
    FROM obat o
    LEFT JOIN pemeriksaan_obat po ON o.id = po.id_id_obat
    $where
    GROUP BY o.id
    ORDER BY o.id ASC
";
$result = mysqli_query($koneksi, $query);

// Auto-generate Kode Obat berikutnya
$q_last = mysqli_query($koneksi, "SELECT id FROM obat ORDER BY id DESC LIMIT 1");
$last_id = ($row_last = mysqli_fetch_assoc($q_last)) ? $row_last['id'] + 1 : 1;
$next_kode_obat = 'OBT-' . str_pad($last_id, 3, '0', STR_PAD_LEFT);
?>

<div class="space-y-6">

    <!-- TOP TOOLBAR -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- Form Search -->
        <form method="GET" action="obat.php" class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari kode obat, nama obat..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 text-xs">
            <?php if (!empty($search)): ?>
                <a href="obat.php" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <!-- Tombol Tambah Obat -->
        <button onclick="openModalTambah()" 
                class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-xs flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Tambah Obat Baru</span>
        </button>
    </div>

    <!-- TABEL DATA OBAT -->
    <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pills text-amber-400"></i>
                    <span>Katalog Farmasi & Obat-obatan</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Total <?= mysqli_num_rows($result) ?> item obat terdaftar</p>
            </div>
            <?php if (!empty($search)): ?>
                <span class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                    Hasil untuk: "<?= htmlspecialchars($search) ?>"
                </span>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/90 border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode Obat</th>
                        <th class="py-3 px-4">Nama Obat</th>
                        <th class="py-3 px-4 text-center">Frekuensi Peresepan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php if (mysqli_num_rows($result) > 0): 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr class="hover:bg-slate-750 transition-colors">
                            <td class="py-3 px-4 text-center text-slate-400"><?= $no++ ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-md bg-amber-500/10 border border-amber-500/30 text-amber-300 font-mono font-bold text-[11px]">
                                    <?= htmlspecialchars($row['kode_obat']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-sm"><?= htmlspecialchars($row['nama_obat']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-700 text-slate-300 text-[11px] font-semibold">
                                    <i class="fa-solid fa-receipt text-teal-400 mr-1"></i>
                                    <?= $row['total_diresepkan'] ?> kali
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="openModalEdit(<?= htmlspecialchars(json_encode($row)) ?>)"
                                            class="p-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30" title="Edit Obat">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button onclick="confirmDelete('obat_action.php?aksi=hapus&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_obat']) ?>')"
                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30" title="Hapus Obat">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-capsules text-4xl mb-3 text-slate-600 block"></i>
                                Data obat tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL TAMBAH OBAT ================= -->
<div id="modalTambah" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-amber-400"></i>
                <span>Tambah Obat Baru</span>
            </h3>
            <button onclick="closeModalTambah()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="obat_action.php?aksi=tambah" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Obat (kode_obat)</label>
                <input type="text" name="kode_obat" value="<?= $next_kode_obat ?>" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Obat & Dosis (nama_obat)</label>
                <input type="text" name="nama_obat" placeholder="Contoh: Paracetamol 500mg Tablet" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalTambah()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20">
                    Simpan Obat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT OBAT ================= -->
<div id="modalEdit" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-pen-to-square text-teal-400"></i>
                <span>Edit Obat</span>
            </h3>
            <button onclick="closeModalEdit()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="obat_action.php?aksi=edit" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Obat (kode_obat)</label>
                <input type="text" name="kode_obat" id="edit_kode_obat" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Obat & Dosis (nama_obat)</label>
                <input type="text" name="nama_obat" id="edit_nama_obat" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalEdit()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-slate-950 text-xs font-bold shadow-lg shadow-teal-500/20">
                    Perbarui Obat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambah() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }
    function closeModalTambah() {
        document.getElementById('modalTambah').classList.add('hidden');
    }

    function openModalEdit(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_kode_obat').value = data.kode_obat;
        document.getElementById('edit_nama_obat').value = data.nama_obat;
        document.getElementById('modalEdit').classList.remove('hidden');
    }
    function closeModalEdit() {
        document.getElementById('modalEdit').classList.add('hidden');
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>

