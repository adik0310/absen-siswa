{{-- resources/views/absensi/create.blade.php --}}
@extends('layouts.guru')

@section('title', 'Input Absen — Form')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    /* ... (Style tetap sama seperti yang kamu buat, sudah bagus) ... */
    :root{
        --primary: #0d6efd; --accent: #10375f; --muted: #f3f6f9;
        --surface: #ffffff; --radius: 12px; --border-soft: rgba(16,55,95,0.08);
        --text-muted: #6b7f96; --hover-row: #f8fbff;
    }
    .container-narrow{ max-width: 1100px; margin: 30px auto; padding: 0 28px; }
    .card{ background: var(--surface); border-radius: var(--radius); padding: 22px; border: 1px solid rgba(16,55,95,0.04); box-shadow: 0 8px 28px rgba(13,38,77,0.06); }
    .info-header h4{ font-weight: 800; color: var(--accent); font-size: 1.25rem; }
    .info-sub{ color: var(--text-muted); font-size: .9rem; }
    .action-bar{ display:flex; justify-content:space-between; align-items:center; padding-bottom:13px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:12px; }
    .date-pill{ padding:8px 12px; background: linear-gradient(180deg,#f5f7fa,#eef2f5); border-radius:8px; font-weight:700; color:var(--accent); }
    .btn-sm{ padding: .4rem .75rem; font-size:.86rem; font-weight:700; border-radius:8px; }
    .btn-outline-success{ background:#f5fff7; border:1px solid rgba(25,135,84,.2); color:#198754; }
    .btn-outline-secondary{ background:#f8f9fa; border:1px solid #d0d5da; }
    .table-wrap{ overflow-x:auto; margin-top:15px; }
    table.attendance{ width:100%; min-width: 750px; border-collapse:separate; border-spacing:0; border:1px solid var(--border-soft); border-radius:10px; overflow:hidden; }
    table.attendance thead th{ background:linear-gradient(180deg,#fff,#f8f9fa); padding:14px 16px; font-weight:800; font-size:.85rem; color:var(--accent); border-bottom:1px solid rgba(16,55,95,0.12); }
    table.attendance tbody td{ padding:12px 16px; font-size:.95rem; border-bottom:1px solid rgba(16,55,95,0.05); }
    table.attendance tbody tr:hover{ background:var(--hover-row); }
    .col-index{ width:60px; text-align:center; font-weight:600; color:var(--text-muted); }
    .col-nis{ width:140px; font-size:.85rem; color:var(--text-muted); }
    .ket-group{ display:flex; gap:8px; flex-wrap:wrap; }
    .ket-radio{ display:none; }
    .ket-label{ padding:8px 12px; border-radius:8px; border:1px solid rgba(16,55,95,0.15); cursor:pointer; font-weight:700; background:#fff; min-width:80px; text-align:center; transition:.15s ease; }
    .ket-label:hover{ transform:translateY(-2px); box-shadow:0 4px 12px rgba(13,38,77,0.07); }
    .ket-radio[data-val="hadir"]:checked + .ket-label{ background:#e9f8ef; border-color:#198754; color:#0b5e37; }
    .ket-radio[data-val="izin"]:checked + .ket-label{ background:#e7faff; border-color:#0dcaf0; color:#055160; }
    .ket-radio[data-val="sakit"]:checked + .ket-label{ background:#fff6e5; border-color:#ffc107; color:#7a5902; }
    .ket-radio[data-val="alfa"]:checked + .ket-label{ background:#ffeaea; border-color:#dc3545; color:#7a1c23; }
</style>
@endpush

@section('content')
<div class="container-narrow">
    {{-- Header --}}
    <div class="card mb-4 info-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4>
                    <i class="bi bi-clipboard2-check-fill me-2 text-primary"></i>
                    Input Absen — {{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}
                </h4>
                <div class="info-sub mt-1">
                    Kelas: <strong>{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong>  
                    &bull; {{ $jadwal->hari ?? '-' }}  
                    &bull; {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                </div>
            </div>
            <a href="{{ route('guru.absensi.index', ['id_jadwal_mengajar'=>$jadwal->id_jadwal_mengajar]) }}" class="btn btn-outline-secondary btn-sm">
               <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Main Form --}}
    <div class="card">
        @if($siswas->isEmpty())
            <div class="alert alert-warning">Tidak ada siswa dalam kelas ini.</div>
        @else
        <form id="absenForm" method="POST" action="{{ route('guru.absensi.store', ['id_jadwal_mengajar' => $jadwal->id_jadwal_mengajar]) }}">
                @csrf
                {{-- Sangat Penting: Kirimkan ID Jadwal --}}
                <input type="hidden" name="id_jadwal_mengajar" value="{{ $jadwal->id_jadwal_mengajar }}">

                <div class="action-bar">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="date-pill">
                            Tanggal: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>
                        </div>
                        <button type="button" id="hadirSemuaBtn" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-people-check me-1"></i> Hadir Semua
                        </button>
                        <button type="button" id="resetSemuaBtn" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-save me-1"></i> Simpan Absensi
                        </button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="attendance">
                        <thead>
                            <tr>
                                <th class="col-index">No</th>
                                <th>Nama Siswa</th>
                                <th class="col-nis">NIS</th>
                                <th style="width:40%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $siswa)
                            @php
                                $dataSiswa = $alreadyAbsen[$siswa->id_siswa] ?? null;
                                $statusdb = $dataSiswa ? $dataSiswa->keterangan : ''; 
                            @endphp
                            <tr>
                                <td class="col-index">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $siswa->nama_siswa }}</div>
                                    @if($dataSiswa && $dataSiswa->jam_masuk && $dataSiswa->keterangan == 'alfa')
                                        <small class="text-warning fw-bold"><i class="bi bi-hourglass-split"></i> Menunggu Scan Keluar</small>
                                    @endif
                                </td>
                                <td class="col-nis">{{ $siswa->nis }}</td>
                                <td>
                                    <div class="ket-group">
                                        <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="h-{{ $siswa->id_siswa }}" value="hadir" data-val="hadir" {{ $statusdb == 'hadir' ? 'checked' : '' }}>
                                        <label class="ket-label" for="h-{{ $siswa->id_siswa }}">Hadir</label>

                                        <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="i-{{ $siswa->id_siswa }}" value="izin" data-val="izin" {{ $statusdb == 'izin' ? 'checked' : '' }}>
                                        <label class="ket-label" for="i-{{ $siswa->id_siswa }}">Izin</label>

                                        <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="s-{{ $siswa->id_siswa }}" value="sakit" data-val="sakit" {{ $statusdb == 'sakit' ? 'checked' : '' }}>
                                        <label class="ket-label" for="s-{{ $siswa->id_siswa }}">Sakit</label>

                                        <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="a-{{ $siswa->id_siswa }}" value="alfa" data-val="alfa" {{ $statusdb == 'alfa' ? 'checked' : '' }}>
                                        <label class="ket-label" for="a-{{ $siswa->id_siswa }}">Alfa</label>
                                    </div>
                                    {{-- ID Siswa dikirim lewat array keterangan[] jadi tidak butuh hidden input tambahan per baris --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const hadirSemuaBtn = document.getElementById('hadirSemuaBtn');
    const resetSemuaBtn = document.getElementById('resetSemuaBtn');

    // 1. Fungsi Hadir Semua (Mencentang semua radio Hadir)
    if (hadirSemuaBtn) {
        hadirSemuaBtn.addEventListener('click', function() {
            const hadirRadios = document.querySelectorAll('input[data-val="hadir"]');
            hadirRadios.forEach(radio => radio.checked = true);
        });
    }

    // 2. Fungsi Reset (Menghapus semua centang)
    if (resetSemuaBtn) {
        resetSemuaBtn.addEventListener('click', function() {
            const allRadios = document.querySelectorAll('.ket-radio');
            allRadios.forEach(radio => radio.checked = false);
        });
    }
});
</script>
@endpush