@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@php
    $primary_hex = '#00D452';
    $dark_accent = '#1a3a22';
    $muted_bg = '#f8f9fa';

    $user = auth()->user();
    
    $toShow = $jadwalsToday ?? collect();
    $todayName = $todayName ?? \Carbon\Carbon::now()->translatedFormat('l');
@endphp

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root{
        --dash-primary: {{ $primary_hex }};
        --dash-accent: {{ $dark_accent }};
        --muted-bg: {{ $muted_bg }};
        --card-radius: 16px;
        --shadow-soft: 0 10px 30px rgba(0,0,0,0.04);
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
    }

    /* Penyesuaian Font Global di Dashboard */
    .dashboard-container, 
    .dashboard-container h5, 
    .dashboard-container p, 
    .dashboard-container table {
        font-family: 'Poppins', sans-serif;
    }

    .card {
        background: #fff;
        border-radius: var(--card-radius);
        padding: 24px;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }

    /* =======================
       HEADER CARD
    ========================*/
    .page-header-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 30px;
        border-radius: 20px;
        background: white;
        border-left: 8px solid var(--dash-primary);
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    /* Efek hiasan background agar tidak polos */
    .page-header-card::before {
        content: "";
        position: absolute;
        top: 0; right: 0;
        width: 150px; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0,212,82,0.05));
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 25px;
        flex: 1;
        z-index: 1;
    }

    .page-header-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--dash-primary);
        color: #fff;
        font-size: 32px;
        box-shadow: 0 8px 20px rgba(0,212,82,0.25);
    }

    .page-header-title {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--dash-accent);
        letter-spacing: -0.5px;
    }

    .page-header-sub {
        margin: 4px 0 0 0;
        color: #6c757d;
        font-size: 1rem;
        font-weight: 400;
    }

    .page-header-right {
        flex-shrink: 0;
        z-index: 1;
    }

    .teacher-avatar-frame {
        width: 200px;
        height: 110px;
        border-radius: 14px;
        overflow: hidden;
        border: 4px solid #f0f0f0;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    /* =======================
       TABLE & TEXT
    ========================*/
    .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 16px 15px;
        color: #343a40;
        font-size: 0.95rem;
        border-bottom: 1px solid #f1f1f1;
    }

    .small-muted {
        color: #7d8a7d;
        font-size: 0.88rem;
    }

    .badge-kelas {
        background: rgba(0, 212, 82, 0.1);
        color: var(--dash-primary);
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    /* RESPONSIVE */
    @media (max-width: 991px) {
        .page-header-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 25px;
        }
        .page-header-right {
            width: 100%;
        }
        .teacher-avatar-frame {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- HEADER --}}
    <div class="page-header-card mb-4">
        <div class="page-header-left">
            <div class="page-header-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>

            <div>
            <h1 class="page-header-title">Selamat Datang, {{ $user->nama ?? 'Guru' }}!</h1>
            
            {{-- TAMBAHKAN KETERANGAN WALI KELAS DI SINI --}}
            @if($kelasWali)
                <div class="mt-1">
                    <span class="badge bg-success" style="font-weight: 500; border-radius: 6px;">
                        <i class="bi bi-star-fill me-1"></i> Wali Kelas: {{ $kelasWali->nama_kelas }}
                    </span>
                </div>
            @endif

            <p class="page-header-sub mt-2">
                <i class="bi bi-clock-history me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        </div>

        <div class="page-header-right">
            <div class="teacher-avatar-frame">
                <img src="{{ asset('guru/gambar.png') }}"
                     alt="Ilustrasi"
                     style="width:100%; height:100%; object-fit:cover;">
            </div>
        </div>
    </div>

    {{-- TABEL JADWAL --}}
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--dash-accent)">
                    <i class="bi bi-calendar-check me-2 text-success"></i> Jadwal Mengajar Hari Ini
                </h5>
                <p class="small-muted mb-0">Pastikan untuk melakukan presensi tepat waktu.</p>
            </div>

            <div class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-600">
                <span class="text-success">●</span> {{ $toShow->count() }} Mata Pelajaran
            </div>
        </div>

        <div class="table-responsive">
            @if($toShow->isEmpty())
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6161/6161320.png" width="80" class="mb-3" style="opacity: 0.5">
                    <h6 class="text-muted fw-normal">Tidak ada jadwal mengajar untuk hari ini.</h6>
                </div>
            @else
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($toShow as $j)
                        <tr>
                            <td class="fw-medium">
                                <i class="bi bi-clock me-2 text-muted"></i>
                                {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</div>
                                <div class="small-muted" style="font-size: 0.8rem">ID: #{{ $j->id_jadwal_mengajar }}</div>
                            </td>
                            <td>
                                <span class="badge-kelas">
                                    {{ $j->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td>
                            
                            <a href="{{ route('guru.absensi.index', ['id_jadwal_mengajar' => $j->id_jadwal_mengajar]) }}" 
                            class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-pencil-square me-1"></i> Absensi
                            </a>
                            <a href="{{ route('guru.absensi.scan', ['id_jadwal_mengajar' => $j->id_jadwal_mengajar]) }}" 
                            class="btn btn-sm btn-outline-success rounded-pill px-3">
                            <i class="bi bi-qr-code-scan me-1"></i> Scan Kartu
                            </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection