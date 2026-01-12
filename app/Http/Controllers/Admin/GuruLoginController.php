<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk keamanan transaksi

class GuruLoginController extends Controller
{
    public function index()
    {
        // Pastikan relasi 'user' sudah benar di Model Guru
        $gurus = Guru::with('user')->orderBy('nama_guru')->get();
        return view('admin.guru_login.index', compact('gurus'));
    }

    public function sync(Request $request)
    {
        $gurus = Guru::whereNull('id_users')->get();
        $now = now();
        $count = 0;

        foreach ($gurus as $g) {
            // Gunakan NIP sebagai username jika ada, jika tidak gunakan 'guru' + id
            $username = $g->nip ?? 'guru' . ($g->id_guru);
            
            // Cek duplikasi username
            if (User::where('username', $username)->exists()) {
                $username = $username . Str::random(2);
            }

            // Gunakan DB Transaction agar jika satu gagal, tidak merusak data lain
            DB::transaction(function () use ($g, $username, $now) {
                $userId = User::insertGetId([
                    'username'   => $username,
                    'nama'       => $g->nama_guru,
                    'email'      => $g->email ?? ($username . '@nuruliman.sch.id'), // Fallback email
                    'password'   => Hash::make('guru123'),
                    'id_role'    => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $g->update(['id_users' => $userId]);
            });
            
            $count++;
        }

        return redirect()->route('admin.guru.login.index')
            ->with('success', "$count akun guru berhasil disinkronkan.");
    }

    public function reset(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        
        if (!$guru->id_users) {
            return redirect()->back()->with('error', 'Guru belum memiliki akses login.');
        }

        $user = User::find($guru->id_users);
        if ($user) {
            $user->update([
                'password' => Hash::make('guru123')
            ]);
            return redirect()->back()->with('success', 'Password ' . $guru->nama_guru . ' berhasil direset ke: guru123');
        }

        return redirect()->back()->with('error', 'Data user tidak sinkron.');
    }
}