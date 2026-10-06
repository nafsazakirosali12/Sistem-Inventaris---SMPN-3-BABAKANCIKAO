<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first();
        $user = Auth::user();

        // ── Stat Cards ────────────────────────────────────────────────────────
        $totalAset      = Inventory::count();
        $totalKategori  = Category::count();
        $totalPeminjaman = Loan::count();

        // ── Ketersediaan Barang (dari Data Peminjaman & Data Inventaris) ─────
        $sedangDipinjam = Loan::where('status', 'dipinjam')->count();
        $tersedia       = Inventory::where('status', 'baik')
            ->whereDoesntHave('loans', function ($q) {
                $q->where('status', 'dipinjam');
            })->count();

        // ── Kondisi Inventaris (4 status) ─────────────────────────────────────
        $baikCount           = Inventory::where('status', 'baik')->count();
        $dipinjamCount       = Inventory::where('status', 'dipinjam')->count();
        $rusakCount          = Inventory::where('status', 'rusak')->count();
        $perluPerbaikanCount = Inventory::where('status', 'perlu_perbaikan')->count();
        $hilangCount         = Inventory::where('status', 'hilang')->count();

        // ── Donut Chart (4 segments: Baik+Dipinjam, Rusak, Perlu Perbaikan, Hilang) ─
        $totalForChart = max($totalAset, 1);

        $baikPct           = round(($baikCount / $totalForChart) * 100);
        $dipinjamChartPct  = round(($dipinjamCount / $totalForChart) * 100);
        $rusakPct          = round(($rusakCount / $totalForChart) * 100);
        $perluPerbaikanPct = round(($perluPerbaikanCount / $totalForChart) * 100);
        $hilangPct         = round(($hilangCount / $totalForChart) * 100);

        // SVG stroke-dasharray (circumference = 238.7 for r=38)
        $circumference      = 238.7;
        $baikDash           = round(($baikPct / 100) * $circumference, 1);
        $dipinjamChartDash  = round(($dipinjamChartPct / 100) * $circumference, 1);
        $rusakDash          = round(($rusakPct / 100) * $circumference, 1);
        $perluPerbaikanDash = round(($perluPerbaikanPct / 100) * $circumference, 1);
        $hilangDash         = round(($hilangPct / 100) * $circumference, 1);

        $offset1 = -$baikDash;
        $offset2 = $offset1 - $dipinjamChartDash;
        $offset3 = $offset2 - $rusakDash;
        $offset4 = $offset3 - $perluPerbaikanDash;

        // ── Tabel Inventaris Terbaru (5 data saja) ────────────────────────────
        $latestInventories = Inventory::with(['category', 'room'])
            ->latest()
            ->take(5)
            ->get();

        // ── Notification bell count ────────────────────────────────────────────
        $pendingLoansCount = Loan::where('status', 'dipinjam')->count();

        return view('admin.dashboard', compact(
            'schoolProfile',
            'user',
            'totalAset',
            'totalKategori',
            'totalPeminjaman',
            'sedangDipinjam',
            'tersedia',
            'baikCount',
            'dipinjamCount',
            'rusakCount',
            'perluPerbaikanCount',
            'hilangCount',
            'baikPct',
            'dipinjamChartPct',
            'rusakPct',
            'perluPerbaikanPct',
            'hilangPct',
            'baikDash',
            'dipinjamChartDash',
            'rusakDash',
            'perluPerbaikanDash',
            'hilangDash',
            'offset1',
            'offset2',
            'offset3',
            'offset4',
            'circumference',
            'latestInventories',
            'pendingLoansCount'
        ));
    }
}
