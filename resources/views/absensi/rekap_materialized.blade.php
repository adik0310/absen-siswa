@extends('layouts.guru')

@section('title', 'Rekap Absensi Bulanan')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .container-fluid { padding: 0 100px; }

    /* Card Styling - Ukuran lebih compact */
    .card-rekap { border-radius: 8px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); background: #fff; }
    .card-rekap-header { background-color: #fff !important; border-bottom: 1px solid #f1f1f1; padding: 1rem; }

    /* Badge & Status Styling */
    .badge-rekap { 
        padding: 4px 10px; 
        border-radius: 5px; 
        font-weight: 700; 
        min-width: 35px; 
        font-size: 0.85rem;
        display: inline-block;
    }
    .bg-hadir { background: #e8f5e9; color: #2e7d32; }
    .bg-izin  { background: #e3f2fd; color: #1565c0; }
    .bg-sakit { background: #fff8e1; color: #f9a825; }
    .bg-alfa  { background: #ffebee; color: #c62828; }

    /* Table Styling - Lebih kecil dan rapat */
    .table-custom thead th { 
        background-color: #f8f9fa; 
        text-transform: uppercase; 
        font-size: 0.7rem; 
        letter-spacing: 0.5px;
        color: #6c757d;
        border: none;
        padding: 10px;
    }
    .table-custom tbody td { border-bottom: 1px solid #f5f5f5; padding: 8px 10px; font-size: 0.9rem; }
    
    .table-custom tbody tr:hover { background-color: #fafafa; }

    /* Override warna biru ke hijau */
    .text-success-custom { color: #198754 !important; }
    .bg-success-subtle-custom { background-color: #e8f5e9 !important; color: #198754 !important; }
</style>
@endpush

@section('content')
@php
    $selKelas = request()->kelas_id ?: ($id_kelas ?? '');
    $selMapel = request()->mapel_id ?: '';

    $monthNames = [
        1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
        7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
    ];

    $navQuery = ['kelas_id' => $selKelas, 'mapel_id' => $selMapel];
    $user = auth()->user();
    $namaGuru = $user->guru->nama_guru ?? $user->nama;
    $nipGuru  = $user->guru->nip ?? '-';
@endphp

<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="row align-items-center mb-3">
        <div class="col-md-7">
            <h5 class="fw-bold text-success-custom mb-1">
                <i class="bi bi-journal-check me-2"></i> Rekap Absensi Bulanan
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle-custom px-2 py-1 rounded shadow-sm" style="font-size: 0.75rem;">
                    <i class="bi bi-person-fill me-1"></i> {{ $namaGuru }}
                </span>
                <span class="text-muted" style="font-size: 0.75rem;">| NIP: {{ $nipGuru }}</span>
            </div>
        </div>

        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            @if($rekap)
                <div class="btn-group btn-group-sm shadow-sm">
                    <button class="btn btn-success px-3" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.85rem;">
                        <li><a class="dropdown-item py-1" href="{{ route('guru.absensi.rekap.pdf', array_merge(['id_kelas'=>$selKelas,'year'=>$year,'month'=>$month], $navQuery)) }}"><i class="bi bi-file-pdf text-danger me-2"></i> Simpan PDF</a></li>
                        <li><a class="dropdown-item py-1" href="{{ route('guru.absensi.rekap.excel', array_merge(['id_kelas'=>$selKelas,'year'=>$year,'month'=>$month], $navQuery)) }}"><i class="bi bi-file-excel text-success me-2"></i> Simpan Excel</a></li>
                    </ul>
                </div>

                <form action="{{ route('guru.absensi.rekap.generate', ['id_kelas'=>$selKelas,'year'=>$year,'month'=>$month]) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-white border shadow-sm ms-1">
                        <i class="bi bi-arrow-clockwise me-1 text-success"></i> Refresh
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- FILTER BOX --}}
    <div class="card card-rekap mb-3 shadow-sm">
        <div class="card-body p-3">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('guru.absensi.rekap') }}">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.7rem;">PILIH KELAS</label>
                    <select name="kelas_id" class="form-select form-select-sm" required onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($allKelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ $selKelas == $k->id_kelas ? 'selected':'' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.7rem;">MATA PELAJARAN</label>
                    <select name="mapel_id" class="form-select form-select-sm" onchange="this.form.submit()" {{ !$selKelas ? 'disabled' : '' }}>
                        <option value="">-- Semua Mapel --</option>
                        @if($selKelas)
                            @foreach($mapels as $m)
                                <option value="{{ $m->id_mata_pelajaran }}" {{ $selMapel == $m->id_mata_pelajaran ? 'selected':'' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('guru.absensi.rekap') }}" class="btn btn-sm btn-light border text-secondary w-100">
                        <i class="bi bi-trash3 me-1"></i> Bersihkan
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- EMPTY STATE --}}
    @if(!$rekap)
        <div class="card card-rekap text-center py-4">
            <div class="card-body">
                <i class="bi bi-clipboard-x text-muted opacity-25 mb-2" style="font-size: 3rem;"></i>
                <h6 class="fw-bold">Belum Ada Data</h6>
                <p class="text-secondary mb-0" style="font-size: 0.8rem;">Silakan gunakan filter untuk melihat laporan.</p>
            </div>
        </div>
    @else

    {{-- TABLE SECTION --}}
    <div class="card card-rekap shadow-sm">
        <div class="card-rekap-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 text-dark" style="font-size: 0.9rem;">
                <i class="bi bi-table me-2 text-success-custom"></i> Daftar Kehadiran
            </h6>
            <span class="badge bg-light text-dark border px-2" style="font-size: 0.7rem;">
                <i class="bi bi-calendar3 me-1"></i> {{ strtoupper($monthNames[$month]) }} {{ $year }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0 align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="40">No</th>
                        <th width="120">NIS</th>
                        <th class="text-start">Nama Siswa</th>
                        <th width="70">H</th>
                        <th width="70">I</th>
                        <th width="70">S</th>
                        <th width="70">A</th>
                        <th width="80" class="bg-success-subtle-custom fw-bold border-0">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $row)
                    <tr class="text-center">
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td class="text-muted small fw-semibold">{{ $row->nis }}</td>
                        <td class="text-start fw-bold text-dark">{{ $row->nama_siswa }}</td>
                        <td><span class="badge-rekap bg-hadir">{{ $row->hadir }}</span></td>
                        <td><span class="badge-rekap bg-izin">{{ $row->izin }}</span></td>
                        <td><span class="badge-rekap bg-sakit">{{ $row->sakit }}</span></td>
                        <td><span class="badge-rekap bg-alfa">{{ $row->alfa }}</span></td>
                        <td class="bg-success-subtle-custom fw-bold border-0" style="font-size: 0.95rem;">{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection