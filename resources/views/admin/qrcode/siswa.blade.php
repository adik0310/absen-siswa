@extends('layouts.admin')

@section('title', 'Kelola QR Code Siswa')

@push('head')
<style>
    /* TRICK: Memaksa container utama melebar maksimal ke kanan-kiri */
    main.container-main {
        max-width: 85% !important;
        padding-left: 40px !important;
        padding-right: 40px !important;
    }

    /* Card styling agar lebih mewah */
    .card-custom {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    /* Header Tabel */
    .table-custom thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #555;
        padding: 15px 20px;
        border-bottom: 2px solid #eee;
    }

    /* Isi Tabel */
    .table-custom tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        font-size: 0.95rem;
        border-bottom: 1px solid #f8f9fa;
    }

    /* NIS Styling */
    .nis-code {
        font-family: 'Monaco', 'Consolas', monospace;
        background: #f1f5f9;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
    }

    /* Badge Soft Color */
    .bg-soft-success {
        background-color: rgba(13, 110, 253, 0.1);
        color: #2bc71d;
        font-weight: 700;
    }

    .btn-preview {
        transition: all 0.3s;
        font-weight: 600;
    }

    .btn-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0 py-4">
    
    {{-- Header Halaman --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-qr-code-scan me-2 text-success"></i> Kelola QR Code Siswa
            </h3>
            <p class="text-muted mb-0">Cetak dan tinjau kartu identitas QR siswa untuk sistem absensi.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-soft-success rounded-pill px-4 py-2 fs-6 shadow-sm">
                <i class="bi bi-people-fill me-2"></i> Total: {{ $siswa->count() }} Siswa
            </span>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold d-flex align-items-center">
                <i class="bi bi-table me-2 text-success"></i> 
                Daftar Kartu QR Siswa
            </h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 180px;">NIS (Nomor Induk)</th>
                            <th style="min-width: 300px;">NAMA LENGKAP SISWA</th>
                            <th style="width: 200px;">KELAS</th>
                            <th class="text-center" style="width: 250px;">AKSI KARTU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $s)
                        <tr>
                            <td class="ps-4">
                                <span class="nis-code">{{ $s->nis }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success bg-opacity-10 text-success rounded-circle p-2 me-3 text-center" style="width: 40px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="fw-bold text-dark fs-6">{{ ucwords(strtolower($s->nama_siswa)) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-success border border-success-subtle px-3 py-2" style="border-radius: 8px;">
                                    <i class="bi bi-door-open-fill me-1"></i> {{ $s->kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.qrcode.show-card', $s->id_siswa) }}" class="btn btn-success btn-preview rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-eye-fill me-2"></i> Preview Kartu
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-search text-muted mb-3 d-block" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h5 class="text-muted">Data siswa tidak ditemukan</h5>
                                    <p class="small text-muted">Pastikan data siswa sudah diinput di menu Manajemen Siswa.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection