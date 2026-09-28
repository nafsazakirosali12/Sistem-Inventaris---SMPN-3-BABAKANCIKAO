<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Category;
use App\Models\Room;
use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first();
        $user = Auth::user();

        // 1. Stat Cards Data
        $totalAset = Inventory::count();
        $totalKategori = Category::count();
        $totalRuangan = Room::count();
        $sedangDipinjam = Inventory::where('status', 'dipinjam')->count();
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();
        $totalGuruAktif = Loan::whereIn('status', ['dipinjam', 'menunggu'])
            ->whereNotNull('nama_peminjam')
            ->distinct('nama_peminjam')
            ->count('nama_peminjam');

        $pendingHariIni = Loan::where('status', 'menunggu')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 2. Condition & Status Breakdown (Donut Chart)
        $baikCount = Inventory::where('status', 'baik')->count();
        $dipinjamCount = $sedangDipinjam;
        $rusakCount = Inventory::where('status', 'rusak')->count();
        $menungguCount = $perluTindakanCount;

        $totalUnitsChart = max($totalAset + $menungguCount, 1);

        $baikPct = round(($baikCount / $totalUnitsChart) * 100);
        $dipinjamPct = round(($dipinjamCount / $totalUnitsChart) * 100);
        $rusakPct = round(($rusakCount / $totalUnitsChart) * 100);
        $menungguPct = round(($menungguCount / $totalUnitsChart) * 100);

        // Calculate SVG stroke dash offsets (circumference = 238.7 for r=38)
        $circumference = 238.7;
        $baikDash = round(($baikPct / 100) * $circumference, 1);
        $dipinjamDash = round(($dipinjamPct / 100) * $circumference, 1);
        $rusakDash = round(($rusakPct / 100) * $circumference, 1);
        $menungguDash = round(($menungguPct / 100) * $circumference, 1);

        $offset1 = -$baikDash;
        $offset2 = $offset1 - $dipinjamDash;
        $offset3 = $offset2 - $rusakDash;

        // 3. Pending Loans Queue (Persetujuan Cepat)
        $pendingLoans = Loan::with(['category', 'inventory.room', 'user'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 4. Inventory Table List with Search & Filtering
        $query = Inventory::with(['category', 'room', 'loans' => function ($q) {
            $q->whereIn('status', ['menunggu', 'dipinjam'])->latest();
        }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $inventories = $query->orderBy('updated_at', 'desc')->paginate(15);
        $rooms = Room::all();
        $categories = Category::all();

        return view('admin.dashboard', compact(
            'schoolProfile',
            'user',
            'totalAset',
            'totalKategori',
            'totalRuangan',
            'sedangDipinjam',
            'perluTindakanCount',
            'totalGuruAktif',
            'pendingHariIni',
            'baikCount',
            'dipinjamCount',
            'rusakCount',
            'menungguCount',
            'totalUnitsChart',
            'baikPct',
            'dipinjamPct',
            'rusakPct',
            'menungguPct',
            'baikDash',
            'dipinjamDash',
            'rusakDash',
            'menungguDash',
            'offset1',
            'offset2',
            'offset3',
            'circumference',
            'pendingLoans',
            'inventories',
            'rooms',
            'categories'
        ));
    }
}
