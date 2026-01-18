@extends('layouts.guru')

@section('title', 'Jadwal Mengajar')

@php
use Carbon\Carbon;

$jadwals = $jadwals ?? collect();

$hariMap = [
    'senin'  => 1,
    'selasa' => 2,
    'rabu'   => 3,
    'kamis'  => 4,
    'jumat'  => 5,
    'sabtu'  => 6,
    'minggu' => 7,
];

$now = Carbon::now()->locale('id');
$todayName = strtolower(trim($now->isoFormat('dddd')));
$todayTime = $now->format('H:i:s');
@endphp

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
:root{
    --primary:#00D452;
    --accent:#1f3d2e;
    --surface:#ffffff;
    --muted:#6b7f69;
    --shadow:0 4px 14px rgba(0,0,0,0.06);
    --highlight:#e8f8f0;
}

.container-narrow{
    max-width:1000px;
    margin:30px auto;
    padding:0 50px;
}

.page-title-box{
    display:flex;
    gap:14px;
    align-items:center;
    margin-bottom:24px;
}

.page-sub{
    color:var(--muted);
    font-size:.9rem;
}

.card-list-container{
    background:var(--surface);
    border-radius:14px;
    padding:26px;
    box-shadow:var(--shadow);
    border:1px solid rgba(0,0,0,0.08);
    margin-bottom:26px;
}

.list-grid{ display:grid; gap:12px; }

.list-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 22px;
    background:#fafafa;
    border-radius:12px;
    border:1px solid rgba(0,0,0,0.08);
    transition:.2s;
}

.list-item:hover{
    background:white;
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(0,0,0,0.08);
}

.list-item.today{
    background:var(--highlight);
    border-left:6px solid var(--primary);
}

.schedule-title{
    font-weight:700;
    color:var(--accent);
    font-size:1.15rem;
    margin-bottom:6px;
}

.schedule-details{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    font-size:.9rem;
    color:var(--muted);
}

.class-tag{
    background:var(--primary);
    color:#fff;
    padding:4px 10px;
    border-radius:6px;
    font-size:.8rem;
    font-weight:500;
}

.date-tag{
    border:1px dashed var(--primary);
    padding:4px 10px;
    border-radius:6px;
    color:var(--accent);
    font-weight:500;
    font-size:.8rem;
    background:white;
}

.btn-action-group{
    display:flex;
    gap:8px;
    flex-shrink:0;
    flex-wrap:wrap;
    margin-left:14px;
}

.btn-action-group .btn{
    padding:.40rem 1rem;
    border-radius:10px;
    font-weight:500;
    font-size:.8rem;
    min-width:130px;
}

.btn.disabled, .btn:disabled {
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
}

@media(max-width:900px){
    .list-item{
        flex-direction:column;
        align-items:flex-start;
        gap:18px;
    }
    .btn-action-group{
        width:100%;
        margin-left:0;
        justify-content:flex-start;
    }
    .btn-action-group .btn{
        flex-grow:1;
        min-width:unset;
    }
}

@media(max-width:600px){
    .container-narrow{
        padding:0 24px;
    }
}
</style>
@endpush

