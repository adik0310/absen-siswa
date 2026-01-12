@extends('layouts.admin')

@section('title', 'Detail Siswa - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* ============================
       Container (Konsisten)
    ============================ */
    .container-page {
        padding-left: 8rem;
        padding-right: 8rem;
        max-width: 1065px;
        margin-left: 1rem;
        margin-right: 10rem;
    }
    @media(max-width: 1400px) { .container-page { padding-left: 4rem; padding-right: 4rem; } }
    @media(max-width: 992px) { .container-page { padding-left: 2rem; padding-right: 2rem; } }
    @media(max-width: 576px) { .container-page { padding-left: 1rem; padding-right: 1rem; } }

    /* Card Style */
    .card-detail {
        border-radius: 14px;
        border: none;
        padding: 2rem;
        background: #fff;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.07);
    }

    /* Header Box */
    .header-box {
        background: #fff;
        padding: 1.3rem;
        border-radius: 14px;
        box-shadow: 0px 3px 10px rgba(0,0,0,0.06);
    }

    /* Detail list */
    .detail-list dt {
        font-weight: 600;
        color: #6c757d;
        padding: 15px 0;
        border-bottom: 1px solid #f1f1f1;
        font-size: 0.9rem;
    }

    .detail-list dd {
        padding: 15px 0;
        font-weight: 500;
        color: #2d3436;
        border-bottom: 1px solid #f1f1f1;
        font-size: 0.95rem;
    }

    .detail-list dt:last-of-type,
    .detail-list dd:last-of-type {
        border-bottom: none;
    }

    /* Badge Detail */
    .badge-detail {
        font-weight: 600;
        padding: 0.5em 0.85em;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    .bg-success-light {
        background-color: #d1e7dd;
        color: #0f5132;
    }
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 header-box">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-check-fill text-success me-2"></i>
            Detail Siswa
        </h4>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}"
               class="btn btn-success btn-sm px-3 rounded-3 shadow-sm border-0"
               style="background-color: #198754;">
                <i class="bi bi-pencil-square me-1"></i> Edit Data
            </a>

            <a href="{{ route('admin.siswa.index') }}"
               class="btn btn-outline-secondary btn-sm px-3 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="card-detail">

        <h5 class="fw-bold mb-4 pb-2 text-dark border-bottom border-2 border-success border-opacity-25">
            Informasi Profil Siswa
        </h5>

        <dl class="row detail-list mb-0">

            <dt class="col-sm-3">ID Sistem</dt>
            <dd class="col-sm-9">
                <span class="badge bg-light text-muted border badge-detail">#{{ $siswa->id_siswa }}</span>
            </dd>

            <dt class="col-sm-3">Nama Lengkap</dt>
            <dd class="col-sm-9 fw-bold text-dark">{{ $siswa->nama_siswa }}</dd>

            <dt class="col-sm-3">Nomor Induk Siswa (NIS)</dt>
            <dd class="col-sm-9 text-success fw-bold">{{ $siswa->nis }}</dd>

            <dt class="col-sm-3">Jenis Kelamin</dt>
            <dd class="col-sm-9">
                @if($siswa->jenis_kelamin == 'L')
                    <span class="badge bg-primary bg-opacity-10 text-primary badge-detail border border-primary border-opacity-25">
                        <i class="bi bi-gender-male me-1"></i> Laki-laki
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger badge-detail border border-danger border-opacity-25">
                        <i class="bi bi-gender-female me-1"></i> Perempuan
                    </span>
                @endif
            </dd>

            <dt class="col-sm-3">Kelas Saat Ini</dt>
            <dd class="col-sm-9">
                <span class="badge bg-success-light badge-detail border border-success border-opacity-25">
                    <i class="bi bi-building me-1"></i>
                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                </span>
            </dd>

            <dt class="col-sm-3">Waktu Registrasi</dt>
            <dd class="col-sm-9 text-muted small">
                <i class="bi bi-calendar-event me-1"></i>
                {{ $siswa->created_at ? $siswa->created_at->format('d F Y, H:i') : '-' }}
            </dd>

            <dt class="col-sm-3">Pembaruan Terakhir</dt>
            <dd class="col-sm-9 text-muted small">
                <i class="bi bi-clock-history me-1"></i>
                {{ $siswa->updated_at ? $siswa->updated_at->format('d F Y, H:i') : '-' }}
            </dd>

        </dl>
    </div>
</div>
@endsection