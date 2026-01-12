{{-- resources/views/admin/absensi/create.blade.php --}}
@extends('layouts.admin')

@section('title','Tambah Absensi - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Page Container (Konsisten)
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1065px;
    margin-left: 3.5rem;
    margin-right: 60rem;
}
@media(max-width: 1400px) { .container-page { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px) { .container-page { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px) { .container-page { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card & Header
============================ */
.card-main {
    border-radius: 14px;
    border: none;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

.header-box {
    padding: 1.2rem 1.5rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
}

.card-header-custom {
    background: #f8fbf9;
    border-bottom: 1px solid #edf2ee;
    padding: 1rem 1.4rem;
}

/* ============================
   Form Styling
============================ */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}
.form-select, .form-control {
    border-radius: 9px;
    padding: 0.65rem 1rem;
    border: 1px solid #dee2e6;
}
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1) !important;
    border-color: #198754 !important;
}

/* Info Box */
.info-siswa {
    background: #f0fdf4;
    border-left: 4px solid #198754;
    padding: .8rem 1rem;
    border-radius: 10px;
    color: #155724;
}

/* Radio Button Custom */
.btn-status-group .btn-check:checked + .btn {
    border-width: 2px;
}
.btn-status-label {
    padding: 0.6rem;
    font-weight: 700;
    font-size: 0.85rem;
    border-radius: 10px !important;
}

.section-title-sm {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center header-box">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-plus-circle-fill me-2 text-success"></i> Tambah Absensi
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.absensi.index') }}" class="text-decoration-none text-muted">Absensi</a></li>
                    <li class="breadcrumb-item active text-success fw-bold" aria-current="page">Tambah Manual</li>
                </ol>
            </nav>
        </div>

        <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary px-4 rounded-3 btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <div class="fw-bold">Mohon perbaiki kesalahan berikut:</div>
                    <ul class="mb-0 small mt-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.absensi.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- KIRI: PEMILIHAN --}}
            <div class="col-lg-7">
                <div class="card card-main h-100">
                    <div class="card-header-custom">
                        <span class="section-title-sm text-success">
                            <i class="bi bi-journal-check me-2"></i> Data Target
                        </span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Jadwal --}}
                        <div class="mb-4">
                            <label class="form-label">Jadwal Mengajar <span class="text-danger">*</span></label>
                            <select name="id_jadwal_mengajar" id="id_jadwal_mengajar" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Jadwal --</option>
                                @foreach($jadwal as $j)
                                    <option value="{{ $j->id_jadwal_mengajar }}"
                                        data-id-kelas="{{ $j->id_kelas ?? optional($j->kelas)->id_kelas }}"
                                        @selected(old('id_jadwal_mengajar') == $j->id_jadwal_mengajar)>
                                        {{ optional($j->kelas)->nama_kelas }} — {{ $j->hari }} 
                                        ({{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }})
                                        — {{ optional($j->mataPelajaran)->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Siswa --}}
                        <div class="mb-0">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <select name="id_siswa" id="id_siswa" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->id_siswa }}"
                                            data-id-kelas="{{ $s->id_kelas }}"
                                            @selected(old('id_siswa') == $s->id_siswa)>
                                        {{ $s->nama_siswa }} — {{ optional($s->kelas)->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="info-siswa mt-3 small">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Nama siswa akan muncul setelah Anda memilih jadwal mengajar di atas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: STATUS --}}
            <div class="col-lg-5">
                <div class="card card-main h-100">
                    <div class="card-header-custom">
                        <span class="section-title-sm text-success">
                            <i class="bi bi-calendar2-check me-2"></i> Status Kehadiran
                        </span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Tanggal --}}
                        <div class="mb-4">
                            <label class="form-label">Tanggal Absensi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <input type="date" name="tanggal" class="form-control border-start-0 shadow-sm"
                                       value="{{ old('tanggal', now()->toDateString()) }}" required>
                            </div>
                        </div>

                        {{-- Status Grid --}}
                        <div class="mb-4">
                            <label class="form-label d-block mb-3">Pilih Keterangan <span class="text-danger">*</span></label>
                            <div class="row g-2 btn-status-group">
                                @foreach(['hadir'=>'success','sakit'=>'warning','izin'=>'info','alfa'=>'danger'] as $status=>$clr)
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="keterangan" id="st_{{ $status }}"
                                           value="{{ $status }}" @checked(old('keterangan') == $status) required>
                                    <label class="btn btn-outline-{{ $clr }} btn-status-label w-100" for="st_{{ $status }}">
                                        {{ ucfirst($status) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="form-label">Catatan Tambahan (Opsional)</label>
                            <textarea name="keterangan_detail" class="form-control shadow-sm" rows="3"
                                      placeholder="Contoh: Izin melalui WhatsApp / Surat menyusul">{{ old('keterangan_detail') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="card-main p-3 mt-4 d-flex justify-content-between align-items-center">
            <span class="small text-muted ps-2"><span class="text-danger">*</span> Wajib diisi</span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.absensi.index') }}" class="btn btn-light px-4 border rounded-3">Batal</a>
                <button type="submit" class="btn btn-success px-5 rounded-3 shadow-sm">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Simpan Absensi
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
(function(){
    const jadwal = document.getElementById('id_jadwal_mengajar');
    const siswa  = document.getElementById('id_siswa');
    if(!jadwal || !siswa) return;

    // Ambil semua data siswa ke memory
    const allSiswa = Array.from(siswa.options)
        .map(o => ({ value:o.value, text:o.text, kelas:o.dataset.idKelas }))
        .filter(o => o.value !== '');

    function filterSiswa(kelasId){
        // Kosongkan select
        siswa.querySelectorAll('option:not([value=""])').forEach(o => o.remove());
        
        // Filter yang sesuai kelas
        const filtered = kelasId ? allSiswa.filter(s => s.kelas == kelasId) : allSiswa;

        filtered.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.value;
            opt.textContent = s.text;
            opt.dataset.idKelas = s.kelas;
            if(s.value == "{{ old('id_siswa') }}") opt.selected = true;
            siswa.appendChild(opt);
        });
    }

    jadwal.addEventListener('change', function(){
        const selectedKelas = this.selectedOptions[0]?.dataset.idKelas;
        filterSiswa(selectedKelas);
    });

    // Inisialisasi saat load (untuk handle 'old' data setelah submit gagal)
    filterSiswa(jadwal.selectedOptions[0]?.dataset.idKelas);
})();
</script>
@endpush
@endsection