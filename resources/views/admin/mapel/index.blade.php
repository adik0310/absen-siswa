@extends('layouts.admin')

@section('title', 'Mata Pelajaran - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Container (Kembali ke ukuran spesifik Anda)
============================ */
.container {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1065px;
    margin-left: 0.01rem;
    margin-right: 48rem;
}
@media(max-width: 1400px) { .container { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px) { .container { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px) { .container { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card utama
============================ */
.card-main {
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: none;
    background-color: #fff;
    padding: 2rem;
}

/* ============================
   Table (Warna Header Hijau Soft)
============================ */
.table-rekap thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
    padding-top: 1rem;
    padding-bottom: 1rem;
    vertical-align: middle;
}
.table-rekap tbody td {
    vertical-align: middle;
    padding: 0.8rem 0.75rem;
}

/* ============================
   Alerts (Tema Hijau)
============================ */
.alert-modern {
    border-left: 4px solid;
    border-radius: 8px;
    padding: 15px;
    font-weight: 600;
}
.alert-success { border-color: #198754; background-color: #d1e7dd; color: #0f5132; }
.alert-danger { border-color: #dc3545; background-color: #f8d7da; color: #842029; }

/* ============================
   Tombol aksi (Rubah ke Hijau)
============================ */
.btn-action {
    padding: .3rem .6rem;
    font-size: 0.8rem;
    border-radius: 6px;
    line-height: 1.2;
}
.btn-success-custom {
    background-color: #198754;
    border-color: #198754;
    color: white;
}
.btn-success-custom:hover {
    background-color: #146c43;
    color: white;
}

/* Table hover */
.table-hover tbody tr:hover { background: #f5f7fa; }
</style>
@endpush

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">
            {{-- Icon rubah ke Hijau --}}
            <i class="bi bi-book-half me-2 text-success"></i> Data Mata Pelajaran
        </h4>
        {{-- Tombol rubah ke Hijau --}}
        <a href="{{ route('admin.mapel.create') }}" class="btn btn-success-custom btn-action shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-modern mb-4 shadow-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-modern mb-4 shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Filter & Search --}}
    <div class="card-main mb-4">
        <form method="GET" action="{{ route('admin.mapel.index') }}" class="row g-3 align-items-end p-3">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Cari Mata Pelajaran</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama mata pelajaran..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-dark btn-action flex-fill">Cari</button>
                <a href="{{ route('admin.mapel.index') }}" class="btn btn-outline-secondary btn-action flex-fill">Reset</a>
            </div>
        </form>
    </div>

    {{-- Card utama --}}
    <div class="card card-main">

        @if($mapels->isEmpty())
            <div class="text-center p-5">
                <i class="bi bi-x-circle-fill text-muted" style="font-size: 2rem;"></i>
                <p class="mt-3 text-muted">Belum ada data mata pelajaran terdaftar.</p>
            </div>
        @else

            <div class="table-responsive">
                <table class="table table-hover table-striped table-rekap align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>Nama Mata Pelajaran</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($mapels as $i => $m)
                            <tr>
                                <td class="ps-4 text-muted">{{ $mapels->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $m->nama_mapel }}</td>
                                <td class="text-center">

                                    {{-- Detail --}}
                                    <a href="{{ route('admin.mapel.show', $m->id_mata_pelajaran) }}"
                                       class="btn btn-outline-secondary btn-sm btn-action me-1"
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit (Warna Hijau) --}}
                                    <a href="{{ route('admin.mapel.edit', $m->id_mata_pelajaran) }}"
                                       class="btn btn-outline-success btn-sm btn-action me-1"
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.mapel.delete', $m->id_mata_pelajaran) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus mata pelajaran {{ $m->nama_mapel }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm btn-action" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            @if($mapels->hasPages())
                <div class="mt-4 d-flex justify-content-between align-items-center p-3 border-top pt-3">
                    <p class="small text-muted mb-0">
                        Halaman {{ $mapels->currentPage() }} dari {{ $mapels->lastPage() }}
                    </p>
                    {{ $mapels->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @endif

    </div>

</div>
@endsection