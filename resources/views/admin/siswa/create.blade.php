@extends('layouts.admin')

@section('title', 'Tambah Siswa - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
    Page Container (Sesuai Ukuran Anda)
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
    Card & Form
============================ */
.card-main {
    border-radius: 14px;
    border: none;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    background-color: #fff;
}

/* Form Labels */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}

/* Input Styles */
.form-control, .form-select {
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    border: 1px solid #dee2e6;
}

/* Input focus glow (Aksen Hijau) */
.form-control:focus,
.form-select:focus {
    border-color: #198754 !important;
    box-shadow: 0 0 0 .15rem rgba(25,135,84,0.25) !important;
}

/* Error Alert */
.alert-error-modern {
    border-left: 4px solid #dc3545;
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 8px;
    padding: 15px 18px;
    font-weight: 600;
}

/* Header Box */
.header-box {
    padding: 1.2rem 1rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}

/* Buttons */
.btn-success-custom {
    background-color: #198754;
    border-color: #198754;
    color: white;
    border-radius: 8px;
    font-weight: 500;
}
.btn-success-custom:hover {
    background-color: #146c43;
    color: white;
}
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center header-box mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-plus-fill text-success me-2"></i> Tambah Data Siswa Baru
        </h4>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary px-4 rounded-3 btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card-main">
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-1">Registrasi Siswa</h5>
            <p class="text-muted small">Lengkapi informasi di bawah ini untuk menambahkan siswa baru ke dalam sistem.</p>
        </div>

        {{-- Error Handling --}}
        @if($errors->any())
            <div class="alert-error-modern mb-4 shadow-sm">
                <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Terjadi kesalahan:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.siswa.store') }}" method="POST">
            @csrf

            <div class="row g-4">

                {{-- Kolom Kiri --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-person-badge text-success me-1"></i> Nama Siswa
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_siswa"
                               value="{{ old('nama_siswa') }}"
                               class="form-control" required maxlength="255"
                               placeholder="Nama lengkap siswa">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-person-vcard text-success me-1"></i> NIS
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nis"
                               value="{{ old('nis') }}"
                               class="form-control" required maxlength="50"
                               placeholder="Nomor Induk Siswa">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-gender-ambiguous text-success me-1"></i> Jenis Kelamin
                            <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-building text-success me-1"></i> Kelas
                            <span class="text-danger">*</span>
                        </label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id_kelas }}"
                                    {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr class="mt-4 mb-4 opacity-25">

            {{-- Tombol --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-light border px-4 rounded-3 text-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-success-custom px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Data Siswa
                </button>
            </div>

        </form>
    </div>
</div>
@endsection