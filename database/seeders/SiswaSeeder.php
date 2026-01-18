<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = now();

        // ===============================
        // KELAS
        // ===============================
        $needed = ['X-A','X-B','X-C','X-D'];

        foreach ($needed as $k) {
            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => $k],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $kelasRecords = DB::table('kelas')
            ->whereIn('nama_kelas', $needed)
            ->get()
            ->keyBy('nama_kelas');

        $rows = [];

        // ===============================
        // X-A
        // ===============================
        $dataXA = [
            ['3091388639','ADZRA FATHIMAH MUKHLISAH MUJADILAH','P'],
            ['3101727413','ALZENA SALMA AZARIA','P'],
            ['0091096868','ANANDA AZ-ZAHRA RIJALDY','P'],
            ['0097593673','ANISA FITRIA','P'],
            ['0102114354','ANNISA SULISTIANI JAHRA','P'],
            ['0109669171','AYU TRI ARTANTI','P'],
            ['0108568966','CARISSA ARDIANTI NATANIELA','P'],
            ['0093066282','DELISHA ZAHRIN FIRDAUS','P'],
            ['0092040178','DESI BAYYINAH SAIFURROHMAN','P'],
            ['3104496331','GAIDHA NURUL AISYAH','P'],
            ['0092446311','GINA SHAFIA','P'],
            ['0108014748','HASNA NURUL KHOIRUNNISA','P'],
            ['0091869595','HAWA RHOUDATUL JANAH','P'],
            ['0093951558','HELWA RAMADANI','P'],
            ['0098058122','IZKA RAHMANIA WARDATUL FAJRI','P'],
            ['0098786075','KEYLA CAHYA FEBRIANTI','P'],
            ['0093069684','LAURA APIANTI','P'],
            ['0093617390','LIS LEVYNA TRIANOVA','P'],
            ['0099650203','MUTIARA WAHYUDIN','P'],
            ['0099845624','NAFHATUL MAULA AZIZAH','P'],
            ['0098790185','NUR FITRI ALIYAH','P'],
            ['0095945416','RATNA AZIZAH SALSABILA','P'],
            ['0105171154','RAYSHA AYUDYA AL MIRA','P'],
            ['0103519099','ROSITA NUR\'AENI','P'],
            ['3108675341','SHAKINA PUTRI KHAIRUNNISA','P'],
            ['0106641806','SITI SAYIDAH','P'],
            ['0093208215','SITI WULAN SARI','P'],
            ['0097222912','SUSI NURBAYAN ALFRIYANTI','P'],
            ['3098887300','WAFI ZAKIYYAH FAAKHIRAH','P'],
        ];

        foreach ($dataXA as $d) {
            $rows[] = [
                'nis' => $d[0],
                'nama_siswa' => $d[1],
                'jenis_kelamin' => $d[2],
                'id_kelas' => $kelasRecords['X-A']->id_kelas,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // ===============================
        // X-B
        // ===============================
        $dataXB = [
            ['0091656617','AZMI FAUZIAH MARDHOTILLAH','P'],
            ['0099547170','CHIKA NAURA RAHMAH','P'],
            ['0092410512','DINDA AIRAH JULIANA','P'],
            ['0099193538','DZAIKRA AUFA NATANIA','P'],
            ['0107927357','FEBY MAULIDINA','P'],
            ['3103278192','GHASSANI SAWQ BALQUES','P'],
            ['3100343552','GINAN AMELIA','P'],
            ['0109148263','INTAN FEBRIYANI','P'],
            ['0108366543','KHADIKAH LUBNAA','P'],
            ['0091420309','MAZIDA NIAMA SYAKIRA','P'],
            ['0098642274','NADHIRA AULIA AFIFATUNNISA','P'],
            ['0106884354','NAILA NURSYAMSIAH','P'],
            ['3109805742','NAIRA KHOIRIL BADRIYAH','P'],
            ['0094050691','NUZHATUL AULIA SEPTIANI','P'],
            ['0108359157','QONITA KHOIRUNNISA','P'],
            ['0096888542','RAISYA RAHMADITA SAKINAH','P'],
            ['0098911029','RIRI LESTARI','P'],
            ['3106646087','RISDIANA THALIA','P'],
            ['0106935748','SALWA NUR KHARISMA','P'],
            ['3095999841','SASKIA NUR AZKIA','P'],
            ['0093454742','SILMI NURUL ZAKIA','P'],
            ['0103458073','SITI ROBIATUL ADAWIYAH','P'],
            ['0096096108','SITI ROHIMATU WAHDAH','P'],
            ['0103369875','SORAYA ZALIYANTI WIGUNA','P'],
            ['3097454768','SUCI SAFITRI','P'],
            ['0098222754','SYENI AIRA NUR','P'],
            ['0108712446','TATI ROHYANI','P'],
            ['0102173072','TYAS AGUSTINA KAMIL','P'],
            ['3093142321','VANIA HIJROTUNNISA','P'],
            ['3095066574','WARSIHA FASILATUZZAHRA','P'],
        ];

        foreach ($dataXB as $d) {
            $rows[] = [
                'nis' => $d[0],
                'nama_siswa' => $d[1],
                'jenis_kelamin' => $d[2],
                'id_kelas' => $kelasRecords['X-B']->id_kelas,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $dataXC = [
            ['0096149541','AGUNG SABRIAN','L'],
            ['0092930367','ALDI PUTRA SEPTIAN','L'],
            ['0102554728','ALFIAN SEPTIA NUR RAMADHAN','L'],
            ['0091189422','AZIZ MUAYAD','L'],
            ['0104380053','DAFFA LUKMANUL HAKIM','L'],
            ['0102505891','FADLAN NUGROHO HERDIANSYAH','L'],
            ['0091114466','HAFIZ GHANI MAULANA','L'],
            ['009135169','LUTHFI EL RAIF HANDRIANA','L'],
            ['3101006502','LUTHFI GIYATSA RIHADATUL A\'ISY','L'],
            ['0105923731','M. HASAN HUSAENI. A','L'],
            ['0092594104','MAHESWARA KAMAYAN','L'],
            ['0105537083','MOCHAMMAD ABIERA RADITYA','L'],
            ['3106871571','MOH KHOIRUL UMAM','L'],
            ['0098810952','MUHAMMAD ILLYAS ZAELANI','L'],
            ['0102114980','MUHAMAD RIZAL','L'],
            ['3104045799','MUHAMAD SAEFUL HASAN BAHRI','L'],
            ['0105874425','MUHAMMAD FATHIR RAMDHANI','L'],
            ['0101789778','MUHAMMAD IKIL ALHAQ','L'],
            ['0104681311','MUHAMMAD JAMALUR RIZIQ','L'],
            ['0082865743','MUHAMMAD SHAKIL','L'],
            ['0106158617','MUHAMMAD SYAFIQ FAJAR','L'],
            ['3099048643','NAUFAL ABDUL AZIZ','L'],
            ['0105368498','NAZRAN AHMAD GHANI','L'],
            ['0095348885','RAFFA FERDIAN','L'],
            ['0105578317','RIANSYAH','L'],
            ['0102400847','SALMAN NURFAJAR','L'],
            ['0111769933','VANNICO ZAIN PRATAMA','L'],
            ['0097965425','ZAMIL SAIDAN KHALID ANISHORI','L'],
        ];

        foreach ($dataXC as $d) {
            $rows[] = [
                'nis' => $d[0],
                'nama_siswa' => $d[1],
                'jenis_kelamin' => $d[2],
                'id_kelas' => $kelasRecords['X-C']->id_kelas,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $dataXD = [
            ['3080479480','ADELIA MAULANI PUTRI','P'],
            ['0107295354','AINUN NISA SYABANI','P'],
            ['0109515756','ANINDA NUR MARSIDHO','P'],
            ['0097393075','AR-RASYID SYAM QUR\'ANI','L'],
            ['0099012796','AULIA AGUSTINA RAMADHANI','P'],
            ['0102381288','AZMI HAKIM AL GIFARI','L'],
            ['0106945547','DALFAH NUR HABIBAH','P'],
            ['0094772218','DWIE OKTAVIA FITRIYANI','P'],
            ['0094641165','HANIFAH MAULIDA','P'],
            ['0096033771','HASBI AL GHIFARI INDRAWAN','L'],
            ['0105145852','KEYSA NADILA PUTERI','P'],
            ['0097576083','MUHAMAD ADNAN RIFALDI','L'],
            ['0093746998','MUHAMAD PADLA SAEPUL HAJAR','L'],
            ['0098310837','MUHAMAD SYAFIQ SHAFWANSYAH','L'],
            ['0095854242','MUHAMMAD MAULANA FATAH','L'],
            ['0104256269','NAJWA FAUZIYAH SURYANA','P'],
            ['0091329728','NAYLA AFIFAH NURUL PADILAH','P'],
            ['3104457425','NELY FATIKHURROHMAH','P'],
            ['3092814486','NISFI SALWA FAUZIAH','P'],
            ['0109552398','RAKA AJI PUTRA','L'],
            ['0105924735','RD. RAJA MAULANA FIRDAUS','L'],
            ['0091460258','RISKA KHUZAIMAH','P'],
            ['0099419174','SAFHA DASTI NOVA RETSA','P'],
            ['0106467049','SAZKYA DWI ARYANTI','P'],
            ['0108477947','WAFI MAULIDA FAKHIRO','P'],
            ['0109827954','WIDINA AULYANI','P'],
            ['0103320991','YASMIN SAPITRI TRIYANI','P'],
            ['0094567300','ZIKRA PUTRI YANI','P'],
            ['0099125951','ZULVA ALDIANSYAH MALIK','L'],
        ];

        foreach ($dataXD as $d) {
            $rows[] = [
                'nis' => $d[0],
                'nama_siswa' => $d[1],
                'jenis_kelamin' => $d[2],
                'id_kelas' => $kelasRecords['X-D']->id_kelas,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('siswa')->insert($rows);
    }
}