@php
    use Carbon\Carbon;

    Carbon::setLocale('id');
    $monthInt = (int) ($month ?? now()->month);
    $yearInt  = (int) ($year ?? now()->year);

    $carbonMonth = Carbon::createFromDate($yearInt, $monthInt, 1);
    $daysInMonth = $carbonMonth->daysInMonth;
    $namaBulan   = $carbonMonth->translatedFormat('F');
    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

    $logoPath = public_path('image/logo_ma.png');
    $logoBase64 = null;
    if (file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoBase64 = base64_encode($logoData);
    }

    $namaGuruTampil = $guruName ?? ($jadwal->guru->nama_guru ?? $namaGuruLogin);
    $nipGuruTampil  = $guruNip ?? ($jadwal->guru->nip ?? $guruNip ?? '-');

@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi - {{ $kelasName ?? '-' }}</title>
    <style>
        @page { 
            size: landscape; 
            margin: 0.5cm; /* Margin diperkecil agar kertas lebih luas */
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8.5px; 
            line-height: 1.1; 
            color: #000;
        }
        
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .header-text { text-align: center; }
        .title { font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .subtitle { font-size: 9px; }
        .divider { border-top: 2px solid #000; border-bottom: 0.5pt solid #000; height: 1px; margin: 5px 0 10px 0; }

        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            /* Ubah ke auto agar tabel fleksibel mengikuti isi jika kolom nama panjang */
            table-layout: auto; 
        }

        table.data-table th, table.data-table td { 
            border: 0.4pt solid #000; 
            padding: 2px 1px; 
            text-align: center;
            vertical-align: middle;
        }

        table.data-table thead th { 
            background-color: #f0f0f0; 
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Penyesuaian Kolom Identitas */
        .col-no { width: 18px; }
        .col-nis { 
            width: 60px; 
            white-space: nowrap; 
        }
        .col-nama { 
            text-align: left !important; 
            padding-left: 4px !important; 
            /* Jangan pakai fixed width agar dia melebar otomatis */
            white-space: nowrap; 
            font-size: 8px; /* Sedikit lebih kecil agar muat banyak */
        }

        /* Kolom Tanggal dibuat sekecil mungkin */
        .col-tgl { 
            width: 16px; 
            font-size: 7px; 
        }

        /* Kolom Total */
        .col-total { 
            width: 18px; 
            font-weight: bold; 
            font-size: 7.5px;
        }

        .text-alfa { color: red; font-weight: bold; }
        .text-sakit { color: orange; font-weight: bold; }

        .footer-table { margin-top: 20px; width: 100%; border: none; }
        .footer-table td { border: none; font-size: 10px; }
    </style>
</head>
<body>

<table class="kop">
    <tr>
        <td width="60">
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" width="50">
            @endif
        </td>
        <td class="header-text">
            <div class="title">REKAPITULASI ABSENSI SISWA</div>
            <div class="subtitle">
                <strong>MADRASAH ALIYAH NURUL IMAN</strong><br>
                <i>Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kota Bandung Jawa Barat.</i>
            </div>
        </td>
        <td width="60">&nbsp;</td>
    </tr>
</table>

<div class="divider"></div>

<table style="width: 100%; margin-bottom: 8px; font-size: 9px;">
    <tr>
        <td width="7%"><strong>Kelas</strong></td>
        <td width="43%">: {{ $kelasName ?? '-' }}</td>
        <td width="7%"><strong>Bulan</strong></td>
        <td width="43%">: {{ $namaBulan }} {{ $yearInt }}</td>
    </tr>
    <tr>
        <td><strong>Mapel</strong></td>
        <td>: {{ $mapelName ?? 'Semua Mata Pelajaran' }}</td>
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
            <th colspan="{{ $daysInMonth }}">Kehadiran</th>
            <th colspan="4">Jumlah</th>
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
    @foreach($rekapData as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="col-nis">{{ $r['siswa']->nis ?? '-' }}</td>
            <td class="col-nama">{{ $r['siswa']->nama_siswa ?? '-' }}</td>

            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $tglKey = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $status = $r['harian'][$tglKey] ?? '';
                    $color = ($status == 'A') ? 'red' : (($status == 'S') ? 'orange' : 'black');
                @endphp
                <td style="color: {{ $color }}; font-weight: bold; font-size: 7px;">{{ $status }}</td>
            @endfor

            <td>{{ $r['sakit'] }}</td>
            <td>{{ $r['izin'] }}</td>
            <td style="color: red;">{{ $r['alfa'] }}</td>
            <td>{{ $r['hadir'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="footer-table">
    <tr>
        <td width="75%"></td>
        <td align="center">
            Bandung, {{ $tanggalCetak }} <br>
            Guru Mata Pelajaran,
            <br><br><br><br>
            <strong><u>( {{ $namaGuruTampil }} )</u></strong><br>
            NIP. {{ $nipGuruTampil }}
        </td>
    </tr>
</table>

</body>
</html>