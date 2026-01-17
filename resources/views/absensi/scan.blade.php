@extends('layouts.guru')

@section('title', 'Scan Kartu Siswa')

@push('head')
<style>
    /* --- HIDE LAYOUT ELEMENTS --- */
    .sidebar, .main-header, .navbar, .footer, header, footer {
        display: none !important;
    }

    .content-wrapper, .main-content {
        margin-left: 0 !important;
        padding-top: 0 !important;
        width: 100% !important;
        background: #f4f7f6;
        min-height: 100vh;
    }

    /* --- SCANNER BOX STYLE --- */
    #reader {
        max-width: 400px;
        margin: 0 auto;
        border: none !important;
    }
    
    #reader button {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        background-color: #00D452;
        color: white;
        font-weight: 600;
        margin-top: 15px;
        box-shadow: 0 4px 10px rgba(0,212,82,0.3);
    }

    #reader img { display: none; }

    #reader__dashboard_section_csr button {
        background-color: #6c757d !important;
    }

    .full-height-center {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* Badge untuk penanda fitur 2x scan */
    .badge-scan {
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 50px;
    }
</style>
@endpush

@section('content')
<div class="full-height-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-sm-10">
                
                <div class="card mb-3 border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="ms-2">
                            <h6 class="fw-bold mb-0">{{ $jadwal->mataPelajaran->nama_mapel }}</h6>
                            <small class="text-muted">{{ $jadwal->kelas->nama_kelas }}</small>
                            <span class="badge bg-primary badge-scan ms-1">Sistem 2x Scan</span>
                        </div>
                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-sm btn-light border ms-auto rounded-pill">
                            <i class="bi bi-x-lg"></i> Tutup
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-4 text-center">
                        <div id="reader" class="overflow-hidden rounded-3"></div>
                        
                        <div id="result-message" class="mt-4">
                            <div class="alert alert-info border py-3 mb-0" style="border-radius: 12px;">
                                <h5 class="mb-1"><i class="bi bi-qr-code-scan me-2"></i> Tap Kartu Kamu</h5>
                                <p class="small mb-0 opacity-75">Silahkan scan untuk Masuk atau Keluar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-center text-muted mt-4 small">
                    <i class="bi bi-info-circle me-1"></i> Status akan menjadi <strong>"Hadir"</strong> jika sudah scan pulang.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner sementara biar tidak scan terus menerus saat proses
        html5QrcodeScanner.clear();

        document.getElementById('result-message').innerHTML = `
            <div class="alert alert-warning py-3" style="border-radius: 12px;">
                <div class="spinner-border spinner-border-sm me-2"></div> 
                Memverifikasi NIS: <strong>${decodedText}</strong>...
            </div>
        `;

        fetch("{{ route('guru.absensi.scan_proses', $jadwal->id_jadwal_mengajar) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nis: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilan Sukses (Bisa Masuk atau Keluar)
                Swal.fire({
                    icon: 'success',
                    title: data.nama,
                    text: data.message, // Pesan dinamis dari Controller
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => { 
                    // Nyalakan lagi scannernya setelah Swal tertutup
                    location.reload(); 
                });
            } else {
                // Tampilan Gagal / Sudah Absen Lengkap
                Swal.fire({
                    icon: 'warning',
                    title: 'Info',
                    text: data.message,
                    confirmButtonText: 'Oke, Mengerti'
                }).then(() => { 
                    location.reload(); 
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi gangguan koneksi', 'error').then(() => { 
                location.reload(); 
            });
        });
    }

    function onScanFailure(error) { }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 20, // Dipercepat biar lebih responsif
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0 
        },
        false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection