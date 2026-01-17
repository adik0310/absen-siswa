@extends('layouts.admin')

@section('title', 'Kelola QR Code Siswa')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people-fill me-2 text-primary"></i>Daftar Kartu QR Siswa
            </h5>
            {{-- Tambahan info jumlah siswa biar lebih informatif --}}
            <span class="badge bg-soft-primary text-primary px-3">Total: {{ $siswa->count() }} Siswa</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 150px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $s)
                    <tr>
                        {{-- NIS dengan gaya font monospace agar mudah dibaca --}}
                        <td class="ps-3">
                            <code class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $s->nis }}</code>
                        </td>
                        
                        {{-- Nama Siswa dengan Inisial --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ ucwords(strtolower($s->nama_siswa)) }}</div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Kelas dengan Badge yang lebih rapi --}}
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2" style="font-size: 0.85rem; border-radius: 8px;">
                                <i class="bi bi-door-open-fill me-1"></i> Kelas {{ $s->kelas->nama_kelas }}
                            </span>
                        </td>
                        
                        {{-- Tombol Preview --}}
                        <td class="text-center">
                            <a href="{{ route('admin.qrcode.show-card', $s->id_siswa) }}" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                                <i class="bi bi-eye-fill me-1"></i> Preview Kartu
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-emoji-frown d-block mb-2" style="font-size: 2rem;"></i>
                            Data siswa tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Tambahan sedikit CSS agar tampilan lebih 'Clean' */
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .table thead th {
        border-top: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #6c757d;
    }
</style>
@endsection