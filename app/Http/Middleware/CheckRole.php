<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...$roles (misal: 'admin', 'guru')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user tidak login atau tidak punya role
        if (!Auth::check()) {
            return redirect('login');
        }

        // Loop semua role yang diizinkan untuk rute ini
        foreach ($roles as $role) {
            // Cek apakah user memiliki role tersebut
            if ($request->user()->role === $role) {
                return $next($request); // Izinkan akses
            }
        }

        // Jika tidak punya role yang diizinkan, lempar ke 403 Forbidden
        abort(403, 'UNAUTHORIZED ACTION.');
    }
}