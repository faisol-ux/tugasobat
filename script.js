'use strict';

/* ============================================================
   1. Data contoh (hanya di sisi frontend)
   ============================================================ */
const seed = {
  pasien: [
    { id: 1, no_registrasi: 'PSN-2026-001', nama_pasien: 'Andi Pratama', alamat: 'Surabaya', tgl_lahir: '1998-04-12' },
    { id: 2, no_registrasi: 'PSN-2026-002', nama_pasien: 'Siti Rahmawati', alamat: 'Sidoarjo', tgl_lahir: '1987-09-23' },
    { id: 3, no_registrasi: 'PSN-2026-003', nama_pasien: 'Budi Santoso', alamat: 'Gresik', tgl_lahir: '2001-01-08' },
    { id: 4, no_registrasi: 'PSN-2026-004', nama_pasien: 'Dewi Anggraini', alamat: 'Surabaya', tgl_lahir: '1995-11-17' }
  ],
  dokter: [
    { id: 1, username: 'dr.rahman', password: '123', nama_dokter: 'dr. Rahman Putra', alamat: 'Surabaya', tgl_lahir: '1980-03-15', jenis: 'Laki-laki', spesialis: 'Penyakit Dalam' },
    { id: 2, username: 'dr.melati', password: '123', nama_dokter: 'dr. Melati Sari', alamat: 'Sidoarjo', tgl_lahir: '1986-07-20', jenis: 'Perempuan', spesialis: 'Anak' },
    { id: 3, username: 'dr.hendra', password: '123', nama_dokter: 'dr. Hendra Wijaya', alamat: 'Surabaya', tgl_lahir: '1978-12-02', jenis: 'Laki-laki', spesialis: 'Umum' }
  ],
  pemeriksaan: [
    { id: 1, id_pasien: 1, id_dokter: 1, tgl_periksa: '2026-10-26', keluhan: 'Demam dan pusing', tensi_darah: '120/80 mmHg' },
    { id: 2, id_pasien: 2, id_dokter: 2, tgl_periksa: '2026-10-26', keluhan: 'Kontrol kesehatan', tensi_darah: '110/70 mmHg' },
    { id: 3, id_pasien: 3, id_dokter: 3, tgl_periksa: '2026-10-25', keluhan: 'Batuk dan pilek', tensi_darah: '118/78 mmHg' }
  ],
  obat: [
    { id: 1, kode_obat: 'OBT-001', nama_obat: 'Paracetamol 500mg' },
    { id: 2, kode_obat: 'OBT-002', nama_obat: 'Amoxicillin 500mg' },
    { id: 3, kode_obat: 'OBT-003', nama_obat: 'Vitamin C' }
  ],
  resep: [
    { id: 1, id_pemeriksaan: 1, id_obat: 1 },
    { id: 2, id_pemeriksaan: 2, id_obat: 3 },
    { id: 3, id_pemeriksaan: 3, id_obat: 2 }
  ]
};

/* ============================================================
   2. Konfigurasi halaman, formulir, dan tabel
   ============================================================ */
