@extends('layouts.admin')

@section('title', 'Detail Mata Pelajaran - Admin')

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
    margin-left: 3rem;
    margin-right: 50rem;
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
   Buttons (Aksen Hijau)
============================ */
.btn-action {
    border-radius: 8px !important;
    padding: 6px 14px !important;
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

/* ============================
   Detail List
============================ */
.detail-list dt {
    font-weight: 600;
    color: #6c757d;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
}
.detail-list dd {
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 500;
    color: #212529;
    font-size: 0.95rem;
}
.detail-list dt:last-child,
.detail-list dd:last-child {
    border-bottom: none;
}

/* ============================
   Badge
============================ */
.badge-detail {
    font-weight: 600;
    padding: 0.45em 0.75em;
    border-radius: 6px;
}
.text-success-custom {
    color: #198754 !important;
}
</style>
@endpush

@section('content')
<div class="container container-page">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h4 class="mb-0">
            <i class="bi bi-book-half me-2 text-success"></i> Detail Mata Pelajaran
        </h4>

        <div class="d-flex gap-2">
            {{-- Tombol Edit Hijau --}}
            <a href="{{ route('admin.mapel.edit', $mapel->id_mata_pelajaran) }}" 
               class="btn btn-success-custom btn-sm btn-action shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>

            <a href="{{ route('admin.mapel.index') }}" 
               class="btn btn-outline-secondary btn-sm btn-action">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Card Detail --}}
    <div class="card">
        <h5 class="fw-bold mb-3 pb-2 text-secondary border-bottom">
            <i class="bi bi-info-circle me-1 text-success"></i> Informasi Mata Pelajaran
        </h5>

        <dl class="row detail-list mb-0">

            <dt class="col-sm-4">ID Mata Pelajaran</dt>
            <dd class="col-sm-8">
                <span class="badge bg-light text-dark border badge-detail">
                    #{{ $mapel->id_mata_pelajaran }}
                </span>
            </dd>

            <dt class="col-sm-4">Nama Mata Pelajaran</dt>
            <dd class="col-sm-8 fw-bold text-success-custom fs-5">{{ $mapel->nama_mapel }}</dd>

            <dt class="col-sm-4">Dibuat Pada</dt>
            <dd class="col-sm-8 text-muted">
                <i class="bi bi-calendar-event me-1"></i>
                {{ $mapel->created_at ? $mapel->created_at->format('d F Y, H:i') : '-' }}
            </dd>

            <dt class="col-sm-4">Terakhir Diubah</dt>
            <dd class="col-sm-8 text-muted">
                <i class="bi bi-clock-history me-1"></i>
                {{ $mapel->updated_at ? $mapel->updated_at->format('d F Y, H:i') : '-' }}
            </dd>

        </dl>
    </div>

</div>
@endsection