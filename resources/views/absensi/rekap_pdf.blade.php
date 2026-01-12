@php
    use Carbon\Carbon;

    Carbon::setLocale('id');

    $monthInt = (int) $month;
    $yearInt  = (int) $year;

    $carbonMonth = Carbon::createFromDate($yearInt, $monthInt, 1);
    $daysInMonth = $carbonMonth->daysInMonth;
    $namaBulan   = $carbonMonth->translatedFormat('F');
    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absen - {{ $kelas->nama_kelas }}</title>
    <style>
        @page { margin: 0.8cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8px; 
            line-height: 1.1; 
            color: #333;
        }
        
        /* KOP SURAT */
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .header-text { text-align: center; }
        .title { font-weight: bold; font-size: 14px; text-transform: uppercase; margin-bottom: 2px; }
        .subtitle { font-size: 9px; line-height: 1.3; }
        .divider { border-top: 2px solid #000; border-bottom: 1px solid #000; height: 1px; margin: 5px 0 10px 0; }

        /* TABEL DATA */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: auto; 
        }
        table.data-table th, table.data-table td { 
            border: 0.5pt solid #000; 
            padding: 3px 1px; 
            text-align: center; 
        }
        table.data-table thead th { 
            background-color: #f0f0f0; 
            font-weight: bold; 
            font-size: 7.5px;
        }
        
        /* Pencegahan Terpotong */
        .col-no { width: 20px; }
        .col-nis { white-space: nowrap; width: 55px; }
        .col-nama { 
            text-align: left !important; 
            padding-left: 5px !important; 
            white-space: nowrap; 
            min-width: 150px; 
        }
        .col-tgl { width: 15px; font-size: 7px; }
        .col-total { width: 20px; font-weight: bold; background-color: #fafafa; }

        /* Warna Status */
        .text-alfa { color: red; font-weight: bold; }
        .text-sakit { color: orange; }

        /* FOOTER / TANDA TANGAN */
        .footer-table { margin-top: 25px; width: 100%; border: none; }
        .footer-table td { border: none; font-size: 10px; }
    </style>
</head>
<body>

<table class="kop">
    <tr>
        <td width="70"><img src="{{ public_path('image/logo_ma.png') }}" width="60"></td>
        <td class="header-text">
            <div class="title">REKAPITULASI ABSENSI SISWA</div>
            <div class="subtitle">
                <strong>MADRASAH ALIYAH NURUL IMAN</strong><br>
                <i>Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kelurahan Cibaduyut Kidul, Kecamatan Bojongloa Kidul, Kota Bandung Jawa Barat.</i>
            </div>
        </td>
        <td width="70">&nbsp;</td>
    </tr>
</table>

<div class="divider"></div>

<table style="width: 100%; margin-bottom: 10px; font-size: 9px;">
    <tr>
        <td width="10%"><strong>Kelas</strong></td>
        <td width="40%">: {{ $kelas->nama_kelas ?? '-' }}</td>
        <td width="10%"><strong>Bulan</strong></td>
        <td width="40%">: {{ $namaBulan }} {{ $yearInt }}</td>
    </tr>
    <tr>
        <td><strong>Mapel</strong></td>
        <td>: {{ $currentMapelName ?? 'Semua Mata Pelajaran' }}</td>
        <td><strong>Tahun</strong></td>
        <td>: {{ $yearInt }}/{{ $yearInt + 1 }}</td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th rowspan="2" class="col-no">No</th>
            <th rowspan="2" class="col-nis">NIS</th>
            <th rowspan="2" class="col-nama">Nama Siswa</th>
            <th colspan="{{ $daysInMonth }}">Tanggal</th>
            <th colspan="4">Total</th>
        </tr>
        <tr>
            @for($d = 1; $d <= $daysInMonth; $d++)
                <th class="col-tgl">{{ $d }}</th>
            @endfor
            <th class="col-total">S</th>
            <th class="col-total">I</th>
            <th class="col-total">A</th>
            <th class="col-total">H</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="col-nis">{{ $r->nis ?? '-' }}</td>
            <td class="col-nama">{{ $r->nama_siswa }}</td>

            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $tgl = Carbon::createFromDate($yearInt, $monthInt, $d)->toDateString();
                    $status = $r->harian[$tgl] ?? '';
                    $initial = '';
                    $class = '';
                    if($status == 'hadir') $initial = 'H';
                    elseif($status == 'sakit') { $initial = 'S'; $class = 'text-sakit'; }
                    elseif($status == 'izin') $initial = 'I';
                    elseif($status == 'alfa') { $initial = 'A'; $class = 'text-alfa'; }
                @endphp
                <td class="{{ $class }}" style="font-size: 6.5px;">{{ $initial }}</td>
            @endfor

            <td class="col-total">{{ $r->sakit }}</td>
            <td class="col-total">{{ $r->izin }}</td>
            <td class="col-total text-alfa">{{ $r->alfa }}</td>
            <td class="col-total">{{ $r->hadir }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="footer-table">
    <tr>
        <td width="70%"></td>
        <td align="center">
            Bandung, {{ $tanggalCetak }} <br>
            Guru Mata Pelajaran,
            <br><br><br><br>
            <strong><u>( {{ $namaGuruLogin }} )</u></strong><br>
            NIP. {{ $nipGuru }}
        </td>
    </tr>
</table>

</body>
</html>