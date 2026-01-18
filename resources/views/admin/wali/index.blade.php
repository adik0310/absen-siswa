@extends('layouts.admin')

@section('title', 'Kelola Wali Kelas')

@push('head')
<style>
    /* Mengikuti standar lebar permintaanmu */
    main.container-main {
        max-width: 91% !important;
        padding-left: 80px !important;
        padding-right: 80px !important;
    }
    
    /* Tabel Style Modern & Formal */
    .table-wali thead th {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        background-color: #f8fafc;
        color: #64748b;
        padding: 1.2rem 1rem !important;
        border-bottom: 2px solid #edf2f7;
    }

    .table-wali tbody td {
        padding: 1.1rem 1rem !important;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    /* Styling Select & Button agar menyatu rapi */
    .input-group-formal {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s;
        max-width: 380px; /* Membatasi lebar agar tidak terlalu panjang */
        margin-left: auto; /* Mendorong ke kanan */
    }

    .input-group-formal:focus-within {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }

    .select-formal {
        border: none !important;
        font-size: 0.8rem !important;
        color: #475569 !important;
        background-color: #fff !important;
        padding-left: 12px !important;
    }

    .btn-formal {
        border: none !important;
        font-size: 0.7rem !important;
        font-weight: 500 !important;
        padding: 0 18px !important;
        letter-spacing: 1px;
        border-radius: 0 !important;
    }

    /* Efek hover baris */
    .table-wali tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Badge formal */
    .badge-wali {
        display: inline-flex;
        align-items: center;
        padding: 7px 13px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 500;
    }
    .badge-assigned { background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .badge-unassigned { background-color: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; border-style: dashed; }

    @media (max-width: 992px) {
        main.container-main {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0 py-3">
    {{-- Header Section --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1">Kelola Wali Kelas</h4>
            <p class="text-muted small mb-0">Sinkronisasi otoritas guru terhadap manajemen kelas.</p>
        </div>
        <div>
            <div class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-3">
                <i class="bi bi-calendar-event me-2 text-success"></i>
                <span class="fw-semibold">T.A. {{ date('Y') }}/{{ date('Y')+1 }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="d-flex align-items-center">
                <div class="bg-success p-2 rounded-2 me-3">
                    <i class="bi bi-shield-check text-white fs-6"></i>
                </div>
                <h6 class="mb-0 fw-bold">Daftar Otoritas Kelas</h6>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if(session('success'))
                <div class="mx-4 mt-3 alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span class="small fw-bold">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-wali align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 text-center" width="60">NO</th>
                            <th width="150">KELAS</th>
                            <th>WALI KELAS</th>
                            <th class="pe-4 text-center" width="450">AKSI PERUBAHAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelasList as $index => $kelas)
                        <tr>
                            <td class="ps-4 text-center text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $kelas->nama_kelas }}</div>
                            </td>
                            <td>
                                @if($kelas->waliKelas)
                                    <div class="badge-wali badge-assigned">
                                        <i class="bi bi-person-fill-check me-2"></i>
                                        {{ $kelas->waliKelas->nama_guru }}
                                    </div>
                                @else
                                    <div class="badge-wali badge-unassigned">
                                        <i class="bi bi-dash-circle me-2"></i>
                                        Belum Terpilih
                                    </div>
                                @endif
                            </td>
                            <td class="pe-4">
                                <form action="{{ route('admin.wali.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                                    <div class="input-group input-group-formal shadow-none">
                                        <select name="id_guru" class="form-select select-formal shadow-none" required>
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach($guruList as $guru)
                                                <option value="{{ $guru->id_guru }}" {{ $kelas->id_guru == $guru->id_guru ? 'selected' : '' }}>
                                                    {{ $guru->nama_guru }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success btn-formal">
                                            <i class="bi bi-save2 me-1"></i> SIMPAN
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="mt-4 p-3 bg-light rounded-3 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center text-primary">
            <i class="bi bi-info-circle me-2"></i>
            <span style="font-size: 0.85rem;">Sistem akan otomatis memperbarui hak akses dashboard guru setelah data disimpan.</span>
        </div>
    </div>
</div>
@endsection