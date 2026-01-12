{{-- resources/views/admin/guru/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Guru - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Container
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1065px;
    margin-left: auto;
    margin-right: auto;
}
@media(max-width:1400px){.container-page{padding-left:4rem;padding-right:4rem;}}
@media(max-width:992px){.container-page{padding-left:2rem;padding-right:2rem;}}
@media(max-width:576px){.container-page{padding-left:1rem;padding-right:1rem;}}

/* ============================
   Card
============================ */
.card-main {
    border-radius: 14px;
    border: none;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* ============================
   Form
============================ */
.form-label { font-weight: 600; color: #2c3e50; }
.form-control, .form-select { border-radius: 8px; border: 1px solid #dee2e6; padding: 0.55rem 1rem; }
.form-control:focus, .form-select:focus { border-color: #198754 !important; box-shadow: 0 0 0 .15rem rgba(25,135,84,0.25); }

/* ============================
   Section Title
============================ */
.section-title {
    font-weight: 700; 
    font-size: 0.95rem;
    color: #198754; 
    text-transform: uppercase; 
    letter-spacing:0.5px;
}

/* ============================
   Alerts
============================ */
.alert-error-modern {
    border-left: 4px solid #dc3545;
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 8px;
    padding: 15px 18px;
    font-weight: 600;
}
.alert-info-modern {
    border-left: 4px solid #ffc107;
    background-color: #fff9e6;
    color: #856404;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 0.85rem;
}

/* ============================
   Header Box
============================ */
.header-box {
    padding: 1.2rem 1rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}
</style>
@endpush

@section('content')
<div class="container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center header-box mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-plus-fill text-success me-2"></i> Tambah Data Guru Baru
            </h4>
            <p class="text-muted small mb-0">Daftarkan tenaga pendidik baru ke sistem.</p>
        </div>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary px-4 rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="alert-error-modern mb-4 shadow-sm">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan:
            </div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guru.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Kiri: Profil Guru --}}
            <div class="col-lg-7">
                <div class="card-main h-100">
                    <div class="mb-3">
                        <span class="section-title">
                            <i class="bi bi-person-circle me-2"></i> Informasi Profil
                        </span>
                    </div>

                    {{-- Nama Guru --}}
                    <div class="mb-4">
                        <label class="form-label">Nama Lengkap Guru <span class="text-danger">*</span></label>
                        <input type="text" name="nama_guru"
                               value="{{ old('nama_guru') }}"
                               class="form-control" required maxlength="255"
                               placeholder="Contoh: Nama Lengkap, Gelar">
                    </div>

                    {{-- NIP --}}
                    <div class="mb-0">
                        <label class="form-label">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip"
                               value="{{ old('nip') }}"
                               class="form-control" required maxlength="50"
                               placeholder="Nomor Induk Pegawai">
                    </div>
                </div>
            </div>

            {{-- Kanan: Akun --}}
            <div class="col-lg-5">
                <div class="card-main h-100">
                    <div class="mb-3">
                        <span class="section-title">
                            <i class="bi bi-shield-lock me-2"></i> Koneksi Akun
                        </span>
                    </div>

                    {{-- Info Box --}}
                    <div class="alert-info-modern mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        ID User akan dibuat otomatis oleh sistem saat guru disimpan. Tidak perlu diisi.
                    </div>

                    {{-- Hidden ID --}}
                    <input type="hidden" name="id_users" value="">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5">
                <i class="bi bi-save me-2"></i> Simpan Data Guru
            </button>
        </div>
    </form>

</div>
@endsection
