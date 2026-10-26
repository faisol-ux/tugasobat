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

let db = JSON.parse(JSON.stringify(seed));
let page = 'dashboard';
let editing = null;
let query = '';
let currentUser = null;

const meta = {
  pasien: {
    title: 'Data Pasien',
    singular: 'Pasien',
    icon: '♙',
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
    icon: '⚕',
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
    icon: '▤',
    fields: [
      ['id_pasien', 'Pasien', 'select', () => db.pasien.map(x => [x.id, x.nama_pasien])],
      ['id_dokter', 'Dokter', 'select', () => db.dokter.map(x => [x.id, x.nama_dokter])],
      ['tgl_periksa', 'Tanggal Pemeriksaan', 'date'],
      ['keluhan', 'Keluhan', 'text'],
      ['tensi_darah', 'Tekanan Darah', 'text']
    ]
  },
  obat: {
    title: 'Data Obat',
    singular: 'Obat',
    icon: '✚',
    fields: [
      ['kode_obat', 'Kode Obat', 'text'],
      ['nama_obat', 'Nama Obat', 'text']
    ]
  },
  resep: {
    title: 'Resep Obat',
    singular: 'Resep Obat',
    icon: '▧',
    fields: [
      ['id_pemeriksaan', 'Pemeriksaan', 'select', () => db.pemeriksaan.map(x => [x.id, 'Pemeriksaan #' + x.id + ' — ' + nameOf('pasien', x.id_pasien)])],
      ['id_obat', 'Obat', 'select', () => db.obat.map(x => [x.id, x.nama_obat])]
    ]
  }
};

document.getElementById('loginForm').addEventListener('submit', e => {
  e.preventDefault();
  const u = document.getElementById('loginUsername').value.trim();
  const p = document.getElementById('loginPassword').value.trim();

  const foundDoctor = db.dokter.find(d => d.username === u && d.password === p);

  if (foundDoctor) {
    currentUser = { role: 'dokter', ...foundDoctor };
    initSession();
    notify('Berhasil masuk sebagai ' + foundDoctor.nama_dokter);
  } else {
    alert('Username atau password salah! (Coba gunakan: dr.rahman / 123)');
  }
});

function bypassAdmin() {
  currentUser = { role: 'admin', nama_dokter: 'Administrator', username: 'admin' };
  initSession();
  notify('Masuk sebagai Administrator');
}

function logout() {
  currentUser = null;
  document.getElementById('loginWrapper').classList.remove('hidden');
  document.getElementById('appContainer').classList.add('hidden');
  document.getElementById('loginForm').reset();
}

function initSession() {
  document.getElementById('loginWrapper').classList.add('hidden');
  document.getElementById('appContainer').classList.remove('hidden');

  if (currentUser.role === 'dokter') {
    document.getElementById('profileName').textContent = currentUser.nama_dokter;
    document.getElementById('profileRole').textContent = 'Dokter Spesialis ' + currentUser.spesialis;
    document.getElementById('userAvatar').textContent = shortName(currentUser.nama_dokter);
    document.getElementById('brandSubRole').textContent = 'PORTAL DOKTER';
    document.querySelectorAll('.admin-only').forEach(el => el.classList.add('hidden'));
  } else {
    document.getElementById('profileName').textContent = 'Administrator';
    document.getElementById('profileRole').textContent = 'Admin Rumah Sakit';
    document.getElementById('userAvatar').textContent = 'AD';
    document.getElementById('brandSubRole').textContent = 'HOSPITAL SYSTEM';
    document.querySelectorAll('.admin-only').forEach(el => el.classList.remove('hidden'));
  }

  page = 'dashboard';
  render();
}

function nameOf(type, id) {
  let x = db[type].find(r => r.id == id);
  return x ? (x.nama_pasien || x.nama_dokter || x.nama_obat || ('#' + id)) : '—';
}

function esc(v) {
  return String(v ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));
}

function shortName(s) {
  return (s || '').split(' ').map(a => a[0]).slice(0, 2).join('').toUpperCase();
}

function stat(label, num, icon, color, foot) {
  return `
    <div class="stat-card">
      <div class="stat-top">
        <span class="stat-label">${label}</span>
        <span class="stat-icon" style="background:${color}">${icon}</span>
      </div>
      <div class="stat-number">${num}</div>
      <div class="stat-foot">${foot}</div>
    </div>
  `;
}