@section('content')
<div class="container-narrow">

    <div class="page-title-box">
        <i class="bi bi-calendar-check-fill" style="color:var(--primary); font-size:2rem"></i>
        <div>
            <h3 class="fw-bold mb-0" style="color:var(--accent);">Jadwal Mingguan</h3>
            <div class="page-sub">Tanggal menyesuaikan hari jadwal secara otomatis</div>
        </div>
    </div>

    <div class="card-list-container">
        <h5 class="fw-bold mb-3" style="color:var(--accent);">Daftar Jadwal Mata Pelajaran</h5>

        @if($jadwals->isEmpty())
            <div class="alert alert-info mt-3 mb-0 border-start border-4 border-info">
                <i class="bi bi-info-circle-fill me-1"></i>
                Tidak ada jadwal mengajar ditemukan.
            </div>
        @else
            <div class="list-grid mt-3">
                @foreach($jadwals as $j)
                    @php
                        $hariDb = strtolower(trim(str_replace(["'", "’"], '', $j->hari)));
                        $hariIndex = $hariMap[$hariDb] ?? null;
                        $key = $j->id_jadwal_mengajar;

                        $tanggalJadwal = $hariIndex
                            ? Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays($hariIndex - 1)
                            : null;

                        $jamMulai = Carbon::parse($j->jam_mulai)->format('H:i:s');
                        $jamSelesai = Carbon::parse($j->jam_selesai)->format('H:i:s');

                        $isToday = ($hariDb === $todayName);
                        $isWithinTime = ($todayTime >= $jamMulai && $todayTime <= $jamSelesai);
                        $btnAbsenEnabled = $isToday && $isWithinTime;

                        // --- PENYESUAIAN ROUTE ---
                        $linkScan = route('guru.absensi.scan', ['id_jadwal_mengajar' => $key]);
                        $linkAbsen = route('guru.absensi.create', ['id_jadwal_mengajar' => $key]);
                        $linkRekap = route('guru.absensi.rekap.by_jadwal', [
                            'id_jadwal' => $key,
                            'year' => now()->year,
                            'month' => now()->month
                        ]);
                        $linkLihat = route('guru.absensi.index', ['id_jadwal_mengajar' => $key]);
                    @endphp

                    <div class="list-item {{ $isToday ? 'today' : '' }}">
                        <div>
                            <div class="schedule-title">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</div>
                            <div class="schedule-details">
                                <span class="class-tag">
                                    <i class="bi bi-people-fill me-1"></i> {{ $j->kelas->nama_kelas ?? '-' }}
                                </span>
                                <div><i class="bi bi-calendar-day me-1"></i> {{ $j->hari }}</div>
                                <span class="date-tag">
                                    <i class="bi bi-calendar-event me-1"></i> {{ $tanggalJadwal?->isoFormat('D MMMM YYYY') ?? '-' }}
                                </span>
                                <div>
                                    <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="btn-action-group">
                            {{-- TOMBOL SCAN --}}
                            <a href="{{ $btnAbsenEnabled ? $linkScan : '#' }}" 
                               class="btn btn-success {{ $btnAbsenEnabled ? '' : 'disabled' }}"
                               @if(!$btnAbsenEnabled) title="Scan hanya bisa dilakukan saat jam pelajaran hari ini" @endif>
                                <i class="bi bi-qr-code-scan me-1"></i> Scan Kartu
                            </a>

                            {{-- Input Manual --}}
                            <a href="{{ $btnAbsenEnabled ? $linkAbsen : '#' }}" 
                               class="btn btn-outline-success {{ $btnAbsenEnabled ? '' : 'disabled' }}"
                               @if(!$btnAbsenEnabled) title="Absen hanya bisa dilakukan saat jam pelajaran hari ini" @endif>
                                <i class="bi bi-pencil-square me-1"></i> Manual
                            </a>

                            {{-- Detail --}}
                            <a href="{{ $isToday ? $linkLihat : '#' }}" 
                               class="btn btn-outline-dark {{ $isToday ? '' : 'disabled' }}"
                               @if(!$isToday) title="Detail hanya bisa dilihat pada hari jadwal" @endif>
                                <i class="bi bi-list-check me-1"></i> Detail
                            </a>

                            {{-- Rekap --}}
                            <a href="{{ $isToday ? $linkRekap : '#' }}" 
                               class="btn btn-primary {{ $isToday ? '' : 'disabled' }}"
                               @if(!$isToday) title="Rekap hanya bisa dilihat pada hari jadwal" @endif>
                                <i class="bi bi-file-earmark-medical me-1"></i> Rekap
                            </a>
                        </div>
                    </div>

                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection