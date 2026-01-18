{{-- resources/views/absensi/create.blade.php --}}
@extends('layouts.guru')

@section('title', 'Input Absen — Form')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --accent: #10375f;
        --success: #2ecc71;
        --info: #4cc9f0;
        --warning: #f1c40f;
        --danger: #e74c3c;
        --surface: #ffffff;
        --radius-lg: 16px;
        --radius-md: 10px;
        --border-soft: rgba(16,55,95,0.08);
        --text-muted: #6b7f96;
        --shadow-sm: 0 4px 12px rgba(13,38,77,0.04);
        --shadow-md: 0 10px 30px rgba(13,38,77,0.08);
    }

    .container-narrow { max-width: 82%; margin: 25px auto; padding: 0 15px; }

    /* Card Styling */
    .card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 25px;
        border: none;
        box-shadow: var(--shadow-md);
        margin-bottom: 25px;
    }

    /* Header Styling */
    .header-content h4 {
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -0.5px;
        margin-bottom: 5px;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        background: #f0f4f8;
        border-radius: 20px;
        font-size: 0.75rem;
        color: var(--accent);
        font-weight: 600;
        margin-right: 8px;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-soft);
        margin-bottom: 20px;
        gap: 15px;
    }

    .date-display {
        font-size: 0.9rem;
        color: var(--accent);
        background: #eef2f7;
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-weight: 600;
    }

    /* Table Styling */
    .table-responsive { border-radius: var(--radius-md); overflow: hidden; }
    
    table.attendance { width: 100%; border-collapse: separate; border-spacing: 0; }
    
    table.attendance thead th {
        background: #f8fabb; /* Warna kuning soft agar beda dengan baris */
        background: #f1f5f9;
        padding: 15px;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    table.attendance tbody td {
        padding: 14px 15px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-soft);
    }

    table.attendance tbody tr:hover { background-color: #fcfdfe; }

    /* Custom Radio Buttons (Status Labels) */
    .ket-group { display: flex; gap: 6px; }
    .ket-radio { display: none; }
    .ket-label {
        flex: 1;
        padding: 10px 5px;
        border-radius: var(--radius-md);
        border: 1.5px solid #edf2f7;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.75rem;
        text-align: center;
        transition: all 0.2s ease;
        background: #fff;
        color: #718096;
        min-width: 65px;
    }

    /* States */
    .ket-label:hover { border-color: #cbd5e0; transform: translateY(-1px); }

    .ket-radio[value="hadir"]:checked + .ket-label { background: #e6fffa; border-color: var(--success); color: #234e52; box-shadow: 0 4px 10px rgba(46, 204, 113, 0.15); }
    .ket-radio[value="izin"]:checked + .ket-label { background: #e6fffa; background: #ebf8ff; border-color: var(--info); color: #2a4365; box-shadow: 0 4px 10px rgba(76, 201, 240, 0.15); }
    .ket-radio[value="sakit"]:checked + .ket-label { background: #fffaf0; border-color: var(--warning); color: #744210; box-shadow: 0 4px 10px rgba(241, 196, 15, 0.15); }
    .ket-radio[value="alfa"]:checked + .ket-label { background: #fff5f5; border-color: var(--danger); color: #742a2a; box-shadow: 0 4px 10px rgba(231, 76, 60, 0.15); }

    /* Floating Button on Mobile */
    @media (max-width: 768px) {
        .action-bar { flex-direction: column; align-items: stretch; }
        .ket-group { display: grid; grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container-narrow">
    {{-- Header Card --}}
    <div class="card header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="header-content">
                <h4>
                    <i class="bi bi-person-check-fill me-2 text-primary"></i>
                    Presensi Siswa
                </h4>
                <div class="mt-2">
                    <span class="info-badge"><i class="bi bi-book me-1"></i> {{ $jadwal->mataPelajaran->nama_mapel }}</span>
                    <span class="info-badge"><i class="bi bi-door-open me-1"></i> Kelas {{ $jadwal->kelas->nama_kelas }}</span>
                    <span class="info-badge"><i class="bi bi-clock me-1"></i> {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                </div>
            </div>
            <a href="{{ route('guru.absensi.index', ['id_jadwal_mengajar'=>$jadwal->id_jadwal_mengajar]) }}" class="btn btn-light btn-sm border">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card">
        @if($siswas->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">Tidak ada siswa terdaftar di kelas ini.</p>
            </div>
        @else
        <form id="absenForm" method="POST" action="{{ route('guru.absensi.store', ['id_jadwal_mengajar' => $jadwal->id_jadwal_mengajar]) }}">
            @csrf
            <input type="hidden" name="id_jadwal_mengajar" value="{{ $jadwal->id_jadwal_mengajar }}">

            <div class="action-bar">
                <div class="d-flex align-items-center gap-2">
                    <div class="date-display">
                        <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </div>
                    <button type="button" id="hadirSemuaBtn" class="btn btn-outline-success btn-sm fw-bold">
                        <i class="bi bi-check-all me-1"></i> Set Hadir Semua
                    </button>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Presensi
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="attendance">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Identitas Siswa</th>
                            <th width="120">NIS</th>
                            <th width="350">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $siswa)
                        @php
                            $dataSiswa = $alreadyAbsen[$siswa->id_siswa] ?? null;
                            $statusdb = $dataSiswa ? $dataSiswa->keterangan : ''; 
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ strtoupper($siswa->nama_siswa) }}</div>
                                @if($dataSiswa && $dataSiswa->jam_masuk && $dataSiswa->keterangan == 'alfa')
                                    <span class="badge bg-light text-warning border border-warning-subtle" style="font-size: 0.65rem;">
                                        <i class="bi bi-clock-history"></i> BELUM SCAN KELUAR
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-family: monospace;">{{ $siswa->nis }}</td>
                            <td>
                                <div class="ket-group">
                                    <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="h-{{ $siswa->id_siswa }}" value="hadir" {{ $statusdb == 'hadir' ? 'checked' : '' }}>
                                    <label class="ket-label" for="h-{{ $siswa->id_siswa }}">HADIR</label>

                                    <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="i-{{ $siswa->id_siswa }}" value="izin" {{ $statusdb == 'izin' ? 'checked' : '' }}>
                                    <label class="ket-label" for="i-{{ $siswa->id_siswa }}">IZIN</label>

                                    <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="s-{{ $siswa->id_siswa }}" value="sakit" {{ $statusdb == 'sakit' ? 'checked' : '' }}>
                                    <label class="ket-label" for="s-{{ $siswa->id_siswa }}">SAKIT</label>

                                    <input type="radio" class="ket-radio" name="keterangan[{{ $siswa->id_siswa }}]" id="a-{{ $siswa->id_siswa }}" value="alfa" {{ $statusdb == 'alfa' ? 'checked' : '' }}>
                                    <label class="ket-label" for="a-{{ $siswa->id_siswa }}">ALFA</label>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const hadirSemuaBtn = document.getElementById('hadirSemuaBtn');
    
    // Fungsi Hadir Semua dengan sedikit animasi visual
    if (hadirSemuaBtn) {
        hadirSemuaBtn.addEventListener('click', function() {
            const hadirRadios = document.querySelectorAll('input[value="hadir"]');
            hadirRadios.forEach(radio => {
                radio.checked = true;
                // Opsional: tambahkan trigger change jika ada script lain yang mendengarkan
                radio.dispatchEvent(new Event('change'));
            });
            
            // Notifikasi sukses kecil
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({
                icon: 'success',
                title: 'Semua siswa diatur Hadir'
            });
        });
    }

    // Konfirmasi sebelum simpan (Opsional tapi bagus untuk UX)
    const form = document.getElementById('absenForm');
    form.addEventListener('submit', function(e) {
        const totalSiswa = {{ count($siswas) }};
        const terisi = document.querySelectorAll('.ket-radio:checked').length;

        if(terisi < totalSiswa) {
            e.preventDefault();
            Swal.fire({
                title: 'Data belum lengkap!',
                text: `Ada ${totalSiswa - terisi} siswa yang belum diabsen. Tetap simpan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});
</script>
@endpush