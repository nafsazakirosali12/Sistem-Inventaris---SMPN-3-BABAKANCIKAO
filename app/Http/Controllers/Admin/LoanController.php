<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\SchoolProfile;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of loans with search, status filter (dipinjam, selesai), date filter, and pagination (15 items per page).
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();
        $categories = Category::orderBy('nama_kategori')->get();

        $query = Loan::with(['category', 'inventory.room', 'user'])->latest();

        // 1. Search Filter (kode peminjaman, nama peminjam, alasan, kategori, kode unit/nama barang)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('alasan_tujuan', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('nama_kategori', 'like', "%{$search}%");
                  })
                  ->orWhereHas('inventory', function ($iq) use ($search) {
                      $iq->where('kode_barang', 'like', "%{$search}%")
                         ->orWhere('nama_barang', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status Filter: strictly 'dipinjam' and 'selesai'
        if ($request->filled('status')) {
            $status = strtolower(trim($request->input('status')));
            if ($status === 'terlambat') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('status', 'dipinjam')
                            ->where('tanggal_kembali', '<', Carbon::now());
                    })->orWhere(function ($sub) {
                        $sub->where('status', 'selesai')
                            ->whereNotNull('tanggal_kembali_aktual')
                            ->whereColumn('tanggal_kembali_aktual', '>', 'tanggal_kembali');
                    });
                });
            } elseif (in_array($status, ['dipinjam', 'selesai'])) {
                $query->where('status', $status);
            }
        }

        // 3. Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 4. Date Range Filter (Max Date: Today WIB)
        if ($request->filled('date_range')) {
            $dateRange = $request->input('date_range');
            $dates = preg_split('/\s*(?:—|-|to)\s*/u', $dateRange);
            $today = Carbon::now('Asia/Jakarta')->endOfDay();

            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

                    // Backend rule: Cannot query dates beyond today
                    if ($startDate->isAfter($today)) {
                        $query->whereRaw('1 = 0');
                    } else {
                        if ($endDate->isAfter($today)) {
                            $endDate = $today;
                        }
                        $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                    }
                } catch (\Exception $e) {
                    // Ignore date parsing errors gracefully
                }
            } elseif (count($dates) === 1 && !empty(trim($dates[0]))) {
                try {
                    $singleDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    if ($singleDate->isAfter($today)) {
                        $query->whereRaw('1 = 0');
                    } else {
                        $query->whereDate('tanggal_pinjam', $singleDate);
                    }
                } catch (\Exception $e) {
                    // Ignore
                }
            }
        }

        // 5. Pagination (15 items per page per flow.md 5 & DESIGN.md 6.8)
        $loans = $query->paginate(15)->withQueryString();

        // 6. Highlighted Transaction from notification
        $highlightId = $request->input('highlight');

        // 7. Active loans count
        $activeLoansCount = Loan::where('status', 'dipinjam')->count();

        return view('admin.peminjaman.index', compact(
            'loans',
            'schoolProfile',
            'user',
            'categories',
            'highlightId',
            'activeLoansCount'
        ));
    }

    /**
     * Store a new loan transaction directly with status 'dipinjam' and auto-allocate available physical unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|min:3|max:150',
            'category_id' => 'required|exists:categories,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'alasan_tujuan' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Find available inventory unit of the category with row locking
            $availableInventory = Inventory::where('category_id', $validated['category_id'])
                ->where('status', 'baik')
                ->whereDoesntHave('loans', function ($q) {
                    $q->where('status', 'dipinjam');
                })
                ->lockForUpdate()
                ->orderBy('kode_barang', 'asc')
                ->first();

            if (!$availableInventory) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Stok unit barang untuk kategori tersebut sedang tidak tersedia.');
            }

            // Generate unique loan code
            $kodePinjam = 'PINJAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $loan = Loan::create([
                'kode_peminjaman' => $kodePinjam,
                'nama_peminjam' => trim($validated['nama_peminjam']),
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'inventory_id' => $availableInventory->id,
                'tanggal_pinjam' => Carbon::parse($validated['tanggal_pinjam']),
                'tanggal_kembali' => Carbon::parse($validated['tanggal_kembali']),
                'status' => 'dipinjam', // Direct status 'dipinjam' without approval
                'alasan_tujuan' => $validated['alasan_tujuan'] ?? null,
            ]);

            return redirect()->route('admin.peminjaman.index', ['highlight' => $loan->id])
                ->with('success', "Peminjaman '{$loan->kode_peminjaman}' berhasil dibuat dengan status Dipinjam.");
        });
    }

    /**
     * Mark a loan transaction as 'selesai' (Item returned by borrower).
     * Saves tanggal_kembali_aktual and releases inventory unit back to available.
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:selesai',
        ]);

        return DB::transaction(function () use ($loan) {
            $now = Carbon::now();

            $loan->update([
                'status' => 'selesai',
                'tanggal_kembali_aktual' => $now,
            ]);

            return redirect()->back()
                ->with('success', "Peminjaman '{$loan->kode_peminjaman}' telah diselesaikan. Unit barang kembali berstatus Tersedia.");
        });
    }
}
