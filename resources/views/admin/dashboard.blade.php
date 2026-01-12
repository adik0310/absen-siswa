@extends('layouts.admin')

@section('title','Dashboard Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    :root {
        --dash-blue: #007bff;     /* 🔵 Warna biru untuk total siswa */
        --dash-green-main: #00D452;
        --dash-green-soft: #21e06f;
        --dash-red: #e71d36;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    
    /* Kartu Statistik */
    .stat-card { 
        border: none; 
        border-radius: 16px; 
        transition: all 0.3s ease; 
        box-shadow: var(--card-shadow); 
        background: #fff; 
    }
    .stat-card:hover { transform: translateY(-5px); }
    
    /* Kartu Utama */
    .main-card { 
        color: white; 
        border-radius: 16px; 
        padding: 24px; 
        overflow: hidden; 
        border: none; 
        box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
    }

    .bg-blue-main { background: var(--dash-blue); } /* 🔵 TOTAL SISWA */
    .bg-green-main { background: var(--dash-green-main); }
    .bg-green-soft { background: var(--dash-green-soft); }
    .bg-red { background: var(--dash-red); }

    /* Container Grafik */
    .chart-container { 
        background: #fff; 
        border-radius: 16px; 
        padding: 25px; 
        box-shadow: var(--card-shadow); 
        height: 100%; 
        position: relative; 
    }

    .chart-center-text {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .progress { background-color: #f0f0f0; overflow: visible; }
    .progress-bar { border-radius: 10px; transition: width 1s; }

    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="mb-4">
        <h3 class="fw-bold">Ringkasan Sistem</h3>
        <p class="text-muted">Pantau kehadiran siswa secara real-time berdasarkan jadwal hari ini.</p>
    </div>

    {{-- 1. Kartu Utama --}}
    <div class="row g-4 mb-4">

        {{-- 🔵 TOTAL SISWA (Keseluruhan) --}}
        <div class="col-md-4">
            <div class="main-card bg-blue-main">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-bold small opacity-75">TOTAL SISWA TERDAFTAR</p>
                        <h2 class="fw-bold mb-0">{{ number_format($data['jumlah_siswa']) }}</h2>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>

        {{-- Hijau: Sudah Input Absen Hari Ini --}}
        <div class="col-md-4">
            <div class="main-card bg-green-main">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-bold small opacity-75">SUDAH ABSEN HARI INI</p>
                        <h2 class="fw-bold mb-0">{{ number_format($data['sudah_presensi']) }}</h2>
                    </div>
                    <i class="bi bi-check-circle-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>

        {{-- Merah: Belum Absen (Sisa Jadwal) --}}
        <div class="col-md-4">
            <div class="main-card bg-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-bold small opacity-75">BELUM ABSEN HARI INI</p>
                        <h2 class="fw-bold mb-0">{{ number_format($data['belum_presensi']) }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Grafik --}}
    <div class="row g-4 mb-4">

        {{-- Line Chart --}}
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="fw-bold mb-4 text-dark">
                    <i class="bi bi-graph-up me-2 text-success"></i>
                    Trend Kehadiran 5 Hari Terakhir
                </h5>
                <div style="height: 320px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Doughnut Chart --}}
        <div class="col-lg-4">
            <div class="chart-container text-center">
                <h5 class="fw-bold mb-4 text-dark text-start">Detail Ketidakhadiran</h5>

                <div style="height: 200px; position: relative;">
                    <canvas id="doughnutChart"></canvas>
                    <div class="chart-center-text">
                        <h4 class="fw-bold mb-0 text-dark">
                            {{ $data['ketidakhadiran']['sakit'] + $data['ketidakhadiran']['izin'] + $data['ketidakhadiran']['alfa'] }}
                        </h4>
                        <small class="text-muted" style="font-size: .6rem;">TOTAL</small>
                    </div>
                </div>

                {{-- Detail List --}}
                <div class="mt-4 text-start">
                    @php
                        $labels = [
                            'sakit' => ['color'=>'warning','text'=>'Sakit'],
                            'izin'  => ['color'=>'success','text'=>'Izin'],
                            'alfa'  => ['color'=>'danger','text'=>'Alfa'],
                        ];
                    @endphp

                    @foreach($labels as $key => $val)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                        <span>
                            <i class="bi bi-circle-fill text-{{ $val['color'] }} me-2 small"></i>
                            {{ $val['text'] }}
                        </span>
                        <span class="fw-bold text-dark">{{ $data['ketidakhadiran'][$key] }} Siswa</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Tabel Aktivitas --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="fw-bold mb-4 text-dark d-flex align-items-center">
                    <i class="bi bi-clock-history me-2 text-success"></i>
                    Progres Absensi Kelas (Jadwal Aktif)
                    <span class="badge bg-success-subtle text-success ms-3 fs-6 fw-normal">
                        {{ count($detail_presensi) }} Kelas Terinput
                    </span>
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengampu</th>
                                <th class="text-center">Kapasitas</th>
                                <th class="text-center" style="width:250px;">Status Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detail_presensi as $d)
                            <tr>
                                <td class="fw-bold text-dark">{{ $d['kelas'] }}</td>
                                <td>{{ $d['mapel'] }}</td>
                                <td>{{ $d['guru'] }}</td>
                                <td class="text-center">{{ $d['total_siswa'] }} Siswa</td>
                                <td>
                                    @php 
                                        $persen = $d['total_siswa'] > 0 
                                            ? ($d['sudah_absen'] / $d['total_siswa']) * 100 
                                            : 0; 
                                    @endphp

                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}" 
                                                 style="width: {{ $persen }}%"></div>
                                        </div>
                                        <small class="fw-bold text-dark">{{ round($persen) }}%</small>
                                    </div>

                                    <p class="mb-0 text-center text-muted mt-1" style="font-size: .75rem;">
                                        {{ $d['sudah_absen'] }} / {{ $d['total_siswa'] }} Terdata
                                    </p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png"
                                         width="60" class="opacity-25 mb-3">
                                    <p class="text-muted">Belum ada kelas yang melakukan input absen hari ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trend = @json($data['trend_kehadiran']);
    const ktdk = @json($data['ketidakhadiran']);
    const totalTidakHadir = ktdk.sakit + ktdk.izin + ktdk.alfa;

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: trend.map(x => x.tanggal),
            datasets: [{
                label: 'Siswa Hadir',
                data: trend.map(x => x.jumlah),
                borderColor: '#00D452',
                backgroundColor: 'rgba(0, 212, 82, .12)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#00D452',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#eee' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Doughnut Chart
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Sakit', 'Izin', 'Alfa'],
            datasets: [{
                data: totalTidakHadir === 0 
                    ? [1, 0, 0]
                    : [ktdk.sakit, ktdk.izin, ktdk.alfa],
                backgroundColor: totalTidakHadir === 0
                    ? ['#e0e0e0']
                    : ['#ff9f1c', '#00D452', '#e71d36'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush