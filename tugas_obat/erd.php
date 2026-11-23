<?php
$page_title = 'Diagram ERD Database';
$page_subtitle = 'Visualisasi skema relasi database sesuai diagram tugas';
include __DIR__ . '/header.php';
require_staff();
?>

<div class="space-y-8">

    <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-diagram-project text-teal-400"></i>
                <span>Relasi 5 Tabel Sesuai Spesifikasi Database</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Aplikasi ini mengimplementasikan penuh skema Entity Relationship Diagram (ERD): Pasien, Dokter, Obat, Pemeriksaan, dan Pemeriksaan Obat.
            </p>
        </div>
        <a href="database.sql" download class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-slate-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-teal-500/20">
            <i class="fa-solid fa-download"></i>
            <span>Unduh database.sql</span>
        </a>
    </div>

    <div class="p-6 md:p-8 rounded-2xl bg-slate-950 border border-slate-800 shadow-2xl overflow-x-auto">
        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-400 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-nodes"></i>
            <span>Visualisasi ERD (Mermaid Render)</span>
        </h3>
        <div class="mermaid flex justify-center">
erDiagram
    PASIEN ||--o{ PEMERIKSAAN : "memiliki rekam"
    DOKTER ||--o{ PEMERIKSAAN : "memeriksa"
    PEMERIKSAAN ||--o{ PEMERIKSAAN_OBAT : "memuat resep"
    OBAT ||--o{ PEMERIKSAAN_OBAT : "diresepkan dalam"

    PASIEN {
        int id PK "AI"
        varchar no_registrasi
        varchar nama_pasien
        varchar alamat
        date tgl_lahir
    }

    DOKTER {
        int id PK "AI"
        varchar username
        varchar password
        varchar nama_dokter
        varchar alamat
        date tgl_lahir
        varchar jenis
        varchar spesialis
    }

    OBAT {
        int id PK "AI"
        varchar kode_obat
        varchar nama_obat
    }

    PEMERIKSAAN {
        int id PK "AI"
        int id_pasien FK
        int id_dokter FK
        date tgl_periksa
        varchar keluhan
        varchar tensi_darah
    }

    PEMERIKSAAN_OBAT {
        int id PK "AI"
        int id_pemeriksaan FK
        int id_id_obat FK
    }
        </div>
    </div>

    <!-- TABLE CARDS DISPLAY (MIRRORING USER'S SCREENSHOT STYLE) -->
    <div>
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-table-cells text-amber-400"></i>
            <span>Struktur Field & Tipe Data Tabel</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Table: pasien -->
            <div class="rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shadow-lg">
                <div class="bg-slate-800 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="font-bold text-sm text-sky-400 font-mono">pasien</span>
                    <span class="text-[10px] bg-sky-500/10 text-sky-300 border border-sky-500/30 px-2 py-0.5 rounded">Master Data</span>
                </div>
                <div class="p-3 text-xs font-mono space-y-1.5 text-slate-300">
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-bold">
                        <span>id</span>
                        <span>int(5) AI PK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>no_registrasi</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>nama_pasien</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>alamat</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>tgl_lahir</span>
                        <span class="text-slate-500">date</span>
                    </div>
                </div>
            </div>

            <!-- Table: dokter -->
            <div class="rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shadow-lg">
                <div class="bg-slate-800 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="font-bold text-sm text-emerald-400 font-mono">dokter</span>
                    <span class="text-[10px] bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded">Master Data</span>
                </div>
                <div class="p-3 text-xs font-mono space-y-1.5 text-slate-300">
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-bold">
                        <span>id</span>
                        <span>int(5) AI PK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>username</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>password</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>nama_dokter</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>alamat</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>tgl_lahir</span>
                        <span class="text-slate-500">date</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>jenis</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>spesialis</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                </div>
            </div>

            <!-- Table: obat -->
            <div class="rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shadow-lg">
                <div class="bg-slate-800 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="font-bold text-sm text-amber-400 font-mono">obat</span>
                    <span class="text-[10px] bg-amber-500/10 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded">Master Data</span>
                </div>
                <div class="p-3 text-xs font-mono space-y-1.5 text-slate-300">
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-bold">
                        <span>id</span>
                        <span>int(5) AI PK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>kode_obat</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>nama_obat</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                </div>
            </div>

            <!-- Table: pemeriksaan -->
            <div class="rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shadow-lg">
                <div class="bg-slate-800 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="font-bold text-sm text-teal-400 font-mono">pemeriksaan</span>
                    <span class="text-[10px] bg-teal-500/10 text-teal-300 border border-teal-500/30 px-2 py-0.5 rounded">Transaksi Medis</span>
                </div>
                <div class="p-3 text-xs font-mono space-y-1.5 text-slate-300">
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-bold">
                        <span>id</span>
                        <span>int(5) AI PK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-sky-400 font-semibold">
                        <span>id_pasien</span>
                        <span>int(5) FK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-emerald-400 font-semibold">
                        <span>id_dokter</span>
                        <span>int(5) FK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>tgl_periksa</span>
                        <span class="text-slate-500">date</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1">
                        <span>keluhan</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>tensi_darah</span>
                        <span class="text-slate-500">vc(255)</span>
                    </div>
                </div>
            </div>

            <!-- Table: pemeriksaan_obat -->
            <div class="rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shadow-lg">
                <div class="bg-slate-800 px-4 py-2.5 border-b border-slate-700 flex items-center justify-between">
                    <span class="font-bold text-sm text-purple-400 font-mono">pemeriksaan_obat</span>
                    <span class="text-[10px] bg-purple-500/10 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded">Junction (Resep)</span>
                </div>
                <div class="p-3 text-xs font-mono space-y-1.5 text-slate-300">
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-bold">
                        <span>id</span>
                        <span>int(5) AI PK</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-1 text-teal-400 font-semibold">
                        <span>id_pemeriksaan</span>
                        <span>int(5) FK</span>
                    </div>
                    <div class="flex justify-between text-amber-400 font-semibold">
                        <span>id_id_obat</span>
                        <span>int(5) FK</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Mermaid JS CDN -->
<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({
        startOnLoad: true,
        theme: 'dark',
        themeVariables: {
            darkMode: true,
            background: '#020617',
            primaryColor: '#1e293b',
            primaryTextColor: '#f8fafc',
            primaryBorderColor: '#334155',
            lineColor: '#14b8a6',
            secondaryColor: '#0f172a',
            tertiaryColor: '#1e293b'
        }
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>

