<?php
$page_title = 'Dokter';
$page_subtitle = 'Kelola staf medis, spesialisasi, dan hak akses login sistem';
include __DIR__ . '/header.php';

// Fitur Pencarian
$search = clean($_GET['search'] ?? '');
$where = "";
if (!empty($search)) {
    $where = "WHERE nama_dokter LIKE '%$search%' OR spesialis LIKE '%$search%' OR username LIKE '%$search%'";
}

$query = "SELECT * FROM dokter $where ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="space-y-6">

    <!-- BANNER KHUSUS PASIEN -->
    <?php if (is_pasien()): ?>
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-200 text-xs flex items-center gap-3">
        <i class="fa-solid fa-user-doctor text-xl text-emerald-400"></i>
        <div>
            <strong class="text-white block font-semibold">Direktori Tim Dokter SIMRS</strong>
            <span>Pasien dapat melihat informasi nama dokter, spesialisasi, dan jadwal pelayanan yang tersedia.</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- TOP TOOLBAR -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- Form Search -->
        <form method="GET" action="dokter.php" class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari dokter, spesialisasi, username..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs">
            <?php if (!empty($search)): ?>
                <a href="dokter.php" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <?php if (!is_pasien()): ?>
        <!-- Tombol Tambah Dokter (Hanya Dokter & Admin) -->
        <button onclick="openModalTambah()" 
                class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-emerald-500/20 text-xs flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-user-doctor"></i>
            <span>Tambah Dokter Baru</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- TABEL DATA DOKTER -->
    <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-stethoscope text-emerald-400"></i>
                    <span>Daftar Dokter Rumah Sakit</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Total <?= mysqli_num_rows($result) ?> dokter terdaftar</p>
            </div>
            <?php if (!empty($search)): ?>
                <span class="text-xs bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 px-2.5 py-1 rounded-lg">
                    Hasil untuk: "<?= htmlspecialchars($search) ?>"
                </span>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/90 border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Dokter</th>
                        <th class="py-3 px-4">Spesialisasi</th>
                        <th class="py-3 px-4">Jenis Kelamin</th>
                        <th class="py-3 px-4">Username Akun</th>
                        <th class="py-3 px-4">Alamat & Tgl Lahir</th>
                        <?php if (!is_pasien()): ?>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        <?php endif; ?>
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
                                <div class="font-bold text-white flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold text-xs">
                                        <?= strtoupper(substr($row['nama_dokter'], 0, 1)) ?>
                                    </div>
                                    <span><?= htmlspecialchars($row['nama_dokter']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-semibold text-[11px]">
                                    <?= htmlspecialchars($row['spesialis']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <?= htmlspecialchars($row['jenis']) ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-slate-300 bg-slate-900 px-2 py-0.5 rounded border border-slate-700">
                                    <?= htmlspecialchars($row['username']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                <div class="text-slate-300"><?= htmlspecialchars($row['alamat']) ?></div>
                                <div class="text-[10px] text-slate-500"><?= tgl_indo($row['tgl_lahir']) ?></div>
                            </td>
                            <?php if (!is_pasien()): ?>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="openModalEdit(<?= htmlspecialchars(json_encode($row)) ?>)"
                                            class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Edit Dokter">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <?php if ($row['id'] != $user['id']): ?>
                                        <button onclick="confirmDelete('dokter_action.php?aksi=hapus&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_dokter']) ?>')"
                                                class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30" title="Hapus Dokter">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-xmark text-4xl mb-3 text-slate-600 block"></i>
                                Data dokter tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php if (!is_pasien()): ?>
<!-- ================= MODAL TAMBAH DOKTER ================= -->
<div id="modalTambah" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden animate-fade-in max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-doctor text-emerald-400"></i>
                <span>Tambah Dokter Baru</span>
            </h3>
            <button onclick="closeModalTambah()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="dokter_action.php?aksi=tambah" method="POST" class="p-6 space-y-4 overflow-y-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username Login</label>
                    <input type="text" name="username" placeholder="Contoh: dr_santoso" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap Dokter (Gelar)</label>
                <input type="text" name="nama_dokter" placeholder="Contoh: dr. Bambang Pamungkas, Sp.B" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jenis Kelamin</label>
                    <select name="jenis" required
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Spesialisasi</label>
                    <input type="text" name="spesialis" placeholder="Umum, Penyakit Dalam, dll" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat</label>
                    <input type="text" name="alamat" placeholder="Kota / Alamat" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalTambah()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold shadow-lg shadow-emerald-500/20">
                    Simpan Dokter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT DOKTER ================= -->
<div id="modalEdit" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden animate-fade-in max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-pen-to-square text-amber-400"></i>
                <span>Edit Dokter</span>
            </h3>
            <button onclick="closeModalEdit()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="dokter_action.php?aksi=edit" method="POST" class="p-6 space-y-4 overflow-y-auto">
            <input type="hidden" name="id" id="edit_id">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username Login</label>
                    <input type="text" name="username" id="edit_username" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="edit_password" placeholder="Kosongkan jika tak diubah"
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap Dokter</label>
                <input type="text" name="nama_dokter" id="edit_nama_dokter" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jenis Kelamin</label>
                    <select name="jenis" id="edit_jenis" required
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Spesialisasi</label>
                    <input type="text" name="spesialis" id="edit_spesialis" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" id="edit_tgl_lahir" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat</label>
                    <input type="text" name="alamat" id="edit_alamat" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalEdit()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20">
                    Perbarui Dokter
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    function openModalTambah() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }
    function closeModalTambah() {
        document.getElementById('modalTambah').classList.add('hidden');
    }

    function openModalEdit(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_nama_dokter').value = data.nama_dokter;
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_spesialis').value = data.spesialis;
        document.getElementById('edit_tgl_lahir').value = data.tgl_lahir;
        document.getElementById('edit_alamat').value = data.alamat;
        document.getElementById('modalEdit').classList.remove('hidden');
    }
    function closeModalEdit() {
        document.getElementById('modalEdit').classList.add('hidden');
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>

