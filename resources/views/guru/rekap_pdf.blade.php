@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $monthInt = (int) $month;
    $yearInt  = (int) $year;
    $maxPertemuan = 12; // Sesuaikan jika ingin lebih/kurang
    
    $carbonMonth = Carbon::createFromDate($yearInt, $monthInt, 1);
    $namaBulan = $carbonMonth->translatedFormat('F');
    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Siswa</title>
    <style>
        @page { margin: 0.8cm; } 
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8px; 
            color: #000; 
            line-height: 1.1; 
        }
        
        /* Kop Surat */
        .kop-table { width: 100%; border-bottom: 1.5pt solid black; margin-bottom: 12px; padding-bottom: 5px; }
        .logo-img { width: 45px; }
        .header-text { text-align: center; }
        .school-name { font-size: 15px; font-weight: bold; margin: 0; }
        .school-address { font-size: 8px; font-style: italic; }

        /* Informasi Atas */
        .info-table { width: 100%; margin-bottom: 10px; font-size: 9px; border-collapse: collapse; }
        
        /* TABEL UTAMA */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: auto; /* Membiarkan tabel melebar mengikuti teks mapel */
        }
        
        table.main-table th, table.main-table td { 
            border: 0.5pt solid black; 
            text-align: center; 
            vertical-align: middle;
            white-space: nowrap; /* Teks mapel tidak boleh turun ke bawah */
            padding: 2px 0;
        }
        
        .bg-gray { background-color: #f2f2f2; font-weight: bold; }
        
        /* SETTING LEBAR KOLOM */
        .col-no { width: 20px; }
        
        /* Kolom Mapel: Dibuat selebar mungkin agar tidak terpotong */
        .col-mapel-text { 
            text-align: left !important; 
            padding: 0 10px !important; 
            font-size: 9px;
            width: 10%; /* Memberikan prioritas lebar pada mapel */
        }

        /* Kolom angka & total dipersempit maksimal */
        .col-pertemuan { width: 40px !important; }
        .col-total { width: 20px !important; font-weight: bold; }

        /* Styling Baris */
        .row-tanggal td {
            font-size: 5.5px; 
            color: #444;
            background-color: #fafafa;
        }

        .row-status td {
            font-weight: bold;
            font-size: 9px;
            border-top: none !important; /* Menghilangkan garis tengah antara tgl & status */
        }

        /* Warna Status */
        .text-alfa { color: red; }
        .text-sakit { color: orange; }
        .text-izin { color: #17a2b8; }
        .text-hadir { color: #28a745; }

        .footer { 
            margin-top: 20px; 
            float: right; 
            width: 180px; 
            text-align: center; 
            font-size: 9px; 
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td width="10%"><img src="{{ public_path('image/logo_ma.png') }}" class="logo-img"></td>
            <td class="header-text">
                <div style="letter-spacing: 1px; font-size: 9px;">REKAPITULASI ABSENSI SISWA</div>
                <div class="school-name">MADRASAH ALIYAH NURUL IMAN</div>
                <div class="school-address">Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kota Bandung</div>
            </td>
            <td width="10%"></td> 
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="12%">Nama Siswa</td>
            <td>: <strong>{{ $siswa->nama_siswa }}</strong></td>
            <td style="text-align: right;">Kelas : <strong>{{ $kelas->nama_kelas }}</strong></td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>: {{ $siswa->nis ?? '-' }}</td>
            <td style="text-align: right;">Bulan : {{ $namaBulan }} {{ $yearInt }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="bg-gray">
                <th rowspan="2" class="col-no">No</th>
                <th rowspan="2" class="col-mapel-text">Mata Pelajaran</th>
                <th colspan="{{ $maxPertemuan }}">Pertemuan</th>
                <th colspan="4">Total</th>
            </tr>
            <tr class="bg-gray">
                @for($i = 1; $i <= $maxPertemuan; $i++)
                    <th class="col-pertemuan">{{ $i }}</th>
                @endfor
                <th class="col-total">S</th>
                <th class="col-total">I</th>
                <th class="col-total">A</th>
                <th class="col-total">H</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mapels as $idx => $m)
                @php
                    $absenMapel = $absensi->filter(function($item) use ($m) {
                        return optional($item->jadwal)->id_mapel == $m->id_mata_pelajaran;
                    })->sortBy('tanggal')->values();
                @endphp

                <tr class="row-tanggal">
                    <td rowspan="2" style="color:black; font-size: 9px;">{{ $idx + 1 }}</td>
                    <td rowspan="2" class="col-mapel-text" style="color:black; font-size: 9px;">
                        <strong>{{ $m->nama_mapel }}</strong>
                    </td>
                    
                    @for($i = 0; $i < $maxPertemuan; $i++)
                        @php $data = $absenMapel->get($i); @endphp
                        <td>{{ $data ? Carbon::parse($data->tanggal)->format('d/m') : '' }}</td>
                    @endfor

                    <td rowspan="2" class="col-total">{{ $absenMapel->where('keterangan','sakit')->count() }}</td>
                    <td rowspan="2" class="col-total">{{ $absenMapel->where('keterangan','izin')->count() }}</td>
                    <td rowspan="2" class="col-total text-alfa">{{ $absenMapel->where('keterangan','alpa')->count() }}</td>
                    <td rowspan="2" class="col-total" style="color: green;">{{ $absenMapel->where('keterangan','hadir')->count() }}</td>
                </tr>

                <tr class="row-status">
                    @for($i = 0; $i < $maxPertemuan; $i++)
                        @php
                            $data = $absenMapel->get($i);
                            $tanda = ''; $class = '';
                            if($data) {
                                if($data->keterangan == 'hadir') { $tanda = 'H'; $class = 'text-hadir'; }
                                elseif($data->keterangan == 'sakit') { $tanda = 'S'; $class = 'text-sakit'; }
                                elseif($data->keterangan == 'izin') { $tanda = 'I'; $class = 'text-izin'; }
                                elseif($data->keterangan == 'alpa') { $tanda = 'A'; $class = 'text-alfa'; }
                            }
                        @endphp
                        <td class="{{ $class }}">{{ $tanda }}</td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Bandung, {{ $tanggalCetak }}<br>
        Wali Kelas,<br><br><br><br><br>
        <strong><u>{{ $guru->nama_guru }}</u></strong><br>
        NIP. {{ $guru->nip ?? '-' }}
    </div>

</body>
</html>