<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah ada request ganti bahasa manual (?lang=ja)
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['id', 'ja'])) {
                Session::put('locale', $lang);
            }
        }

        // 2. Cek Session, jika tidak ada, cek Header Browser (Deteksi Awal)
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Deteksi sederhana dari browser language (biasanya mencerminkan lokasi user)
            $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            if ($browserLang == 'ja') {
                App::setLocale('ja');
            } else {
                App::setLocale('id'); // Default Indonesia
            }
        }

        return $next($request);
    }
}