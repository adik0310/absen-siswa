@extends('layouts.admin')

@section('title', 'Jadwal Mengajar - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* TRICK: Memaksa container utama melebar maksimal ke kanan-kiri */
    main.container-main {
        max-width: 85% !important;
        padding-left: 40px !important;
        padding-right: 40px !important;
    }

    .container-custom { 
        width: 100%;
    }

    /* Card utama lebih lega */
    .card-main {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: #fff;
        margin-bottom: 2rem;
    }

    /* Table styling - Lebih lebar dan rapi */
    .table-custom thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        color: #555;
        padding: 15px 20px;
        border-bottom: 2px solid #eee;
    }

    .table-custom tbody td {
        padding: 15px 15px;
        vertical-align: middle;
        font-size: 0.8rem;
        border-bottom: 1px solid #f5f5f5;
    }

    /* Badge Hari - Dibuat sedikit lebih besar agar terbaca jelas */
    .badge-hari {
        min-width: 85px;
        padding: 6px 0;
        border-radius: 8px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        display: inline-block;
        text-align: center;
    }

    .hari-senin  { background: #fee2e2; color: #b91c1c; }
    .hari-selasa { background: #ffedd5; color: #c2410c; }
    .hari-rabu   { background: #fef9c3; color: #a16207; }
    .hari-kamis  { background: #dcfce7; color: #15803d; }
    .hari-jumat  { background: #e0f2fe; color: #0369a1; }
    .hari-sabtu  { background: #f3e8ff; color: #7e22ce; }

    /* Waktu Style */
    .text-time {
        font-family: 'Monaco', 'Consolas', monospace;
        font-weight: 700;
        color: #198754;
        background: rgba(25, 135, 84, 0.08);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8em;
    }

    .class-badge {
        background-color: #334155;
        color: white;
        padding: 3px 10px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.7rem;
        display: inline-block;
    }

    .text-success-custom { color: #198754 !important; }
    .btn-success-custom { background-color: #198754 !important; color: white; border: none; font-weight: 600; }
    .btn-success-custom:hover { background-color: #146c43 !important; color: white; }

    .btn-white {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 0.6rem;
        transition: all 0.2s;
    }
    .btn-white:hover { 
        background: #f8fafc; 
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Page Header */
    .page-header-box {
        background: white;
        border-radius: 15px;
        padding: 1.25rem 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-custom py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-box">
        <div>
            <h4 class="fw-bold mb-1 text-success-custom">
                <i class="bi bi-calendar2-week me-2"></i> Jadwal Mengajar
            </h4>
            <p class="text-muted mb-0">Manajemen operasional waktu dan penempatan kelas pembelajaran.</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-success-custom rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-2"></i> Tambah Jadwal Baru
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-3 px-4 mb-4 fw-semibold rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter Bar --}}
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6 col-lg-4">
            <form method="GET" action="{{ route('admin.jadwal.index') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari guru, kelas, atau mapel..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success-custom px-4">Cari</button>
                </div>
            </form>
        </div>
        @if(request('search'))
        <div class="col-md-auto">
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-light border px-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
            </a>
        </div>
        @endif
    </div>

    {{-- Main Content Card --}}
    <div class="card card-main overflow-hidden">
        @if($jadwalList->isEmpty())
            <div class="text-center py-5">
                <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                    <i class="bi bi-calendar-x text-muted opacity-50" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Data Jadwal Kosong</h5>
                <p class="text-muted">Belum ada jadwal yang terdaftar atau pencarian tidak ditemukan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">NO</th>
                            <th width="120">HARI</th>
                            <th width="200">WAKTU</th>
                            <th width="250">KELAS & MATA PELAJARAN</th>
                            <th>GURU PENGAMPU</th>
                            <th width="150">RUANGAN</th>
                            <th class="text-center" width="150">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalList as $i => $j)
                            @php $hariClass = 'hari-' . strtolower($j->hari); @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                                <td><span class="badge-hari {{ $hariClass }}">{{ $j->hari }}</span></td>
                                <td>
                                    <span class="text-time">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--:--' }}</span>
                                    <i class="bi bi-arrow-right mx-2 text-muted small"></i>
                                    <span class="text-time">{{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--:--' }}</span>
                                </td>
                                <td>
                                    <div class="mb-1"><span class="class-badge">{{ $j->kelas->nama_kelas ?? '-' }}</span></div>
                                    <div class="fw-bold text-dark fs-6">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <span class="fw-semibold text-dark">{{ $j->guru->nama_guru ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-white text-dark border px-3 py-2 fw-medium shadow-sm">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $j->ruangan ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.jadwal.show', $j->id_jadwal_mengajar) }}" class="btn btn-white shadow-sm" title="Detail">
                                            <i class="bi bi-eye-fill text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.jadwal.edit', $j->id_jadwal_mengajar) }}" class="btn btn-white shadow-sm" title="Edit">
                                            <i class="bi bi-pencil-fill text-success"></i>
                                        </a>
                                        <form action="{{ route('admin.jadwal.delete', $j->id_jadwal_mengajar) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-white shadow-sm" onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                                <i class="bi bi-trash3-fill text-danger"></i>
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