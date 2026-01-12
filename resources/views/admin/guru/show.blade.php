@extends('layouts.admin')

@section('title', 'Detail Guru - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* Perbaikan Container agar Seimbang */
.container-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.5rem;
}

/* Card Profile */
.card-profile {
    border-radius: 16px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    overflow: hidden;
    background: #fff;
    height: 100%;
}

/* Header BG menggunakan Hijau MA */
.profile-header-bg {
    height: 120px;
    background: linear-gradient(135deg, #15803d, #22c55e);
}

.profile-avatar-wrapper {
    margin-top: -60px;
    display: flex;
    justify-content: center;
    margin-bottom: 15px;
}

.profile-avatar {
    width: 110px;
    height: 110px;
    background: #fff;
    border-radius: 50%;
    font-size: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #15803d;
    border: 5px solid #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Info Label & Value */
.info-label {
    font-size: .75rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.info-value {
    font-size: 1.05rem;
    color: #1e293b;
    padding-bottom: 12px;
    margin-bottom: 15px;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 600;
}

.info-value:last-child { border-bottom: none; }

/* Badges Modern */
.badge-soft-success {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    padding: 8px 15px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
}

.badge-soft-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fee2e2;
    padding: 8px 15px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
}

/* Header Box */
.header-box {
    padding: 1.25rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.03);
    margin-bottom: 1.5rem;
}

.btn-action {
    border-radius: 10px;
    padding: 0.6rem 1.25rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="container-page py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center header-box gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-person-badge text-success me-2"></i> Detail Profil Guru
            </h4>
            <p class="text-muted small mb-0">Informasi lengkap data personal dan akses sistem.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary btn-action bg-white">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.guru.edit', $guru->id_guru) }}" class="btn btn-success btn-action shadow-sm px-4">
                <i class="bi bi-pencil-square me-1"></i> Edit Profil
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left Card: Identitas Utama --}}
        <div class="col-lg-4">
            <div class="card-profile text-center">
                <div class="profile-header-bg"></div>

                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <h5 class="fw-bold text-dark mb-1">{{ $guru->nama_guru }}</h5>
                    <p class="text-muted small mb-4">NIP: <span class="fw-bold text-dark">{{ $guru->nip ?? '-' }}</span></p>

                    <div class="text-start mt-4 pt-3 border-top">
                        <div class="info-label">Status Akses Sistem</div>
                        <div class="mt-2">
                            @if($guru->id_users)
                                <div class="badge-soft-success w-100 text-center">
                                    <i class="bi bi-check-circle-fill me-2"></i> Akun Aktif
                                </div>
                                <p class="text-muted small text-center mt-2 mb-0">Terhubung dengan ID User: #{{ $guru->id_users }}</p>
                            @else
                                <div class="badge-soft-danger w-100 text-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Belum Memiliki Akun
                                </div>
                                <p class="text-muted small text-center mt-2 mb-0">Segera lakukan sinkronisasi akun.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Card: Detail Informasi --}}
        <div class="col-lg-8">
            <div class="card-profile p-4 p-md-5">

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white rounded-3 p-2 me-3">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Informasi Kepegawaian</h5>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-label">ID Sistem Guru</div>
                        <div class="info-value">#{{ $guru->id_guru }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Nomor Induk Pegawai (NIP)</div>
                        <div class="info-value text-success">{{ $guru->nip ?? 'Tidak tersedia' }}</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-label">Tanggal Terdaftar</div>
                        <div class="info-value">
                            <i class="bi bi-calendar-event me-2 text-muted"></i>
                            {{ $guru->created_at ? $guru->created_at->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Waktu Pendaftaran</div>
                        <div class="info-value">
                            <i class="bi bi-clock me-2 text-muted"></i>
                            {{ $guru->created_at ? $guru->created_at->format('H:i') : '-' }} WIB
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="info-label">Pembaruan Data Terakhir</div>
                        <div class="info-value">
                            <i class="bi bi-arrow-clockwise me-2 text-muted"></i>
                            {{ $guru->updated_at ? $guru->updated_at->diffForHumans() : '-' }}
                        </div>
                    </div>
                </div>

                {{-- Alert/Notice Section --}}
                <div class="mt-5 p-3 rounded-4 bg-light border-start border-4 border-success">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-shield-lock-fill text-success fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Data Terverifikasi</h6>
                            <p class="text-muted small mb-0">
                                Profil ini bersifat rahasia. Perubahan data NIP dan Nama akan berdampak pada laporan presensi bulanan dan kredensial login guru.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection