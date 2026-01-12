{{-- resources/views/absensi/create.blade.php --}}
@extends('layouts.guru')

@section('title', 'Input Absen — Form')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    :root{
        --primary: #0d6efd;
        --accent: #10375f;
        --muted: #f3f6f9;
        --surface: #ffffff;
        --radius: 12px;
        --border-soft: rgba(16,55,95,0.08);
        --text-muted: #6b7f96;
        --hover-row: #f8fbff;
    }

    .container-narrow{
        max-width: 1100px;
        margin: 30px auto;
        padding: 0 28px;
    }

    .card{
        background: var(--surface);
        border-radius: var(--radius);
        padding: 22px;
        border: 1px solid rgba(16,55,95,0.04);
        box-shadow: 0 8px 28px rgba(13,38,77,0.06);
    }

    .info-header h4{
        font-weight: 800;
        color: var(--accent);
        font-size: 1.25rem;
    }

    .info-sub{
        color: var(--text-muted);
        font-size: .9rem;
    }

    .action-bar{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding-bottom:13px;
        border-bottom:1px solid var(--border-soft);
        flex-wrap:wrap;
        gap:12px;
    }

    .date-pill{
        padding:8px 12px;
        background: linear-gradient(180deg,#f5f7fa,#eef2f5);
        border-radius:8px;
        font-weight:700;
        color:var(--accent);
    }

    .btn-sm{
        padding: .4rem .75rem;
        font-size:.86rem;
        font-weight:700;
        border-radius:8px;
    }

    .btn-outline-success{
        background:#f5fff7;
        border:1px solid rgba(25,135,84,.2);
        color:#198754;
    }

    .btn-outline-secondary{
        background:#f8f9fa;
        border:1px solid #d0d5da;
    }

    .table-wrap{
        overflow-x:auto;
        margin-top:15px;
    }

    table.attendance{
        width:100%;
        min-width: 750px;
        border-collapse:separate;
        border-spacing:0;
        border:1px solid var(--border-soft);
        border-radius:10px;
        overflow:hidden;
    }

    table.attendance thead th{
        background:linear-gradient(180deg,#fff,#f8f9fa);
        padding:14px 16px;
        font-weight:800;
        font-size:.85rem;
        color:var(--accent);
        border-bottom:1px solid rgba(16,55,95,0.12);
    }

    table.attendance tbody td{
        padding:12px 16px;
        font-size:.95rem;
        border-bottom:1px solid rgba(16,55,95,0.05);
    }

    table.attendance tbody tr:hover{
        background:var(--hover-row);
    }

    .col-index{ width:60px; text-align:center; font-weight:600; color:var(--text-muted); }
    .col-nis{ width:140px; font-size:.85rem; color:var(--text-muted); }

    .ket-group{
        display:flex;
        gap:8px;
        flex-wrap:wrap;
    }

    .ket-radio{ display:none; }

    .ket-label{
        padding:8px 12px;
        border-radius:8px;
        border:1px solid rgba(16,55,95,0.15);
        cursor:pointer;
        font-weight:700;
        background:#fff;
        min-width:80px;
        text-align:center;
        transition:.15s ease;
    }

    .ket-label:hover{
        transform:translateY(-2px);
        box-shadow:0 4px 12px rgba(13,38,77,0.07);
    }

    .ket-radio[data-val="hadir"]:checked + .ket-label{
        background:#e9f8ef; border-color:#198754; color:#0b5e37;
    }
    .ket-radio[data-val="izin"]:checked + .ket-label{
        background:#e7faff; border-color:#0dcaf0; color:#055160;
    }
    .ket-radio[data-val="sakit"]:checked + .ket-label{
        background:#fff6e5; border-color:#ffc107; color:#7a5902;
    }
    .ket-radio[data-val="alfa"]:checked + .ket-label{
        background:#ffeaea; border-color:#dc3545; color:#7a1c23;
    }

    @media(max-width:850px){
        .action-bar{ flex-direction:column; align-items:flex-start; }
    }
</style>
@endpush


@section('content')
<div class="container-narrow">

    {{-- ======================= Header ======================= --}}
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
                    &bull; {{ \Illuminate\Support\Str::limit($jadwal->jam_mulai,5) }} - {{ \Illuminate\Support\Str::limit($jadwal->jam_selesai,5) }}
                </div>
            </div>

            <a href="{{ route('guru.absensi.index', ['id_jadwal_mengajar'=>$jadwal->id_jadwal_mengajar]) }}"
               class="btn btn-outline-secondary btn-sm">
               <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>


    {{-- ======================= Main Form ======================= --}}
    <div class="card">

        @if($siswas->isEmpty())
            <div class="alert alert-warning border-start border-4 border-warning">
                <i class="bi bi-exclamation-circle me-1"></i>
                Tidak ada siswa dalam kelas ini.
            </div>

        @else

            <form id="absenForm" method="POST"
                  action="{{ route('guru.absensi.store', ['id_jadwal_mengajar'=>$jadwal->id_jadwal_mengajar]) }}">
                @csrf

                {{-- Action Bar --}}
                <div class="action-bar">
                    <div class="d-flex align-items-center gap-3 flex-wrap">

                        <div class="date-pill">
                            Tanggal: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>
                        </div>

                        <button type="button" id="hadirSemuaBtn" class="btn btn-outline-success btn-sm" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                            <i class="bi bi-people-check me-1"></i> Hadir Semua
                        </button>

                        <button type="button" id="resetSemuaBtn" class="btn btn-outline-secondary btn-sm" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('guru.absensi.index', ['id_jadwal_mengajar'=>$jadwal->id_jadwal_mengajar]) }}"
                           class="btn btn-outline-danger btn-sm">
                           <i class="bi bi-x-circle me-1"></i> Batal
                        </a>

                        <button class="btn btn-primary btn-sm" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>


                {{-- Table --}}
                <div class="table-wrap mt-3">
                    <table class="attendance">
                        <thead>
                        <tr>
                            <th class="col-index">#</th>
                            <th>Nama Siswa</th>
                            <th class="col-nis">NIS</th>
                            <th style="width:40%;">Keterangan</th>
                        </tr>
                        </thead>

                        <tbody id="siswaTableBody">

                        @foreach($siswas as $i => $s)
                            @php $idS = $s->id_siswa ?? $s->id; @endphp

                            <tr>
                                <td class="col-index">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $s->nama_siswa }}</td>
                                <td class="col-nis">{{ $s->nis }}</td>

                                <td>
                                    <div class="ket-group">

                                        <input type="radio" data-val="hadir" id="h_{{ $idS }}" class="ket-radio" name="keterangan[{{ $idS }}]" value="hadir" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                                        <label for="h_{{ $idS }}" class="ket-label">Hadir</label>

                                        <input type="radio" data-val="izin" id="i_{{ $idS }}" class="ket-radio" name="keterangan[{{ $idS }}]" value="izin" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                                        <label for="i_{{ $idS }}" class="ket-label">Izin</label>

                                        <input type="radio" data-val="sakit" id="s_{{ $idS }}" class="ket-radio" name="keterangan[{{ $idS }}]" value="sakit" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                                        <label for="s_{{ $idS }}" class="ket-label">Sakit</label>

                                        <input type="radio" data-val="alfa" id="a_{{ $idS }}" class="ket-radio" name="keterangan[{{ $idS }}]" value="alfa" {{ (!$isToday || !$isWithinTime) ? 'disabled' : '' }}>
                                        <label for="a_{{ $idS }}" class="ket-label">Alfa</label>
                                    </div>

                                    <input type="hidden" name="siswa_ids[]" value="{{ $idS }}">
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

    // Pastikan tombol tidak null baru pasang event
    if (hadirSemuaBtn) {
        hadirSemuaBtn.addEventListener('click', function() {
            // Cari semua radio button 'hadir' yang tidak disabled
            const radios = document.querySelectorAll('input[data-val="hadir"]:not(:disabled)');
            radios.forEach(r => r.checked = true);
        });
    }

    if (resetSemuaBtn) {
        resetSemuaBtn.addEventListener('click', function() {
            // Uncheck semua yang tidak disabled
            const radios = document.querySelectorAll('.ket-radio:not(:disabled)');
            radios.forEach(r => r.checked = false);
        });
    }
    
    // Supaya Label bisa diklik (Backup jika radio-nya tersembunyi/hidden)
    document.querySelectorAll('.ket-label').forEach(label => {
        label.addEventListener('click', function() {
            const radioId = this.getAttribute('for');
            const radio = document.getElementById(radioId);
            if (radio && !radio.disabled) {
                radio.checked = true;
            }
        });
    });
});
</script>
@endpush
