<?php
$page_title = 'Edit Pemeriksaan';
$page_subtitle = 'Perbarui data rekam medis dan resep obat yang diberikan';
include __DIR__ . '/header.php';
require_staff();

$id = (int)($_GET['id'] ?? 0);
$q = mysqli_query($koneksi, "SELECT * FROM pemeriksaan WHERE id = $id");
if (!$q || mysqli_num_rows($q) == 0) {
    set_flash('danger', 'Data pemeriksaan tidak ditemukan.');
    header("Location: pemeriksaan.php");
    exit;
}
$periksa = mysqli_fetch_assoc($q);

// Ambil ID obat & aturan pakai yang saat ini sudah diresepkan untuk pemeriksaan ini
$q_selected_obat = mysqli_query($koneksi, "SELECT id_id_obat, aturan_pakai FROM pemeriksaan_obat WHERE id_pemeriksaan = $id");
$selected_obat_ids = [];
$selected_obat_aturan = [];
while ($row = mysqli_fetch_assoc($q_selected_obat)) {
    $selected_obat_ids[] = $row['id_id_obat'];
    $selected_obat_aturan[$row['id_id_obat']] = $row['aturan_pakai'];
}

// Data relasi
$pasien_list = mysqli_query($koneksi, "SELECT id, no_registrasi, nama_pasien, alamat, keluhan, tensi_darah FROM pasien ORDER BY nama_pasien ASC");
$dokter_list = mysqli_query($koneksi, "SELECT id, nama_dokter, spesialis FROM dokter ORDER BY nama_dokter ASC");
$obat_list   = mysqli_query($koneksi, "SELECT id, kode_obat, nama_obat FROM obat ORDER BY nama_obat ASC");
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Back Button -->
    <div>
        <a href="pemeriksaan.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Pemeriksaan</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl shadow-xl overflow-hidden">
        
        <div class="p-6 border-b border-slate-700/80 bg-slate-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Edit Rekam Pemeriksaan #<?= $periksa['id'] ?></h3>
                    <p class="text-xs text-slate-400">Ubah diagnosis medis atau perbarui resep obat</p>
                </div>
            </div>
            <span class="text-xs bg-slate-900 border border-slate-700 text-amber-400 px-3 py-1.5 rounded-lg font-mono">
                ID: #<?= $periksa['id'] ?>
            </span>
        </div>

        <form action="pemeriksaan_action.php?aksi=edit" method="POST" class="p-6 md:p-8 space-y-6">
            <input type="hidden" name="id" value="<?= $periksa['id'] ?>">
            
            <!-- SECTION 1: DATA PASIEN & DOKTER -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pasien -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Pasien <span class="text-rose-400">*</span>
                    </label>
                    <select name="id_pasien" id="id_pasien" required onchange="syncEditPasien()"
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <?php while ($p = mysqli_fetch_assoc($pasien_list)): ?>
                            <option value="<?= $p['id'] ?>" 
                                    data-keluhan="<?= htmlspecialchars($p['keluhan']) ?>"
                                    data-tensi="<?= htmlspecialchars($p['tensi_darah']) ?>"
                                    <?= ($p['id'] == $periksa['id_pasien']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_pasien']) ?> (<?= htmlspecialchars($p['no_registrasi']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dokter -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Dokter Pemeriksa <span class="text-rose-400">*</span>
                    </label>
                    <select name="id_dokter" id="id_dokter" required
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                            <option value="<?= $d['id'] ?>" <?= ($d['id'] == $periksa['id_dokter']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nama_dokter']) ?> (Spesialis: <?= htmlspecialchars($d['spesialis']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <!-- SECTION 2: TANGGAL & TENSI DARAH -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Periksa -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Tanggal Pemeriksaan <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="tgl_periksa" value="<?= $periksa['tgl_periksa'] ?>" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Tensi Darah -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Tekanan / Tensi Darah <span class="text-rose-400">*</span>
                    </label>
                    <div class="space-y-2">
                        <input type="text" name="tensi_darah" id="tensi_darah" value="<?= htmlspecialchars($periksa['tensi_darah']) ?>" required
                               class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        
                        <!-- Quick Presets -->
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="text-slate-400">Pilihan:</span>
                            <button type="button" onclick="setTensi('110/70 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">110/70</button>
                            <button type="button" onclick="setTensi('120/80 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">120/80</button>
                            <button type="button" onclick="setTensi('130/85 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">130/85</button>
                            <button type="button" onclick="setTensi('140/90 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 hover:border-amber-500">140/90</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: KELUHAN PASIEN -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Keluhan Pasien <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-[11px] text-teal-400 font-medium bg-teal-500/15 border border-teal-500/30 px-2.5 py-0.5 rounded-md">
                        <i class="fa-solid fa-circle-check text-teal-400 mr-1"></i>
                        Dari data pasien
                    </span>
                </div>
                <textarea name="keluhan" id="keluhan" rows="3" required
                          class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"><?= htmlspecialchars($periksa['keluhan']) ?></textarea>
            </div>

            <!-- SECTION 4: RESEP OBAT (pemeriksaan_obat) -->
            <div class="pt-4 border-t border-slate-700/80">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Resep Obat Terkait (Tabel: pemeriksaan_obat)
                        </label>
                        <p class="text-[11px] text-slate-400">Perbarui centang obat yang diresepkan untuk pemeriksaan ini</p>
                    </div>
                    <span id="counterObat" class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/30 px-3 py-1 rounded-full font-semibold">
                        <?= count($selected_obat_ids) ?> obat dipilih
                    </span>
                </div>

                <!-- Grid Checkbox Obat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                    <?php while ($ob = mysqli_fetch_assoc($obat_list)): 
                        $isChecked = in_array($ob['id'], $selected_obat_ids);
                        $aturanVal = $selected_obat_aturan[$ob['id']] ?? '3x1 Sehari Sesudah Makan';
                    ?>
                        <div class="p-3 rounded-xl bg-slate-900/60 hover:bg-slate-900 border <?= $isChecked ? 'border-amber-500 bg-amber-500/5' : 'border-slate-700/70' ?> transition-all">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="obat_ids[]" value="<?= $ob['id'] ?>" <?= $isChecked ? 'checked' : '' ?> onchange="toggleObatAturan(this); updateCounter()"
                                       class="w-4 h-4 rounded text-amber-500 bg-slate-800 border-slate-600 focus:ring-amber-400 focus:ring-offset-slate-900">
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-white truncate"><?= htmlspecialchars($ob['nama_obat']) ?></div>
                                    <div class="text-[10px] text-amber-400 font-mono"><?= htmlspecialchars($ob['kode_obat']) ?></div>
                                </div>
                            </label>
                            <!-- Aturan Pakai Input -->
                            <div class="mt-2.5 <?= $isChecked ? '' : 'hidden' ?> obat-aturan-box" id="aturan_box_<?= $ob['id'] ?>">
                                <label class="block text-[10px] font-semibold text-amber-300 mb-1">Aturan Pakai / Dosis:</label>
                                <input type="text" name="aturan_pakai[<?= $ob['id'] ?>]" value="<?= htmlspecialchars($aturanVal) ?>" placeholder="Contoh: 3x1 sehari sesudah makan"
                                       class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-lg text-white font-mono text-[11px] focus:ring-1 focus:ring-amber-400 focus:outline-none">
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="pt-6 border-t border-slate-700/80 flex items-center justify-end gap-3">
                <a href="pemeriksaan.php" 
                   class="px-5 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/25 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    function syncEditPasien() {
        const select = document.getElementById('id_pasien');
        if (!select) return;
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            const keluhan = opt.getAttribute('data-keluhan') || '';
            const tensi = opt.getAttribute('data-tensi') || '';
            if (keluhan) {
                document.getElementById('keluhan').value = keluhan;
            }
            if (tensi) {
                document.getElementById('tensi_darah').value = tensi;
            }
        }
    }

    function toggleObatAturan(checkbox) {
        const box = document.getElementById('aturan_box_' + checkbox.value);
        if (box) {
            if (checkbox.checked) {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
        }
    }

    function setTensi(val) {
        document.getElementById('tensi_darah').value = val;
    }

    function updateCounter() {
        const checked = document.querySelectorAll('input[name="obat_ids[]"]:checked').length;
        document.getElementById('counterObat').innerText = checked + ' obat dipilih';
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>

