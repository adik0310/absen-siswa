{{-- resources/views/admin/rekap/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Rekap Absensi - Admin')

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
.card-filter {
    background: #ffffff;
    border-radius: 14px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

.card-header-custom {
    background: #f8fbf9;
    border-bottom: 1px solid #edf2ee;
    padding: 1.1rem 1.4rem;
}

.header-box {
    padding: 1.2rem 1.5rem;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
}

/* ============================
   Form Styling
============================ */
.form-label { 
    font-weight: 600; 
    color: #2c3e50; 
    font-size: 0.88rem;
    margin-bottom: 8px; 
}

.form-select {
    border-radius: 9px;
    padding: 0.65rem 1rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s;
}

.form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1) !important;
    border-color: #198754 !important;
}

.hint-text { 
    font-size: 0.78rem; 
    color: #6c757d; 
    margin-top: 5px;
    display: block;
}

/* ============================
   Button & Badge
============================ */
.btn-generate { 
    padding: 0.75rem 2.5rem;
    font-weight: 700; 
    border-radius: 10px;
    background-color: #198754;
    border: none;
    transition: all 0.3s;
}

.btn-generate:hover {
    background-color: #157347;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
}

.section-title-sm {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container container-page py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center header-box">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-bar-graph me-2 text-success"></i> Rekap Absensi
            </h4>
            <p class="text-muted small mb-0">Generate laporan kehadiran siswa per bulan secara otomatis.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 rounded-3 btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    {{-- FORM FILTER --}}
    <div class="card-filter">
        <div class="card-header-custom">
            <span class="section-title-sm text-success">
                <i class="bi bi-funnel-fill me-2"></i> Konfigurasi Laporan
            </span>
        </div>
        
        <form id="formRekap" class="p-4">
            <div class="row g-4">
                {{-- Baris 1: Filter Akademik --}}
                <div class="col-md-4">
                    <label for="selectKelas" class="form-label">Target Kelas <span class="text-danger">*</span></label>
                    <select id="selectKelas" class="form-select shadow-sm" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="selectMapel" class="form-label">Mata Pelajaran</label>
                    <select id="selectMapel" class="form-select shadow-sm">
                        <option value="0">-- Semua Mapel --</option>
                    </select>
                    <span class="hint-text italic"><i class="bi bi-info-circle me-1"></i> Otomatis memfilter mapel di kelas tersebut</span>
                </div>

                <div class="col-md-4">
                    <label for="selectGuru" class="form-label">Guru Pengajar</label>
                    <select id="selectGuru" class="form-select shadow-sm">
                        <option value="0">-- Semua Guru --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Baris 2: Periode Waktu --}}
                <div class="col-md-6">
                    <label for="selectMonth" class="form-label">Periode Bulan</label>
                    <select id="selectMonth" class="form-select shadow-sm">
                        @foreach(range(1,12) as $m)
                            @php $monthName = \Carbon\Carbon::create()->month($m)->translatedFormat('F'); @endphp
                            <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" @if($m == date('m')) selected @endif>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="selectYear" class="form-label">Tahun Akademik</label>
                    <select id="selectYear" class="form-select shadow-sm">
                        @for($y = date('Y'); $y >= date('Y')-3; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Footer Action --}}
                <div class="col-12 mt-5">
                    <div class="d-flex justify-content-between align-items-center border-top pt-4">
                        <div class="text-muted small">
                            <i class="bi bi-exclamation-circle me-1"></i> Tanda <span class="text-danger">*</span> wajib diisi.
                        </div>
                        <button type="button" id="btnViewRekap" class="btn btn-success btn-generate shadow-sm">
                            <i class="bi bi-file-earmark-arrow-down me-2"></i> Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectKelas = document.getElementById('selectKelas');
    const selectMapel = document.getElementById('selectMapel');
    const selectGuru  = document.getElementById('selectGuru');

    // 1. FILTER MAPEL BERDASARKAN KELAS
    selectKelas.addEventListener('change', function(){
        const idKelas = this.value;
        selectMapel.innerHTML = '<option value="0">-- Semua Mapel --</option>';
        selectGuru.innerHTML = '<option value="0">-- Semua Guru --</option>';

        if (!idKelas) return;

        fetch("{{ url('admin/rekap/get-mapel') }}/" + idKelas)
            .then(res => res.json())
            .then(data => {
                data.forEach(mp => {
                    const opt = document.createElement('option');
                    opt.value = mp.id_mapel;
                    opt.textContent = mp.nama_mapel;
                    selectMapel.appendChild(opt);
                });
            });
    });

    // 2. FILTER GURU BERDASARKAN MAPEL & KELAS (PERBAIKAN UTAMA)
    selectMapel.addEventListener('change', function(){
        const idKelas = selectKelas.value;
        const idMapel = this.value;

        // Reset dropdown guru
        selectGuru.innerHTML = '<option value="0">-- Semua Guru --</option>';

        // Jika mapel dipilih (bukan 0) dan kelas sudah dipilih
        if (idMapel !== "0" && idKelas) {
            selectGuru.innerHTML = '<option value="0">Memuat Guru...</option>';

            fetch(`{{ url('admin/rekap/get-guru') }}/${idKelas}/${idMapel}`)
                .then(res => res.json())
                .then(data => {
                    selectGuru.innerHTML = '<option value="0">-- Semua Guru --</option>';
                    if (data.length > 0) {
                        data.forEach(g => {
                            const opt = document.createElement('option');
                            opt.value = g.id_guru;
                            opt.textContent = g.nama_guru;
                            selectGuru.appendChild(opt);
                        });
                    } else {
                        selectGuru.innerHTML = '<option value="0">Tidak ada guru di jadwal</option>';
                    }
                })
                .catch(err => {
                    console.error("Gagal ambil guru:", err);
                    selectGuru.innerHTML = '<option value="0">-- Semua Guru --</option>';
                });
        }
    });

    // 3. TOMBOL TAMPILKAN LAPORAN
    document.getElementById('btnViewRekap').addEventListener('click', function(){
        const kelas = selectKelas.value;
        const year  = document.getElementById('selectYear').value;
        const month = document.getElementById('selectMonth').value;
        const mapel = selectMapel.value;
        const guru  = selectGuru.value;

        if (!kelas) {
            alert('Pilih kelas terlebih dahulu!');
            return;
        }

        let url = `{{ url('admin/rekap') }}/${kelas}/${year}/${month}?mapel=${mapel}&guru=${guru}`;
        window.location.href = url;
    });
});
</script>
@endpush