function pageHead(title, desc, buttonText) {
  let showBtn = buttonText && (currentUser.role === 'admin' || page === 'pemeriksaan');
  return `
    <div class="page-heading">
      <div>
        <div class="eyebrow">Sistem Informasi Rumah Sakit</div>
        <h1>${title}</h1>
        <p>${desc}</p>
      </div>
      ${showBtn ? `<button class="primary-btn" onclick="openModal()">＋ ${buttonText}</button>` : ''}
    </div>
  `;
}

function dashboard() {
  let listPemeriksaan = currentUser.role === 'dokter' 
    ? db.pemeriksaan.filter(x => x.id_dokter === currentUser.id) 
    : db.pemeriksaan;

  return `
    ${pageHead('Selamat datang, ' + (currentUser.role === 'dokter' ? currentUser.nama_dokter : 'Admin') + ' 👋', currentUser.role === 'dokter' ? 'Berikut jadwal dan aktivitas pemeriksaan Anda.' : 'Berikut ringkasan aktivitas rumah sakit hari ini.', '')}
    <div class="stats">
      ${stat('Total Pasien', db.pasien.length, '♙', '#e5efff', 'Data pasien terdaftar')}
      ${currentUser.role === 'admin' ? stat('Dokter Aktif', db.dokter.length, '⚕', '#e4f7f3', 'Tenaga medis terdaftar') : stat('Pemeriksaan Saya', listPemeriksaan.length, '⚕', '#e4f7f3', 'Total pasien ditangani')}
      ${stat('Pemeriksaan', db.pemeriksaan.length, '▤', '#fff1dc', 'Total catatan pemeriksaan')}
      ${stat('Jenis Obat', db.obat.length, '✚', '#f0e9ff', 'Data obat tersedia')}
    </div>
    <div class="grid-main">
      <div>
        <section class="panel">
          <div class="panel-head">
            <div>
              <div class="panel-title">Aktivitas Pemeriksaan</div>
              <div class="panel-sub">Ringkasan pemeriksaan mingguan</div>
            </div>
            <span class="status done">Minggu ini</span>
          </div>
          <div class="chart">
            ${[['Sen', 3, 2], ['Sel', 5, 3], ['Rab', 4, 3], ['Kam', 6, 3], ['Jum', 8, 5], ['Sab', 4, 2], ['Min', 3, 1]].map(a => `
              <div class="chart-col">
                <div class="bar-stack">
                  <div class="bar" style="height:${a[1] * 13}%"></div>
                  <div class="bar mainbar" style="height:${a[2] * 19}%"></div>
                </div>
                <span class="chart-label">${a[0]}</span>
              </div>
            `).join('')}
          </div>
          <div class="legend">
            <span><i style="background:#bfece4"></i>Pasien baru</span>
            <span><i style="background:#17a895"></i>Pemeriksaan</span>
          </div>
        </section>
        <section class="panel">
          <div class="panel-head">
            <div>
              <div class="panel-title">Pemeriksaan Terbaru</div>
              <div class="panel-sub">Catatan medis yang baru ditambahkan</div>
            </div>
            <button class="link-btn" onclick="go('pemeriksaan')">Lihat semua →</button>
          </div>
          ${tableHTML('pemeriksaan', listPemeriksaan.slice(0, 4), true)}
        </section>
      </div>
      <div>
        <section class="panel">
          <div class="panel-head">
            <div>
              <div class="panel-title">Jadwal Hari Ini</div>
              <div class="panel-sub">Senin, 26 Oktober 2026</div>
            </div>
            <span style="font-size:21px">🗓</span>
          </div>
          <div class="appointment-list">
            ${listPemeriksaan.filter(x => x.tgl_periksa === '2026-10-26').map((x, i) => `
              <div class="appointment">
                <div class="datebox">
                  <b>${String(9 + i * 2).padStart(2, '0')}:00</b>
                  <span>WIB</span>
                </div>
                <div class="appt-info">
                  <strong>${esc(nameOf('pasien', x.id_pasien))}</strong>
                  <span>${esc(nameOf('dokter', x.id_dokter))}</span>
                </div>
                <span class="status ${i === 0 ? 'done' : 'scheduled'}">${i === 0 ? 'Selesai' : 'Terjadwal'}</span>
              </div>
            `).join('') || '<div class="empty">Belum ada jadwal hari ini.</div>'}
          </div>
        </section>
      </div>
    </div>
  `;
}

