<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';

// Cek autentikasi
check_auth();
$user = current_user();

// Tentukan menu aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'SIMRS' ?> - Sistem Informasi Rumah Sakit</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen bg-slate-900 overflow-y-auto">
        
        <!-- TOP NAVBAR -->
        <header class="sticky top-0 z-30 bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="sidebarToggle" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-slate-800 border border-slate-700">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span><?= $page_title ?? 'Dashboard' ?></span>
                    </h1>
                    <p class="text-[11px] text-slate-400 hidden sm:block"><?= $page_subtitle ?? 'SIMRS - Sistem Informasi Rumah Sakit & Pelayanan Obat' ?></p>
                </div>
            </div>

            <!-- Profile & Info -->
            <div class="flex items-center gap-3">
                <?php if (!is_pasien()): ?>
                <div class="hidden lg:flex items-center gap-2">
                    <a href="/tugasobat-main/rumah_sakit_crud/" target="_blank" 
                       class="px-2.5 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-[11px] text-slate-300 hover:text-emerald-300 border border-slate-700 flex items-center gap-1.5 transition-all" title="Buka CRUD Dokter">
                        <i class="fa-solid fa-user-doctor text-emerald-400"></i>
                        <span>CRUD Dokter</span>
                    </a>
                    <a href="/tugasobat-main/rumah_sakit_crud/resep_obat/" target="_blank" 
                       class="px-2.5 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-[11px] text-slate-300 hover:text-amber-300 border border-slate-700 flex items-center gap-1.5 transition-all" title="Buka CRUD Resep">
                        <i class="fa-solid fa-prescription-bottle-medical text-amber-400"></i>
                        <span>CRUD Resep</span>
                    </a>
                </div>
                <?php endif; ?>

                <!-- User Badge -->
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                    <?php
                        $role_bg = is_pasien() ? 'bg-sky-500/20 text-sky-400 border-sky-500/30' : (is_admin() ? 'bg-purple-500/20 text-purple-400 border-purple-500/30' : 'bg-teal-500/20 text-teal-400 border-teal-500/30');
                        $role_label = is_pasien() ? 'Pasien' : (is_admin() ? 'Admin SIMRS' : 'Dokter');
                    ?>
                    <div class="w-7 h-7 rounded-lg <?= $role_bg ?> border flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr($user['nama_user'], 0, 1)) ?>
                    </div>
                    <div class="text-left text-xs hidden sm:block">
                        <div class="font-semibold text-white leading-tight"><?= htmlspecialchars($user['nama_user']) ?></div>
                        <div class="text-[10px] <?= is_pasien() ? 'text-sky-400' : 'text-teal-400' ?> font-semibold">
                            <?= $role_label ?> <?= !empty($user['spesialis']) ? '&bull; ' . htmlspecialchars($user['spesialis']) : '' ?>
                        </div>
                    </div>
                </div>

                <a href="logout.php" onclick="return confirmLogout(event)" 
                   class="px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-semibold flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="hidden md:inline">Keluar</span>
                </a>
            </div>
        </header>

        <!-- TOP QUICK NAVIGATION BAR -->
        <nav class="bg-slate-950/80 border-b border-slate-800/80 px-6 py-2.5 flex items-center justify-between overflow-x-auto gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-hospital text-teal-400"></i>
                    <span class="hidden sm:inline">Navigasi:</span>
                </span>

                <!-- 1. Tombol Pasien (Semua Role Bisa Akses) -->
                <a href="pasien.php" 
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all <?= ($current_page == 'pasien.php' || str_contains($current_page, 'pasien_')) ? 'bg-sky-500 text-slate-950 font-bold shadow-md shadow-sky-500/25' : 'bg-slate-800/90 hover:bg-slate-800 text-sky-300 border border-slate-700/80' ?>">
                    <i class="fa-solid fa-hospital-user text-xs"></i>
                    <span>Pasien</span>
                </a>

                <!-- 2. Tombol Dokter (Semua Role Bisa Akses, Pasien hanya melihat) -->
                <a href="dokter.php" 
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all <?= ($current_page == 'dokter.php' || str_contains($current_page, 'dokter_')) ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/25' : 'bg-slate-800/90 hover:bg-slate-800 text-emerald-300 border border-slate-700/80' ?>">
                    <i class="fa-solid fa-user-doctor text-xs"></i>
                    <span>Dokter</span>
                </a>

                <?php if (!is_pasien()): ?>
                    <!-- 3. Tombol Obat (Hanya Dokter & Admin) -->
                    <a href="obat.php" 
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all <?= ($current_page == 'obat.php' || str_contains($current_page, 'obat_')) ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/25' : 'bg-slate-800/90 hover:bg-slate-800 text-amber-300 border border-slate-700/80' ?>">
                        <i class="fa-solid fa-pills text-xs"></i>
                        <span>Obat</span>
                    </a>

                    <!-- 4. Tombol Pemeriksaan (Hanya Dokter & Admin) -->
                    <a href="pemeriksaan.php" 
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all <?= (str_contains($current_page, 'pemeriksaan')) ? 'bg-teal-500 text-slate-950 font-bold shadow-md shadow-teal-500/25' : 'bg-slate-800/90 hover:bg-slate-800 text-teal-300 border border-slate-700/80' ?>">
                        <i class="fa-solid fa-stethoscope text-xs"></i>
                        <span>Pemeriksaan</span>
                    </a>
                <?php else: ?>
                    <span class="text-[11px] text-slate-400 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg italic">
                        <i class="fa-solid fa-lock text-[10px] text-amber-400 mr-1"></i>Menu Obat & Pemeriksaan hanya dapat diakses oleh Dokter / Admin
                    </span>
                <?php endif; ?>
            </div>

            <!-- Role Indicator -->
            <div class="text-[11px] text-slate-400 hidden md:flex items-center gap-2">
                <span class="w-2 h-2 rounded-full <?= is_pasien() ? 'bg-sky-400' : (is_admin() ? 'bg-purple-400' : 'bg-emerald-400') ?> animate-pulse"></span>
                <span>Role: <strong class="text-white"><?= strtoupper($user['role']) ?></strong></span>
            </div>
        </nav>

        <!-- MAIN BODY CONTAINER -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
            
            <?php 
            $flash = get_flash();
            if ($flash): 
                $alert_colors = [
                    'success' => 'bg-emerald-500/15 border-emerald-500/40 text-emerald-300',
                    'danger'  => 'bg-rose-500/15 border-rose-500/40 text-rose-300',
                    'warning' => 'bg-amber-500/15 border-amber-500/40 text-amber-300',
                    'info'    => 'bg-sky-500/15 border-sky-500/40 text-sky-300',
                ];
                $color = $alert_colors[$flash['type']] ?? $alert_colors['info'];
                $icon = [
                    'success' => 'fa-circle-check',
                    'danger'  => 'fa-circle-xmark',
                    'warning' => 'fa-triangle-exclamation',
                    'info'    => 'fa-circle-info',
                ][$flash['type']] ?? 'fa-circle-info';
            ?>
                <div class="mb-6 p-4 rounded-xl border <?= $color ?> flex items-center gap-3 animate-fade-in shadow-lg">
                    <i class="fa-solid <?= $icon ?> text-xl"></i>
                    <span class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>
