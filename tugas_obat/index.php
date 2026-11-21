<?php
$page_title = 'Dashboard SIMRS';
$page_subtitle = 'Sistem Informasi Rumah Sakit & Pelayanan Rekam Medis Obat';
include __DIR__ . '/header.php';

// Hitung total statistik
$total_pasien = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pasien"))['jml'] ?? 0;
$total_dokter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM dokter"))['jml'] ?? 0;
$total_obat   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM obat"))['jml'] ?? 0;
$total_periksa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pemeriksaan"))['jml'] ?? 0;

// Query 5 Pemeriksaan Terbaru
$query_terbaru = "
    SELECT 
        p.id, 
        p.tgl_periksa, 
        p.keluhan, 
        p.tensi_darah,
        pas.nama_pasien, 
        pas.no_registrasi,
        dok.nama_dokter, 
        dok.spesialis,
        GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS daftar_obat,
        COUNT(po.id) AS jumlah_obat
    FROM pemeriksaan p
    JOIN pasien pas ON p.id_pasien = pas.id
    JOIN dokter dok ON p.id_dokter = dok.id
    LEFT JOIN pemeriksaan_obat po ON p.id = po.id_pemeriksaan
    LEFT JOIN obat o ON po.id_id_obat = o.id
    GROUP BY p.id
    ORDER BY p.tgl_periksa DESC, p.id DESC
    LIMIT 5
";
$res_terbaru = mysqli_query($koneksi, $query_terbaru);

// Query Obat Terlaris
$query_obat_populer = "
    SELECT 
        o.kode_obat, 
        o.nama_obat, 
        COUNT(po.id) AS total_diresepkan
    FROM obat o
    LEFT JOIN pemeriksaan_obat po ON o.id = po.id_id_obat
    GROUP BY o.id
    ORDER BY total_diresepkan DESC
    LIMIT 4
";
$res_obat_populer = mysqli_query($koneksi, $query_obat_populer);

// Daftar 4 Dokter Terbaru untuk view Pasien
$res_dokter_info = mysqli_query($koneksi, "SELECT nama_dokter, spesialis, jenis FROM dokter ORDER BY id ASC LIMIT 4");
?>

<!-- BANNER SELAMAT DATANG (SESUAI WHITEBOARD: "SIMRS - Selamat datang...") -->
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-600 via-emerald-700 to-slate-900 p-6 md:p-8 text-white shadow-xl shadow-teal-900/30 mb-8 border border-teal-500/30">
    <div class="relative z-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold mb-3">
            <i class="fa-solid fa-hospital-user"></i>
            <span>SIMRS &bull; <?= is_pasien() ? 'Portal Layanan Pasien Mandiri' : 'Portal Manajemen Rumah Sakit' ?></span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Selamat Datang, <?= htmlspecialchars($user['nama_user']) ?>!</h2>
        
        <?php if (is_pasien()): ?>
            <p class="text-teal-100 text-sm mt-2 leading-relaxed">
                Anda masuk sebagai <strong class="text-white underline">Pasien</strong>. Silakan lakukan pendaftaran pasien baru dan konsultasikan keluhan serta tekanan darah Anda, atau periksa jadwal dokter spesialis kami.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="pasien.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-950 font-bold text-xs shadow-lg hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-user-plus text-sky-600"></i>
                    <span>Daftar Pasien Baru (Isi Keluhan & Tensi)</span>
                </a>
                <a href="dokter.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all backdrop-blur-sm border border-white/20">
                    <i class="fa-solid fa-user-doctor"></i>
                    <span>Lihat Daftar Dokter</span>
                </a>
            </div>
        <?php else: ?>
            <p class="text-teal-100 text-sm mt-2 leading-relaxed">
                Sistem rekam medis dan peresepan obat terintegrasi. Anda memiliki hak akses penuh sebagai <strong class="text-white underline"><?= strtoupper($user['role']) ?> (<?= htmlspecialchars($user['spesialis']) ?>)</strong>.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="pemeriksaan_tambah.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-950 text-teal-400 font-bold text-xs shadow-lg transition-all">
                    <i class="fa-solid fa-stethoscope"></i>
                    <span>Periksa Pasien Baru</span>
                </a>
                <a href="pasien.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all backdrop-blur-sm">
                    <i class="fa-solid fa-users"></i>
                    <span>Pasien</span>
                </a>
                <a href="obat.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all backdrop-blur-sm">
                    <i class="fa-solid fa-pills"></i>
                    <span>Obat</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Ilustrasi / Gambar Sesuai Sketsa Whiteboard ("Gambar...") -->
    <div class="absolute -right-4 -bottom-6 text-white/10 text-9xl pointer-events-none select-none">
        <i class="fa-solid fa-heart-pulse"></i>
    </div>
