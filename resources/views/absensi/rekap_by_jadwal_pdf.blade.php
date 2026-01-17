<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>REKAP ABSEN SISWA</title>
    <style>
        @page { margin: 0.8cm; }
        
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9px; 
            line-height: 1.2; 
            color: #333;
        }
        
        .kop-table { width: 100%; border-collapse: collapse; }
        .logo { width: 60px; text-align: center; }
        .kop-text { text-align: center; }
        .instansi { font-size: 14px; font-weight: bold; margin: 0; }
        .alamat { font-size: 8px; margin-top: 2px; }

        .divider { border-bottom: 1.5px solid #000; margin: 8px 0; }

        /* Gaya Form Atas */
        .info-table { width: 100%; margin-bottom: 5px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        /* Kelas & Mapel rata kiri, Tgl & Tahun rata kanan */
        .text-right { text-align: right; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table th, table.data-table td { 
            border: 1px solid #000; 
            padding: 3px 2px; 
            text-align: center; 
        }
        .col-nama { text-align: left !important; width: 220px; text-transform: uppercase; }
        .bg-light { background-color: #f5f5f5; }
        
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
    </style>
</head>
<body>

<table class="kop-table">
    <tr>
        <td class="logo">
            <img src="{{ public_path('image/logo_ma.png') }}" width="50">
        </td>
        <td class="kop-text">
            <div class="instansi">REKAP ABSENSI SISWA</div>
            <div class="instansi">MADRASAH ALIYAH NURUL IMAN</div>
            <div class="alamat">
                <i>Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kelurahan Cibaduyut Kidul,<br>
                Kecamatan Bojong Loa Kidul, Kota Bandung, Jawa Barat</i>
            </div>
        </td>
        <td width="60"></td>
    </tr>
</table>

<div class="divider"></div>

<table class="info-table">
    <tr>
        <td width="12%">Kelas</td>
        <td width="38%">: {{ $jadwal->kelas->nama_kelas }}</td>
        <td width="50%" class="text-right">Tgl Cetak: {{ $tanggalCetak }}</td>
    </tr>
    <tr>
        <td>Mapel</td>
        <td>: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
        <td class="text-right">Thn Ajaran: 2023/2024</td> 
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr class="bg-light">
            <th rowspan="2" width="20">No</th>
            <th rowspan="2" width="60">NIS</th>
            <th rowspan="2">Nama Siswa</th>
            <th colspan="4">Absensi</th>
        </tr>
        <tr class="bg-light">
            <th width="20">H</th>
            <th width="20">S</th>
            <th width="20">I</th>
            <th width="20">A</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->nis ?? '-' }}</td>
            <td class="col-nama">{{ $r->nama_siswa }}</td>
            <td>{{ $r->hadir > 0 ? 'H' : '' }}</td>
            <td>{{ $r->sakit > 0 ? 'S' : '' }}</td>
            <td>{{ $r->izin > 0 ? 'I' : '' }}</td>
            <td>{{ $r->alfa > 0 ? 'A' : '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>