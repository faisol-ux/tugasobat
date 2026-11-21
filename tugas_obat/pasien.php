<?php
$page_title = 'Pasien';
$page_subtitle = 'Kelola registrasi pasien, identitas, dan rekam medis keluhan';
include __DIR__ . '/header.php';

// Fitur Pencarian
$search = clean($_GET['search'] ?? '');
$where = "";
if (!empty($search)) {
    $where = "WHERE nama_pasien LIKE '%$search%' OR no_registrasi LIKE '%$search%' OR alamat LIKE '%$search%'";
}

$query = "SELECT * FROM pasien $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

// Auto-generate No Registrasi berikutnya
$q_last = mysqli_query($koneksi, "SELECT id FROM pasien ORDER BY id DESC LIMIT 1");
$last_id = ($row_last = mysqli_fetch_assoc($q_last)) ? $row_last['id'] + 1 : 1;
$next_no_reg = 'REG-' . date('Y') . '-' . str_pad($last_id, 3, '0', STR_PAD_LEFT);

// Ambil daftar dokter untuk pilihan konsultasi di form pendaftaran
$dokter_options = mysqli_query($koneksi, "SELECT id, nama_dokter, spesialis FROM dokter ORDER BY nama_dokter ASC");
?>

<div class="space-y-6">

    <!-- BANNER KHUSUS PASIEN -->
    <?php if (is_pasien()): ?>
    <div class="p-4 rounded-xl bg-sky-500/10 border border-sky-500/30 text-sky-200 text-xs flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-hospital-user text-xl text-sky-400"></i>
            <div>
                <strong class="text-white block font-semibold">Pendaftaran Pasien Mandiri</strong>
                <span>Silakan klik tombol <strong>"Tambah Pasien Baru"</strong> untuk mendaftarkan nama, keluhan klinis, dan tensi darah Anda.</span>
            </div>
        </div>
        <button onclick="openModalTambah()" 
                class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold rounded-xl text-xs flex items-center gap-2 whitespace-nowrap shadow-lg shadow-sky-500/20">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Daftar Sekarang</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- TOP TOOLBAR -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- Form Search -->
        <form method="GET" action="pasien.php" class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari nama pasien, no. registrasi, alamat..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 text-xs">
            <?php if (!empty($search)): ?>
                <a href="pasien.php" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <!-- Tombol Tambah Pasien (Dapat Diakses Pasien, Dokter & Admin) -->
        <button onclick="openModalTambah()" 
                class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-sky-500/20 text-xs flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-user-plus"></i>
            <span>+ Tambah Pasien</span>
        </button>
    </div>

    <!-- TABEL DATA PASIEN -->
    <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-sky-400"></i>
                    <span>Daftar Pasien Terdaftar</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Total <?= mysqli_num_rows($result) ?> pasien terdata di sistem</p>
            </div>
            <?php if (!empty($search)): ?>
                <span class="text-xs bg-sky-500/10 text-sky-300 border border-sky-500/20 px-2.5 py-1 rounded-lg">
                    Hasil untuk: "<?= htmlspecialchars($search) ?>"
                </span>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/90 border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">No. Registrasi</th>
                        <th class="py-3 px-4">Nama Pasien</th>
                        <th class="py-3 px-4">Tanggal Lahir</th>
                        <th class="py-3 px-4">Alamat Domisili</th>
                        <th class="py-3 px-4 text-center">Keluhan Terakhir</th>
                        <?php if (!is_pasien()): ?>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php if (mysqli_num_rows($result) > 0): 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                            // Ambil keluhan & tensi terakhir pasien ini
                            $q_periksa = mysqli_query($koneksi, "SELECT keluhan, tensi_darah, tgl_periksa FROM pemeriksaan WHERE id_pasien = " . $row['id'] . " ORDER BY id DESC LIMIT 1");
                            $periksa_last = mysqli_fetch_assoc($q_periksa);
                    ?>
                        <tr class="hover:bg-slate-750 transition-colors">
                            <td class="py-3 px-4 text-center text-slate-400"><?= $no++ ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-md bg-sky-500/10 border border-sky-500/30 text-sky-300 font-mono font-bold text-[11px]">
                                    <?= htmlspecialchars($row['no_registrasi']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white"><?= htmlspecialchars($row['nama_pasien']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-slate-300 whitespace-nowrap">
                                <i class="fa-regular fa-calendar text-slate-400 mr-1.5"></i>
                                <?= tgl_indo($row['tgl_lahir']) ?>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <i class="fa-solid fa-location-dot text-rose-400/80 mr-1.5"></i>
                                <?= htmlspecialchars($row['alamat']) ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php 
                                    $display_keluhan = !empty($row['keluhan']) ? $row['keluhan'] : ($periksa_last['keluhan'] ?? '');
                                    $display_tensi = !empty($row['tensi_darah']) ? $row['tensi_darah'] : ($periksa_last['tensi_darah'] ?? '');
                                ?>
                                <?php if (!empty($display_keluhan)): ?>
                                    <div class="text-[11px] text-slate-300 font-medium truncate max-w-xs text-left" title="<?= htmlspecialchars($display_keluhan) ?>">
                                        <?= htmlspecialchars($display_keluhan) ?>
                                    </div>
                                    <?php if (!empty($display_tensi)): ?>
                                        <span class="inline-block px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 font-mono text-[10px] mt-0.5">
                                            Tensi: <?= htmlspecialchars($display_tensi) ?>
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-slate-500 italic text-[10px]">Belum ada keluhan</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if (!is_pasien()): ?>
                            <!-- Aksi Edit & Hapus Hanya Muncul untuk Dokter & Admin -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="pemeriksaan_tambah.php?id_pasien=<?= $row['id'] ?>" 
                                       class="p-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30" 
                                       title="Periksa & Beri Resep Obat (Keluhan Otomatis Masuk)">
                                        <i class="fa-solid fa-stethoscope"></i>
                                    </a>
                                    <button onclick="openModalEdit(<?= htmlspecialchars(json_encode($row)) ?>)"
                                            class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Edit Pasien">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button onclick="confirmDelete('pasien_action.php?aksi=hapus&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_pasien']) ?>')"
                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30" title="Hapus Pasien">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= is_pasien() ? '6' : '7' ?>" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 text-slate-600 block"></i>
                                Data pasien tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL TAMBAH PASIEN (LENGKAP: NAMA, KELUHAN, TENSI SESUAI INSTRUKSI) ================= -->
