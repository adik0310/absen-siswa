@extends('layouts.admin')

@section('title', 'Detail Jadwal Mengajar - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Container proporsional sesuai standar halaman sebelumnya */
    .container-custom {
        padding: 0 45px;
    }

    /* Card Detail Styling */
    .card-detail {
        border-radius: 12px;
        border: none;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        background: #fff;
    }

    /* List Styling */
    .detail-list dt {
        font-weight: 600;
        color: #64748b;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .detail-list dd {
        font-weight: 500;
        color: #1e293b;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        margin-bottom: 0;
    }
    .detail-list dt:last-of-type,
    .detail-list dd:last-of-type {
        border-bottom: none;
    }

    /* Badge Hari - Tetap berwarna namun font disesuaikan */
    .badge-hari {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .hari-senin  { background: #fee2e2; color: #b91c1c; }
    .hari-selasa { background: #ffedd5; color: #c2410c; }
    .hari-rabu   { background: #fef9c3; color: #a16207; }
    .hari-kamis  { background: #dcfce7; color: #15803d; }
    .hari-jumat  { background: #e0f2fe; color: #0369a1; }
    .hari-sabtu  { background: #f3e8ff; color: #7e22ce; }

    /* Custom Header Box */
    .page-header-box {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .text-success-custom {
        color: #198754 !important;
    }

    .badge-class {
        background-color: #475569;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container-custom py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-box">
        <div>
            <h5 class="fw-bold mb-1 text-success-custom">
                <i class="bi bi-info-circle-fill me-2"></i> Detail Jadwal Mengajar
            </h5>
            <p class="text-muted mb-0 small">Informasi lengkap mengenai agenda pembelajaran.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.jadwal.edit', $jadwal->id_jadwal_mengajar) }}"
               class="btn btn-success btn-sm px-3 rounded-pill shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Jadwal
            </a>

            <a href="{{ route('admin.jadwal.index') }}"
               class="btn btn-sm btn-outline-secondary px-3 rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="card card-detail">
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-calendar2-check text-success fs-3"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="fw-bold text-dark mb-0">Informasi Jadwal</h5>
                <small class="text-muted">ID Registrasi: #{{ $jadwal->id_jadwal_mengajar }}</small>
            </div>
        </div>

        <dl class="row detail-list">
            <dt class="col-sm-3">Hari</dt>
            <dd class="col-sm-9">
                <span class="badge-hari hari-{{ strtolower($jadwal->hari) }}">
                    {{ $jadwal->hari }}
                </span>
            </dd>

            <dt class="col-sm-3">Mata Pelajaran</dt>
            <dd class="col-sm-9 fw-bold">
                {{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}
            </dd>

            <dt class="col-sm-3">Kelas</dt>
            <dd class="col-sm-9">
                <span class="badge-class">
                    {{ $jadwal->kelas->nama_kelas ?? '-' }}
                </span>
            </dd>

            <dt class="col-sm-3">Guru Pengajar</dt>
            <dd class="col-sm-9">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-success-custom me-2 fs-5"></i>
                    <span>{{ $jadwal->guru->nama_guru ?? '-' }}</span>
                </div>
            </dd>

            <dt class="col-sm-3">Waktu Belajar</dt>
            <dd class="col-sm-9 fw-bold text-success-custom">
                <i class="bi bi-clock-history me-1"></i>
                {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '--:--' }} 
                <span class="text-muted mx-2">s/d</span> 
                {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '--:--' }}
            </dd>

            <dt class="col-sm-3">Ruangan</dt>
            <dd class="col-sm-9 text-secondary">
                <i class="bi bi-geo-alt-fill me-1"></i>
                {{ $jadwal->ruangan ?? 'Belum Ditentukan' }}
            </dd>

            <dt class="col-sm-3">Log Sistem</dt>
            <dd class="col-sm-9">
                <div class="row small text-muted">
                    <div class="col-md-6">
                        <i class="bi bi-plus-circle me-1"></i> Input: {{ $jadwal->created_at ? $jadwal->created_at->format('d/m/Y H:i') : '-' }}
                    </div>
                    <div class="col-md-6">
                        <i class="bi bi-pencil me-1"></i> Update: {{ $jadwal->updated_at ? $jadwal->updated_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
            </dd>
        </dl>
    </div>
</div>
@endsection