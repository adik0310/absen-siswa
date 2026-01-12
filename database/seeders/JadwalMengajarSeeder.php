<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JadwalMengajarSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'jadwal_mengajar';
        $now = now();

        // 1. Bersihkan tabel sebelum mengisi (disable FK agar tidak error)
        Schema::disableForeignKeyConstraints();
        DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Ambil data pendukung dari database
        $guruMap = DB::table('guru')->pluck('id_guru', 'nama_guru')->toArray();
        $mapelMap = DB::table('mata_pelajaran')->pluck('id_mata_pelajaran', 'nama_mapel')->toArray();
        $kelasMap = DB::table('kelas')
                     ->whereIn('nama_kelas', ['X-A', 'X-B', 'X-C', 'X-D'])
                     ->pluck('id_kelas', 'nama_kelas')
                     ->toArray();

        if (empty($kelasMap) || empty($mapelMap) || empty($guruMap)) {
            echo "Gagal: Pastikan tabel 'kelas', 'mata_pelajaran', dan 'guru' sudah ada isinya.\n";
            return;
        }

        // 3. Normalisasi Nama Guru (Menyamakan tulisan di jadwal vs database)
        $guruNormalizations = [
            'Ahmad Bukhori, SH., S.Pd.' => 'Ahmad Bukhori, S.H., S.Pd.',
            "Mohamad Puad Syafi'i, M.Α." => "Mohamad Puad Syafi'i, M.A.",
            'Yasril Ahmad Syairazi, S.Pd' => 'Yasril Ahmad Syairazi, S.Pd.',
            'Ikhsanudin Yusup, S.Pd.' => 'Ikhsanudin Yusup, S.Pd.',
            'Mustagitsul Aziez Pangestu' => 'Mustagitsul Aziez Pangestu, S.Pd.',
            'Mustagitsul Aziez Pangestu, S.Pd.' => 'Mustagitsul Aziez Pangestu, S.Pd.',
            'Lathifah Shofiani, S.Ag' => 'Lathifah Shofiani, S.Ag.',
            'Wildan Arip Abdillah, S.Pd' => 'Wildan Arip Abdillah, S.Pd.',
            'Nur Asiah Jamilah, S.Pd' => 'Nur Asiah Jamilah, S.Pd.',
        ];

        // 4. Fungsi bantu untuk memproses teks "Mapel (Guru)"
        $getJadwalDetails = function (string $rawSlot) use ($guruNormalizations) : ?array {
            $skip = ['Upacara', 'Upacara Bendera', 'Istirahat', 'Sholat Dzuhur', 'Sholat Jumat', 'ISTIRAHAT', 'SHOLAT DZUHUR', ''];
            $trimmedSlot = trim($rawSlot);
            
            if ($trimmedSlot === '' || in_array($trimmedSlot, $skip, true)) {
                return null;
            }

            // Pisahkan Mapel dan Guru
            if (preg_match('/(.*?)\s*\((.*?)\)\s*$/u', $trimmedSlot, $matches)) {
                $mapelRaw = trim($matches[1]);
                $guruRaw = trim($matches[2]);
            } else {
                $parts = preg_split('/\s*[—\-–]\s*|\t+/', $trimmedSlot);
                $mapelRaw = trim($parts[0]);
                $guruRaw = isset($parts[1]) ? trim($parts[1]) : '';
            }

            // Normalisasi Nama Mata Pelajaran
            $mapelLower = mb_strtolower($mapelRaw, 'UTF-8');
            if (str_contains($mapelLower, 'biologi')) $mapel = 'Biologi';
            elseif (str_contains($mapelLower, 'sunda')) $mapel = 'Bahasa Sunda';
            elseif (str_contains($mapelLower, 'inggris')) $mapel = 'Bahasa Inggris';
            elseif (str_contains($mapelLower, 'indonesia')) $mapel = 'Bahasa Indonesia';
            elseif (str_contains($mapelLower, 'matematika') || $mapelLower === 'mtk') $mapel = 'Matematika';
            elseif (str_contains($mapelLower, 'ushul fikih')) $mapel = 'Ushul Fikih';
            elseif (str_contains($mapelLower, 'fikih')) $mapel = 'Fikih';
            elseif (str_contains($mapelLower, 'penjas')) $mapel = 'Penjas';
            elseif (str_contains($mapelLower, 'ppkn')) $mapel = 'PPKn';
            elseif (str_contains($mapelLower, 'kimia')) $mapel = 'Kimia';
            elseif (str_contains($mapelLower, 'geografi')) $mapel = 'Geografi';
            elseif (str_contains($mapelLower, 'seni')) $mapel = 'Seni Budaya';
            elseif (str_contains($mapelLower, 'btq')) $mapel = 'BTQ';
            elseif (str_contains($mapelLower, 'arab')) $mapel = 'Bahasa Arab';
            elseif (str_contains($mapelLower, 'akidah') || str_contains($mapelLower, 'aqidah')) $mapel = 'Akidah Akhlak';
            elseif (str_contains($mapelLower, 'ekonomi')) $mapel = 'Ekonomi';
            elseif (str_contains($mapelLower, 'sejarah')) $mapel = 'Sejarah';
            elseif (str_contains($mapelLower, 'sosiologi')) $mapel = 'Sosiologi';
            elseif (str_contains($mapelLower, 'informatika')) $mapel = 'Informatika';
            elseif (str_contains($mapelLower, 'ski')) $mapel = 'SKI';
            elseif (str_contains($mapelLower, 'fisika')) $mapel = 'Fisika';
            elseif (str_contains($mapelLower, 'qur\'an') || str_contains($mapelLower, 'alqur')) $mapel = 'Qur\'an Hadits';
            elseif (str_contains($mapelLower, 'p5')) $mapel = 'Fasilitator P5';
            else $mapel = $mapelRaw;

            // Bersihkan nama guru dari kode kelas seperti (10B)
            $guruName = preg_replace('/\s*\(\d+[A-Z]?\)\s*/', '', $guruRaw);
            $guruName = trim($guruName);

            if (isset($guruNormalizations[$guruName])) {
                $guruName = $guruNormalizations[$guruName];
            }

            if ($guruName === '' || $guruName === '-' || $guruName === '—' || stripos($guruName, 'tidak') !== false) {
                $guruName = ''; 
            }

            return ['mapel' => $mapel, 'guru' => $guruName];
        };

        $jadwalData = [
            'Senin' => [
                '07:50-08:25' => ['X-A' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)', 'X-B' => 'Bahasa Sunda (Anisa Shaina Saviera, S.Hum.)', 'X-C' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-D' => 'Biologi (Sera Afriyanti, S.Pd.)'],
                '08:25-09:00' => ['X-A' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)', 'X-B' => 'Bahasa Sunda (Anisa Shaina Saviera, S.Hum.)', 'X-C' => 'Bahasa Sunda (Anisa Shaina Saviera, S.Hum.)', 'X-D' => 'Biologi (Sera Afriyanti, S.Pd.)'],
                '09:00-09:30' => ['X-A' => 'Penjas (Mustagitsul Aziez Pangestu, S.Pd.)', 'X-B' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-C' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-D' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)'],
                '10:00-10:35' => ['X-A' => 'Penjas (Mustagitsul Aziez Pangestu, S.Pd.)', 'X-B' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-C' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-D' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)'],
                '10:35-11:10' => ['X-A' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-B' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-C' => 'Fikih (Ayi Saefulloh, S.Pd.)', 'X-D' => 'Geografi (Aini Nurfatwa, S.E.)'],
                '11:10-11:40' => ['X-A' => 'Bahasa Sunda (Anisa Shaina Saviera, S.Hum.)', 'X-B' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-C' => 'Fikih (Ayi Saefulloh, S.Pd.)', 'X-D' => 'Geografi (Aini Nurfatwa, S.E.)'],
                '12:30-13:05' => ['X-A' => 'Bahasa Sunda (Anisa Shaina Saviera, S.Hum.)', 'X-B' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-C' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-D' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)'],
                '13:05-13:40' => ['X-A' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)', 'X-B' => 'Penjas (Mustagitsul Aziez Pangestu, S.Pd.)', 'X-C' => 'Teknologi Informatika (Jihan Fadhilah, S.T.)', 'X-D' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)'],
            ],
            'Selasa' => [
                '07:00-07:50' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '07:50-08:25' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '08:25-09:00' => ['X-A' => 'SKI (Wildan Arip Abdillah, S.Pd.)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-D' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)'],
                '09:00-09:30' => ['X-A' => 'SKI (Wildan Arip Abdillah, S.Pd.)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-D' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)'],
                '10:00-10:35' => ['X-A' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-B' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'SKI (Wildan Arip Abdillah, S.Pd.)'],
                '10:35-11:10' => ['X-A' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-B' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'Sejarah (Delina Mulyani, S.Sos.)'],
                '11:10-11:40' => ['X-A' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)', 'X-B' => 'SKI (Wildan Arip Abdillah, S.Pd.)', 'X-C' => 'Ekonomi (H. Dadang Hermawan, S.E.)', 'X-D' => 'Sejarah (Delina Mulyani, S.Sos.)'],
                '12:30-13:05' => ['X-A' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-B' => 'SKI (Wildan Arip Abdillah, S.Pd.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'Ekonomi (H. Dadang Hermawan, S.E.)'],
                '13:05-13:40' => ['X-A' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-B' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)', 'X-C' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-D' => 'Ekonomi (H. Dadang Hermawan, S.E.)'],
            ],
            'Rabu' => [
                '07:00-07:50' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '07:50-08:25' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '08:25-09:00' => ['X-A' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-B' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-C' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-D' => 'Matematika (Hj. Didah Hamidah, S.Pd.)'],
                '09:00-09:30' => ['X-A' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-B' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-C' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-D' => 'Matematika (Hj. Didah Hamidah, S.Pd.)'],
                '10:00-10:35' => ['X-A' => 'Fisika (H. Encep Rachman, S.Pd.)', 'X-B' => 'Geografi (Aini Nurfatwa, S.E.)', 'X-C' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-D' => 'Penjas (Mustagitsul Aziez Pangestu, S.Pd.)'],
                '10:35-11:10' => ['X-A' => 'Geografi (Aini Nurfatwa, S.E.)', 'X-B' => 'Geografi (Aini Nurfatwa, S.E.)', 'X-C' => 'PPKn (Ahmad Bukhori, S.H., S.Pd.)', 'X-D' => 'Penjas (Mustagitsul Aziez Pangestu, S.Pd.)'],
                '11:10-11:40' => ['X-A' => 'Geografi (Aini Nurfatwa, S.E.)', 'X-B' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-C' => 'Ushul Fikih (Mohamad Puad Syafi\'i, M.A.)', 'X-D' => 'Akidah Akhlak (Nur Asiah Jamilah, S.Pd.)'],
                '12:30-13:05' => ['X-A' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-B' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-C' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)', 'X-D' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)'],
                '13:05-13:40' => ['X-A' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-B' => 'Fasilitator P5 (Delina Mulyani, S.Sos.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)'],
            ],
            'Kamis' => [
                '07:00-07:50' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-D' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)'],
                '07:50-08:25' => ['X-A' => 'BTQ (Noknia)', 'X-B' => 'BTQ (Putri Alicia)', 'X-C' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '08:25-09:00' => ['X-A' => 'Akidah Akhlak (Nur Asiah Jamilah, S.Pd.)', 'X-B' => 'Ushul Fikih (Mohamad Puad Syafi\'i, M.A.)', 'X-C' => 'Kimia (Deasy Resnasari, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '10:00-10:35' => ['X-A' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-B' => 'Fisika (H. Encep Rachman, S.Pd.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Biologi (Sera Afriyanti, S.Pd.)'],
                '10:35-11:10' => ['X-A' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-B' => 'Fisika (H. Encep Rachman, S.Pd.)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'Matematika (Hj. Didah Hamidah, S.Pd.)'],
                '11:10-11:40' => ['X-A' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-B' => 'Informatika (Jihan Fadhilah, S.T.)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'Matematika (Hj. Didah Hamidah, S.Pd.)'],
                '12:30-13:05' => ['X-A' => 'Sejarah (Delina Mulyani, S.Sos.)', 'X-B' => 'Informatika (Jihan Fadhilah, S.T.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)'],
                '13:05-13:40' => ['X-A' => 'Ushul Fikih (Mohamad Puad Syafi\'i, M.A.)', 'X-B' => 'Biologi (Sera Afriyanti, S.Pd.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Fasilitator P5 (Deasy Resnasari, S.Pd.)'],
            ],
            "Jum'at" => [
                '07:00-07:45' => ['X-A' => 'Sosiologi (Dra. Sumiati)', 'X-B' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'Fikih (KH. Shofwan Aly, M.Pd.I.)'],
                '07:45-08:15' => ['X-A' => 'Sosiologi (Dra. Sumiati)', 'X-B' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '08:15-08:45' => ['X-A' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)', 'X-B' => 'Sosiologi (Dra. Sumiati)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)'],
                '08:45-09:15' => ['X-A' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)', 'X-B' => 'Sosiologi (Dra. Sumiati)', 'X-C' => 'Sosiologi (Dra. Sumiati)', 'X-D' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)'],
                '09:17-10:15' => ['X-A' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-B' => 'Bahasa Inggris (Delani Febrianti, S.Pd.)', 'X-C' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-D' => 'SKI (Wildan Arip Abdillah, S.Pd.)'],
                '10:15-10:45' => ['X-A' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-B' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)', 'X-C' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-D' => 'Informatika (Jihan Fadhilah, S.T.)'],
                '10:45-11:15' => ['X-A' => 'Ekonomi (Wulan Permanasari, S.Pd.)', 'X-B' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)', 'X-C' => 'Seni Budaya (Ikhsanudin Yusup, S.Pd.)', 'X-D' => 'SKI (Wildan Arip Abdillah, S.Pd.)'],
                '12:45-13:45' => ['X-A' => 'Fasilitator P5 (Deasy Resnasari, S.Pd.)', 'X-B' => 'Ekonomi (Wulan Permanasari, S.Pd.)', 'X-C' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)', 'X-D' => 'Sejarah (Delina Mulyani, S.Sos.)'],
                
            ],
            'Sabtu' => [
                '07:00-07:50' => ['X-A' => 'Informatika (Jihan Fadhilah, S.T.)', 'X-B' => 'Akidah Akhlak (Nur Asiah Jamilah, S.Pd.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '07:50-08:25' => ['X-A' => 'Informatika (Jihan Fadhilah, S.T.)', 'X-B' => 'Akidah Akhlak (Nur Asiah Jamilah, S.Pd.)', 'X-C' => 'BTQ (Silviana Intan Saharini, S.Pd.)', 'X-D' => 'BTQ (Muhammad Fahim Nurul Haqi)'],
                '08:25-09:00' => ['X-A' => 'Qur\'an Hadits (Lathifah Shofiani, S.Ag.)', 'X-B' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-C' => 'Bahasa Arab (Yasril Ahmad Syairazi, S.Pd.)', 'X-D' => 'Qur\'an Hadits (Lathifah Shofiani, S.Ag.)'],
                '09:30-10:00' => ['X-A' => 'Qur\'an Hadits (Lathifah Shofiani, S.Ag.)', 'X-B' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-C' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-D' => 'Biologi (Sera Afriyanti, S.Pd.)'],
                '10:00-10:35' => ['X-A' => 'Biologi (Sera Afriyanti, S.Pd.)', 'X-B' => 'Qur\'an Hadits (Lathifah Shofiani, S.Ag.)', 'X-C' => 'Matematika (Hj. Didah Hamidah, S.Pd.)', 'X-D' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)'],
                '10:35-11:10' => ['X-A' => 'Biologi (Sera Afriyanti, S.Pd.)', 'X-B' => 'Qur\'an Hadits (Lathifah Shofiani, S.Ag.)', 'X-C' => 'Biologi (Sera Afriyanti, S.Pd.)', 'X-D' => 'Ushul Fikih (Mohamad Puad Syafi\'i, M.A.)'],
                '11:10-11:40' => ['X-A' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-B' => 'Ekonomi (H. Dadang Hermawan, S.E.)', 'X-C' => 'Biologi (Sera Afriyanti, S.Pd.)', 'X-D' => 'Ushul Fikih (Mohamad Puad Syafi\'i, M.A.)'],
                '12:30-13:05' => ['X-A' => 'Bahasa Indonesia (Sri Rahayu Anggaraeni, S.Pd.)', 'X-B' => 'Fasilitator P5 (Delina Mulyani, S.Sos.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)'],
                '13:05-13:40' => ['X-A' => 'Fasilitator P5 (Deasy Resnasari, S.Pd.)', 'X-B' => 'Fasilitator P5 (Delina Mulyani, S.Sos.)', 'X-C' => 'Fasilitator P5 (H. Dadang Hermawan, S.E.)', 'X-D' => 'Fasilitator P5 (Reni Yuliani, S.Sos.)'],
            ],
        ];

        // 6. Proses Penyimpanan Data
        $tempStore = [];
        foreach ($jadwalData as $hari => $slotsByTime) {
            $perKelasEntries = [];
            foreach ($slotsByTime as $waktu => $classes) {
                if (!str_contains($waktu, '-')) continue;
                list($start, $end) = explode('-', $waktu);
                $jamMulai = date('H:i:s', strtotime(trim($start)));
                $jamSelesai = date('H:i:s', strtotime(trim($end)));

                foreach ($classes as $namaKelas => $rawSlot) {
                    
                    // --- PERBAIKAN: Pastikan array per kelas sudah ada sebelum count() ---
                    if (!isset($perKelasEntries[$namaKelas])) {
                        $perKelasEntries[$namaKelas] = [];
                    }

                    $details = $getJadwalDetails($rawSlot);
                    if (!$details) {
                        $perKelasEntries[$namaKelas][] = null;
                        continue;
                    }

                    $mapelName = $details['mapel'];
                    $guruName = $details['guru'];

                    // Skip jika Mapel tidak ada di DB
                    if (!isset($mapelMap[$mapelName])) {
                        continue;
                    }

                    $id_mapel = $mapelMap[$mapelName];
                    $id_guru = ($guruName !== '' && isset($guruMap[$guruName])) ? $guruMap[$guruName] : null;
                    $id_kelas = $kelasMap[$namaKelas];

                    // Ambil record terakhir untuk kelas ini guna penggabungan jam berurutan
                    $entriesRef = &$perKelasEntries[$namaKelas];
                    $lastIdx = count($entriesRef) - 1;
                    $last = $lastIdx >= 0 ? $entriesRef[$lastIdx] : null;

                    if ($last && $last['id_mapel'] == $id_mapel && $last['id_guru'] == $id_guru && $last['jam_selesai'] === $jamMulai) {
                        // Jika mapel dan gurunya sama, gabungkan saja jamnya
                        $entriesRef[$lastIdx]['jam_selesai'] = $jamSelesai;
                    } else {
                        // Jika beda, buat baris jadwal baru
                        $entriesRef[] = [
                            'id_mapel' => $id_mapel,
                            'id_guru' => $id_guru,
                            'id_kelas' => $id_kelas,
                            'hari' => $hari,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'ruangan' => "Ruang $namaKelas",
                            'created_at' => $now, 
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            // Pindahkan data dari array sementara per kelas ke array final
            foreach ($perKelasEntries as $entries) {
                foreach ($entries as $ent) {
                    if ($ent) {
                        $key = $ent['hari'].$ent['id_kelas'].$ent['id_mapel'].($ent['id_guru']??'0').$ent['jam_mulai'];
                        $tempStore[$key] = $ent;
                    }
                }
            }
        }

        // 7. Insert ke Database secara massal (Chunking)
        $final = array_values($tempStore);
        if (!empty($final)) {
            Schema::disableForeignKeyConstraints();
            foreach (array_chunk($final, 200) as $chunk) {
                DB::table($table)->insert($chunk);
            }
            Schema::enableForeignKeyConstraints();
        }

        echo "Selesai! Berhasil memasukkan " . count($final) . " jadwal mengajar.\n";
    }
}