{{-- resources/views/admin/rekap/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hasil Rekap Absensi - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    /* ============================
       Card & Layout
    ============================ */
    .card-rekap { 
        border-radius: 14px; 
        border: none; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.06); 
        background: #fff; 
        overflow: hidden;
    }
    
    .card-rekap-header { 
        background: #f8fbf9;
        padding: 1.2rem 1.5rem; 
        border-bottom: 1px solid #edf2ee; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }

    /* ============================
       Badge Counts (Warna Lembut)
    ============================ */
    .badge-rekap { 
        padding: 6px 10px; 
        border-radius: 8px; 
        font-weight: 700; 
        min-width: 38px; 
        font-size: 0.85rem;
        display: inline-block;
    }
    .bg-hadir { background: #eefdf4; color: #198754; border: 1px solid #d1f7e0; }
    .bg-izin  { background: #eef8ff; color: #0d6efd; border: 1px solid #d0e9ff; }
    .bg-sakit { background: #fff9ee; color: #f59e0b; border: 1px solid #ffecd2; }
    .bg-alfa  { background: #fff5f5; color: #dc3545; border: 1px solid #fed7d7; }

    /* ============================
       Table Styling
    ============================ */
    .table-rekap thead th { 
        background-color: #f8f9fa; 
        text-transform: uppercase; 
        font-size: 0.72rem; 
        letter-spacing: 0.8px;
        color: #5a6a85;
        border-top: none;
        padding: 15px 12px;
    }
    .table-rekap tbody td { 
        border-bottom: 1px solid #f1f1f1; 
        padding: 14px 12px; 
        font-size: 0.9rem;
    }
    .tr-hover:hover { background-color: #fcfdfc; }
    
    /* ============================
       Info Section
    ============================ */
    .meta-box {
        padding: 1rem;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #f1f1f1;
    }
    .label-meta { font-size: 0.7rem; color: #8e99a3; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
    .value-meta { font-size: 0.95rem; color: #2c3e50; font-weight: 700; }
    
    .btn-export { border-radius: 9px; padding: 0.5rem 1.2rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- TOP NAVIGATION & ACTIONS --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-check-fill text-success me-2"></i> Laporan Rekap Absensi
            </h4>
            <p class="text-muted small mb-0">Menampilkan statistik kehadiran berdasarkan filter yang dipilih.</p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rekap.index') }}" class="btn btn-outline-secondary btn-export">
                <i class="bi bi-filter me-1"></i> Ganti Filter
            </a>
            
            @if(!empty($rekapData))
            <div class="dropdown">
                <button class="btn btn-success btn-export dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Unduh Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <form action="{{ route('admin.rekap.pdf') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_kelas" value="{{ $id_kelas }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="mapel" value="{{ $mapelId ?? 0 }}">
                            <input type="hidden" name="guru" value="{{ $guruId ?? 0 }}">
                            <button type="submit" class="dropdown-item py-2">
                                <i class="bi bi-file-pdf text-danger me-2"></i> Dokumen PDF
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.rekap.excel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_kelas" value="{{ $id_kelas }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="mapel" value="{{ $mapelId ?? 0 }}">
                            <input type="hidden" name="guru" value="{{ $guruId ?? 0 }}">
                            <button type="submit" class="dropdown-item py-2">
                                <i class="bi bi-file-excel text-success me-2"></i> Spreadsheet Excel
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    {{-- INFO SUMMARY CARD --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="meta-box shadow-sm">
                <div class="label-meta"><i class="bi bi-house-door me-1"></i> Kelas</div>
                <div class="value-meta text-truncate">{{ $kelasName }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="meta-box shadow-sm">
                <div class="label-meta"><i class="bi bi-book me-1"></i> Mata Pelajaran</div>
                <div class="value-meta text-truncate">{{ $mapelName ?? 'Semua Mata Pelajaran' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="meta-box shadow-sm">
                <div class="label-meta"><i class="bi bi-calendar3 me-1"></i> Periode</div>
                <div class="value-meta">{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="meta-box shadow-sm bg-success text-white border-0">
                <div class="label-meta text-white-50">Total Siswa</div>
                <div class="value-meta fs-5">{{ count($rekapData) }}</div>
            </div>
        </div>
    </div>

    @if(empty($rekapData))
        <div class="card card-rekap text-center py-5">
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-clipboard-x text-light-emphasis" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Tidak Ada Data Absensi</h5>
                <p class="text-muted small mx-auto" style="max-width: 400px;">
                    Belum ada data absensi yang tercatat untuk kelas dan periode ini. Silakan hubungi pengajar atau coba filter periode lain.
                </p>
                <a href="{{ route('admin.rekap.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">Kembali ke Filter</a>
            </div>
        </div>
    @else

    {{-- TABLE DATA --}}
    <div class="card card-rekap">
        <div class="card-rekap-header">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-check text-success me-2"></i>Data Kehadiran Siswa
            </h6>
            <span class="badge bg-white text-success border border-success-subtle fw-semibold px-3 py-2">
                Format: Bulanan
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-rekap mb-0 align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="50">No</th>
                        <th width="120">NIS</th>
                        <th class="text-start">Nama Siswa</th>
                        <th width="100">Hadir</th>
                        <th width="100">Izin</th>
                        <th width="100">Sakit</th>
                        <th width="100">Alfa</th>
                        <th width="120" class="bg-light fw-bold">Kumulatif</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($rekapData as $r)
                    <tr class="tr-hover">
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="text-secondary fw-semibold">{{ $r['siswa']->nis }}</td>
                        <td class="text-start fw-bold text-dark">{{ $r['siswa']->nama_siswa }}</td>
                        <td><span class="badge-rekap bg-hadir">{{ $r['hadir'] }}</span></td>
                        <td><span class="badge-rekap bg-izin">{{ $r['izin'] }}</span></td>
                        <td><span class="badge-rekap bg-sakit">{{ $r['sakit'] }}</span></td>
                        <td><span class="badge-rekap bg-alfa">{{ $r['alfa'] }}</span></td>
                        <td class="fw-bold bg-light-subtle text-primary">
                            {{ $r['hadir'] + $r['sakit'] + $r['izin'] + $r['alfa'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection