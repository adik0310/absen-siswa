{{-- resources/views/absensi/index.blade.php --}}
@extends('layouts.guru')

@section('title', 'Absensi — Ringkasan Hari Ini')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>

    /* Container -------------------------------------------------------- */
    .container {
        max-width: 1100px;
        margin: 28px auto;
        padding: 0 35px;
    }

    /* Modern Card ------------------------------------------------------ */
    .card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }

    /* Soft Badges ------------------------------------------------------ */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-soft-success { background: #d1e7dd; color: #0f5132; }
    .badge-soft-info    { background: #cff4fc; color: #055160; }
    .badge-soft-warning { background: #fff3cd; color: #664d03; }
    .badge-soft-danger  { background: #f8d7da; color: #842029; }

    /* Avatar Circle ---------------------------------------------------- */
    .avatar-circle {
        width: 42px;
        height: 42px;
        background-color: #e9ecef;
        color: #495057;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        margin-right: 12px;
    }

    /* Modern Warning --------------------------------------------------- */
    .alert-modern-warning {
        background-color: #fff8e1;
        border-left: 4px solid #ffc107;
        color: #856404;
        border-radius: 10px;
    }

    /* Table Improvements ------------------------------------------------ */
    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
        border-bottom-color: #f0f0f0;
    }
    .table tbody tr:hover {
        background-color: #fafbfd;
    }

</style>
@endpush


@section('content')
@php
    $today = \Carbon\Carbon::today()->translatedFormat('d F Y');

    // Helper inisial siswa
    function getInitials($name) {
        $words = explode(' ', trim($name));
        return strtoupper(substr($words[0] ?? '',0,1) . (isset($words[1]) ? substr($words[1],0,1) : ''));
    }
@endphp

<div class="container">

    {{-- Header ---------------------------------------------------------- --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Ringkasan Absensi</h4>
            <p class="text-muted mb-0 small">Pantau kehadiran siswa hari ini.</p>
        </div>
        <a href="{{ route('guru.jadwal.index') }}"
           class="btn btn-white border shadow-sm btn-sm px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Jadwal
        </a>
    </div>

    {{-- Akses View Only ------------------------------------------------- --}}
    @if(isset($accessAllowed) && $accessAllowed === false)
        <div class="alert alert-modern-warning d-flex align-items-center mb-4 p-3 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
            <div>
                <h6 class="fw-bold mb-1">Akses Terbatas</h6>
                <p class="mb-0 small">Anda sedang melihat jadwal guru lain. Mode
                    <strong>View-Only</strong> aktif.</p>
            </div>
        </div>
    @endif

    {{-- Info Mapel / Kelas ---------------------------------------------- --}}
    <div class="card">
        <div class="card-body p-4">
            <div class="row align-items-center">

                {{-- Detail Mapel & Kelas --}}
                <div class="col-md-8">

                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary rounded-pill me-2">Mapel</span>
                        <h5 class="mb-0 fw-bold">
                            {{ $jadwal->mataPelajaran->nama_mapel ?? ($jadwal->mapel->nama_mapel ?? '-') }}
                        </h5>
                    </div>

                    <div class="d-flex flex-wrap gap-3 small text-secondary mt-2">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-people me-1"></i>
                            Kelas: <strong class="ms-1">{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $jadwal->hari ?? '-' }}, {{ $today }}
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock me-1"></i>
                            {{ \Illuminate\Support\Str::limit($jadwal->jam_mulai,5,'') }}
                            -
                            {{ \Illuminate\Support\Str::limit($jadwal->jam_selesai,5,'') }}
                        </div>

                    </div>
                </div>

                {{-- Statistik Ringkas --}}
                <div class="d-flex justify-content-md-end gap-3 text-center">

                    <div>
                        <h6 class="fw-bold mb-0 text-success">
                            {{ $absensis->where('keterangan','hadir')->count() }}
                        </h6>
                        <small class="text-muted">HADIR</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-0 text-info">
                            {{ $absensis->where('keterangan','izin')->count() }}
                        </h6>
                        <small class="text-muted">IZIN</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-0 text-warning">
                            {{ $absensis->where('keterangan','sakit')->count() }}
                        </h6>
                        <small class="text-muted">SAKIT</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-0 text-danger">
                            {{ $absensis->where('keterangan','alfa')->count() }}
                        </h6>
                        <small class="text-muted">ALFA</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            {{ $absensis->count() }}
                        </h6>
                        <small class="text-muted">TOTAL</small>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Table Absensi ---------------------------------------------------- --}}
    <div class="card">

        @if($absensis->isEmpty())
            <div class="text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png"
                     style="width: 60px; opacity: 0.5;" class="mb-3">
                <h6 class="text-muted">Belum ada data absensi.</h6>
            </div>

        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3" style="width: 50px;">#</th>
                            <th class="py-3">Siswa</th>
                            <th class="text-center py-3" style="width: 150px;">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($absensis as $i => $a)
                            @php $namaSiswa = optional($a->siswa)->nama_siswa ?? '-'; @endphp

                            <tr>
                                <td class="ps-4 text-muted">{{ $i+1 }}</td>

                                <td>
                                    <div class="d-flex align-items-center">

                                        <div class="avatar-circle">
                                            {{ getInitials($namaSiswa) }}
                                        </div>

                                        <div>
                                            <div class="fw-bold">{{ $namaSiswa }}</div>
                                            <div class="small text-muted">
                                                NIS: {{ optional($a->siswa)->nis ?? '-' }}
                                            </div>
                                        </div>

                                    </div>
                                </td>

                                <td class="text-center">

                                    @switch($a->keterangan)
                                        @case('hadir')
                                            <span class="badge-soft badge-soft-success">
                                                <i class="bi bi-check-circle-fill"></i> Hadir
                                            </span>
                                            @break

                                        @case('izin')
                                            <span class="badge-soft badge-soft-info">
                                                <i class="bi bi-info-circle-fill"></i> Izin
                                            </span>
                                            @break

                                        @case('sakit')
                                            <span class="badge-soft badge-soft-warning">
                                                <i class="bi bi-thermometer-half"></i> Sakit
                                            </span>
                                            @break

                                        @default
                                            <span class="badge-soft badge-soft-danger">
                                                <i class="bi bi-x-circle-fill"></i> Alfa
                                            </span>
                                    @endswitch

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
