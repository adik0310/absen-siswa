@extends('layouts.guru')

@section('title', 'Detail Rekap Siswa')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .container-fluid { padding: 0 115px; }

    /* Card Styling */
    .card-rekap { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
    .card-rekap-header { background-color: #fff !important; border-bottom: 1px solid #f1f1f1; padding: 1.25rem; }

    /* Status Badge Styling - Senada dengan Halaman Rekap Bulanan */
    .badge-status { 
        padding: 2px 8px; 
        border-radius: 4px; 
        font-weight: 800; 
        font-size: 0.7rem;
        display: inline-block;
        min-width: 25px;
        text-align: center;
    }
    .bg-hadir { background: #e8f5e9; color: #2e7d32; }
    .bg-izin  { background: #e3f2fd; color: #1565c0; }
    .bg-sakit { background: #fff8e1; color: #f9a825; }
    .bg-alfa  { background: #ffebee; color: #c62828; }

    /* Table Styling */
    .table-custom thead th { 
        background-color: #f8f9fa; 
        text-transform: uppercase; 
        font-size: 0.65rem; 
        letter-spacing: 0.5px;
        color: #6c757d;
        vertical-align: middle;
        border-top: none;
    }
    .table-custom tbody td { border-bottom: 1px solid #f5f5f5; font-size: 0.85rem; padding: 10px 5px; }
    
    .text-success-custom { color: #198754 !important; }
    .bg-success-subtle-custom { background-color: #e8f5e9 !important; color: #198754 !important; }
    
    /* Kolom Tanggal (Abu-abu Halus) */
    .pertemuan-col { font-size: 0.65rem; color: #8898aa; background-color: #fafafa; font-weight: 600; }
    
    /* Info Siswa Header */
    .info-siswa-box { background: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #198754; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    {{-- NAVIGASI ATAS --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('guru.rekap.wali') }}" class="btn btn-sm btn-outline-secondary mb-3 shadow-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Siswa
            </a>
            <h4 class="fw-bold text-success-custom mb-0">
                <i class="bi bi-person-check-fill me-2"></i>Laporan Kehadiran Siswa
            </h4>
        </div>
        
        <div class="text-end">
            <a href="{{ route('guru.rekap.pdf', ['id_siswa' => $siswa->id_siswa, 'month' => $month, 'year' => $year]) }}" class="btn btn-danger shadow-sm">
                <i class="bi bi-file-pdf me-1"></i> Cetak Laporan (PDF)
            </a>
        </div>
    </div>

    {{-- INFO SISWA & FILTER --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="info-siswa-box shadow-sm h-100 d-flex flex-column justify-content-center">
                <h5 class="fw-bold mb-1 text-dark">{{ strtoupper($siswa->nama_siswa) }}</h5>
                <p class="mb-0 text-muted small">
                    NIS: <strong>{{ $siswa->nis ?? '-' }}</strong> | Kelas: <strong>{{ $kelas->nama_kelas }}</strong> | MA Nurul Iman
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-rekap h-100 p-3 d-flex justify-content-center shadow-sm">
                <form method="GET">
                    <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.7rem;">PILIH PERIODE BULAN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-success text-success"><i class="bi bi-calendar3"></i></span>
                        <select name="month" class="form-select border-success shadow-none" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card card-rekap shadow-sm">
        <div class="card-rekap-header d-flex justify-content-between align-items-center bg-light">
            <h6 class="fw-bold m-0 text-dark">
                <i class="bi bi-grid-3x3-gap me-2 text-success-custom"></i> Rekap Absensi per Mata Pelajaran
            </h6>
            <span class="badge bg-success-subtle-custom px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem;">
                Periode: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0 align-middle text-center border-start border-end">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-start ps-4" style="min-width: 200px; background: #fff;">Mata Pelajaran</th>
                        <th colspan="{{ $maxPertemuan }}" class="bg-light border-bottom text-success py-2">Pertemuan</th>
                        <th colspan="4" class="bg-success-subtle-custom border-0">Total</th>
                    </tr>
                    <tr>
                        @for($i = 1; $i <= $maxPertemuan; $i++)
                            <th width="50px" class="bg-white border-bottom" style="font-size: 0.6rem;">P{{ $i }}</th>
                        @endfor
                        <th width="40px" class="bg-sakit border-bottom">S</th>
                        <th width="40px" class="bg-izin border-bottom">I</th>
                        <th width="40px" class="bg-alfa border-bottom">A</th>
                        <th width="40px" class="bg-hadir border-bottom">H</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mapels as $mp)
                        @php
                            $absenSiswaMapel = $absensi->filter(function($item) use ($mp) {
                                return optional($item->jadwal)->id_mapel == $mp->id_mata_pelajaran;
                            })->sortBy('tanggal')->values();

                            $sakit = $absenSiswaMapel->where('keterangan', 'sakit')->count();
                            $izin  = $absenSiswaMapel->where('keterangan', 'izin')->count();
                            $alpa  = $absenSiswaMapel->where('keterangan', 'alpa')->count();
                            $hadir = $absenSiswaMapel->where('keterangan', 'hadir')->count();
                        @endphp
                        
                        {{-- Baris 1: Tanggal --}}
                        <tr>
                            <td rowspan="2" class="text-start ps-4 fw-bold text-dark border-end bg-white">
                                <i class="bi bi-journal-text me-2 text-success"></i>{{ $mp->nama_mapel }}
                            </td>
                            @for($i = 0; $i < $maxPertemuan; $i++)
                                <td class="pertemuan-col py-1 border-0">
                                    @if(isset($absenSiswaMapel[$i]))
                                        {{ \Carbon\Carbon::parse($absenSiswaMapel[$i]->tanggal)->format('d/m') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                            {{-- Counter Total --}}
                            <td rowspan="2" class="fw-bold text-warning border-start" style="font-size: 1rem;">{{ $sakit }}</td>
                            <td rowspan="2" class="fw-bold text-info" style="font-size: 1rem;">{{ $izin }}</td>
                            <td rowspan="2" class="fw-bold text-danger" style="font-size: 1rem;">{{ $alpa }}</td>
                            <td rowspan="2" class="fw-bold text-success bg-success-subtle-custom" style="font-size: 1.1rem;">{{ $hadir }}</td>
                        </tr>

                        {{-- Baris 2: Simbol Kehadiran --}}
                        <tr>
                            @for($i = 0; $i < $maxPertemuan; $i++)
                                <td class="py-2 border-top-0">
                                    @if(isset($absenSiswaMapel[$i]))
                                        @php 
                                            $ket = strtoupper(substr($absenSiswaMapel[$i]->keterangan, 0, 1));
                                            $badgeClass = match($ket) {
                                                'H' => 'bg-hadir', 'A' => 'bg-alfa',
                                                'S' => 'bg-sakit', 'I' => 'bg-izin',
                                                default => ''
                                            };
                                        @endphp
                                        <span class="badge-status {{ $badgeClass }}">{{ $ket }}</span>
                                    @else
                                        <span class="text-muted opacity-25" style="font-size: 0.7rem;">-</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER LEGEND --}}
    <div class="mt-4 row align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm border border-success-subtle" style="font-size: 0.8rem;">
                <span class="fw-bold text-muted"><i class="bi bi-info-circle-fill me-1 text-success"></i> Petunjuk:</span>
                <span class="badge-status bg-hadir">H</span> <small>Hadir</small>
                <span class="badge-status bg-sakit">S</span> <small>Sakit</small>
                <span class="badge-status bg-izin">I</span> <small>Izin</small>
                <span class="badge-status bg-alfa">A</span> <small>Alfa</small>
            </div>
        </div>
        <div class="col-md-6 text-md-end text-muted small">
            * Data diupdate secara otomatis setiap kali guru mata pelajaran mengisi absensi.
        </div>
    </div>
</div>
@endsection