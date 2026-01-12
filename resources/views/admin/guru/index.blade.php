@extends('layouts.admin')

@section('title', 'Data Guru - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* Container seragam sesuai permintaan */
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

/* Header Box */
.header-box {
    padding: 1.2rem 1.5rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
}

/* Card utama */
.card-main {
    border-radius: 14px;
    border: none;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

/* Table styling */
.table-rekap thead th {
    background: #f8fbf9;
    color: #2d3436;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1.2rem 1rem;
    border-bottom: 2px solid #e9ecef;
}
.table-rekap tbody td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f1f1;
}
.table-hover tbody tr:hover {
    background-color: #f0fdf4; /* Hover hijau sangat muda */
}

/* Avatar Circle */
.avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 12px; /* Square rounded looks modern */
    background: #198754;
    color: #fff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2);
}

/* Badge status akun */
.badge-soft {
    border-radius: 8px;
    font-size: 0.7rem;
    padding: 6px 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.bg-soft-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.bg-soft-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

/* Alerts */
.alert-modern {
    border-left: 5px solid #198754;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    color: #2d3436;
}

/* Tombol Aksi */
.btn-action {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.2s;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.btn-action:hover {
    transform: translateY(-2px);
    background: #fff;
}
.btn-view:hover { color: #0ea5e9; border-color: #0ea5e9; }
.btn-edit:hover { color: #10b981; border-color: #10b981; }
.btn-delete:hover { color: #ef4444; border-color: #ef4444; }
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="header-box d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-person-workspace text-success me-2"></i> Manajemen Guru
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active text-success fw-bold" aria-current="page">Data Guru</li>
                </ol>
            </nav>
        </div>
        <div class="text-end">
            <a href="{{ route('admin.guru.create') }}" class="btn btn-success px-4 rounded-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Guru
            </a>
        </div>
    </div>

    {{-- Info Stats --}}
    <div class="mb-4">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
            <i class="bi bi-people-fill text-success me-2"></i> Total Terdaftar: <b>{{ $gurus->count() }}</b> Guru Aktif
        </span>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-modern mb-4 d-flex align-items-center shadow-sm">
            <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
            <div>
                <div class="fw-bold">Berhasil!</div>
                <div class="small opacity-75">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card-main">
        @if($gurus->isEmpty())
            <div class="text-center py-5">
                <img src="https://illustrations.popsy.co/gray/data-report.svg" style="width: 140px;" class="opacity-50 mb-3">
                <h5 class="fw-bold text-muted">Belum Ada Data Guru</h5>
                <p class="text-muted small">Klik tombol "Tambah Guru" untuk mengisi data.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-rekap mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Lengkap</th>
                            <th>Identitas (NIP)</th>
                            <th>Status Akses</th>
                            <th class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $i => $g)
                        <tr>
                            <td class="text-center text-muted fw-bold small">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($g->nama_guru, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">{{ $g->nama_guru }}</div>
                                        <div style="font-size: 0.75rem;" class="text-muted">Tenaga Pendidik</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-medium">
                                    <i class="bi bi-card-text me-1 text-success"></i>
                                    {{ $g->nip ?? 'Belum Diatur' }}
                                </span>
                            </td>
                            <td>
                                @if($g->id_users)
                                    <span class="badge-soft bg-soft-success">
                                        <i class="bi bi-shield-check me-1"></i> Terkoneksi
                                    </span>
                                @else
                                    <span class="badge-soft bg-soft-danger">
                                        <i class="bi bi-shield-slash me-1"></i> No Account
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.guru.show', $g->id_guru) }}" class="btn-action btn-view" title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.guru.edit', $g->id_guru) }}" class="btn-action btn-edit" title="Ubah Data">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.guru.delete', $g->id_guru) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data guru {{ $g->nama_guru }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-action btn-delete" title="Hapus Permanen">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection