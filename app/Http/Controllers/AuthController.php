<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // ================= LOGIN =================
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ((int) $user->id_role === 1) {
                return redirect()->route('admin.dashboard');
            } elseif ((int) $user->id_role === 2) {
                return redirect()->route('guru.dashboard');
            }

            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['login' => 'Akun tidak memiliki akses.']);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ================= PROFILE =================
    public function editProfile()
    {
        return view('auth.profile', [
            'user' => Auth::user()
        ]);
    }

    public function updateProfile(Request $request)
{
    $user = Auth::user();

    // VALIDASI DASAR
    $request->validate([
        'nama'  => 'required|string|max:100',
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($user->id_users, 'id_users')
        ],
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // UPDATE NAMA & EMAIL
    $user->nama  = $request->nama;
    $user->email = $request->email;

    // UPDATE FOTO
    if ($request->hasFile('foto')) {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = $request->file('foto')->store('foto_profil', 'public');
    }

    /**
     * ===============================
     * PASSWORD AMAN (ANTI AUTO-FILL)
     * ===============================
     */
    if (
        $request->filled('password') ||
        $request->filled('password_confirmation')
    ) {
        $request->validate([
            'password' => 'required|min:7|same:password_confirmation',
            'password_confirmation' => 'required'
        ]);

        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profil berhasil diperbarui.');
}

}
