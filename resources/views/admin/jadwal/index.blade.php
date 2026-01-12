@extends('layouts.admin')

@section('title', 'Jadwal Mengajar - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Container lebih proporsional */
    .container-custom { 
        padding: 0 45px;
    }

    /* Card utama lebih compact */
    .card-main {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        background: #fff;
        margin-bottom: 2rem;
    }

    /* Table styling - Rapat & Bersih */
    .table-custom thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 15px;
        border: none;
    }

    .table-custom tbody td {
        padding: 10px 15px;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f5f5f5;
    }

    /* Badge Hari - Tetap berwarna sesuai hari namun lebih kecil */
    .badge-hari {
        width: 75px;
        padding: 4px 0;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.65rem;
        display: inline-block;
        text-align: center;
    }

    .hari-senin  { background: #fee2e2; color: #b91c1c; }
    .hari-selasa { background: #ffedd5; color: #c2410c; }
    .hari-rabu   { background: #fef9c3; color: #a16207; }
    .hari-kamis  { background: #dcfce7; color: #15803d; }
    .hari-jumat  { background: #e0f2fe; color: #0369a1; }
    .hari-sabtu  { background: #f3e8ff; color: #7e22ce; }

    /* Waktu & Badge - Aksen Hijau */
    .text-time {
        font-family: 'Monaco', monospace;
        font-weight: 700;
        color: #198754;
        background: #e8f5e9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .class-badge {
        background-color: #475569;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    /* Custom Success Color */
    .text-success-custom { color: #198754 !important; }
    .btn-success-custom { background-color: #198754 !important; color: white; border: none; }
    .btn-success-custom:hover { background-color: #146c43 !important; color: white; }

    .btn-white {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 0.25rem 0.5rem;
    }
    .btn-white:hover { background: #f8fafc; }

    /* Page Header */
    .page-header-box {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')
<div class="container-custom py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-box">
        <div>
            <h5 class="fw-bold mb-1 text-success-custom">
                <i class="bi bi-calendar2-week me-2"></i> Jadwal Mengajar
            </h5>
            <p class="text-muted mb-0 small">Manajemen waktu dan kelas pembelajaran.</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-sm btn-success-custom rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-2 px-3 mb-4 fw-semibold small">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="row g-2 mb-3 align-items-center">
        <div class="col-md-5">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="input-group input-group-sm">
                <input type="text" name="search" class="form-control border-secondary-subtle" placeholder="Cari guru, kelas, atau mapel..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-success-custom">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        @if(request('search'))
        <div class="col-md-auto">
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-sm btn-light border text-muted">
                <i class="bi bi-x-circle me-1"></i> Reset
            </a>
        </div>
        @endif
    </div>

    {{-- Main Content Card --}}
    <div class="card card-main overflow-hidden">
        @if($jadwalList->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted opacity-25" style="font-size: 3rem;"></i>
                <h6 class="mt-3 fw-bold text-dark">Tidak Ada Data Jadwal</h6>
                <p class="text-muted small">Coba cari dengan kata kunci lain atau tambahkan jadwal baru.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th width="100">Hari</th>
                            <th width="180">Waktu</th>
                            <th width="200">Kelas & Mapel</th>
                            <th>Guru</th>
                            <th width="120">Ruang</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalList as $i => $j)
                            @php $hariClass = 'hari-' . strtolower($j->hari); @endphp
                            <tr>
                                <td class="text-center text-muted small">{{ $i + 1 }}</td>
                                <td><span class="badge-hari {{ $hariClass }}">{{ $j->hari }}</span></td>
                                <td>
                                    <span class="text-time">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--:--' }}</span>
                                    <small class="text-muted px-1">-</small>
                                    <span class="text-time">{{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--:--' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="class-badge mb-1">{{ $j->kelas->nama_kelas ?? '-' }}</span><br>
                                        <span class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-success-custom opacity-75"></i>
                                        <span class="fw-semibold">{{ $j->guru->nama_guru ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.75rem;">
                                        <i class="bi bi-door-open me-1"></i>{{ $j->ruangan ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.jadwal.show', $j->id_jadwal_mengajar) }}" class="btn btn-white" title="Detail">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.jadwal.edit', $j->id_jadwal_mengajar) }}" class="btn btn-white" title="Edit">
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                        <form action="{{ route('admin.jadwal.delete', $j->id_jadwal_mengajar) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-white" onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                                <i class="bi bi-trash text-danger"></i>
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

    {{-- Pagination (jika ada) --}}
    @if(method_exists($jadwalList, 'links'))
    <div class="d-flex justify-content-center mt-2">
        {{ $jadwalList->links() }}
    </div>
    @endif
</div>
@endsection