</div>

<!-- MODUL PELAYANAN UTAMA SIMRS -->
<div class="mb-8">
    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
        <span>Menu Pelayanan SIMRS</span>
        <span class="text-[11px] text-teal-400 font-normal">Pilih modul di bawah untuk mulai</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 <?= is_pasien() ? 'lg:grid-cols-2' : 'lg:grid-cols-4' ?> gap-5">
        <!-- 1. Pasien Card -->
        <a href="pasien.php" class="group block p-5 rounded-2xl bg-slate-800/80 border border-slate-700 hover:border-sky-500/50 shadow-lg hover:shadow-sky-500/10 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pasien</span>
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($total_pasien) ?></span>
                <span class="text-xs text-sky-400 font-medium flex items-center gap-1">
                    <span><?= is_pasien() ? '+ Tambah Pasien' : 'Kelola Pasien' ?></span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Pendaftaran & rekam keluhan pasien</p>
        </a>

        <!-- 2. Dokter Card -->
        <a href="dokter.php" class="group block p-5 rounded-2xl bg-slate-800/80 border border-slate-700 hover:border-emerald-500/50 shadow-lg hover:shadow-emerald-500/10 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokter</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($total_dokter) ?></span>
                <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                    <span><?= is_pasien() ? 'Lihat Dokter' : 'Kelola Dokter' ?></span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2"><?= is_pasien() ? 'Lihat jadwal dokter yang bertugas' : 'Data staf medis & spesialisasi' ?></p>
        </a>

        <?php if (!is_pasien()): ?>
        <!-- 3. Obat Card (Hanya Dokter & Admin) -->
        <a href="obat.php" class="group block p-5 rounded-2xl bg-slate-800/80 border border-slate-700 hover:border-amber-500/50 shadow-lg hover:shadow-amber-500/10 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Obat</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-pills"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($total_obat) ?></span>
                <span class="text-xs text-amber-400 font-medium flex items-center gap-1">
                    <span>Daftar Obat</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Katalog & ketersediaan farmasi</p>
        </a>

        <!-- 4. Pemeriksaan Card (Hanya Dokter & Admin) -->
        <a href="pemeriksaan.php" class="group block p-5 rounded-2xl bg-slate-800/80 border border-slate-700 hover:border-teal-500/50 shadow-lg hover:shadow-teal-500/10 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemeriksaan</span>
                <div class="w-10 h-10 rounded-xl bg-teal-500/15 text-teal-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-medical"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($total_periksa) ?></span>
                <span class="text-xs text-teal-400 font-medium flex items-center gap-1">
                    <span>Semua Rekam</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Rekam klinis, tensi & resep obat</p>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (is_pasien()): ?>
    <!-- KONTEN KHUSUS PASIEN: Informasi Dokter & Petunjuk Pendaftaran -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Box 1: Daftar Dokter Spesialis -->
        <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-doctor text-emerald-400"></i>
                    <span>Daftar Tim Dokter yang Bertugas</span>
                </h3>
                <a href="dokter.php" class="text-xs text-emerald-400 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="space-y-3">
                <?php while ($d = mysqli_fetch_assoc($res_dokter_info)): ?>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold">
                                <?= strtoupper(substr($d['nama_dokter'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-bold text-white"><?= htmlspecialchars($d['nama_dokter']) ?></div>
                                <div class="text-[11px] text-emerald-400"><?= htmlspecialchars($d['spesialis']) ?></div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-300 text-[10px] font-semibold">Tersedia</span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Box 2: Panduan Pasien -->
        <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-circle-info text-sky-400"></i>
                    <span>Petunjuk Pendaftaran Pasien</span>
                </h3>
                <ol class="text-xs text-slate-300 space-y-3 list-decimal list-inside leading-relaxed">
                    <li>Buka menu <strong class="text-sky-300">Pasien</strong> pada bilah navigasi di atas.</li>
                    <li>Klik tombol <strong class="text-white bg-sky-600 px-2 py-0.5 rounded text-[11px]">+ Tambah Pasien Baru</strong>.</li>
                    <li>Isi formulir lengkap: <strong class="text-white">Nama Pasien, Keluhan, dan Tekanan / Tensi Darah</strong>.</li>
                    <li>Data Anda akan langsung tersimpan dan diteruskan ke meja dokter pemeriksa.</li>
                </ol>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-700">
                <a href="pasien.php" class="w-full py-2.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-hospital-user"></i>
                    <span>Menuju Halaman Pendaftaran Pasien</span>
                </a>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- KONTEN UNTUK DOKTER & ADMIN: Rekam Medis & Obat Populer -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Pemeriksaan Terbaru -->
        <div class="lg:col-span-2 bg-slate-800/70 border border-slate-700/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-teal-400"></i>
                        <span>Rekam Medis & Pemeriksaan Terbaru</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">5 aktivitas pemeriksaan pasien terakhir</p>
                </div>
                <a href="pemeriksaan.php" class="text-xs font-semibold text-teal-400 hover:text-teal-300">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-700 text-slate-400 uppercase text-[11px] font-semibold">
                            <th class="py-3 px-3">Pasien</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Keluhan & Tensi</th>
                            <th class="py-3 px-3">Obat Diberikan</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php if (mysqli_num_rows($res_terbaru) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($res_terbaru)): ?>
                                <tr class="hover:bg-slate-750 transition-colors">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-white"><?= htmlspecialchars($row['nama_pasien']) ?></div>
                                        <span class="text-[10px] text-teal-400 font-mono"><?= htmlspecialchars($row['no_registrasi']) ?></span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap text-slate-300">
                                        <?= date('d/m/Y', strtotime($row['tgl_periksa'])) ?>
                                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($row['nama_dokter']) ?></div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-slate-200 line-clamp-1"><?= htmlspecialchars($row['keluhan']) ?></div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-[10px] font-mono text-teal-300">
                                            <?= htmlspecialchars($row['tensi_darah']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <?php if (!empty($row['daftar_obat'])): ?>
                                            <div class="text-slate-300 line-clamp-1" title="<?= htmlspecialchars($row['daftar_obat']) ?>">
                                                <i class="fa-solid fa-capsules text-amber-400 mr-1"></i>
                                                <?= htmlspecialchars($row['daftar_obat']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-500 italic">Tanpa obat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <a href="pemeriksaan_detail.php?id=<?= $row['id'] ?>" 
                                           class="p-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[11px] inline-flex items-center gap-1">
                                            <i class="fa-regular fa-eye"></i>
                                            <span>Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-notes-medical text-3xl mb-2 text-slate-600 block"></i>
                                    Belum ada riwayat pemeriksaan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Obat Populer -->
        <div class="space-y-6">
            <div class="bg-slate-800/70 border border-slate-700/80 rounded-2xl p-6 shadow-xl">
                <h3 class="text-base font-bold text-white flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-prescription text-amber-400"></i>
                    <span>Obat Sering Diresepkan</span>
                </h3>
                
                <div class="space-y-3">
                    <?php while ($ob = mysqli_fetch_assoc($res_obat_populer)): ?>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-pills"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-white"><?= htmlspecialchars($ob['nama_obat']) ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($ob['kode_obat']) ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded-full bg-slate-800 text-teal-400 border border-slate-700 font-semibold text-[11px]">
                                    <?= $ob['total_diresepkan'] ?>x
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-700/60 text-center">
                    <a href="obat.php" class="text-xs text-teal-400 hover:underline">Kelola Katalog Obat &rarr;</a>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
