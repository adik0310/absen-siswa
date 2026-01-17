@extends('layouts.admin')

@section('title', 'Edit Jadwal Mengajar - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Container yang proporsional */
    .container-custom {
        padding: 0 45px;
    }

    /* Card Edit lebih compact */
    .card-edit {
        border-radius: 12px;
        border: none;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        background: #fff;
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }
    .form-control, .form-select {
        font-size: 0.9rem;
        border-color: #e2e8f0;
    }
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1);
    }

    /* Error Alert Modern */
    .alert-error-modern {
        border-left: 4px solid #dc3545;
        background-color: #fff5f5;
        color: #842029;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 0.85rem;
    }

    /* Section Title dengan aksen hijau */
    .section-title {
        font-weight: 700;
        color: #198754;
        margin-bottom: 1.2rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Soft Divider */
    .divider {
        border: none;
        height: 1px;
        background: #f1f5f9;
        margin: 20px 0;
    }

    /* Buttons */
    .btn-custom {
        padding: 7px 20px;
        font-weight: 600;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    
    .btn-success-custom {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    .btn-success-custom:hover {
        background-color: #146c43;
        border-color: #146c43;
        color: white;
    }

    .page-header-box {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')

@php
    $hariOptions = ['Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
@endphp

<div class="container-custom py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-box">
        <div>
            <h5 class="fw-bold mb-1 text-success">
                <i class="bi bi-pencil-square me-2"></i> Edit Jadwal Mengajar
            </h5>
            <p class="text-muted mb-0 small">Perbarui detail waktu atau penugasan guru.</p>
        </div>

        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-sm btn-outline-secondary px-3 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card card-edit">

        {{-- Error Alert --}}
        @if($errors->any())
            <div class="alert-error-modern mb-4">
                <div class="fw-bold mb-1 small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formEditJadwal" action="{{ route('admin.jadwal.update', $jadwal->id_jadwal_mengajar) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Section 1: Waktu & Hari --}}
            <div class="section-title">
                <i class="bi bi-clock-history"></i> Detail Waktu & Hari
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Hari <span class="text-danger">*</span></label>
                    <select name="hari" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach($hariOptions as $h)
                            <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>
                                {{ $h }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                    {{-- Input diubah ke type text agar manual tanpa ikon jam --}}
                    <input type="text" id="jam_mulai" name="jam_mulai" 
                        value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" 
                        class="form-control form-control-sm jam-input" 
                        placeholder="HH:mm (Contoh: 07:30)" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="text" id="jam_selesai" name="jam_selesai" 
                        value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" 
                        class="form-control form-control-sm jam-input" 
                        placeholder="HH:mm (Contoh: 09:00)" required>
                </div>
            </div>

            <hr class="divider">

            {{-- Section 2: Kelas & Mapel --}}
            <div class="section-title">
                <i class="bi bi-book-half"></i> Penugasan Kelas & Mapel
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select name="id_kelas" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}" {{ old('id_kelas', $jadwal->id_kelas) == $k->id_kelas ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="id_mapel" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id_mata_pelajaran }}" {{ old('id_mapel', $jadwal->id_mapel) == $m->id_mata_pelajaran ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                    <select name="id_guru" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}" {{ old('id_guru', $jadwal->id_guru) == $g->id_guru ? 'selected' : '' }}>
                                {{ $g->nama_guru }} ({{ $g->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Ruangan (Opsional)</label>
                    <input type="text" name="ruangan" value="{{ old('ruangan', $jadwal->ruangan) }}" class="form-control form-control-sm" maxlength="100" placeholder="Contoh: Lab Komputer A">
                </div>
            </div>

            <hr class="divider">

            {{-- Footer Buttons --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-light btn-custom border text-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-success-custom btn-custom">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // 1. Saat mengetik, otomatis ganti titik (.) jadi titik dua (:)
    document.querySelectorAll('.jam-input').forEach(function(input) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace('.', ':');
        });
    });

    // 2. Saat form disubmit, pastikan lagi tidak ada titik yang lolos
    document.getElementById('formEditJadwal').addEventListener('submit', function(e) {
        let jamMulai = document.getElementById('jam_mulai');
        let jamSelesai = document.getElementById('jam_selesai');

        if (jamMulai.value) jamMulai.value = jamMulai.value.replace('.', ':');
        if (jamSelesai.value) jamSelesai.value = jamSelesai.value.replace('.', ':');
    });
</script>

@endsection