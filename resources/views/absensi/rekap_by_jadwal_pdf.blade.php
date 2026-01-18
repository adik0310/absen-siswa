<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>REKAP ABSEN SISWA - {{ $jadwal->kelas->nama_kelas }}</title>
    <style>
        @page { 
            margin: 0.8cm; /* Margin diperkecil agar area cetak lebih luas */
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9px; /* Ukuran font standar laporan agar muat banyak baris */
            line-height: 1.2; 
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        /* Kop Surat Lebih Ringkas */
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .logo { width: 50px; text-align: center; }
        .kop-text { text-align: center; padding-right: 50px; }
        .instansi-utama { font-size: 12px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .instansi-sub { font-size: 10px; font-weight: bold; margin: 0; }
        .alamat { font-size: 7.5px; font-style: italic; margin-top: 1px; }

        .divider { border-bottom: 1.5px solid #000; margin-top: 3px; }
        .divider-thin { border-bottom: 0.5px solid #000; margin-top: 1px; margin-bottom: 8px; }

        /* Info Kelas & Mapel Lebih Rapat */
        .info-table { width: 100%; margin-bottom: 8px; font-size: 9px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        /* Tabel Data Rapat */
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th { 
            background-color: #f2f2f2; 
            border: 0.5px solid #000; 
            padding: 4px; 
            text-align: center; 
            text-transform: uppercase;
        }
        table.data-table td { 
            border: 0.5px solid #000; 
            padding: 3px 5px; 
            text-align: center; 
        }
        
        .col-nama { text-align: left !important; text-transform: uppercase; font-size: 8.5px; }
        
        /* Footer Tanda Tangan */
        .footer-table { 
            width: 100%; 
            margin-top: 15px; /* Jarak dikurangi agar tidak loncat ke halaman baru */
            border-collapse: collapse;
        }
        .footer-content {
            width: 30%; 
            text-align: center;
            font-size: 9px;
        }

        /* Paksa satu halaman jika memungkinkan */
        .page-break { page-break-after: avoid; }
    </style>
</head>
<body>
    @php
    use Carbon\Carbon;
    $monthInt = (int)$month;
    $yearInt = (int)$year;
    $namaBulan = Carbon::createFromDate($yearInt, $monthInt, 1)->translatedFormat('F');
@endphp

<table class="kop-table">
    <tr>
        <td class="logo">
            <img src="{{ public_path('image/logo_ma.png') }}" width="45">
        </td>
        <td class="kop-text">
            <div class="instansi-utama">Madrasah Aliyah Nurul Iman</div>
            <div class="instansi-sub">Laporan Rekapitulasi Absensi Siswa Harian</div>
            <div class="alamat">
                Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kelurahan Cibaduyut Kidul,<br>
                Kota Bandung, Jawa Barat
            </div>
        </td>
    </tr>
</table>

<div class="divider"></div>
<div class="divider-thin"></div>

<table class="info-table">
    <tr>
        <td width="15%">Mata Pelajaran</td>
        <td width="35%">: <span class="fw-bold">{{ $jadwal->mataPelajaran->nama_mapel }}</span></td>
        <td width="50%" class="text-right">Tanggal Cetak: <span class="fw-bold">{{ $tanggalCetak }}</span></td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>: <span class="fw-bold">{{ $jadwal->kelas->nama_kelas }}</span></td>
        <td class="text-right">Tahun Ajaran: <span class="fw-bold">{{ $yearInt }}/{{ $yearInt + 1 }}</span></td> 
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="50%">Nama Lengkap Siswa</th>
            <th width="18%">NIS</th>
            <th width="7%">H</th>
            <th width="7%">S</th>
            <th width="7%">I</th>
            <th width="7%">A</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="col-nama">{{ strtoupper($r->nama_siswa) }}</td>
            <td>{{ $r->nis ?? '-' }}</td>
            <td style="{{ $r->hadir > 0 ? 'font-weight:bold;' : 'color:#ccc;' }}">{{ $r->hadir }}</td>
            <td style="{{ $r->sakit > 0 ? 'font-weight:bold;' : 'color:#ccc;' }}">{{ $r->sakit }}</td>
            <td style="{{ $r->izin > 0 ? 'font-weight:bold;' : 'color:#ccc;' }}">{{ $r->izin }}</td>
            <td style="{{ $r->alfa > 0 ? 'font-weight:bold; color:red;' : 'color:#ccc;' }}">{{ $r->alfa }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="footer-table">
    <tr>
        <td width="70%"></td> 
        <td class="footer-content">
            Bandung, {{ $tanggalCetak }} <br>
            Guru Mata Pelajaran,
            <br><br><br><br>
            <strong><u>{{ $namaGuruLogin }}</u></strong><br>
            NIP. {{ $nipGuru }}
        </td>
    </tr>
</table>

</body>
</html>