const PAGE_META = {
  pasien: {
    title: 'Data Pasien',
    singular: 'Pasien',
    description: 'Kelola identitas dan informasi dasar pasien yang terdaftar.',
    fields: [
      ['no_registrasi', 'Nomor Registrasi', 'text'],
      ['nama_pasien', 'Nama Pasien', 'text'],
      ['alamat', 'Alamat', 'text'],
      ['tgl_lahir', 'Tanggal Lahir', 'date']
    ]
  },
  dokter: {
    title: 'Data Dokter',
    singular: 'Dokter',
    description: 'Data tenaga medis beserta spesialisasi yang aktif bertugas.',
    fields: [
      ['username', 'Username', 'text'],
      ['password', 'Password', 'password'],
      ['nama_dokter', 'Nama Dokter', 'text'],
      ['alamat', 'Alamat', 'text'],
      ['tgl_lahir', 'Tanggal Lahir', 'date'],
      ['jenis', 'Jenis Kelamin', 'select', ['Laki-laki', 'Perempuan']],
      ['spesialis', 'Spesialis', 'text']
    ]
  },
  pemeriksaan: {
    title: 'Pemeriksaan',
    singular: 'Pemeriksaan',
    description: 'Catat hasil pemeriksaan dan pantau kondisi pasien.',
    fields: [
      ['id_pasien', 'Pasien', 'select', () => db.pasien.map(row => [row.id, row.nama_pasien])],
      ['id_dokter', 'Dokter', 'select', () => db.dokter.map(row => [row.id, row.nama_dokter])],
      ['tgl_periksa', 'Tanggal Pemeriksaan', 'date'],
      ['keluhan', 'Keluhan', 'text'],
      ['tensi_darah', 'Tekanan Darah', 'text']
    ]
  },
  obat: {
    title: 'Data Obat',
    singular: 'Obat',
    description: 'Kelola ketersediaan obat dan kode inventarisnya.',
    fields: [
      ['kode_obat', 'Kode Obat', 'text'],
      ['nama_obat', 'Nama Obat', 'text']
    ]
  },
  resep: {
    title: 'Resep Obat',
    singular: 'Resep Obat',
    description: 'Hubungkan hasil pemeriksaan dengan obat yang diresepkan.',
    fields: [
      ['id_pemeriksaan', 'Pemeriksaan', 'select', () => db.pemeriksaan.map(row =>
        [row.id, `Pemeriksaan #${row.id} — ${nameOf('pasien', row.id_pasien)}`])],
      ['id_obat', 'Obat', 'select', () => db.obat.map(row => [row.id, row.nama_obat])]
    ]
  }
};

const TABLE_COLUMNS = {
  pasien: [
    ['nama_pasien', 'Nama Pasien'],
    ['no_registrasi', 'No. Registrasi'],
    ['alamat', 'Alamat'],
    ['tgl_lahir', 'Tanggal Lahir']
  ],
  dokter: [
    ['nama_dokter', 'Nama Dokter'],
    ['spesialis', 'Spesialis'],
    ['username', 'Username'],
    ['alamat', 'Alamat']
  ],
  pemeriksaan: [
    ['id', 'ID'],
    ['id_pasien', 'Pasien'],
    ['id_dokter', 'Dokter'],
    ['tgl_periksa', 'Tanggal'],
    ['keluhan', 'Keluhan'],
    ['tensi_darah', 'Tekanan Darah']
  ],
  obat: [
    ['kode_obat', 'Kode Obat'],
    ['nama_obat', 'Nama Obat']
  ],
  resep: [
    ['id', 'ID'],
    ['id_pemeriksaan', 'Pemeriksaan'],
    ['id_obat', 'Obat']
  ]
};

const COMPACT_COLUMNS = TABLE_COLUMNS.pemeriksaan.slice(0, 5);
const REFERENCE_KEYS = new Set(['id_pasien', 'id_dokter', 'id_pemeriksaan', 'id_obat']);
const TODAY = '2026-10-26';
const TODAY_LABEL = 'Senin, 26 Oktober 2026';

/* ============================================================
   3. State aplikasi
   ============================================================ */
let db = JSON.parse(JSON.stringify(seed));
let page = 'dashboard';
let editingId = null;
let searchQuery = '';
let currentUser = null;
let toastTimer = null;

/* ============================================================
   4. Utilitas
   ============================================================ */
const $ = (id) => document.getElementById(id);

const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#39;'
}[char]));

const icon = (name) => `<svg class="icon" aria-hidden="true"><use href="#ico-${name}"/></svg>`;

function shortName(name) {
  return (name || '')
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase();
}

function nameOf(type, id) {
  const row = db[type].find((item) => item.id == id);
  return row ? (row.nama_pasien || row.nama_dokter || row.nama_obat || `#${id}`) : '—';
}

