{{-- resources/views/admin/guru/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Guru - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Container Page
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}
@media(max-width: 1400px){ .container-page { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px){ .container-page { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px){ .container-page { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card & Form
============================ */
.card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    background: #fff;
}

.header-box {
    padding: 1rem 1rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}

.section-title {
    font-size: .85rem;
    font-weight: 700;
    color: #198754;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    font-size: .9rem;
}

.form-control {
    border-radius: 8px;
    padding: .65rem 1rem;
    border: 1px solid #d1d7dc;
    transition: all .2s ease;
}
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 .25rem rgba(25,135,84,.15);
}

.input-group-text {
    background: #f8f9fa;
    border-right: none;
}
.form-control.border-start-0 { border-left: none; }

.alert-error-modern {
    border-left: 4px solid #dc3545;
    background: #fff6f6;
    color: #842029;
    padding: 14px 18px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.btn-login {
    display: block;
    width: 100%;
    text-align: center;
    border-radius: 8px;
}

/* Footer Card */
.card-footer {
    border-radius: 14px;
    padding: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
</style>
@endpush

@section('content')
<div class="container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center header-box">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-pencil-square me-2 text-success"></i> Edit Data Guru
            </h4>
            <p class="text-muted small mb-0">Pastikan seluruh data sesuai dokumen kepegawaian.</p>
        </div>

        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary btn-action">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="alert-error-modern mb-4">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guru.update', $guru->id_guru) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- LEFT: IDENTITAS --}}
            <div class="col-lg-7">
                <div class="card h-100 p-4">
                    <span class="section-title mb-3 d-block">
                        <i class="bi bi-person-vcard me-2"></i> Identitas Utama
                    </span>

                    {{-- Nama --}}
                    <div class="mb-4">
                        <label class="form-label">Nama Lengkap Guru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nama_guru"
                                value="{{ old('nama_guru', $guru->nama_guru) }}"
                                class="form-control border-start-0 shadow-sm"
                                required maxlength="255"
                                placeholder="Contoh: Budi Santoso, S.Pd">
                        </div>
                    </div>

                    {{-- NIP --}}
                    <div class="mb-0">
                        <label class="form-label">NIP (Nomor Induk Pegawai) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                            <input type="text" name="nip"
                                value="{{ old('nip', $guru->nip) }}"
                                class="form-control border-start-0 shadow-sm"
                                required maxlength="50"
                                placeholder="Masukkan NIP resmi">
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: AKSES LOGIN --}}
            <div class="col-lg-5">
                <div class="card h-100 p-4">
                    <span class="section-title mb-3 d-block">
                        <i class="bi bi-gear-wide-connected me-2"></i> Akses Login
                    </span>

                    <a href="{{ route('admin.guru.login.index') }}" class="btn btn-primary btn-login shadow-sm py-2">
                        <i class="bi bi-person-lock me-1"></i> Kelola Login Guru
                    </a>

                    <div class="p-3 mt-3 rounded-3 bg-light border">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <p class="text-muted small mb-0">
                                Klik tombol di atas untuk mengatur akun login guru, reset password, atau sinkronisasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="mt-4 card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Tanda (<span class="text-danger">*</span>) wajib diisi
            </span>

            <div class="d-flex gap-3">
                <a href="{{ route('admin.guru.index') }}" class="btn btn-light border btn-action px-4">Batal</a>
                <button type="submit" class="btn btn-success btn-action px-5 shadow">
                    <i class="bi bi-check2-circle me-1"></i> Perbarui Data
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
