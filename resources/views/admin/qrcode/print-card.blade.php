<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu - {{ $siswa->nama_siswa }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* PENGATURAN HALAMAN CETAK */
        @page {
            size: A4;
            margin: 0;
        }
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        /* DESAIN KARTU */
        .id-card-container {
            width: 323px;
            height: 204px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            position: relative;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }

        /* HEADER */
        .id-card-header {
            background: linear-gradient(135deg, #00D452 0%, #008a35 100%);
            padding: 6px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            border-bottom: 2px solid #ffca28;
            height: 48px;
        }
        .card-logo {
            width: 30px;
            height: 30px;
            object-fit: contain;
            background: white;
            padding: 2px;
            border-radius: 4px;
        }
        .school-name {
            margin: 0;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 0.5px;
            color: white !important;
        }
        .school-address {
            margin: 0;
            font-size: 7.5px;
            opacity: 0.9;
            color: white !important;
        }

        /* BODY */
        .id-card-body {
            flex-grow: 1;
            background-color: #fafafa;
            display: flex;
            align-items: center;
        }

        /* QR CODE BESAR */
        .qr-wrapper-large {
            padding: 5px;
        }
        .qr-code-bg {
            background: white;
            display: inline-block;
            padding: 6px;
            border-radius: 8px;
            border: 1.5px solid #00D452; /* Border senada tema */
        }
        .qr-code-bg svg {
            width: 85px !important;
            height: 85px !important;
            display: block;
        }
        .scan-me-text {
            font-size: 7px;
            font-weight: 800;
            color: #008a35;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        /* DATA SISWA (WARNA SENADA) */
        .theme-color {
            color: #006b29 !important; /* Hijau Tua Profesional */
        }
        .info-group {
            margin-bottom: 8px;
            text-align: left;
        }
        .info-group label {
            display: block;
            font-size: 6.5px;
            font-weight: 700;
            color: #666;
            margin-bottom: 0px;
            text-transform: uppercase;
        }
        .info-value {
            font-weight: 800;
            font-size: 11px;
            line-height: 1.1;
        }

        /* BADGE KELAS */
        .badge-kelas {
            background: #008a35;
            color: white;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            display: inline-block;
        }

        /* FOOTER */
        .id-card-footer {
            background: #222;
            padding: 4px 15px;
            font-size: 7.5px;
            font-weight: 600;
            color: #fff;
        }

        /* TOMBOL NAVIGASI */
        .btn-print-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        /* CSS KHUSUS PRINT */
        @media print {
            .btn-print-wrapper { display: none !important; }
            body { background: white !important; padding: 0; }
            .id-card-container {
                box-shadow: none !important;
                border: 1px solid #e0e0e0 !important;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-wrapper d-flex gap-2">
        <a href="{{ route('admin.qrcode.siswa') }}" class="btn btn-light shadow-sm px-3 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-success shadow-lg px-4 rounded-pill">
            <i class="bi bi-printer-fill me-2"></i> Cetak Kartu Sekarang
        </button>
    </div>

    <div class="id-card-container">
        <div class="id-card-header">
            <img src="{{ asset('image/logo_ma.png') }}" alt="Logo" class="card-logo">
            <div class="header-text text-start">
                <h6 class="school-name">MA NURUL IMAN</h6>
                <p class="school-address">Sistem Presensi Kartu Digital</p>
            </div>
        </div>

        <div class="id-card-body">
            <div class="row g-0 w-100 align-items-center">
                <div class="col-5 text-center border-end">
                    <div class="qr-wrapper-large">
                        <div class="qr-code-bg">
                            {!! $qrcode !!}
                        </div>
                        <div class="scan-me-text text-uppercase">Scan Untuk Absen</div>
                    </div>
                </div>

                <div class="col-7 px-3">
                    <div class="info-group">
                        <label>Nama Lengkap</label>
                        <div class="info-value theme-color text-uppercase">{{ $siswa->nama_siswa }}</div>
                    </div>
                    <div class="info-group">
                        <label>Nomor Induk Siswa (NIS)</label>
                        <div class="info-value theme-color">{{ $siswa->nis }}</div>
                    </div>
                    <div class="info-group mb-0">
                        <label>Kelas</label>
                        <div class="info-value mt-1">
                            <span class="badge-kelas">KELAS {{ $siswa->kelas->nama_kelas ?? $siswa->kelas }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="id-card-footer d-flex justify-content-between px-3">
            <span>MA Nurul Iman - Official Card</span>
            <span>TP. 2025/2026</span>
        </div>
    </div>

</body>
</html>