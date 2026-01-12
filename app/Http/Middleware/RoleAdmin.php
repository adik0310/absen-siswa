<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAdmin
{

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('auth.login');
        }

        if ((int) $user->id_role !== 1) {
            return abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
