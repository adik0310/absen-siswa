{{-- resources/views/admin/absensi/edit.blade.php --}}
@extends('layouts.admin')

@section('title','Edit Absensi - Admin')

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

/* Footer Action */
.footer-save {
    padding: 1.2rem;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
                <i class="bi bi-pencil-square me-2 text-success"></i> Edit Absensi
            </h4>
            <p class="text-muted small mb-0">Ubah status kehadiran siswa untuk jadwal tertentu.</p>
        </div>

        <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary px-4 rounded-3 btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
            <div class="d-flex">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <div>
                    <div class="fw-bold">Periksa kembali inputan Anda:</div>
                    <ul class="mb-0 small mt-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.absensi.update', $row->id_absensi ?? $row->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- KIRI: JADWAL & SISWA --}}
            <div class="col-lg-7">
                <div class="card card-main h-100">
                    <div class="card-header-custom">
                        <span class="section-title-sm text-success">
                            <i class="bi bi-person-badge me-2"></i> Subjek & Jadwal
                        </span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Pilih Jadwal --}}
                        <div class="mb-4">
                            <label class="form-label">Jadwal Mengajar <span class="text-danger">*</span></label>
                            <select name="id_jadwal_mengajar" id="id_jadwal_mengajar" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Jadwal --</option>
                                @foreach($jadwal as $j)
                                    @php $selectedId = old('id_jadwal_mengajar', $row->id_jadwal_mengajar ?? null); @endphp
                                    <option value="{{ $j->id_jadwal_mengajar }}"
                                        data-id-kelas="{{ $j->id_kelas ?? optional($j->kelas)->id_kelas }}"
                                        {{ (string)$selectedId === (string)$j->id_jadwal_mengajar ? 'selected' : '' }}>
                                        {{ optional($j->kelas)->nama_kelas }} — {{ $j->hari }} ({{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Siswa --}}
                        <div class="mb-0">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <select name="id_siswa" id="id_siswa" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $s)
                                    @php $selectedId = old('id_siswa', $row->id_siswa ?? null); @endphp
                                    <option value="{{ $s->id_siswa }}"
                                        data-id-kelas="{{ $s->id_kelas }}"
                                        {{ (string)$selectedId === (string)$s->id_siswa ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="info-siswa mt-3 small">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Daftar siswa akan disaring otomatis berdasarkan kelas dari jadwal yang dipilih.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: STATUS & TANGGAL --}}
            <div class="col-lg-5">
                <div class="card card-main h-100">
                    <div class="card-header-custom">
                        <span class="section-title-sm text-success">
                            <i class="bi bi-check2-square me-2"></i> Detail Kehadiran
                        </span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Tanggal --}}
                        <div class="mb-4">
                            <label class="form-label">Tanggal Absensi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="tanggal" class="form-control shadow-sm"
                                       value="{{ old('tanggal', \Carbon\Carbon::parse($row->tanggal)->toDateString()) }}" required>
                            </div>
                        </div>

                        {{-- Status Radios --}}
                        <div class="mb-4">
                            <label class="form-label d-block mb-3">Status Kehadiran</label>
                            <div class="row g-2">
                                @foreach(['hadir'=>'success','sakit'=>'warning','izin'=>'info','alfa'=>'danger'] as $st => $cl)
                                <div class="col-6">
                                    <input type="radio" class="btn-check" id="st_{{ $st }}"
                                           name="keterangan" value="{{ $st }}"
                                           {{ old('keterangan', $row->keterangan) == $st ? 'checked' : '' }}>
                                    <label for="st_{{ $st }}" class="btn btn-outline-{{ $cl }} w-100 py-2 rounded-3 small fw-bold">
                                        {{ ucfirst($st) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="keterangan_detail" class="form-control shadow-sm" rows="3"
                                      placeholder="Misal: Izin melalui telepon / surat dokter...">{{ old('keterangan_detail', $row->keterangan_detail) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer-save mt-4 d-flex justify-content-between align-items-center">
            <div class="d-none d-md-block">
                <span class="text-muted small">
                    <i class="bi bi-clock-history me-1"></i> 
                    Update terakhir: {{ $row->updated_at->format('d/m/Y H:i') }}
                </span>
            </div>

            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="{{ route('admin.absensi.index') }}" class="btn btn-light border px-4 rounded-3 flex-fill">Batal</a>
                <button type="submit" class="btn btn-success px-5 rounded-3 shadow-sm flex-fill">
                    <i class="bi bi-check-circle me-2"></i> Perbarui Data
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

        const allSiswa = Array.from(siswa.options).map(o => ({
            value: o.value,
            text: o.text,
            kelas: o.dataset.idKelas
        })).filter(o => o.value !== '');

        const initSiswa = "{{ old('id_siswa', $row->id_siswa ?? '') }}";

        function render(kelas, keepSelected = false){
            siswa.querySelectorAll('option:not([value=""])').forEach(o => o.remove());
            const filtered = kelas ? allSiswa.filter(s => s.kelas == kelas) : allSiswa;

            filtered.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.value;
                opt.textContent = s.text;
                opt.dataset.idKelas = s.kelas;
                if(keepSelected && s.value == initSiswa) opt.selected = true;
                siswa.appendChild(opt);
            });
        }

        jadwal.addEventListener('change', e => {
            const k = jadwal.selectedOptions[0]?.dataset.idKelas;
            render(k);
        });

        // Trigger awal
        render(jadwal.selectedOptions[0]?.dataset.idKelas, true);
    })();
</script>
@endpush
@endsection