@php
    use Carbon\Carbon;
    $monthInt = (int)$month;
    $yearInt = (int)$year;
    
    // Total kolom = No(1) + Nama(1) + NIS(1) + Hari(n) + S,I,A,H(4)
    $totalColspan = 3 + $daysInMonth + 4;
    $namaBulan = Carbon::createFromDate($yearInt, $monthInt, 1)->translatedFormat('F');
@endphp

<table>
    {{-- KOP SURAT --}}
    <tr>
        <th colspan="{{ $totalColspan }}" style="text-align: center; font-weight: bold; font-size: 14px;">
            REKAPITULASI ABSENSI SISWA PER HARI
        </th>
    </tr>
    <tr>
        <th colspan="{{ $totalColspan }}" style="text-align: center; font-weight: bold; font-size: 12px;">
            MADRASAH ALIYAH NURUL IMAN <br>
            <p><i>Jalan Cibaduyut Raya Blok TVRI III RT.03 RW.03, Kelurahan Cibaduyut Kidul, Kecamatan Bojongloa Kidul, Kota Bandung Jawa Barat.</i></p>
        </th>
    </tr>
    <tr><td colspan="{{ $totalColspan }}"></td></tr> {{-- Baris Kosong --}}

    {{-- INFO JADWAL --}}
    <tr>
        <td style="font-weight: bold;">Kelas:</td>
        <td style="text-align: left;">{{ $jadwal->kelas->nama_kelas }}</td>
        <td colspan="{{ $daysInMonth - 1 }}"></td>
        <td style="font-weight: bold;">Bulan:</td>
        <td colspan="3" style="text-align: left;">{{ $namaBulan }} {{ $yearInt }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Mapel:</td>
        <td style="text-align: left;">{{ $jadwal->mataPelajaran->nama_mapel }}</td>
        <td colspan="{{ $daysInMonth - 1 }}"></td>
        <td style="font-weight: bold;">Guru:</td>
        <td colspan="3" style="text-align: left;">{{ $jadwal->guru->nama_guru }}</td>
    </tr>
    <tr><td colspan="{{ $totalColspan }}"></td></tr>

    {{-- HEADER TABEL --}}
    <thead>
        <tr>
            <th rowspan="2" style="background-color: #d3d3d3; border: 2px solid #000; text-align: center; vertical-align: middle; width: 50px;">No</th>
            <th rowspan="2" style="background-color: #d3d3d3; border: 2px solid #000; text-align: center; vertical-align: middle; width: 250px;">Nama Siswa</th>
            <th rowspan="2" style="background-color: #d3d3d3; border: 2px solid #000; text-align: center; vertical-align: middle; width: 100px;">NIS</th>
            <th colspan="{{ $daysInMonth }}" style="background-color: #d3d3d3; border: 2px solid #000; text-align: center; font-weight: bold;">Tanggal</th>
            <th colspan="4" style="background-color: #d3d3d3; border: 2px solid #000; text-align: center; font-weight: bold;">Total</th>
        </tr>
        <tr>
            @for($d = 1; $d <= $daysInMonth; $d++)
                <th style="background-color: #f0f0f0; border: 2px solid #000; text-align: center; width: 30px;">{{ $d }}</th>
            @endfor
            <th style="background-color: #ffcccc; border: 2px solid #000; text-align: center; width: 35px;">S</th>
            <th style="background-color: #fff3cd; border: 2px solid #000; text-align: center; width: 35px;">I</th>
            <th style="background-color: #f8d7da; border: 2px solid #000; text-align: center; width: 35px;">A</th>
            <th style="background-color: #d4edda; border: 2px solid #000; text-align: center; width: 35px;">H</th>
        </tr>
    </thead>

    {{-- ISI DATA --}}
    <tbody>
        @foreach($rekap as $index => $r)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $r->nama_siswa }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->nis }}</td>
            
            {{-- Loop Status Harian --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $tglStr = Carbon::createFromDate($yearInt, $monthInt, $d)->toDateString();
                    $status = $r->harian[$tglStr] ?? '';
                    $initial = match($status) {
                        'hadir' => 'H',
                        'sakit' => 'S',
                        'izin'  => 'I',
                        'alfa'  => 'A',
                        default => ''
                    };
                @endphp
                <td style="border: 1px solid #000; text-align: center; {{ $status == 'alfa' ? 'color: #FF0000; font-weight: bold;' : '' }}">
                    {{ $initial }}
                </td>
            @endfor

            {{-- Kolom Total --}}
            <td style="border: 1px solid #000; text-align: center;">{{ $r->sakit }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->izin }}</td>
            <td style="border: 1px solid #000; text-align: center; color: #FF0000; font-weight: bold;">{{ $r->alfa }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $r->hadir }}</td>
        </tr>
        @endforeach
    </tbody>

    {{-- TANDA TANGAN --}}
    <tr><td colspan="{{ $totalColspan }}"></td></tr>
    <tr>
        <td colspan="{{ $totalColspan - 4 }}"></td>
        <td colspan="4" style="text-align: center;">Bandung, {{ date('d/m/Y') }}</td>
    </tr>
    <tr>
        <td colspan="{{ $totalColspan - 4 }}"></td>
        <td colspan="4" style="text-align: center;">Guru Mata Pelajaran,</td>
    </tr>
    <tr><td colspan="{{ $totalColspan }}"></td></tr>
    <tr><td colspan="{{ $totalColspan }}"></td></tr>
    <tr>
        <td colspan="{{ $totalColspan - 4 }}"></td>
        <td colspan="4" style="text-align: center;"><strong><u>{{ $namaGuruLogin }}</u></strong></td>
    </tr>
    <tr>
        <td colspan="{{ $totalColspan - 4 }}"></td>
        <td colspan="4" style="text-align: center;">NIP. {{ $nipGuru }}</td>
    </tr>
</table>