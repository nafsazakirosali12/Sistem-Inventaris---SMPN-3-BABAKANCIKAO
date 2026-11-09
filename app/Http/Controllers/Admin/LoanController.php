<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanDetail;
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

        $query = Loan::with([
            'category',
            'inventory.room',
            'user',
            'details.inventory.category',
            'details.inventory.room',
        ])->latest();

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
                         ->orWhere('nama_barang', 'like', "%{$search}%")
                         ->orWhereHas('room', fn($rq) => $rq->where('nama_ruangan', 'like', "%{$search}%"));
                  })
                  ->orWhereHas('details', function ($dq) use ($search) {
                      $dq->where('kode_barang', 'like', "%{$search}%")
                         ->orWhereHas('inventory', function ($diq) use ($search) {
                             $diq->where('nama_barang', 'like', "%{$search}%")
                                 ->orWhereHas('category', fn($dcq) => $dcq->where('nama_kategori', 'like', "%{$search}%"))
                                 ->orWhereHas('room', fn($drq) => $drq->where('nama_ruangan', 'like', "%{$search}%"));
                         });
                  });
            });
        }

        // 2. Status Filter: strictly 'dipinjam' and 'selesai'
        if ($request->filled('status')) {
            $status = strtolower(trim($request->input('status')));
            if (in_array($status, ['dipinjam', 'selesai'])) {
                $query->where('status', $status);
            }
        }

        // 3. Category Filter
        if ($request->filled('category_id')) {
            $catId = $request->input('category_id');
            $query->where(function ($q) use ($catId) {
                $q->where('category_id', $catId)
                  ->orWhereHas('details.inventory', function ($iq) use ($catId) {
                      $iq->where('category_id', $catId);
                  });
            });
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

        // 5. Pagination: 10 item per halaman jika data lebih dari 10
        $loans = $query->paginate(10)->withQueryString();

        // 6. Highlighted Transaction from notification
        $highlightId = $request->input('highlight');

        // 7. Active units count (sum of all units currently on active loan)
        $activeUnitsCount = LoanDetail::where('status', 'dipinjam')->count();
        if ($activeUnitsCount === 0) {
            $activeUnitsCount = Loan::where('status', 'dipinjam')->sum('jumlah');
        }

        return view('admin.peminjaman.index', compact(
            'loans',
            'schoolProfile',
            'user',
            'categories',
            'highlightId',
            'activeUnitsCount'
        ));
    }

    /**
     * Store a new loan transaction directly with status 'dipinjam' and auto-allocate available physical unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'alasan_tujuan' => 'required|string|min:3|max:500',
        ], [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'alasan_tujuan.required' => 'Keperluan atau alasan peminjaman wajib diisi.',
            'alasan_tujuan.min' => 'Keperluan atau alasan peminjaman minimal 3 karakter.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Find available inventory unit of the category with row locking
            $availableInventory = Inventory::where('category_id', $validated['category_id'])
                ->where('status', 'baik')
                ->whereDoesntHave('loans', function ($q) {
                    $q->where('status', 'dipinjam');
                })
                ->whereDoesntHave('loanDetails', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('status', 'dipinjam')
                            ->orWhere(function ($s2) {
                                $s2->whereNull('status')
                                   ->whereHas('loan', fn($lq) => $lq->where('status', 'dipinjam'));
                            });
                    });
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
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::parse($validated['tanggal_pinjam']),
                'tanggal_kembali' => Carbon::parse($validated['tanggal_kembali']),
                'status' => 'dipinjam',
                'alasan_tujuan' => $validated['alasan_tujuan'] ?? null,
            ]);

            // Save detail
            LoanDetail::create([
                'loan_id' => $loan->id,
                'inventory_id' => $availableInventory->id,
                'kode_barang' => $availableInventory->kode_barang,
                'status' => 'dipinjam',
            ]);

            return redirect()->route('admin.peminjaman.index', ['highlight' => $loan->id])
                ->with('success', "Peminjaman '{$loan->kode_peminjaman}' berhasil dibuat dengan status Dipinjam.");
        });
    }

    /**
     * Update entire loan transaction status:
     * - 'selesai': all items returned, records tanggal_kembali_aktual
     * - 'dipinjam': reverts finished loan back to active loan if accidentally completed
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:selesai,dipinjam',
            'alasan_tujuan' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $loan) {
            $now = Carbon::now();

            if ($validated['status'] === 'selesai') {
                $loan->update([
                    'status' => 'selesai',
                    'tanggal_kembali_aktual' => $now,
                ]);

                // Also mark all details as selesai
                $loan->details()->update([
                    'status' => 'selesai',
                    'tanggal_kembali_aktual' => $now,
                ]);

                return redirect()->back()
                    ->with('success', "Seluruh barang pada peminjaman '{$loan->kode_peminjaman}' telah diselesaikan.");
            } else {
                // Revert all to 'dipinjam'
                $loan->update([
                    'status' => 'dipinjam',
                    'tanggal_kembali_aktual' => null,
                ]);

                $loan->details()->update([
                    'status' => 'dipinjam',
                    'tanggal_kembali_aktual' => null,
                ]);

                return redirect()->back()
                    ->with('success', "Status seluruh barang pada peminjaman '{$loan->kode_peminjaman}' berhasil dikembalikan menjadi 'Dipinjam'.");
            }
        });
    }

    /**
     * Update individual unit/item status within a loan (Pisah button selesai masing-masing)
     */
    public function updateDetail(Request $request, LoanDetail $loanDetail)
    {
        $validated = $request->validate([
            'status' => 'required|in:selesai,dipinjam',
        ]);

        return DB::transaction(function () use ($validated, $loanDetail) {
            $loan = $loanDetail->loan;
            $now = Carbon::now();
            $itemCode = $loanDetail->kode_barang;

            if ($validated['status'] === 'selesai') {
                $loanDetail->update([
                    'status' => 'selesai',
                    'tanggal_kembali_aktual' => $now,
                ]);

                // Check if all details of this loan are now completed
                $remainingActive = $loan->details()->where('status', 'dipinjam')->count();
                if ($remainingActive === 0) {
                    $loan->update([
                        'status' => 'selesai',
                        'tanggal_kembali_aktual' => $now,
                    ]);
                    $message = "Unit barang '{$itemCode}' berhasil diselesaikan. Seluruh barang dalam peminjaman ini telah selesai dikembalikan.";
                } else {
                    $message = "Unit barang '{$itemCode}' berhasil diselesaikan dan dikembalikan ke stok. Masih ada {$remainingActive} barang yang belum dikembalikan.";
                }

                return redirect()->back()->with('success', $message);
            } else {
                // Revert this item to 'dipinjam'
                $loanDetail->update([
                    'status' => 'dipinjam',
                    'tanggal_kembali_aktual' => null,
                ]);

                // Also ensure parent loan status is 'dipinjam'
                $loan->update([
                    'status' => 'dipinjam',
                    'tanggal_kembali_aktual' => null,
                ]);

                return redirect()->back()->with('success', "Status unit '{$itemCode}' berhasil dikembalikan menjadi 'Dipinjam'.");
            }
        });
    }
}
