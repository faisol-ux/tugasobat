<?php
// sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Mobile overlay backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 hidden md:hidden"></div>

<aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-slate-800 flex flex-col transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">
    <!-- Brand / Header -->
    <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-notes-medical text-lg"></i>
            </div>
            <div>
                <span class="font-extrabold text-white text-base tracking-tight block">SIMRS</span>
                <span class="text-[10px] text-teal-400 font-semibold tracking-wider uppercase block">Portal Rumah Sakit</span>
            </div>
        </a>
        <button id="sidebarClose" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 space-y-6 overflow-y-auto text-sm">
        
        <!-- Menu Utama -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 px-3 mb-2">Menu Utama</div>
            <ul class="space-y-1">
                <li>
                    <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'index.php') ? 'bg-teal-500 text-slate-950 font-bold shadow-lg shadow-teal-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-base"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Data Master -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 px-3 mb-2">Data Master</div>
            <ul class="space-y-1">
                <!-- 1. Pasien (Bisa diakses Pasien, Dokter, Admin) -->
                <li>
                    <a href="pasien.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'pasien.php' || str_contains($current, 'pasien_')) ? 'bg-sky-500 text-slate-950 font-bold shadow-lg shadow-sky-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-hospital-user w-5 text-center text-base text-sky-400"></i>
                        <span>Pasien</span>
                    </a>
                </li>
                <!-- 2. Dokter (Bisa diakses semua, Pasien read-only) -->
                <li>
                    <a href="dokter.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'dokter.php' || str_contains($current, 'dokter_')) ? 'bg-emerald-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-user-doctor w-5 text-center text-base text-emerald-400"></i>
                        <span>Dokter</span>
                    </a>
                </li>

                <?php if (!is_pasien()): ?>
                <!-- 3. Obat (Hanya Dokter & Admin) -->
                <li>
                    <a href="obat.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'obat.php' || str_contains($current, 'obat_')) ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-pills w-5 text-center text-base text-amber-400"></i>
                        <span>Obat</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <?php if (!is_pasien()): ?>
        <!-- Pelayanan & Resep (Hanya Dokter & Admin) -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 px-3 mb-2">Pelayanan Klinis</div>
            <ul class="space-y-1">
                <li>
                    <a href="pemeriksaan.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'pemeriksaan.php' || $current == 'pemeriksaan_tambah.php' || $current == 'pemeriksaan_edit.php' || $current == 'pemeriksaan_detail.php') ? 'bg-teal-500 text-slate-950 font-bold shadow-lg shadow-teal-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-stethoscope w-5 text-center text-base text-teal-400"></i>
                        <span>Pemeriksaan</span>
                    </a>
                </li>
                <li>
                    <a href="resep.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'resep.php') ? 'bg-teal-500 text-slate-950 font-bold shadow-lg shadow-teal-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-prescription-bottle-medical w-5 text-center text-base text-purple-400"></i>
                        <span>Resep (Pemeriksaan Obat)</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Skema Database -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 px-3 mb-2">Referensi</div>
            <ul class="space-y-1">
                <li>
                    <a href="erd.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-medium <?= ($current == 'erd.php') ? 'bg-teal-500 text-slate-950 font-bold shadow-lg shadow-teal-500/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-diagram-project w-5 text-center text-base"></i>
                        <span>Diagram ERD Database</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Aplikasi Terintegrasi -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 px-3 mb-2">Aplikasi Terintegrasi</div>
            <ul class="space-y-1">
                <li>
                    <a href="/tugasobat-main/rumah_sakit_crud/" target="_blank" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all text-slate-300 hover:bg-slate-800 hover:text-teal-300 font-medium">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-doctor w-5 text-center text-base text-emerald-400"></i>
                            <span>CRUD Dokter</span>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
                    </a>
                </li>
                <li>
                    <a href="/tugasobat-main/rumah_sakit_crud/resep_obat/" target="_blank" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all text-slate-300 hover:bg-slate-800 hover:text-amber-300 font-medium">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-prescription-bottle-medical w-5 text-center text-base text-amber-400"></i>
                            <span>CRUD Resep Obat</span>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

    </nav>

    <!-- Footer Profile in Sidebar -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/60">
        <div class="flex items-center justify-between text-xs text-slate-400">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>DB: rumah sakit</span>
            </span>
            <span class="font-mono text-[10px] text-slate-300"><?= strtoupper($user['role'] ?? 'USER') ?></span>
        </div>
    </div>
</aside>
