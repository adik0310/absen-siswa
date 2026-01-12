@php
    use Carbon\Carbon;
    $monthInt = (int)$month;
    $yearInt = (int)$year;
    $daysInMonth = Carbon::createFromDate($yearInt, $monthInt, 1)->daysInMonth;
    $namaBulan = Carbon::createFromDate($yearInt, $monthInt, 1)->translatedFormat('F');
    
    // Hitung total kolom untuk merge (No, NIS, Nama + Jumlah Hari + 4 Kolom Total)
    $totalColumns = 3 + $daysInMonth + 4;
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
        <th colspan="{{ $totalColumns }}" style="text-align: center; font-style: italic;">Jalan Cibaduyut Raya Blok TVRI III, Kota Bandung, Jawa Barat</th>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- INFO JADWAL (Dibuat sejajar rapi) --}}
    <tr>
        <td colspan="2"><strong>Kelas</strong></td>
        <td colspan="5">: {{ $jadwal->kelas->nama_kelas }}</td>
        <td colspan="2"><strong>Bulan</strong></td>
        <td colspan="5">: {{ $namaBulan }} {{ $yearInt }}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Mapel</strong></td>
        <td colspan="5">: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
        <td colspan="2"><strong>Tahun</strong></td>
        <td colspan="5">: {{ $yearInt }}/{{ $yearInt + 1 }}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Guru</strong></td>
        <td colspan="12">: {{ $jadwal->guru->nama_guru }}</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>

    {{-- HEADER TABEL --}}
    <thead>
        <tr>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; vertical-align: middle;">No</th>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; vertical-align: middle;">NIS</th>
            <th rowspan="2" style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold; vertical-align: middle;">Nama Siswa</th>
            <th colspan="{{ $daysInMonth }}" style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">Tanggal</th>
            <th colspan="4" style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">Total</th>
        </tr>
        <tr>
            @for ($d = 1; $d <= $daysInMonth; $d++)
                <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">{{ $d }}</th>
            @endfor
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">S</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">I</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">A</th>
            <th style="border: 2px solid #000; text-align: center; background-color: #f0f0f0; font-weight: bold;">H</th>
        </tr>
    </thead>

    {{-- ISI DATA --}}
    <tbody>
        @foreach($rekap as $index => $r)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->nis }}</td>
            <td style="border: 1px solid #000;">{{ $r->nama_siswa }}</td>
            
            @for ($d = 1; $d <= $daysInMonth; $d++)
                @php
                    // Pastikan format tanggal match dengan yang ada di controller (Y-m-d)
                    $tglKey = sprintf('%04d-%02d-%02d', $yearInt, $monthInt, $d);
                    $ket = $r->harian[$tglKey] ?? '';
                    $display = match($ket) {
                        'hadir' => 'H',
                        'izin'  => 'I',
                        'sakit' => 'S',
                        'alfa'  => 'A',
                        default => ''
                    };
                @endphp
                <td style="border: 1px solid #000; text-align: center; @if($display == 'A') color: #FF0000; font-weight: bold; @endif">
                    {{ $display }}
                </td>
            @endfor

            <td style="border: 1px solid #000; text-align: center;">{{ $r->sakit }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->izin }}</td>
            <td style="border: 1px solid #000; text-align: center; color: #FF0000; font-weight: bold;">{{ $r->alfa }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->hadir }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- BAGIAN TANDA TANGAN --}}
<table>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr>
        <td colspan="{{ 3 + $daysInMonth }}"></td>
        <td colspan="4" style="text-align: center;">Bandung, {{ Carbon::now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="{{ 3 + $daysInMonth }}"></td>
        <td colspan="4" style="text-align: center;">Guru Mata Pelajaran,</td>
    </tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr><td colspan="{{ $totalColumns }}"></td></tr>
    <tr>
        <td colspan="{{ 3 + $daysInMonth }}"></td>
        <td colspan="4" style="text-align: center; font-weight: bold; text-decoration: underline;">( {{ $namaGuruLogin }} )</td>
    </tr>
    <tr>
        <td colspan="{{ 3 + $daysInMonth }}"></td>
        <td colspan="4" style="text-align: center;">NIP. {{ $nipGuru }}</td>
    </tr>
</table>