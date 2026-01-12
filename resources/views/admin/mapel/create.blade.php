@extends('layouts.admin')

@section('title', 'Tambah Mata Pelajaran - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Container (Tetap sesuai ukuran Anda)
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1080px;
    margin-left: 1rem;
    margin-right: 55rem;
}
@media(max-width: 1400px) { .container-page { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px) { .container-page { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px) { .container-page { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card
============================ */
.card {
    border-radius: 14px;
    border: none;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* ============================
   Header
============================ */
.page-header h4 {
    font-weight: 700;
    color: #2b2b2b;
}

/* ============================
   Form
============================ */
.form-label {
    font-weight: 600;
    color: #343a40;
}
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
}

/* ============================
   Error Alert
============================ */
.alert-error-modern {
    border-left: 4px solid #dc3545;
    background-color: #fbe9ea;
    color: #842029;
    border-radius: 8px;
    padding: 15px 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

/* ============================
   Buttons
============================ */
.btn-action {
    border-radius: 8px !important;
    padding: 6px 16px !important;
    font-weight: 500;
}
.btn-success-custom {
    background-color: #198754;
    border-color: #198754;
    color: white;
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
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h4 class="mb-0">
            <i class="bi bi-plus-circle-fill me-2 text-success"></i> Tambah Mata Pelajaran Baru
        </h4>

        <a href="{{ route('admin.mapel.index') }}" 
           class="btn btn-outline-secondary btn-sm btn-action">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-1">Informasi Kurikulum</h5>
            <p class="text-muted small">Inputkan nama mata pelajaran baru untuk ditambahkan ke sistem.</p>
        </div>

        {{-- Error Alert --}}
        @if($errors->any())
            <div class="alert-error-modern mb-4">
                <div class="fw-bold mb-2"><i class="bi bi-exclamation-circle me-2"></i>Terjadi kesalahan:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.mapel.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-journal-plus"></i>
                    </span>
                    <input type="text"
                           name="nama_mapel"
                           value="{{ old('nama_mapel') }}"
                           class="form-control border-start-0"
                           required
                           maxlength="255"
                           placeholder="Contoh: Matematika, Bahasa Indonesia">
                </div>
            </div>

            <hr class="my-4 text-secondary opacity-25">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.mapel.index') }}" 
                   class="btn btn-light btn-action border text-secondary px-4">
                   Batal
                </a>

                <button type="submit" class="btn btn-success-custom btn-action px-4">
                    <i class="bi bi-save me-1"></i> Simpan Mata Pelajaran
                </button>
            </div>

        </form>
    </div>

</div>
@endsection