function formatDate(value) {
  if (!value) return '—';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return esc(value);
  return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function showToast(message) {
  const toast = $('toast');
  toast.textContent = message;
  toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
}

/* ============================================================
   5. Komponen tampilan
   ============================================================ */
function statCard({ label, value, iconName, tone, note }) {
  return `
    <article class="stat-card stat-card--${tone}">
      <div class="stat-top">
        <span class="stat-label">${label}</span>
        <span class="stat-icon">${icon(iconName)}</span>
      </div>
      <div class="stat-number">${value}</div>
      <div class="stat-note">${note}</div>
    </article>
  `;
}

function pageHeading(title, description, action = '') {
  return `
    <header class="page-heading">
      <div>
        <span class="eyebrow">Sistem Informasi Rumah Sakit</span>
        <h1>${title}</h1>
        <p>${description}</p>
      </div>
      ${action}
    </header>
  `;
}

function cellValue(type, row, key) {
  if (key === 'id') return `<span class="id-chip">#${esc(row.id)}</span>`;

  if (type === 'pemeriksaan') {
    if (key === 'id_pasien') return esc(nameOf('pasien', row[key]));
    if (key === 'id_dokter') return esc(nameOf('dokter', row[key]));
  }

  if (type === 'resep') {
    if (key === 'id_pemeriksaan') return `Pemeriksaan #${esc(row[key])}`;
    if (key === 'id_obat') return esc(nameOf('obat', row[key]));
  }

  if (key === 'tgl_periksa' || key === 'tgl_lahir') return formatDate(row[key]);

  return esc(row[key]);
}

function tableMarkup(type, rows, { compact = false } = {}) {
  const columns = compact ? COMPACT_COLUMNS : TABLE_COLUMNS[type];
  const showActions = !compact;
  const columnCount = columns.length + (showActions ? 1 : 0);

  const body = rows.map((row) => {
    const cells = columns.map(([key]) => {
      const value = cellValue(type, row, key);
      const isPerson = key === 'nama_pasien' || key === 'nama_dokter';

      return `
        <td>
          ${isPerson
            ? `<div class="person-cell"><span class="mini-avatar">${shortName(row[key])}</span>${value}</div>`
            : value}
        </td>
      `;
    }).join('');

    const actions = showActions
      ? `
        <td>
          <div class="actions">
            <button type="button" class="small-action" data-action="edit" data-id="${row.id}" title="Edit" aria-label="Edit">
              ${icon('edit')}
            </button>
            ${currentUser.role === 'admin'
              ? `<button type="button" class="small-action" data-action="delete" data-id="${row.id}" title="Hapus" aria-label="Hapus">
                  ${icon('trash')}
                </button>`
              : ''}
          </div>
        </td>
      `
      : '';

    return `<tr>${cells}${actions}</tr>`;
  }).join('');

  return `
    <div class="table-wrap">
      <table class="data-table${showActions ? ' data-table--actions' : ''}">
        <thead>
          <tr>
            ${columns.map(([, label]) => `<th>${label}</th>`).join('')}
            ${showActions ? '<th>Aksi</th>' : ''}
          </tr>
        </thead>
        <tbody>
          ${body || `<tr><td colspan="${columnCount}"><div class="empty">Belum ada data yang bisa ditampilkan.</div></td></tr>`}
        </tbody>
      </table>
    </div>
  `;
}

/* ============================================================
   6. Halaman
   ============================================================ */
function dashboardView() {
  const isDoctor = currentUser.role === 'dokter';
  const greetingName = isDoctor ? currentUser.nama_dokter : 'Admin';
  const myExams = isDoctor
    ? db.pemeriksaan.filter((row) => row.id_dokter === currentUser.id)
    : db.pemeriksaan;
  const todayExams = myExams.filter((row) => row.tgl_periksa === TODAY);

  const stats = [
    { label: 'Total Pasien', value: db.pasien.length, iconName: 'patients', tone: 'blue', note: 'Pasien terdaftar' },
    isDoctor
      ? { label: 'Pemeriksaan Saya', value: myExams.length, iconName: 'exam', tone: 'sky', note: 'Total pasien ditangani' }
      : { label: 'Dokter Aktif', value: db.dokter.length, iconName: 'doctor', tone: 'teal', note: 'Tenaga medis terdaftar' },
    { label: 'Pemeriksaan', value: db.pemeriksaan.length, iconName: 'exam', tone: 'indigo', note: 'Total catatan pemeriksaan' },
    { label: 'Jenis Obat', value: db.obat.length, iconName: 'medicine', tone: 'sky', note: 'Obat tersedia' }
  ].map(statCard).join('');

  const weekly = [
    ['Sen', 3, 2], ['Sel', 5, 3], ['Rab', 4, 3], ['Kam', 6, 3],
    ['Jum', 8, 5], ['Sab', 4, 2], ['Min', 3, 1]
  ];

  const appointments = todayExams.map((exam, index) => `
    <div class="appointment">
      <div class="datebox">
        <b>${String(9 + index * 2).padStart(2, '0')}:00</b>
        <span>WIB</span>
      </div>
      <div class="appt-info">
        <strong>${esc(nameOf('pasien', exam.id_pasien))}</strong>
        <span>${esc(nameOf('dokter', exam.id_dokter))}</span>
      </div>
      <span class="status status--${index === 0 ? 'done' : 'scheduled'}">${index === 0 ? 'Selesai' : 'Terjadwal'}</span>
    </div>
  `).join('');

  return `
    ${pageHeading(
      `Selamat datang, ${esc(greetingName)} 👋`,
      isDoctor
        ? 'Berikut ringkasan pemeriksaan dan jadwal Anda hari ini.'
        : 'Berikut ringkasan aktivitas rumah sakit hari ini.'
    )}

    <div class="stats">${stats}</div>

    <div class="grid-main">
      <div>
        <section class="panel">
          <div class="panel-head">
            <div>
              <h2 class="panel-title">Aktivitas Pemeriksaan</h2>
              <p class="panel-sub">Ringkasan pemeriksaan mingguan</p>
            </div>
            <span class="status status--done">Minggu ini</span>
          </div>

          <div class="chart">
            ${weekly.map(([day, baru, periksa]) => `
              <div class="chart-col">
                <div class="bar-stack">
                  <div class="bar bar--soft" style="--h:${baru * 10}%"></div>
                  <div class="bar bar--main" style="--h:${periksa * 14}%"></div>
                </div>
                <span class="chart-label">${day}</span>
              </div>
            `).join('')}
          </div>

          <div class="legend">
            <span><i class="legend-soft"></i>Pasien baru</span>
            <span><i class="legend-main"></i>Pemeriksaan</span>
          </div>
        </section>

        <section class="panel">
          <div class="panel-head">
            <div>
              <h2 class="panel-title">Pemeriksaan Terbaru</h2>
              <p class="panel-sub">Catatan medis yang baru ditambahkan</p>
            </div>
            <button type="button" class="link-btn" data-nav="pemeriksaan">
              Lihat semua ${icon('arrow-right')}
            </button>
          </div>
          ${tableMarkup('pemeriksaan', myExams.slice(0, 4), { compact: true })}
        </section>
      </div>

      <div>
        <section class="panel">
          <div class="panel-head">
            <div>
              <h2 class="panel-title">Jadwal Hari Ini</h2>
              <p class="panel-sub">${TODAY_LABEL}</p>
            </div>
            <span class="panel-badge">${icon('calendar')}</span>
          </div>

          <div class="appointment-list">
            ${appointments || '<div class="empty">Belum ada jadwal hari ini.</div>'}
          </div>
        </section>
      </div>
    </div>
  `;
}

function listView(type) {
  const meta = PAGE_META[type];
  const rows = getVisibleRows(type);
  const canCreate = currentUser.role === 'admin' || type === 'pemeriksaan';

  const action = canCreate
    ? `<button type="button" class="primary-btn" data-action="create">${icon('plus')} Tambah ${meta.singular}</button>`
    : '';

  return `
    ${pageHeading(meta.title, meta.description, action)}

    <section class="panel">
      <div class="panel-head">
        <div>
          <h2 class="panel-title">Daftar ${meta.title}</h2>
          <p class="panel-sub">${rows.length} data tercatat</p>
        </div>
        <button type="button" class="secondary-btn" data-action="export" data-type="${type}">
          ${icon('download')} Export CSV
        </button>
      </div>

      <div class="toolbar">
        <label class="searchbox">
          ${icon('search')}
          <input id="searchInput" type="search" placeholder="Cari ${meta.title.toLowerCase()}..." value="${esc(searchQuery)}" autocomplete="off">
        </label>
        <button type="button" class="secondary-btn" data-action="reset">
          ${icon('reset')} Reset
        </button>
      </div>

      ${tableMarkup(type, rows)}
    </section>
  `;
}

function getVisibleRows(type) {
  let rows = db[type].filter((row) =>
    Object.values(row).join(' ').toLowerCase().includes(searchQuery.toLowerCase())
  );

  if (currentUser.role === 'dokter' && type === 'pemeriksaan') {
    rows = rows.filter((row) => row.id_dokter === currentUser.id);
  }

  return rows;
}

function render({ animate = false } = {}) {
  const content = $('content');
  const label = page === 'dashboard' ? 'Dashboard' : PAGE_META[page].title;

  $('crumb').textContent = label;
  document.querySelectorAll('#nav button').forEach((button) => {
    button.classList.toggle('active', button.dataset.page === page);
  });

  content.classList.remove('content--enter');
  content.innerHTML = page === 'dashboard' ? dashboardView() : listView(page);

  if (animate) {
    void content.offsetWidth;
    content.classList.add('content--enter');
  }
}

/* ============================================================
   7. Navigasi & pencarian
   ============================================================ */
function go(nextPage) {
  page = nextPage;
  searchQuery = '';
  render({ animate: true });
  closeSidebar();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function setQuery(value) {
  searchQuery = value;
  const caret = $('searchInput')?.selectionStart ?? value.length;

  render();

  const input = $('searchInput');
  if (!input) return;
  input.focus();
  input.setSelectionRange(caret, caret);
}

function openSidebar() {
  $('sidebar').classList.add('open');
  $('sidebarBackdrop').classList.add('show');
}

function closeSidebar() {
  $('sidebar').classList.remove('open');
  $('sidebarBackdrop').classList.remove('show');
}

/* ============================================================
   8. Sesi pengguna
   ============================================================ */
function startSession() {
  const isDoctor = currentUser.role === 'dokter';

  $('loginWrapper').classList.add('hidden');
  $('appContainer').classList.remove('hidden');

  $('profileName').textContent = isDoctor ? currentUser.nama_dokter : 'Administrator';
  $('profileRole').textContent = isDoctor
    ? `Dokter Spesialis ${currentUser.spesialis}`
    : 'Admin Rumah Sakit';
  $('userAvatar').textContent = isDoctor ? shortName(currentUser.nama_dokter) : 'AD';
  $('brandSubRole').textContent = isDoctor ? 'Portal Dokter' : 'Hospital System';

  document.querySelectorAll('.admin-only').forEach((element) => {
    element.classList.toggle('hidden', isDoctor);
  });

  page = 'dashboard';
  searchQuery = '';
  render({ animate: true });
  window.scrollTo({ top: 0 });
}

function logout() {
  currentUser = null;
  closeSidebar();
  $('appContainer').classList.add('hidden');
  $('loginWrapper').classList.remove('hidden');
  $('loginWrapper').scrollTop = 0;
  $('loginForm').reset();
}

/* ============================================================
   9. Modal & form data
   ============================================================ */
function renderField({ key, label, type, options, record, isEditing }) {
  if (currentUser.role === 'dokter' && key === 'id_dokter') {
    return `<input type="hidden" name="${key}" value="${currentUser.id}">`;
  }

  const value = record[key] ?? '';

  if (type === 'select') {
    const items = typeof options === 'function' ? options() : options.map((item) => [item, item]);

    return `
      <div class="field">
        <label for="field-${key}">${label}</label>
        <select id="field-${key}" name="${key}" required>
          <option value="" disabled ${value === '' ? 'selected' : ''}>Pilih ${label.toLowerCase()}</option>
          ${items.map(([optionValue, optionLabel]) => `
            <option value="${esc(optionValue)}" ${String(value) === String(optionValue) ? 'selected' : ''}>
              ${esc(optionLabel)}
            </option>
          `).join('')}
        </select>
      </div>
    `;
  }

  const isPassword = type === 'password';
  const required = !isPassword || !isEditing;

  return `
    <div class="field">
      <label for="field-${key}">${label}</label>
      <input id="field-${key}" name="${key}" type="${type}" value="${esc(value)}"
        placeholder="Masukkan ${label.toLowerCase()}" ${required ? 'required' : ''}>
    </div>
  `;
}

function openModal(id = null) {
  const meta = PAGE_META[page];
  if (!meta) return;

  const record = id ? db[page].find((row) => row.id === id) : {};

  editingId = id;
  $('modalTitle').textContent = `${id ? 'Edit' : 'Tambah'} ${meta.singular}`;
  $('formFields').innerHTML = meta.fields
    .map(([key, label, type, options]) => renderField({
      key,
      label,
      type,
      options,
      record,
      isEditing: Boolean(id)
    }))
    .join('');

  $('modalBackdrop').classList.add('show');
}

function closeModal() {
  $('modalBackdrop').classList.remove('show');
  $('dataForm').reset();
  editingId = null;
}

function saveRow(formData) {
  const meta = PAGE_META[page];
  const payload = {};

  meta.fields.forEach(([key]) => {
    let value = formData.get(key);

    if (currentUser.role === 'dokter' && key === 'id_dokter') {
      value = currentUser.id;
    }
    if (REFERENCE_KEYS.has(key)) {
      value = Number(value);
    }
    if (value !== null) {
      payload[key] = value;
    }
  });

  if (editingId !== null) {
    const index = db[page].findIndex((row) => row.id === editingId);
    db[page][index] = { ...db[page][index], ...payload };
    closeModal();
    render();
    showToast('Data berhasil diperbarui.');
    return;
  }

  const nextId = Math.max(0, ...db[page].map((row) => Number(row.id) || 0)) + 1;
  db[page].push({ id: nextId, ...payload });
  closeModal();
  render();
  showToast('Data berhasil ditambahkan.');
}

function removeRow(id) {
  if (!confirm('Yakin ingin menghapus data ini?')) return;

  db[page] = db[page].filter((row) => row.id !== id);
  render();
  showToast('Data berhasil dihapus.');
}

function downloadCSV(type) {
  const rows = getVisibleRows(type);
  if (!rows.length) {
    showToast('Tidak ada data untuk diekspor.');
    return;
  }

  const keys = Object.keys(rows[0]);
  const lines = [
    keys.join(','),
    ...rows.map((row) => keys
      .map((key) => `"${String(row[key] ?? '').replace(/"/g, '""')}"`)
      .join(','))
  ];

  const url = URL.createObjectURL(new Blob(['\ufeff' + lines.join('\n')], {
    type: 'text/csv;charset=utf-8;'
  }));

  const link = document.createElement('a');
  link.href = url;
  link.download = `${type}.csv`;
  link.click();
  URL.revokeObjectURL(url);

  showToast('Data berhasil diekspor.');
}

/* ============================================================
   10. Event listener
   ============================================================ */
$('loginForm').addEventListener('submit', (event) => {
  event.preventDefault();

  const username = $('loginUsername').value.trim();
  const password = $('loginPassword').value.trim();
  const doctor = db.dokter.find((row) => row.username === username && row.password === password);

  if (!doctor) {
    showToast('Username atau password salah. Coba dr.rahman / 123.');
    return;
  }

  currentUser = { role: 'dokter', ...doctor };
  startSession();
  showToast(`Selamat datang, ${doctor.nama_dokter}.`);
});

$('demoLogin').addEventListener('click', () => {
  currentUser = { role: 'admin', username: 'admin', nama_dokter: 'Administrator' };
  startSession();
  showToast('Masuk sebagai Administrator.');
});

$('logoutBtn').addEventListener('click', logout);
$('menuToggle').addEventListener('click', openSidebar);
$('sidebarBackdrop').addEventListener('click', closeSidebar);

$('nav').addEventListener('click', (event) => {
  const button = event.target.closest('button[data-page]');
  if (button) go(button.dataset.page);
});

$('content').addEventListener('click', (event) => {
  const navTarget = event.target.closest('[data-nav]');
  if (navTarget) {
    go(navTarget.dataset.nav);
    return;
  }

  const actionTarget = event.target.closest('[data-action]');
  if (!actionTarget) return;

  const { action, id, type } = actionTarget.dataset;

  if (action === 'create') openModal();
  if (action === 'edit') openModal(Number(id));
  if (action === 'delete') removeRow(Number(id));
  if (action === 'export') downloadCSV(type);
  if (action === 'reset') {
    searchQuery = '';
    render();
  }
});

$('content').addEventListener('input', (event) => {
  if (event.target.id === 'searchInput') setQuery(event.target.value);
});

$('dataForm').addEventListener('submit', (event) => {
  event.preventDefault();
  saveRow(new FormData(event.target));
});

$('closeModal').addEventListener('click', closeModal);
$('cancelModal').addEventListener('click', closeModal);

$('modalBackdrop').addEventListener('click', (event) => {
  if (event.target === event.currentTarget) closeModal();
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  closeModal();
  closeSidebar();
});
