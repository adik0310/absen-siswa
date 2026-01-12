<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>REKAP ABSEN SISWA</title>
    <style>
        @page { margin: 1cm; }
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
        }
        
        /* Gaya Kop Surat */
        .kop-table { width: 100%; border-collapse: collapse; }
        .logo { width: 80px; text-align: center; }
        .kop-text { text-align: center; }
        .instansi { font-size: 18px; font-weight: bold; margin: 0; }
        .alamat { font-size: 10px; margin-top: 5px; }

        .divider { border-bottom: 2px solid #000; margin: 10px 0; }
        .title-doc { text-align: center; font-weight: bold; font-size: 14px; text-decoration: underline; margin-bottom: 15px; }

        /* Gaya Form Atas */
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; }

        /* Gaya Tabel Absen */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { 
            border: 1px solid #000; 
            padding: 5px; 
            text-align: center; 
        }
        .col-nama { text-align: left !important; width: 200px; }
    </style>
</head>
<body>

<table class="kop-table">
    <tr>
        <td class="logo">
            <img src="{{ public_path('image/logo_ma.png') }}" width="70">
        </td>
        <td class="kop-text">
            <div class="instansi">REKAP ABSENSI SISWA</div>
            <div class="instansi">MADRASAH ALIYAH NURUL IMAN</div>
            <div class="alamat">
                <i>
                Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kelurahan Cibaduyut Kidul,<br>
                Kecamatan Bojong Loa Kidul, Kota Bandung, Jawa Barat
                </i>
            </div>
        </td>
        <td width="80"></td>
    </tr>
</table>

<div class="divider"></div>

<table class="info-table">
    <tr>
        <td width="15%">Kelas</td>
        <td width="35%">: {{ $jadwal->kelas->nama_kelas }}</td>
        <td width="25%">Hari, Tanggal/Bulan/Tahun</td>
        <td width="25%">: {{ $tanggalCetak }}</td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
        <td>Tahun Ajaran</td>
        <td>: 2023/2024</td> </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th rowspan="2" width="30">No</th>
            <th rowspan="2" width="80">NIS</th>
            <th rowspan="2">Nama Siswa</th>
            <th colspan="4">Kehadiran Siswa</th>
            <th rowspan="2" width="50">Total</th>
        </tr>
        <tr>
            <th width="25">H</th>
            <th width="25">S</th>
            <th width="25">I</th>
            <th width="25">A</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->nis ?? '-' }}</td>
            <td class="col-nama">{{ $r->nama_siswa }}</td>
            <td>{{ $r->hadir > 0 ? '✓' : '' }}</td>
            <td>{{ $r->sakit > 0 ? '✓' : '' }}</td>
            <td>{{ $r->izin > 0 ? '✓' : '' }}</td>
            <td>{{ $r->alfa > 0 ? '✓' : '' }}</td>
            <td>{{ $r->hadir + $r->sakit + $r->izin + $r->alfa }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>