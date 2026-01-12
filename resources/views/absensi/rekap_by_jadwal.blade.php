{{-- resources/views/guru/rekap/show.blade.php --}}
@extends('layouts.guru')

@section('title', 'Rekap Absensi — Jadwal')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    /* Kontainer utama tetap lega namun rapi */
    .container-page { padding: 1.5rem 3rem; max-width: 1200px; margin: 0 auto; }
    @media(max-width: 768px) { .container-page { padding: 1rem; } }

    /* Card Styling - Menghilangkan border kasar */
    .card-rekap { 
        border-radius: 12px; 
        border: none; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        background: #fff; 
        overflow: hidden;
    }

    /* Badge Modern & Soft */
    .badge-soft {
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-block;
        min-width: 38px;
        text-align: center;
        border: 1px solid transparent;
    }
    .badge-soft-success { background-color: #f0fdf4; color: #166534; border-color: #dcfce7; }
    .badge-soft-info    { background-color: #eff6ff; color: #1e40af; border-color: #dbeafe; }
    .badge-soft-warning { background-color: #fffbeb; color: #92400e; border-color: #fef3c7; }
    .badge-soft-danger  { background-color: #fef2f2; color: #991b1b; border-color: #fee2e2; }

    /* Tabel yang lebih bersih */
    .table-custom thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 14px 10px;
        border: none;
    }
    .table-custom tbody td { 
        border-bottom: 1px solid #f1f5f9; 
        padding: 12px 10px; 
        font-size: 0.88rem; 
        color: #334155;
    }
    .table-custom tbody tr:hover { background-color: #f8fafc; }

    /* Aksen & Meta */
    .text-success-custom { color: #10b981 !important; }
    .bg-total-column { background-color: #f0fdf4 !important; font-weight: 800; color: #166534; }
    .text-nis { font-size: 0.78rem; color: #94a3b8; font-family: monospace; }
</style>
@endpush

@section('content')
@php
    $monthNames = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
@endphp

<div class="container-page">

    {{-- === 1. TOP HEADER === --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jadwal.index') }}" class="text-decoration-none text-muted">Jadwal</a></li>
                    <li class="breadcrumb-item active text-success" aria-current="page">Rekap Bulanan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Rekap Absensi Siswa
            </h4>
            <p class="text-muted mb-0 small">Periode Laporan: <strong>{{ $monthNames[$month] ?? $month }} {{ $year }}</strong></p>
        </div>

        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-sm btn-white border shadow-sm px-3 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- === 2. JADWAL DETAIL CARD === --}}
    <div class="card card-rekap mb-4 border-start border-success border-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="badge bg-success-subtle text-success mb-2 px-3 py-2 rounded-pill small fw-bold">
                        <i class="bi bi-tag-fill me-1"></i> Informasi Kelas
                    </span>
                    <h5 class="fw-extrabold mb-1 text-dark">
                         {{ $jadwal->mataPelajaran->nama_mapel ?? ($jadwal->mapel->nama_mapel ?? '-') }}
                    </h5>
                    <div class="d-flex flex-wrap gap-4 mt-2 text-secondary" style="font-size: 0.85rem;">
                        <span><i class="bi bi-door-open me-1"></i> <strong>{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong></span>
                        <span><i class="bi bi-calendar-event me-1"></i> {{ $jadwal->hari ?? '-' }}</span>
                        <span><i class="bi bi-clock me-1"></i> {{ \Illuminate\Support\Str::limit($jadwal->jam_mulai ?? '',5,'') }} - {{ \Illuminate\Support\Str::limit($jadwal->jam_selesai ?? '',5,'') }} WIB</span>
                    </div>
                </div>

                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <p class="text-muted small mb-2 text-uppercase fw-bold letter-spacing-1">Ekspor Laporan</p>
                    <div class="btn-group shadow-sm">
                        <a href="{{ route('guru.absensi.rekap.pdf_download_by_jadwal', ['id_jadwal' => $jadwal->id_jadwal_mengajar ?? $jadwal->id,'year' => $year,'month' => $month]) }}" class="btn btn-outline-danger btn-sm px-3">
                            <i class="bi bi-filetype-pdf me-1"></i> PDF
                        </a>
                        <a href="{{ route('guru.absensi.rekap.excel_by_jadwal', ['id_jadwal' => $jadwal->id_jadwal_mengajar ?? $jadwal->id, 'year' => $year, 'month' => $month]) }}" class="btn btn-outline-success btn-sm px-3">
                            <i class="bi bi-filetype-exe me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === 3. TABLE REKAP DATA === --}}
    @if($rekap->isEmpty())
        <div class="card card-rekap text-center py-5 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-clipboard-x text-light" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Data Belum Tersedia</h5>
                <p class="text-muted mx-auto mb-0" style="max-width: 400px;">
                    Sepertinya belum ada data absensi yang diinput untuk jadwal ini pada bulan {{ $monthNames[$month] }}.
                </p>
            </div>
        </div>
    @else
        <div class="card card-rekap">
            <div class="table-responsive">
                <table class="table table-custom mb-0 align-middle">
                    <thead class="text-center">
                        <tr>
                            <th style="width:60px">No</th>
                            <th class="text-start">Nama Lengkap Siswa</th>
                            <th style="width:140px">Nomor Induk (NIS)</th>
                            <th style="width:85px">Hadir</th>
                            <th style="width:85px">Izin</th>
                            <th style="width:85px">Sakit</th>
                            <th style="width:85px">Alfa</th>
                            <th style="width:100px" class="bg-total-column border-0">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekap as $i => $r)
                            <tr class="text-center">
                                <td class="text-muted fw-bold">{{ $i+1 }}</td>
                                <td class="text-start fw-bold text-dark">
                                    {{ strtoupper($r->nama_siswa) }}
                                </td>
                                <td class="text-nis">{{ $r->nis }}</td>
                                <td><span class="badge-soft badge-soft-success">{{ $r->hadir }}</span></td>
                                <td><span class="badge-soft badge-soft-info">{{ $r->izin }}</span></td>
                                <td><span class="badge-soft badge-soft-warning">{{ $r->sakit }}</span></td>
                                <td><span class="badge-soft badge-soft-danger">{{ $r->alfa }}</span></td>
                                <td class="bg-total-column border-0">{{ $r->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if(method_exists($rekap, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $rekap->withQueryString()->links() }}
            </div>
        @endif
    @endif

</div>
@endsection