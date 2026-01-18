{{-- resources/views/absensi/index.blade.php --}}
@extends('layouts.guru')

@section('title', 'Absensi — Ringkasan Hari Ini')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .container { max-width: 85%; margin: 28px auto; padding: 0 35px; }
    .card { border: none; border-radius: 14px; box-shadow: 0 3px 12px rgba(0,0,0,0.05); margin-bottom: 24px; overflow: hidden; }
    .badge-soft { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px; }
    .badge-soft-success { background: #d1e7dd; color: #0f5132; }
    .badge-soft-info    { background: #cff4fc; color: #055160; }
    .badge-soft-warning { background: #fff3cd; color: #664d03; }
    .badge-soft-danger  { background: #f8d7da; color: #842029; }
    .badge-soft-secondary { background: #e9ecef; color: #6c757d; }
    .avatar-circle { width: 42px; height: 42px; background-color: #e9ecef; color: #495057; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 12px; }
    .table > :not(caption) > * > * { padding: 1rem 1rem; border-bottom-color: #f0f0f0; }
    .btn-primary-custom { background-color: #0d6efd; color: white; border: none; }
    .btn-primary-custom:hover { background-color: #0b5ed7; color: white; }
</style>
@endpush

@section('content')
@php
    $today = \Carbon\Carbon::today()->translatedFormat('d F Y');
    function getInitials($name) {
        $words = explode(' ', trim($name));
        return strtoupper(substr($words[0] ?? '',0,1) . (isset($words[1]) ? substr($words[1],0,1) : ''));
    }

    // Ambil daftar siswa di kelas tersebut
    $semuaSiswa = \App\Models\Siswa::where('id_kelas', $jadwal->id_kelas)->orderBy('nama_siswa')->get();

    /* PENTING: Kita ambil data absensi hari ini, 
       tapi kita ambil yang paling baru (latest) untuk setiap siswa 
       supaya kalau ada data double di database, yang muncul cuma yang terbaru.
    */
    $dataAbsensi = $absensis->sortByDesc('created_at')->unique('id_siswa')->keyBy('id_siswa');
@endphp

<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">Ringkasan Absensi</h4>
            <p class="text-muted mb-0 small">Daftar kehadiran kelas {{ $jadwal->kelas->nama_kelas ?? '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-white border shadow-sm btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Jadwal
            </a>
            <a href="{{ route('guru.absensi.create', ['id_jadwal_mengajar' => $jadwal->id_jadwal_mengajar]) }}" class="btn btn-primary-custom shadow-sm btn-sm px-3 py-2 rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Input Manual / Ubah Status
            </a>
        </div>
    </div>

    {{-- Info Mapel / Statistik --}}
    <div class="card">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary rounded-pill me-2">Mapel</span>
                        <h5 class="mb-0 fw-bold">{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</h5>
                    </div>
                    <div class="d-flex flex-wrap gap-3 small text-secondary mt-2">
                        <span><i class="bi bi-people"></i> Kelas: <strong>{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong></span>
                        <span><i class="bi bi-calendar-event"></i> {{ $jadwal->hari }}, {{ $today }}</span>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex justify-content-md-end gap-3 text-center mt-3 mt-md-0">
                        <div><h6 class="fw-bold mb-0 text-success">{{ $absensis->where('keterangan','hadir')->count() }}</h6><small class="text-muted">HADIR</small></div>
                        <div><h6 class="fw-bold mb-0 text-info">{{ $absensis->where('keterangan','izin')->count() }}</h6><small class="text-muted">IZIN</small></div>
                        <div><h6 class="fw-bold mb-0 text-warning">{{ $absensis->where('keterangan','sakit')->count() }}</h6><small class="text-muted">SAKIT</small></div>
                        <div><h6 class="fw-bold mb-0 text-danger">{{ $absensis->where('keterangan','alfa')->count() }}</h6><small class="text-muted">ALFA</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Absensi --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Nama Siswa</th>
                        <th class="py-3">Nis</th>
                        <th class="text-center py-3" style="width: 200px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semuaSiswa as $index => $siswa)
                        @php 
                            // Cari apakah siswa ini sudah ada data absennya hari ini
                            $absen = $dataAbsensi->get($siswa->id_siswa); 
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">{{ getInitials($siswa->nama_siswa) }}</div>
                                    <div>
                                        <div class="fw-bold">{{ $siswa->nama_siswa }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            @if($absen && $absen->jam_masuk) 
                                                <span class="text-primary"><i class="bi bi-box-arrow-in-right"></i> {{ $absen->jam_masuk }}</span> 
                                            @endif
                                            @if($absen && $absen->jam_keluar) 
                                                <span class="ms-2 text-success"><i class="bi bi-box-arrow-left"></i> {{ $absen->jam_keluar }}</span> 
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><div class="fw-bold text-muted">{{ $siswa->nis }}</div></td>
                            <td class="text-center">
                                @if(!$absen)
                                    <span class="badge-soft badge-soft-secondary"><i class="bi bi-dash-circle"></i> Belum Absen</span>
                                @elseif($absen->keterangan == 'hadir')
                                    <span class="badge-soft badge-soft-success"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                                @elseif($absen->keterangan == 'alfa' && $absen->jam_masuk && !$absen->jam_keluar)
                                    <span class="badge-soft badge-soft-warning"><i class="bi bi-hourglass-split"></i> Proses</span>
                                @elseif($absen->keterangan == 'izin')
                                    <span class="badge-soft badge-soft-info"><i class="bi bi-info-circle-fill"></i> Izin</span>
                                @elseif($absen->keterangan == 'sakit')
                                    <span class="badge-soft badge-soft-warning"><i class="bi bi-thermometer-half"></i> Sakit</span>
                                @else
                                    <span class="badge-soft badge-soft-danger"><i class="bi bi-x-circle-fill"></i> Alfa</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection