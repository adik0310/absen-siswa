{{-- resources/views/admin/guru/login.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Login Guru')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* Container utama */
.container-page {
    padding: 1.5rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Card utama */
.card-main {
    border-radius: 12px;
    border: none;
    background-color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

/* Header Tabel - Menggunakan warna dasar hijau gelap yang soft */
.table-rekap thead th {
    background-color: #f0fdf4; 
    font-weight: 700;
    color: #166534;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.05em;
    padding: 1.2rem 1rem;
    border-bottom: 2px solid #dcfce7;
}

.table-rekap tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

/* Badge Status Hijau & Merah MA */
.badge-status {
    padding: 0.45rem 0.75rem;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.72rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.bg-soft-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.bg-soft-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

/* Info Banner - Dirubah dari Biru ke Hijau Soft */
.info-banner {
    background: #f0fdf4;
    border-left: 5px solid #22c55e;
    border-radius: 12px;
    padding: 1.2rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 1.2rem;
    align-items: center;
}

/* Tombol Aksi */
.btn-action {
    padding: 0.5rem 0.9rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}

/* Custom warna tombol primary agar tetap hijau bertema */
.btn-outline-primary {
    color: #15803d;
    border-color: #15803d;
}
.btn-outline-primary:hover {
    background-color: #15803d;
    border-color: #15803d;
}

.text-id-guru { font-family: monospace; font-size: 0.75rem; color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-page">

    {{-- TOP HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-check text-success me-2"></i>Kelola Login Guru
            </h4>
            <p class="text-muted small mb-0">Manajemen akses akun dan keamanan data pengajar MA Nurul Iman.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary btn-action bg-white shadow-sm text-dark">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            <form action="{{ route('admin.guru.login.sync') }}" method="POST"
                  onsubmit="return confirm('Sistem akan membuatkan akun otomatis untuk guru yang belum punya. Lanjutkan?');">
                @csrf
                <button class="btn btn-success btn-action shadow-sm px-4">
                    <i class="bi bi-arrow-repeat me-1"></i> Sinkronisasi Akun
                </button>
            </form>
        </div>
    </div>

    {{-- INFO BANNER - Sekarang bertema Hijau --}}
    <div class="info-banner shadow-sm">
        <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm"
             style="width: 45px; height: 45px; flex-shrink: 0;">
            <i class="bi bi-key-fill fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-success">Keamanan Kredensial</h6>
            <p class="text-dark small mb-0 opacity-75">
                Password default: <mark class="bg-success text-white fw-bold rounded px-2 small">guru123</mark>. 
                Pastikan guru memperbarui password secara mandiri di halaman pengaturan profil.
            </p>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card-main">
        <div class="table-responsive">
            <table class="table table-hover table-rekap align-middle mb-0">
                <thead>
                    <tr class="text-center">
                        <th style="width:60px;">No</th>
                        <th class="text-start">Nama & Identitas Guru</th>
                        <th>Status Akun</th>
                        <th class="text-start">Username & Email</th>
                        <th style="width: 250px;">Aksi Keamanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $i => $guru)
                        <tr class="text-center">
                            <td class="text-muted fw-bold small">{{ $i + 1 }}</td>

                            <td class="text-start">
                                <div class="fw-bold text-dark mb-0">{{ $guru->nama_guru }}</div>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="text-id-guru">ID: {{ $guru->id_guru }}</span>
                                    <span class="text-muted small">|</span>
                                    <span class="small text-success fw-semibold">NIP: {{ $guru->nip }}</span>
                                </div>
                            </td>

                            <td>
                                @if($guru->user)
                                    <span class="badge-status bg-soft-success">
                                        <i class="bi bi-shield-fill-check"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge-status bg-soft-danger">
                                        <i class="bi bi-shield-fill-exclamation"></i> Non-Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="text-start">
                                @if($guru->user)
                                    <div class="small fw-bold text-dark">
                                        <i class="bi bi-person-fill text-success me-1"></i> {{ $guru->user->username ?? '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-envelope-fill me-1"></i> {{ $guru->user->email ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">Akses belum dibuat</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('admin.guru.edit', $guru->id_guru) }}" 
                                       class="btn btn-sm btn-outline-success btn-action" 
                                       title="Edit Data Guru">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    @if($guru->user)
                                        <form action="{{ route('admin.guru.login.reset', $guru->id_guru) }}"
                                              method="POST" 
                                              onsubmit="return confirm('Reset password ke default (guru123)?');">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-warning btn-action" title="Reset Password">
                                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light disabled btn-action border text-muted opacity-50">
                                            <i class="bi bi-lock"></i> Kosong
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-person-x text-muted opacity-25" style="font-size: 4rem;"></i>
                                <h6 class="mt-3 fw-bold text-muted">Data Guru Tidak Ditemukan</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        
</div>
@endsection