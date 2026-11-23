<?php
$page_title = 'Input Pemeriksaan Baru';
$page_subtitle = 'Formulir rekam medis pemeriksaan fisik dan peresepan obat';
include __DIR__ . '/header.php';
require_staff();

$selected_id_pasien = (int)($_GET['id_pasien'] ?? 0);

// Ambil daftar pasien lengkap dengan keluhan dan tensi dari tabel pasien
$pasien_list = mysqli_query($koneksi, "SELECT id, no_registrasi, nama_pasien, alamat, keluhan, tensi_darah FROM pasien ORDER BY id DESC");

// Ambil daftar dokter
$dokter_list = mysqli_query($koneksi, "SELECT id, nama_dokter, spesialis FROM dokter ORDER BY nama_dokter ASC");

// Ambil daftar obat
$obat_list = mysqli_query($koneksi, "SELECT id, kode_obat, nama_obat FROM obat ORDER BY nama_obat ASC");
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
                <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Formulir Rekam Medis & Pemeriksaan</h3>
                    <p class="text-xs text-slate-400">Keluhan pasien otomatis diambil dari data pendaftaran (tanpa perlu ketik ulang)</p>
                </div>
            </div>
            <span class="text-xs bg-slate-900 border border-slate-700 text-teal-400 px-3 py-1.5 rounded-lg font-mono">
                <?= date('d M Y') ?>
            </span>
        </div>

        <form action="pemeriksaan_action.php?aksi=tambah" method="POST" class="p-6 md:p-8 space-y-6">
            
            <!-- SECTION 1: DATA PASIEN & DOKTER -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pasien -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Pilih Pasien <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <select name="id_pasien" id="id_pasien" required onchange="syncPasienData()"
                                class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">-- Pilih Pasien Terdaftar --</option>
                            <?php while ($p = mysqli_fetch_assoc($pasien_list)): 
                                $is_selected = ($selected_id_pasien > 0 && $selected_id_pasien == $p['id']);
                            ?>
                                <option value="<?= $p['id'] ?>" 
                                        data-keluhan="<?= htmlspecialchars($p['keluhan']) ?>"
                                        data-tensi="<?= htmlspecialchars($p['tensi_darah']) ?>"
                                        <?= $is_selected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama_pasien']) ?> (<?= htmlspecialchars($p['no_registrasi']) ?>) - <?= htmlspecialchars($p['alamat']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        Pasien belum terdaftar? <a href="pasien.php" class="text-teal-400 hover:underline">Tambah pasien baru dahulu</a>
                    </p>
                </div>

                <!-- Dokter -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Dokter Pemeriksa <span class="text-rose-400">*</span>
                    </label>
                    <select name="id_dokter" id="id_dokter" required
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                            <option value="<?= $d['id'] ?>" <?= ($d['id'] == $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nama_dokter']) ?> (Spesialis: <?= htmlspecialchars($d['spesialis']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">Otomatis dipilih sesuai akun dokter yang sedang login.</p>
                </div>
            </div>

            <!-- SECTION 2: TANGGAL & TENSI DARAH -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Periksa -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Tanggal Pemeriksaan <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="tgl_periksa" value="<?= date('Y-m-d') ?>" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <!-- Tensi Darah -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Tekanan / Tensi Darah (mmHg) <span class="text-rose-400">*</span>
                        </label>
                        <span id="tensiAutoBadge" class="hidden text-[10px] text-teal-400 font-medium bg-teal-500/10 border border-teal-500/20 px-2 py-0.5 rounded-md">
                            <i class="fa-solid fa-circle-check"></i> Dari data pasien
                        </span>
                    </div>
                    <div class="space-y-2">
                        <input type="text" name="tensi_darah" id="tensi_darah" placeholder="Contoh: 120/80 mmHg" required
                               class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        
                        <!-- Quick Presets -->
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="text-slate-400">Pilihan cepat:</span>
                            <button type="button" onclick="setTensi('110/70 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 hover:border-teal-500 transition-colors">110/70</button>
                            <button type="button" onclick="setTensi('120/80 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 hover:border-teal-500 transition-colors">120/80</button>
                            <button type="button" onclick="setTensi('130/85 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 hover:border-teal-500 transition-colors">130/85</button>
                            <button type="button" onclick="setTensi('140/90 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 hover:border-teal-500 transition-colors">140/90</button>
                            <button type="button" onclick="setTensi('150/95 mmHg')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-teal-300 hover:border-teal-500 transition-colors">150/95</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: KELUHAN PASIEN -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Keluhan & Anamnesa Pasien <span class="text-rose-400">*</span>
                    </label>
                    <span id="keluhanAutoBadge" class="hidden text-[11px] text-teal-400 font-medium bg-teal-500/15 border border-teal-500/30 px-2.5 py-1 rounded-md">
                        <i class="fa-solid fa-circle-check text-teal-400 mr-1"></i>
                        Data keluhan terisi otomatis dari profil pasien
                    </span>
                </div>
                <textarea name="keluhan" id="keluhan" rows="3" required
                          placeholder="Pilih pasien di atas, keluhan pasien akan otomatis terisi di sini..."
                          class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle text-teal-400"></i>
                    <span>Keluhan terisi otomatis sesuai catatan pendaftaran pasien. Dokter dapat menambahkan catatan diagnosa bila diperlukan.</span>
                </p>
            </div>

            <!-- SECTION 4: RESEP OBAT (pemeriksaan_obat) -->
            <div class="pt-4 border-t border-slate-700/80">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Resep Obat Diberikan (Tabel: pemeriksaan_obat)
                        </label>
                        <p class="text-[11px] text-slate-400">Centang obat yang diresepkan untuk pasien ini (bisa memilih lebih dari 1 obat)</p>
                    </div>
                    <span id="counterObat" class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/30 px-3 py-1 rounded-full font-semibold">
                        0 obat dipilih
                    </span>
                </div>

                <!-- Live filter obat -->
                <div class="mb-3">
                    <input type="text" id="filterObatInput" onkeyup="filterObatList()" placeholder="Ketik untuk memfilter nama obat..." 
                           class="w-full px-3.5 py-2 bg-slate-900/60 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>

                <!-- Grid Checkbox Obat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1" id="obatContainer">
                    <?php while ($ob = mysqli_fetch_assoc($obat_list)): ?>
                        <div class="obat-item p-3 rounded-xl bg-slate-900/60 hover:bg-slate-900 border border-slate-700/70 hover:border-amber-500/50 transition-all">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="obat_ids[]" value="<?= $ob['id'] ?>" onchange="toggleObatAturan(this); updateCounter()"
                                       class="w-4 h-4 rounded text-amber-500 bg-slate-800 border-slate-600 focus:ring-amber-400 focus:ring-offset-slate-900">
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-white truncate obat-nama"><?= htmlspecialchars($ob['nama_obat']) ?></div>
                                    <div class="text-[10px] text-amber-400 font-mono"><?= htmlspecialchars($ob['kode_obat']) ?></div>
                                </div>
                            </label>
                            <!-- Edit Text Aturan Pakai -->
                            <div class="mt-2.5 hidden obat-aturan-box" id="aturan_box_<?= $ob['id'] ?>">
                                <label class="block text-[10px] font-semibold text-purple-300 mb-1">Edit Aturan Pakai (Edit Text):</label>
                                <input type="text" name="aturan_pakai[<?= $ob['id'] ?>]" value="3x1 Sehari Sesudah Makan" placeholder="Contoh: 3x1 sehari sesudah makan"
                                       class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 focus:border-purple-500 rounded-lg text-white font-mono text-[11px] focus:ring-1 focus:ring-purple-400 focus:outline-none">
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
                        class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/25 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Rekam Pemeriksaan</span>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    function syncPasienData() {
        const select = document.getElementById('id_pasien');
        if (!select) return;
        const opt = select.options[select.selectedIndex];
        const elKeluhan = document.getElementById('keluhan');
        const elTensi = document.getElementById('tensi_darah');
        const badgeKeluhan = document.getElementById('keluhanAutoBadge');
        const badgeTensi = document.getElementById('tensiAutoBadge');

        if (opt && opt.value) {
            const keluhan = opt.getAttribute('data-keluhan') || '';
            const tensi = opt.getAttribute('data-tensi') || '';
            
            if (elKeluhan) {
                elKeluhan.value = keluhan;
            }
            if (elTensi && tensi) {
                elTensi.value = tensi;
            }
            if (badgeKeluhan) {
                if (keluhan) {
                    badgeKeluhan.classList.remove('hidden');
                } else {
                    badgeKeluhan.classList.add('hidden');
                }
            }
            if (badgeTensi) {
                if (tensi) {
                    badgeTensi.classList.remove('hidden');
                } else {
                    badgeTensi.classList.add('hidden');
                }
            }
        } else {
            if (badgeKeluhan) badgeKeluhan.classList.add('hidden');
            if (badgeTensi) badgeTensi.classList.add('hidden');
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

    function filterObatList() {
        const input = document.getElementById('filterObatInput').value.toLowerCase();
        const items = document.querySelectorAll('.obat-item');
        items.forEach(item => {
            const text = item.querySelector('.obat-nama').innerText.toLowerCase();
            if (text.includes(input)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Auto trigger saat halaman selesai dimuat (jika pasien sudah terpilih dari URL parameter atau default)
    document.addEventListener('DOMContentLoaded', function() {
        syncPasienData();
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
