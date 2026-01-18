@extends('layouts.admin')

@section('title','Kelola Absensi - Admin')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ============================
   Page Container (Konsisten)
============================ */
.container-page {
    padding-left: 8rem;
    padding-right: 8rem;
    max-width: 1065px;
    margin-left: 3.5rem;
    margin-right: 60rem;
}
@media(max-width: 1400px) { .container-page { padding-left: 4rem; padding-right: 4rem; } }
@media(max-width: 992px) { .container-page { padding-left: 2rem; padding-right: 2rem; } }
@media(max-width: 576px) { .container-page { padding-left: 1rem; padding-right: 1rem; } }

/* ============================
   Card & Header Styling
============================ */
.card-main {
    border-radius: 14px;
    border: none;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.header-box {
    padding: 1.2rem 1.5rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}

/* ============================
   Table & Badges
============================ */
.table-rekap thead th {
    background-color: #f8fbf9;
    font-weight: 700;
    color: #2d3436;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.5px;
    padding: 1.1rem 1rem;
    border-bottom: 2px solid #e9ecef;
}

.table-rekap tbody td {
    padding: 1rem 0.8rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f1f1;
}

.badge-status {
    padding: 0.45rem 0.75rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.7rem;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Warna Soft untuk Status */
.bg-soft-hadir  { background:#dcfce7; color:#15803d; border: 1px solid #bbf7d0; }
.bg-soft-izin   { background:#e0f2fe; color:#0369a1; border: 1px solid #bae6fd; }
.bg-soft-sakit  { background:#fef9c3; color:#a16207; border: 1px solid #fef08a; }
.bg-soft-alfa   { background:#fee2e2; color:#b91c1c; border: 1px solid #fecaca; }

/* ============================
   Filter & Form
============================ */
.filter-section {
    padding: 1.5rem;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #f1f1f1;
}

.form-select-sm {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.45rem 0.75rem;
}

.form-select-sm:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
}

/* Small details */
.small-details { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
.table-hover tbody tr:hover { background-color: #f0fdf4; }

.btn-action-custom {
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 8px;
}
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 header-box">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-journal-check me-2 text-success"></i> Data Absensi
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active text-success fw-bold" aria-current="page">Kelola Absensi</li>
                </ol>
            </nav>
        </div>

        <a href="{{ route('admin.absensi.create') }}" 
           class="btn btn-success px-4 rounded-3 shadow-sm border-0">
            <i class="bi bi-plus-lg me-1"></i> Tambah Absensi
        </a>
    </div>

    {{-- Filter Section --}}
    <div class="card-main filter-section mb-4">
        <form method="GET" action="{{ route('admin.absensi.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Kelas</label>
                <select name="id_kelas" id="id_kelas_filter" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" @selected(request('id_kelas') == $k->id_kelas)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel_filter" class="form-select form-select-sm">
                    <option value="">Semua Mapel</option>
                    @foreach($mapel as $m)
                        <option value="{{ $m->id_mata_pelajaran }}" @selected(request('id_mapel') == $m->id_mata_pelajaran)>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Guru Pengampu</label>
                <select name="id_guru" id="id_guru_filter" class="form-select form-select-sm">
                    <option value="">Semua Guru</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" @selected(request('id_guru') == $g->id_guru)>{{ $g->nama_guru }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-success btn-action-custom flex-fill shadow-sm">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary btn-action-custom flex-fill">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Info Bar --}}
    <div class="mb-3 ps-2">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
            <i class="bi bi-database-fill text-success me-2"></i>
            Total Riwayat: <b class="text-success">{{ $absensi->total() }}</b> Record
        </span>
    </div>

    {{-- Tabel Absensi --}}
    <div class="card-main">
        <div class="table-responsive">
            <table class="table table-hover table-rekap align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:60px;">No</th>
                        <th>Tanggal</th>
                        <th>Info Siswa</th>
                        <th>Akademik</th>
                        <th>Guru</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $i => $a)
                    <tr>
                        <td class="ps-4 text-muted fw-bold small">{{ $absensi->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-bold text-dark small">
                                <i class="bi bi-calendar3 me-2 text-success"></i>
                                {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-0">{{ $a->siswa->nama_siswa ?? '-' }}</div>
                            <div class="small-details">NIS: {{ $a->siswa->nis ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border-0 p-0 fw-bold small">
                                {{ $a->jadwal->kelas->nama_kelas ?? '-' }}
                            </span>
                            <div class="small-details text-truncate" style="max-width: 150px;">
                                {{ $a->jadwal->mataPelajaran->nama_mapel ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $a->jadwal->guru->nama_guru ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            @php 
                                $ket = strtolower($a->keterangan ?? 'alfa');
                                $icon = match($ket) {
                                    'hadir' => 'bi-check-circle-fill',
                                    'izin' => 'bi-info-circle-fill',
                                    'sakit' => 'bi-thermometer-half',
                                    default => 'bi-x-circle-fill',
                                };
                            @endphp
                            <span class="badge-status bg-soft-{{ $ket }}">
                                <i class="bi {{ $icon }}"></i> {{ ucfirst($a->keterangan ?? 'Alfa') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.absensi.edit', $a->id_absensi) }}" 
                                   class="btn btn-sm btn-outline-success btn-action-custom" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.absensi.destroy', $a->id_absensi) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-action-custom" 
                                            onclick="return confirm('Hapus data absensi ini?')" title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/data-report.svg" style="width: 120px;" class="opacity-50 mb-3">
                            <h6 class="fw-bold text-muted">Data Absensi Tidak Ditemukan</h6>
                            <p class="text-muted small">Coba sesuaikan filter atau tambahkan data baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <p class="small text-muted mb-0">
            Menampilkan <b>{{ $absensi->count() }}</b> dari <b>{{ $absensi->total() }}</b> data
        </p>
        {{ $absensi->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Jalankan setiap kali dropdown Kelas atau Mapel berubah
    $('#id_kelas_filter, #id_mapel_filter').on('change', function() {
        let kelasId = $('#id_kelas_filter').val();
        let mapelId = $('#id_mapel_filter').val();
        let guruSelect = $('#id_guru_filter');

        // Jika dua-duanya sudah dipilih, kita minta data ke server
        if (kelasId && mapelId) {
            $.ajax({
                url: "{{ route('admin.get.guru.jadwal') }}", // Kita akan buat route ini
                type: "GET",
                data: { id_kelas: kelasId, id_mapel: mapelId },
                success: function(res) {
                    guruSelect.empty().append('<option value="">Semua Guru</option>');
                    $.each(res, function(id, nama) {
                        guruSelect.append('<option value="'+ id +'">'+ nama +'</option>');
                    });
                }
            });
        }
    });
});
</script>