<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View; // Pastikan ini ada
use Illuminate\Support\Facades\Auth; // Pastikan ini ada
use App\Models\LpkProfile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan 'dashboard.blade.php' sebagai komponen 'x-dashboard-layout'
        Blade::component('layouts.dashboard', 'dashboard-layout'); 

        View::composer('layouts.app', function ($view) {
            
            if (Auth::check()) {
                $user = Auth::user();
                
                // Ambil 5 notifikasi terakhir yang BELUM DIBACA
                $notifications = $user->unreadNotifications()->limit(5)->get();
                
                // Ambil JUMLAH total notifikasi yang BELUM DIBACA
                $unreadCount = $user->unreadNotifications()->count();

                // --- 2. TAMBAHKAN LOGIKA INI ---
                // Ambil profil LPK (pastikan selalu ada)
                $lpkProfile = LpkProfile::firstOrCreate(['id' => 1]);
                // ---------------------------------

                $view->with([
                    'notifications' => $notifications,
                    'unreadCount'   => $unreadCount,
                    'lpkProfile'    => $lpkProfile, // <-- 3. KIRIM KE VIEW
                ]);
                
            } else {
                // Beri nilai default jika user belum login
                $view->with([
                    'notifications' => collect(),
                    'unreadCount'   => 0,
                    'lpkProfile'    => null, // <-- 4. BERI NILAI DEFAULT
                ]);
            }
        });
    }
}
