@extends('layouts.admin')

@section('title', 'Data Guru - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Page Container (Full Width Consistency)
============================ */
main.container-main {
    max-width: 100% !important;
    padding-left: 40px !important;
    padding-right: 40px !important;
    margin-left: 80px !important;
    margin-right: 80px !important;
}

/* Header Box */
.header-box {
    padding: 1.5rem;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Card utama */
.card-main {
    border-radius: 15px;
    border: none;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

/* Table styling */
.table-rekap thead th {
    background: #f8fbf9;
    color: #555;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    padding: 1.2rem 1rem;
    border-bottom: 2px solid #e9ecef;
}
.table-rekap tbody td {
    vertical-align: middle;
    padding: 1rem;
    border-bottom: 1px solid #f8fafc;
}

/* Avatar Circle - Square Rounded Modern */
.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #198754, #28a745);
    color: #fff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
}

/* Badge status akun */
.badge-soft {
    border-radius: 50px;
    font-size: 0.75rem;
    padding: 6px 12px;
    font-weight: 700;
}
.bg-soft-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.bg-soft-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

/* Tombol Aksi */
.btn-action-circle {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.2s;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.btn-action-circle:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background: #fff;
}
.btn-view:hover { color: #0ea5e9; border-color: #0ea5e9; }
.btn-edit:hover { color: #10b981; border-color: #10b981; }
.btn-delete:hover { color: #ef4444; border-color: #ef4444; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="header-box d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-person-workspace text-success me-2"></i> Manajemen Guru
            </h3>
            <p class="text-muted mb-0 small">Kelola data tenaga pendidik dan akses akun mereka.</p>
        </div>
        <div>
            <a href="{{ route('admin.guru.create') }}" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i> Tambah Guru
            </a>
        </div>
    </div>

    {{-- Info Stats --}}
    <div class="mb-4">
        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-people-fill text-success me-2"></i> Total: <b>{{ $gurus->count() }}</b> Guru Terdaftar
        </span>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center">
            <div class="bg-success text-white rounded-circle p-2 me-3">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="fw-bold">{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card-main">
        @if($gurus->isEmpty())
            <div class="text-center py-5">
                <img src="https://illustrations.popsy.co/gray/data-report.svg" style="width: 180px;" class="opacity-50 mb-4">
                <h5 class="fw-bold text-muted">Belum Ada Data Guru</h5>
                <p class="text-muted small">Daftar guru yang Anda tambahkan akan muncul di sini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-rekap mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No</th>
                            <th>Profil Guru</th>
                            <th>NIP / Identitas</th>
                            <th>Status Akses Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $i => $g)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($g->nama_guru, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 fs-6">{{ $g->nama_guru }}</div>
                                        <div class="text-muted x-small" style="font-size: 0.75rem;">Tenaga Pendidik Aktif</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-inline-flex align-items-center px-2 py-1 bg-light rounded border">
                                    <i class="bi bi-card-heading me-2 text-success"></i>
                                    <span class="fw-bold text-secondary small">{{ $g->nip ?? 'NIP Belum Diatur' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($g->id_users)
                                    <span class="badge-soft bg-soft-success">
                                        <i class="bi bi-shield-check me-1"></i> Terkoneksi Akun
                                    </span>
                                @else
                                    <span class="badge-soft bg-soft-danger">
                                        <i class="bi bi-shield-slash me-1"></i> Belum Punya Akun
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.guru.show', $g->id_guru) }}" class="btn-action-circle btn-view" title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.guru.edit', $g->id_guru) }}" class="btn-action-circle btn-edit" title="Ubah Data">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.guru.delete', $g->id_guru) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data guru {{ $g->nama_guru }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-action-circle btn-delete" title="Hapus Permanen">
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