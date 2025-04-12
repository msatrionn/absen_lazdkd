<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JabatanMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowed)
    {
        $user = Auth::user();
        $jabatan = DB::table('staff')->where('id_user', $user->id)->value('id_jabatan');

        if (!in_array($jabatan, $allowed)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
