@extends('layouts.guru')

@section('title', 'Daftar Siswa Wali Kelas')

@push('head')
<style>
    .container-fluid { padding: 0 45px; }
    
    /* Card Styling */
    .card-custom { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: #fff; }
    
    /* Search Bar Styling */
    .search-box { border-radius: 8px; border: 1px solid #e0e0e0; transition: all 0.3s; }
    .search-box:focus-within { border-color: #198754; box-shadow: 0 0 0 0.25 cold-rgba(25, 135, 84, 0.1); }
    
    /* Table Styling */
    .table-custom thead th { 
        background-color: #f8f9fa; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        letter-spacing: 0.8px;
        color: #6c757d;
        border: none;
        padding: 15px;
    }
    .table-custom tbody td { padding: 15px; border-bottom: 1px solid #f8f9fa; font-size: 0.9rem; }
    .table-custom tbody tr:hover { background-color: #f1f8f5; transition: 0.2s; }

    /* Button Styling */
    .btn-detail { 
        background-color: #e8f5e9; 
        color: #198754; 
        border: none; 
        font-weight: 600; 
        transition: 0.3s;
        border-radius: 6px;
    }
    .btn-detail:hover { background-color: #198754; color: #fff; }

    .text-success-custom { color: #198754 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    {{-- HEADER --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-success-custom mb-1">
                <i class="bi bi-people-fill me-2"></i>Daftar Siswa
            </h4>
            <p class="text-muted small mb-0">Manajemen rekap absensi kelas: <strong>{{ $kelas->nama_kelas }}</strong></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
             <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                Total: {{ $siswas->count() }} Siswa
             </span>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group search-box">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Cari nama siswa..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success px-4 mx-1 my-1 rounded-2" style="font-size: 0.85rem;">Cari</button>
                    </div>
                </div>
                @if(request('search'))
                <div class="col-md-2">
                    <a href="{{ route('guru.rekap.wali') }}" class="btn btn-sm btn-light border text-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="80" class="text-center">No</th>
                        <th>Identitas Siswa</th>
                        <th width="200">NIS</th>
                        <th width="150" class="text-center">Laporan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $idx => $s)
                    <tr>
                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ strtoupper($s->nama_siswa) }}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Siswa Aktif</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="text-secondary fw-bold" style="font-size: 0.85rem;">{{ $s->nis ?? '-' }}</code>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('guru.rekap.detail', $s->id_siswa) }}" class="btn btn-sm btn-detail px-3">
                                <i class="bi bi-eye-fill me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-emoji-frown text-muted opacity-25" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Nama "<strong>{{ request('search') }}</strong>" tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection