        </main>

        <footer class="border-t border-slate-800 bg-slate-950/40 px-6 py-4 text-center text-xs text-slate-500">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
                <p>&copy; <?= date('Y') ?> <strong>SIM Rumah Sehat</strong> - Tugas Pemrograman Web Relasional Database</p>
                <p class="text-slate-300 font-mono text-[11px]">Tabel: pasien | dokter | obat | pemeriksaan | pemeriksaan_obat</p>
            </div>
        </footer>

    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarBackdrop.classList.remove('hidden');
            });
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
        }

        if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#14b8a6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                background: '#1e293b',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
            return false;
        }

        function confirmDelete(url, namaItem) {
            Swal.fire({
                title: 'Hapus Data?',
                text: `Data "${namaItem}" akan dihapus permanen dari sistem.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#1e293b',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
</body>
</html>

