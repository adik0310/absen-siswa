<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleGuru
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ((int) $user->id_role !== 2) {
            if ((int)$user->id_role === 1) {
                return redirect()->route('admin.dashboard');
            }
            return abort(403, 'Akses ditolak. Hanya guru yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
