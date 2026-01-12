@php
    use Carbon\Carbon;
    
    Carbon::setLocale('id');
    
    // Pastikan casting ke integer agar tidak ada error format
    $monthInt = (int)$month;
    $yearInt = (int)$year;
    
    $carbonMonth = Carbon::createFromDate($yearInt, $monthInt, 1);
    $daysInMonth = $carbonMonth->daysInMonth;
    $namaBulan = $carbonMonth->translatedFormat('F');
    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
    
    // Total kolom: No(1) + NIS(1) + Nama(1) + Tanggal(daysInMonth) + S-I-A-H(4)
    $totalColumns = 3 + $daysInMonth + 4;

    // Ambil variabel dari payload Controller (Kunci utama perbaikan)
    $tampilMapel = $mapelName ?? 'Semua Mata Pelajaran';
    $tampilGuru  = $guruName ?? '-';
    $tampilNip   = $guruNip ?? '-';
@endphp

<table>
    {{-- KOP SURAT --}}
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-weight: bold; font-size: 14pt;">REKAPITULASI ABSENSI SISWA</th>
    </tr>
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-weight: bold; font-size: 16pt;">MADRASAH ALIYAH NURUL IMAN</th>
    </tr>
    <tr>
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-style: italic;">Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kota Bandung, Jawa Barat</th>
    </tr>
    <tr><td colspan="{{ $totalColumns }}" style="border-bottom: 2px solid #000;"></td></tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- INFO DATA --}}
    <tr>
        <td colspan="2"><strong>Kelas</strong></td>
        <td colspan="5">: {{ $kelasName ?? '-' }}</td>
        <td colspan="2"><strong>Bulan</strong></td>
        <td colspan="{{ $totalColumns - 9 }}">: {{ $namaBulan }} {{ $yearInt }}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Mapel</strong></td>
        <td colspan="5">: {{ $tampilMapel }}</td>
        <td colspan="2"><strong>Tahun</strong></td>
        <td colspan="{{ $totalColumns - 9 }}">: {{ $yearInt }}/{{ $yearInt + 1 }}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Guru</strong></td>
        <td colspan="5">: {{ $tampilGuru }}</td>
        <td colspan="2"><strong>NIP</strong></td>
        <td colspan="{{ $totalColumns - 9 }}">: {{ $tampilNip }}</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- HEADER TABEL --}}
    <thead>
        <tr>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #d9d9d9; font-weight: bold; vertical-align: middle;">No</th>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #d9d9d9; font-weight: bold; vertical-align: middle;">NIS</th>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #d9d9d9; font-weight: bold; vertical-align: middle;">Nama Siswa</th>
            <th colspan="{{ $daysInMonth }}" style="border: 2px solid #000; text-align: center; background-color: #d9d9d9; font-weight: bold;">Kehadiran</th>
            <th colspan="4" style="border: 2px solid #000; text-align: center; background-color: #d9d9d9; font-weight: bold;">Jumlah</th>
        </tr>
        <tr>
            @for ($d = 1; $d <= $daysInMonth; $d++)
                <th style="border: 2px solid #000; text-align: center; background-color: #f2f2f2; font-weight: bold;">{{ $d }}</th>
            @endfor
            <th style="border: 2px solid #000; text-align: center; background-color: #f2f2f2; font-weight: bold;">S</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f2f2f2; font-weight: bold;">I</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f2f2f2; font-weight: bold;">A</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f2f2f2; font-weight: bold;">H</th>
        </tr>
    </thead>

    {{-- ISI DATA --}}
    <tbody>
        @foreach($rekapData as $index => $r)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r['siswa']->nis ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $r['siswa']->nama_siswa ?? '-' }}</td>
            
            @for ($d = 1; $d <= $daysInMonth; $d++)
                @php
                    // Key YYYY-MM-DD harus sinkron dengan Controller
                    $tglKey = sprintf('%04d-%02d-%02d', $yearInt, $monthInt, $d);
                    $display = $r['harian'][$tglKey] ?? '';
                @endphp
                <td style="border: 1px solid #000; text-align: center; @if($display == 'A') color: #ff0000; @endif">
                    {{ $display }}
                </td>
            @endfor

            <td style="border: 1px solid #000; text-align: center;">{{ $r['sakit'] }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r['izin'] }}</td>
            <td style="border: 1px solid #000; text-align: center; color: #ff0000; font-weight: bold;">{{ $r['alfa'] }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r['hadir'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- FOOTER TANDA TANGAN --}}
<table>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr>
        <td colspan="{{ $totalColumns - 5 }}"></td>
        <td colspan="5" style="text-align: center;">Bandung, {{ $tanggalCetak }}</td>
    </tr>
    <tr>
        <td colspan="{{ $totalColumns - 5 }}"></td>
        <td colspan="5" style="text-align: center;">Guru Mata Pelajaran,</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr>
        <td colspan="{{ $totalColumns - 5 }}"></td>
        <td colspan="5" style="text-align: center; font-weight: bold; text-decoration: underline;">
            ( {{ $tampilGuru }} )
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalColumns - 5 }}"></td>
        <td colspan="5" style="text-align: center;">
            NIP. {{ $tampilNip }}
        </td>
    </tr>
</table>