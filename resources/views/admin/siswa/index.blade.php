@extends('layouts.admin')

@section('title', 'Data Siswa - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Page Container (Ukuran Asli Anda)
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1065px;
    margin-left: 1rem;
    margin-right: 10rem;
}
@media(max-width: 1400px) { .container-page { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px) { .container-page { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px) { .container-page { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card utama
============================ */
.card-main {
    border-radius: 14px;
    border: none;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

/* ============================
   Table
============================ */
.table-rekap thead th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 1rem;
    vertical-align: middle;
}
.table-rekap tbody td {
    padding: 0.85rem 0.8rem;
    vertical-align: middle;
}

/* ============================
   Alert modern (Tema Hijau)
============================ */
.alert-modern {
    padding: 15px 18px;
    border-left: 4px solid;
    border-radius: 8px;
    font-weight: 600;
}
.alert-success { border-color: #198754; background:#d1e7dd; color:#0f5132; }
.alert-danger  { border-color: #dc3545; background:#f8d7da; color:#842029; }

/* ============================
   Badge kelas (Dirubah ke Hijau agar senada)
============================ */
.badge-kelas {
    background: #198754; /* Hijau */
    color: #fff;
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
}

/* ============================
   Header Box
============================ */
.header-box {
    padding: 1.2rem 1rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}

/* ============================
   Tombol aksi & Custom Success
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
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center header-box mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-people-fill text-success me-2"></i> Data Siswa
        </h4>

        <a href="{{ route('admin.siswa.create') }}" class="btn btn-success-custom btn-action shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-modern mb-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter & Search --}}
    <div class="card-main mb-4">
        <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-3 align-items-end p-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Cari Siswa</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama siswa atau NIS..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Kelas</label>
                <select name="id_kelas" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" @selected(request('id_kelas') == $k->id_kelas)>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="L" @selected(request('jenis_kelamin') == 'L')>Laki-laki</option>
                    <option value="P" @selected(request('jenis_kelamin') == 'P')>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark btn-action flex-fill">Filter</button>
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary btn-action flex-fill">Reset</a>
            </div>
        </form>
    </div>

    {{-- Card Tabel --}}
    <div class="card-main shadow-sm">
        @if($siswas->isEmpty())
            <div class="text-center p-5">
                <i class="bi bi-person-x-fill text-muted opacity-50" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">Belum ada data siswa terdaftar dalam sistem.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped table-rekap mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:60px;">No</th>
                            <th>Nama Siswa</th>
                            <th style="width:120px;">NIS</th>
                            <th style="width:140px;">Jenis Kelamin</th>
                            <th style="width:120px;">Kelas</th>
                            <th class="text-center" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($siswas as $i => $s)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $siswas->firstItem() + $i }}</td>
                            <td class="fw-semibold text-dark">{{ $s->nama_siswa }}</td>
                            <td class="text-muted small">{{ $s->nis }}</td>
                            <td>
                                @if($s->jenis_kelamin == 'L')
                                    <span class="badge bg-light text-primary border rounded-pill small px-2">
                                        <i class="bi bi-gender-male me-1"></i> Laki-laki
                                    </span>
                                @else
                                    <span class="badge bg-light text-danger border rounded-pill small px-2">
                                        <i class="bi bi-gender-female me-1"></i> Perempuan
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-kelas">
                                    {{ $s->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.siswa.show', $s->id_siswa) }}"
                                   class="btn btn-sm btn-outline-secondary btn-action me-1"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.siswa.edit', $s->id_siswa) }}"
                                   class="btn btn-sm btn-outline-success btn-action me-1"
                                   title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.siswa.delete', $s->id_siswa) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus siswa {{ $s->nama_siswa }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-action" title="Hapus">
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
            @if($siswas->hasPages())
                <div class="mt-4 d-flex justify-content-between align-items-center p-3 border-top">
                    <p class="small text-muted mb-0">
                        Menampilkan {{ $siswas->firstItem() }} - {{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa
                    </p>
                    {{ $siswas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection