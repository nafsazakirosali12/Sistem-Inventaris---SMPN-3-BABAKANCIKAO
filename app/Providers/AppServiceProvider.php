<?php

namespace App\Providers;

use App\Models\SchoolProfile;
use App\Models\Loan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            if (!array_key_exists('schoolProfile', $view->getData())) {
                $schoolProfile = null;
                if (Schema::hasTable('school_profiles')) {
                    $schoolProfile = SchoolProfile::first();
                }
                if (!$schoolProfile) {
                    $schoolProfile = new SchoolProfile([
                        'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
                        'npsn' => '20203040',
                        'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
                        'alamat' => 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat',
                        'telepon' => '(0264) 1234567',
                        'email' => 'info@smpn3babakancikao.sch.id',
                    ]);
                }
                $view->with('schoolProfile', $schoolProfile);
            }
        });

        View::composer(['components.admin.navbar', 'layouts.admin', 'admin.*'], function ($view) {
            try {
                if (Schema::hasTable('loans')) {
                    $pendingLoansCount = Loan::where('status', 'dipinjam')->count();
                    $pendingLoansNotifications = Loan::where('status', 'dipinjam')
                        ->with(['category', 'inventory'])
                        ->latest()
                        ->take(5)
                        ->get();

                    $view->with('pendingLoansCount', $pendingLoansCount)
                         ->with('pendingLoansNotifications', $pendingLoansNotifications);
                }
            } catch (\Exception $e) {
                // Graceful fallback during migration or fresh installs
            }
        });
    }
}