function cell(type, r, key) {
  if (type === 'pemeriksaan') {
    if (key === 'id_pasien') return esc(nameOf('pasien', r[key]));
    if (key === 'id_dokter') return esc(nameOf('dokter', r[key]));
  }
  if (type === 'resep') {
    if (key === 'id_pemeriksaan') return 'Pemeriksaan #' + esc(r[key]);
    if (key === 'id_obat') return esc(nameOf('obat', r[key]));
  }
  return esc(r[key]);
}

function tableHTML(type, rows, compact = false) {
  let columns = {
    pasien: [['nama_pasien', 'Nama Pasien'], ['no_registrasi', 'No. Registrasi'], ['alamat', 'Alamat'], ['tgl_lahir', 'Tanggal Lahir']],
    dokter: [['nama_dokter', 'Nama Dokter'], ['spesialis', 'Spesialis'], ['username', 'Username'], ['alamat', 'Alamat']],
    pemeriksaan: [['id', 'ID'], ['id_pasien', 'Pasien'], ['id_dokter', 'Dokter'], ['tgl_periksa', 'Tanggal'], ['keluhan', 'Keluhan'], ['tensi_darah', 'Tekanan Darah']],
    obat: [['kode_obat', 'Kode Obat'], ['nama_obat', 'Nama Obat']],
    resep: [['id', 'ID'], ['id_pemeriksaan', 'Pemeriksaan'], ['id_obat', 'Obat']]
  };
  
  let cols = columns[type] || columns.pasien;
  if (compact) cols = columns.pemeriksaan.slice(0, 5);

  return `
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            ${cols.map(c => `<th>${c[1]}</th>`).join('')}
            ${!compact ? '<th>Aksi</th>' : ''}
          </tr>
        </thead>
        <tbody>
          ${rows.map(r => `
            <tr>
              ${cols.map(c => `
                <td>
                  ${c[0] === 'nama_pasien' || c[0] === 'nama_dokter' ? `
                    <div class="person-cell">
                      <div class="mini-avatar">${shortName(r[c[0]])}</div>
                      ${cell(type, r, c[0])}
                    </div>
                  ` : cell(type, r, c[0])}
                </td>
              `).join('')}
              ${!compact ? `
                <td>
                  <div class="actions">
                    <button class="small-action" title="Edit" onclick="openModal(${r.id})">✎</button>
                    ${currentUser.role === 'admin' ? `<button class="small-action" title="Hapus" onclick="removeRow(${r.id})">⌫</button>` : ''}
                  </div>
                </td>
              ` : ''}
            </tr>
          `).join('') || `<tr><td colspan="${cols.length + 1}"><div class="empty">Data belum tersedia.</div></td></tr>`}
        </tbody>
      </table>
    </div>
  `;
}

function listPage(type) {
  const m = meta[type];
  let rows = db[type].filter(r => Object.values(r).join(' ').toLowerCase().includes(query.toLowerCase()));
  
  if (currentUser.role === 'dokter' && type === 'pemeriksaan') {
    rows = rows.filter(r => r.id_dokter === currentUser.id);
  }

  return `
    ${pageHead(m.title, `Kelola dan pantau informasi ${m.title.toLowerCase()} rumah sakit.`, `Tambah ${m.singular}`)}
    <section class="panel">
      <div class="table-title-row">
        <div>
          <h3>Daftar ${m.title}</h3>
          <span>${rows.length} data tercatat</span>
        </div>
        <button class="secondary-btn" onclick="downloadCSV('${type}')">↓ Export CSV</button>
      </div>
      <div class="search-row">
        <div class="searchbox">
          <span>⌕</span>
          <input id="searchInput" placeholder="Cari data..." value="${esc(query)}" oninput="setQuery(this.value)">
        </div>
        <button class="secondary-btn" onclick="query='';render()">↻ Reset</button>
      </div>
      ${tableHTML(type, rows)}
    </section>
  `;
}

