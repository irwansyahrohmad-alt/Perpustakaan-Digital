@extends('layouts.perpus')

@section('title', 'Kelola Data Buku')

@section('content')
    <style>
        /* kotak pesan milik halaman ini disembunyikan sampai JS menampilkannya */
        #alert.alert { display: none; }
    </style>

    <h2>Kelola Data Buku</h2>
    <div id="alert" class="alert"></div>

    <form id="form-buku">
        <input type="hidden" id="buku-id">
        <div>
            <input type="text" id="kategori" placeholder="Kategori">
            <div class="field-error" data-error="kategori"></div>
        </div>
        <div>
            <input type="text" id="judul" placeholder="Judul">
            <div class="field-error" data-error="judul"></div>
        </div>
        <div>
            <input type="text" id="penulis" placeholder="Penulis">
            <div class="field-error" data-error="penulis"></div>
        </div>
        <div>
            <input type="number" id="tahun_terbit" placeholder="Tahun Terbit">
            <div class="field-error" data-error="tahun_terbit"></div>
        </div>
        <div>
            <button type="submit" id="btn-simpan">Simpan</button>
            <button type="button" id="btn-batal" style="display:none">Batal</button>
        </div>
    </form>

    <div id="tabel-wrapper">
        <table>
            <thead>
                <tr><th>No</th><th>Kategori</th><th>Judul</th><th>Penulis</th><th>Tahun Terbit</th><th>Aksi</th></tr>
            </thead>
            <tbody id="tabel-buku"></tbody>
        </table>
    </div>

    <script>
    const API_URL = "{{ url('/api/buku') }}";
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const HEADERS = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF };

    const form = document.getElementById('form-buku');
    const tbody = document.getElementById('tabel-buku');
    const alertBox = document.getElementById('alert');
    const fields = ['kategori', 'judul', 'penulis', 'tahun_terbit'];
    let daftarBuku = [];

    function showAlert(message, type = 'success') {
        alertBox.textContent = message;
        alertBox.className = 'alert ' + type;
        alertBox.style.display = 'block';
    }

    function clearErrors() {
        document.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
    }

    function showFieldErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const el = document.querySelector(`[data-error="${field}"]`);
            if (el) el.textContent = messages[0];
        });
    }

    function errorMessage(error) {
        return error instanceof TypeError
            ? 'API tidak dapat diakses. Periksa koneksi atau server.'
            : error.message;
    }

    // Dipanggil saat response.ok === false (422 validasi, 404, 500, dll.)
    async function handleError(response) {
        const data = await response.json().catch(() => ({}));
        if (response.status === 422 && data.errors) {
            showFieldErrors(data.errors);
            throw new Error('Validasi gagal. Periksa kembali isian formulir.');
        }
        throw new Error(data.message || `Terjadi kesalahan (status ${response.status}).`);
    }

    function resetForm() {
        form.reset();
        document.getElementById('buku-id').value = '';
        document.getElementById('btn-simpan').textContent = 'Simpan';
        document.getElementById('btn-batal').style.display = 'none';
        clearErrors();
    }

    // ===== 1. READ (GET /api/buku) =====
    async function loadBuku() {
        try {
            const response = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) await handleError(response);
            const result = await response.json();
            daftarBuku = result.data;
            renderTable();
        } catch (error) {
            showAlert('Gagal memuat data: ' + errorMessage(error), 'error');
        }
    }

    function renderTable() {
        tbody.innerHTML = '';
        if (daftarBuku.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">Belum ada data buku.</td></tr>';
            return;
        }
        daftarBuku.forEach((buku, i) => {
            const tr = document.createElement('tr');
            [i + 1, buku.kategori, buku.judul, buku.penulis, buku.tahun_terbit].forEach(val => {
                const td = document.createElement('td');
                td.textContent = val;
                tr.appendChild(td);
            });
            const tdAksi = document.createElement('td');
            const btnUbah = document.createElement('button');
            btnUbah.textContent = 'Ubah';
            btnUbah.onclick = () => isiFormUbah(buku.id);
            const btnHapus = document.createElement('button');
            btnHapus.textContent = 'Hapus';
            btnHapus.onclick = () => hapusBuku(buku.id);
            tdAksi.append(btnUbah, ' ', btnHapus);
            tr.appendChild(tdAksi);
            tbody.appendChild(tr);
        });
    }

    // ===== 2 & 3. CREATE (POST) / UPDATE (PUT) =====
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const id = document.getElementById('buku-id').value;
        const payload = {};
        fields.forEach(f => payload[f] = document.getElementById(f).value);

        const isEdit = id !== '';
        const url = isEdit ? `${API_URL}/${id}` : API_URL;

        try {
            const response = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: HEADERS,
                body: JSON.stringify(payload),
            });
            if (!response.ok) await handleError(response);

            showAlert(isEdit ? 'Buku berhasil diubah.' : 'Buku berhasil ditambahkan.');
            resetForm();
            await loadBuku();
        } catch (error) {
            showAlert((isEdit ? 'Gagal mengubah buku: ' : 'Gagal menambah buku: ') + errorMessage(error), 'error');
        }
    });

    function isiFormUbah(id) {
        const buku = daftarBuku.find(b => b.id === id);
        if (!buku) return;
        document.getElementById('buku-id').value = buku.id;
        fields.forEach(f => document.getElementById(f).value = buku[f] ?? '');
        document.getElementById('btn-simpan').textContent = 'Update';
        document.getElementById('btn-batal').style.display = 'inline';
        clearErrors();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('btn-batal').addEventListener('click', resetForm);

    // ===== 4. DELETE (DELETE /api/buku/{id}) =====
    async function hapusBuku(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) return;
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            if (!response.ok) await handleError(response);

            showAlert('Buku berhasil dihapus.');
            await loadBuku();
        } catch (error) {
            showAlert('Gagal menghapus buku: ' + errorMessage(error), 'error');
        }
    }

    loadBuku();
    </script>
@endsection