<div id="modalTambah" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden animate-fade-in max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between bg-slate-850">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-sky-400"></i>
                <span>Tambah Pasien Baru</span>
            </h3>
            <button onclick="closeModalTambah()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="pasien_action.php?aksi=tambah" method="POST" class="p-6 space-y-4 overflow-y-auto">
            <!-- 1. IDENTITAS PASIEN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Registrasi Pasien</label>
                    <input type="text" name="no_registrasi" value="<?= $next_no_reg ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pasien <span class="text-rose-400">*</span></label>
                    <input type="text" name="nama_pasien" placeholder="Contoh: Budi Santoso" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <!-- 2. KELUHAN PASIEN (INSTRUKSI USER) -->
            <div>
                <label class="block text-xs font-bold text-sky-300 mb-1 uppercase tracking-wider">
                    Keluhan & Gejala Pasien <span class="text-rose-400">*</span>
                </label>
                <textarea name="keluhan" rows="3" required
                          placeholder="Jelaskan keluhan medis yang dirasakan (misal: Demam tinggi 3 hari, pusing, batuk, nyeri lambung)..."
                          class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <!-- 3. TENSI DARAH (INSTRUKSI USER) -->
            <div>
                <label class="block text-xs font-bold text-sky-300 mb-1 uppercase tracking-wider">
                    Tekanan / Tensi Darah (mmHg) <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="tensi_darah" id="modal_tensi_darah" placeholder="Contoh: 120/80 mmHg" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                
                <!-- Quick Preset Tensi -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2 text-[11px]">
                    <span class="text-slate-400">Pilihan cepat tensi:</span>
                    <button type="button" onclick="setModalTensi('110/70 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-sky-300 hover:border-sky-500">110/70</button>
                    <button type="button" onclick="setModalTensi('120/80 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-sky-300 hover:border-sky-500">120/80</button>
                    <button type="button" onclick="setModalTensi('130/85 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-sky-300 hover:border-sky-500">130/85</button>
                    <button type="button" onclick="setModalTensi('140/90 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-sky-300 hover:border-sky-500">140/90</button>
                </div>
            </div>

            <!-- 4. PILIH DOKTER KONSULTASI -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Dokter Konsultasi</label>
                <select name="id_dokter"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php 
                    mysqli_data_seek($dokter_options, 0);
                    while ($d = mysqli_fetch_assoc($dokter_options)): 
                    ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['nama_dokter']) ?> (Spesialis: <?= htmlspecialchars($d['spesialis']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- 5. TGL LAHIR & ALAMAT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" value="<?= date('Y-m-d', strtotime('-25 years')) ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Domisili</label>
                    <input type="text" name="alamat" placeholder="Kota / Alamat lengkap" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-2">
                <button type="button" onclick="closeModalTambah()" 
                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-slate-950 text-xs font-bold shadow-lg shadow-sky-500/20">
                    Simpan & Daftarkan Pasien
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT PASIEN (HANYA DOKTER & ADMIN) ================= -->
<?php if (!is_pasien()): ?>
<div id="modalEdit" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-pen-to-square text-amber-400"></i>
                <span>Edit Pasien</span>
            </h3>
            <button onclick="closeModalEdit()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="pasien_action.php?aksi=edit" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Registrasi (no_registrasi)</label>
                <input type="text" name="no_registrasi" id="edit_no_registrasi" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap Pasien (nama_pasien)</label>
                <input type="text" name="nama_pasien" id="edit_nama_pasien" required
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Keluhan Pasien (keluhan)</label>
                <textarea name="keluhan" id="edit_keluhan" rows="2"
                          class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Tekanan / Tensi Darah (tensi_darah)</label>
                <input type="text" name="tensi_darah" id="edit_tensi_darah" placeholder="Contoh: 120/80 mmHg"
                       class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Lahir (tgl_lahir)</label>
                    <input type="date" name="tgl_lahir" id="edit_tgl_lahir" required
                           class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Domisili (alamat)</label>
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
                    Perbarui Pasien
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

    function setModalTensi(val) {
        document.getElementById('modal_tensi_darah').value = val;
    }

    function openModalEdit(data) {
        if (!document.getElementById('modalEdit')) return;
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_no_registrasi').value = data.no_registrasi;
        document.getElementById('edit_nama_pasien').value = data.nama_pasien;
        document.getElementById('edit_keluhan').value = data.keluhan || '';
        document.getElementById('edit_tensi_darah').value = data.tensi_darah || '';
        document.getElementById('edit_tgl_lahir').value = data.tgl_lahir;
        document.getElementById('edit_alamat').value = data.alamat;
        document.getElementById('modalEdit').classList.remove('hidden');
    }
    function closeModalEdit() {
        if (!document.getElementById('modalEdit')) return;
        document.getElementById('modalEdit').classList.add('hidden');
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>