function render() {
  document.getElementById('crumb').textContent = page === 'dashboard' ? 'Dashboard' : meta[page].title;
  document.querySelectorAll('#nav button').forEach(b => b.classList.toggle('active', b.dataset.page === page));
  document.getElementById('content').innerHTML = page === 'dashboard' ? dashboard() : listPage(page);
}

function go(p) {
  page = p;
  query = '';
  render();
  document.getElementById('sidebar').classList.remove('open');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function setQuery(v) {
  query = v;
  let pos = document.getElementById('searchInput')?.selectionStart;
  render();
  let el = document.getElementById('searchInput');
  if (el) {
    el.focus();
    el.setSelectionRange(pos, pos);
  }
}

function openModal(id = null) {
  editing = id;
  const m = meta[page];
  if (!m) return;
  
  const current = id ? db[page].find(r => r.id === id) : {};
  document.getElementById('modalTitle').textContent = (id ? 'Edit ' : 'Tambah ') + m.singular;
  
  document.getElementById('formFields').innerHTML = m.fields.map(([key, label, type, opts]) => {
    let val = current[key] ?? '';
    
    if (currentUser.role === 'dokter' && key === 'id_dokter') {
      val = currentUser.id;
      return `<input type="hidden" name="${key}" value="${val}">`;
    }

    if (type === 'select') {
      let options = typeof opts === 'function' ? opts() : opts.map(v => [v, v]);
      return `
        <div class="field">
          <label>${label}</label>
          <select name="${key}" required>
            <option value="">Pilih ${label}</option>
            ${options.map(o => `<option value="${esc(o[0])}" ${String(val) === String(o[0]) ? 'selected' : ''}>${esc(o[1])}</option>`).join('')}
          </select>
        </div>
      `;
    }
    return `
      <div class="field ${type === 'textarea' ? 'full' : ''}">
        <label>${label}</label>
        <input name="${key}" type="${type}" value="${esc(val)}" ${key === 'password' && !id ? 'required' : ''} ${key !== 'password' ? 'required' : ''} placeholder="Masukkan ${label.toLowerCase()}">
      </div>
    `;
  }).join('');
  
  document.getElementById('modalBackdrop').classList.add('show');
}

function closeModal() {
  document.getElementById('modalBackdrop').classList.remove('show');
}

function notify(msg) {
  let t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2400);
}

document.getElementById('dataForm').addEventListener('submit', e => {
  e.preventDefault();
  const m = meta[page], fd = new FormData(e.target), obj = {};
  
  m.fields.forEach(([key, label, type]) => {
    let val = fd.get(key);
    if (currentUser.role === 'dokter' && key === 'id_dokter') val = currentUser.id;
    if (['id_pasien', 'id_dokter', 'id_pemeriksaan', 'id_obat'].includes(key)) val = Number(val);
    if (val !== null) obj[key] = val;
  });
  
  if (editing) {
    let idx = db[page].findIndex(r => r.id === editing);
    db[page][idx] = { ...db[page][idx], ...obj };
    notify('Data berhasil diperbarui.');
  } else {
    obj.id = Math.max(0, ...db[page].map(r => Number(r.id) || 0)) + 1;
    db[page].push(obj);
    notify('Data berhasil ditambahkan.');
  }
  
  closeModal();
  render();
});

function removeRow(id) {
  if (confirm('Yakin ingin menghapus data ini?')) {
    db[page] = db[page].filter(r => r.id !== id);
    render();
    notify('Data berhasil dihapus.');
  }
}

function downloadCSV(type) {
  const rows = db[type], keys = Object.keys(rows[0] || {});
  let csv = [keys.join(','), ...rows.map(r => keys.map(k => '"' + String(r[k] ?? '').replace(/"/g, '""') + '"').join(','))].join('\n');
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' }));
  a.download = type + '.csv';
  a.click();
  URL.revokeObjectURL(a.href);
}

document.querySelectorAll('#nav button').forEach(b => b.addEventListener('click', () => go(b.dataset.page)));
document.getElementById('closeModal').onclick = closeModal;
document.getElementById('cancelModal').onclick = closeModal;
document.getElementById('modalBackdrop').addEventListener('click', e => {
  if (e.target.id === 'modalBackdrop') closeModal();
});
document.getElementById('menuToggle').onclick = () => document.getElementById('sidebar').classList.toggle('open');