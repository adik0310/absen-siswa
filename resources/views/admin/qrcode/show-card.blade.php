@extends('layouts.admin')

@section('title', 'Pratinjau Kartu Pelajar')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark">Pratinjau Kartu Absensi</h4>
                <p class="text-muted small">Ukuran kartu telah disesuaikan dengan standar KTP (85.6mm x 53.9mm)</p>
            </div>

            {{-- Desain Kartu --}}
            <div class="id-card-container mx-auto">
                <div class="id-card-header">
                    <img src="{{ asset('image/logo_ma.png') }}" alt="Logo" class="card-logo">
                    <div class="header-text text-start">
                        <h6 class="school-name">MA NURUL IMAN</h6>
                        <p class="school-address">Sistem Presensi Kartu Digital</p>
                    </div>
                </div>

                <div class="id-card-body">
                    <div class="row g-0 w-100 align-items-center">
                        {{-- QR Code --}}
                        <div class="col-5 text-center border-end">
                            <div class="qr-wrapper-large">
                                <div class="qr-code-bg">
                                    {!! $qrcode !!}
                                </div>
                                <div class="scan-me-text">SCAN UNTUK ABSEN</div>
                            </div>
                        </div>

                        {{-- Data Siswa dengan Warna Senada --}}
                        <div class="col-7 px-3">
                            <div class="info-group">
                                <label>Nama Lengkap</label>
                                <div class="info-value theme-color text-uppercase">{{ $siswa->nama_siswa }}</div>
                            </div>
                            <div class="info-group">
                                <label>Nomor Induk Siswa</label>
                                <div class="info-value theme-color">{{ $siswa->nis }}</div>
                            </div>
                            <div class="info-group mb-0">
                                <label>Kelas</label>
                                <div class="info-value mt-1">
                                    <span class="badge-kelas">KELAS {{ $siswa->kelas->nama_kelas ?? $siswa->kelas }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="id-card-footer d-flex justify-content-between px-3">
                    <span>MA Nurul Iman - Official Card</span>
                    <span>TP. 2025/2026</span>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-center gap-3 mt-5 no-print">
                <a href="{{ route('admin.qrcode.siswa') }}" class="btn btn-outline-secondary px-4 shadow-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('admin.qrcode.print-card', $siswa->id_siswa) }}" class="btn btn-success px-4 shadow-sm rounded-pill">
                    <i class="bi bi-printer-fill me-2"></i> Cetak Kartu
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .id-card-container {
        width: 323px;
        height: 204px;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        font-family: 'Segoe UI', Roboto, sans-serif;
    }

    .id-card-header {
        background: linear-gradient(135deg, #00D452 0%, #008a35 100%);
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
        border-bottom: 2px solid #ffca28;
    }

    .card-logo {
        width: 30px;
        height: 30px;
        object-fit: contain;
        background: white;
        padding: 2px;
        border-radius: 4px;
    }

    .school-name { margin: 0; font-weight: 800; font-size: 11px; letter-spacing: 0.5px; }
    .school-address { margin: 0; font-size: 7.5px; opacity: 0.9; }

    .id-card-body {
        flex-grow: 1;
        display: flex;
        align-items: center;
        background-color: #fafafa;
    }

    .qr-wrapper-large { padding: 5px; }
    .qr-code-bg {
        background: white;
        display: inline-block;
        padding: 6px;
        border-radius: 8px;
        border: 1.5px solid #00D452; /* Border QR warna senada */
    }
    .qr-code-bg svg { width: 85px !important; height: 85px !important; }
    
    .scan-me-text {
        font-size: 7px;
        font-weight: 800;
        color: #008a35;
        margin-top: 4px;
    }

    /* WARNA SENADA UNTUK DATA */
    .theme-color {
        color: #006b29 !important; /* Hijau tua agar senada dengan logo/header */
    }

    .info-group { margin-bottom: 8px; }
    .info-group label {
        display: block;
        font-size: 6.5px;
        font-weight: 700;
        color: #666; /* Warna label abu tua agar bersih */
        text-transform: uppercase;
        margin-bottom: 0px;
    }
    .info-value {
        font-weight: 800;
        font-size: 11px;
        line-height: 1.1;
    }

    .badge-kelas {
        background: #008a35;
        color: white;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 700;
        display: inline-block;
    }

    .id-card-footer {
        background: #222;
        padding: 4px 15px;
        font-size: 7.5px;
        color: #fff;
    }

    @media print {
        .no-print { display: none !important; }
        .id-card-container { box-shadow: none !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection