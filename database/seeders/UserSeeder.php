<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // === 1. ADMIN ===
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
                'id_role' => 1,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        // === 2. GURU ===
        $guruList = DB::table('guru')->get();

        foreach ($guruList as $guru) {
            if (!$guru->nip) continue;

            $email = strtolower($guru->nip).'@gmail.com';

            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'nama' => $guru->nama_guru,
                    'password' => Hash::make('guru123'),
                    'id_role' => 2,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
