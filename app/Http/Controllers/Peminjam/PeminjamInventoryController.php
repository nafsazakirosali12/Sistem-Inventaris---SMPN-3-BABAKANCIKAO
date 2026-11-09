<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\SchoolProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamInventoryController extends Controller
{
    /**
     * Display inventory catalog cards grouped strictly by category with available stock and detail/loan actions.
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();

        // 1. All categories for Select2 filter
        $allCategories = Category::orderBy('nama_kategori')->get();

        // 2. Query categories with eager loaded inventories with status 'baik' and rooms
        $categoryQuery = Category::with(['inventories' => function ($q) {
            $q->with('room')
              ->where('status', 'baik')
              ->orderBy('kode_barang', 'asc');
        }]);

        // Search filter (nama kategori, merek/tipe barang di dalamnya, atau ruangan)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $categoryQuery->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('inventories', function ($iq) use ($search) {
                      $iq->where('status', 'baik')
                         ->where(function ($sq) use ($search) {
                             $sq->where('nama_barang', 'like', "%{$search}%")
                                ->orWhere('kode_barang', 'like', "%{$search}%")
                                ->orWhere('merk_type', 'like', "%{$search}%")
                                ->orWhereHas('room', function ($rq) use ($search) {
                                    $rq->where('nama_ruangan', 'like', "%{$search}%");
                                });
                         });
                  });
            });
        }

        // Category filter (Dropdown Select2)
        if ($request->filled('category_id')) {
            $categoryQuery->where('id', $request->input('category_id'));
        }

        // Pagination: 8 cards per page when more than 8 cards
        $categories = $categoryQuery->orderBy('nama_kategori', 'asc')
            ->paginate(8)
            ->withQueryString();

        // 3. Prepare detailed metadata map for each category (available stock, units, rooms, photo)
        $activeBorrowedInventoryIds = DB::table('loan_details')
            ->join('loans', 'loan_details.loan_id', '=', 'loans.id')
            ->where(function ($q) {
                $q->where('loan_details.status', 'dipinjam')
                  ->orWhere(function ($s2) {
                      $s2->whereNull('loan_details.status')
                         ->where('loans.status', 'dipinjam');
                  });
            })
            ->pluck('loan_details.inventory_id')
            ->filter()
            ->unique()
            ->all();

        $directBorrowedIds = DB::table('loans')
            ->where('status', 'dipinjam')
            ->whereNotNull('inventory_id')
            ->pluck('inventory_id')
            ->all();

        $borrowedIdsSet = array_flip(array_unique(array_merge($activeBorrowedInventoryIds, $directBorrowedIds)));

        // Load all categories with inventories having status 'baik' to feed the multi-category loan modal
        $allCategoriesWithInventories = Category::with(['inventories' => function ($q) {
            $q->with('room')
              ->where('status', 'baik')
              ->orderBy('kode_barang', 'asc');
        }])->orderBy('nama_kategori')->get();

        $categoriesDataMap = [];
        $stockPerCategory = [];

        foreach ($allCategoriesWithInventories as $cat) {
            $allUnits = [];
            $availableUnits = [];
            $rooms = [];
            $pjRooms = [];

            // Gambar kategori diambil dari gambar barang pertama yang terdaftar di database (id terkecil)
            $firstRegisteredInv = $cat->inventories->sortBy('id')->first();
            $firstWithPhoto = $cat->inventories->first(function ($inv) {
                return !empty($inv->foto) && file_exists(public_path($inv->foto));
            });
            $fotoUrl = ($firstRegisteredInv && $firstRegisteredInv->foto_url)
                ? $firstRegisteredInv->foto_url
                : ($firstWithPhoto ? $firstWithPhoto->foto_url : null);

            foreach ($cat->inventories as $inv) {
                // Hanya barang berstatus baik yang diproses dan ditampilkan
                if ($inv->status !== 'baik') {
                    continue;
                }

                if ($inv->room) {
                    if (!in_array($inv->room->nama_ruangan, $rooms)) {
                        $rooms[] = $inv->room->nama_ruangan;
                    }
                    if (!empty($inv->room->penanggung_jawab) && !in_array($inv->room->penanggung_jawab, $pjRooms)) {
                        $pjRooms[] = $inv->room->penanggung_jawab;
                    }
                }

                $isBorrowed = isset($borrowedIdsSet[$inv->id]) || $inv->status === 'dipinjam';
                $isAvailable = !$isBorrowed;

                $unitData = [
                    'id' => $inv->id,
                    'kode_barang' => $inv->kode_barang,
                    'nama_barang' => $inv->nama_barang,
                    'merk_type' => $inv->merk_type ?: '-',
                    'foto_url' => $inv->foto_url, // Pop-up detail bisa menampilkan gambar setiap unit fisik
                    'ruangan' => $inv->room->nama_ruangan ?? 'Ruang Belum Diatur',
                    'penanggung_jawab_ruangan' => $inv->room->penanggung_jawab ?? '-',
                    'status' => $inv->status,
                    'is_borrowed' => $isBorrowed,
                    'is_available' => $isAvailable,
                    'status_label' => $isBorrowed ? 'Dipinjam' : 'Tersedia',
                ];

                $allUnits[] = $unitData;

                if ($isAvailable) {
                    $availableUnits[] = $unitData;
                }
            }

            $stokTersedia = count($availableUnits);
            $stockPerCategory[$cat->id] = $stokTersedia;

            $categoriesDataMap[$cat->id] = [
                'id' => $cat->id,
                'nama_kategori' => $cat->nama_kategori,
                'kode_kategori' => $cat->kode_kategori,
                'keterangan' => $cat->keterangan ?: '-',
                'foto_url' => $fotoUrl,
                'ruangan_summary' => !empty($rooms) ? implode(', ', $rooms) : 'Ruang Belum Diatur',
                'pj_ruangan_summary' => !empty($pjRooms) ? implode(', ', $pjRooms) : 'Belum Ditentukan',
                'total_inventaris' => count($allUnits),
                'stok_tersedia' => $stokTersedia,
                'available_units' => $availableUnits,
                'all_units' => $allUnits,
            ];
        }

        // Attach pre-calculated properties to paginated categories for Blade rendering
        foreach ($categories as $cat) {
            $data = $categoriesDataMap[$cat->id] ?? null;
            $cat->stok_tersedia = $data ? $data['stok_tersedia'] : 0;
            $cat->total_inventaris = $data ? $data['total_inventaris'] : 0;
            $cat->ruangan_summary = $data ? $data['ruangan_summary'] : 'Ruang Belum Diatur';
            $cat->pj_ruangan_summary = $data ? $data['pj_ruangan_summary'] : 'Belum Ditentukan';
            $cat->foto_url = $data ? $data['foto_url'] : null;
        }

        return view('peminjam.inventaris.index', compact(
            'categories',
            'allCategories',
            'schoolProfile',
            'user',
            'stockPerCategory',
            'categoriesDataMap'
        ));
    }

    /**
     * Store a new multi-category loan application submitted by a Peminjam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|min:3|max:150',
            'tanggal_pinjam' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    // Buffer toleransi 15 menit untuk pengisian form & latensi jaringan
                    $pinjamTime = Carbon::parse($value);
                    if ($pinjamTime->lt(Carbon::now()->subMinutes(15))) {
                        $fail('Tanggal dan jam peminjaman tidak boleh di waktu yang sudah lewat.');
                    }
                },
            ],
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'alasan_tujuan' => 'required|string|min:3|max:500',
            'items' => 'nullable|array',
            'items.*.category_id' => 'required_with:items|exists:categories,id',
            'items.*.jumlah' => 'required_with:items|integer|min:1|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'jumlah' => 'nullable|integer|min:1|max:100',
        ], [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'nama_peminjam.min' => 'Nama peminjam minimal 3 karakter.',
            'tanggal_pinjam.required' => 'Tanggal & jam peminjaman wajib diisi.',
            'tanggal_kembali.required' => 'Batas tanggal & jam pengembalian wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Batas tanggal dan jam pengembalian tidak boleh sebelum waktu peminjaman.',
            'alasan_tujuan.required' => 'Keperluan atau alasan peminjaman wajib diisi.',
            'alasan_tujuan.min' => 'Keperluan atau alasan peminjaman minimal 3 karakter.',
        ]);

        // Standardize requested items into [category_id => qty]
        $requestedItems = [];
        if (!empty($validated['items']) && is_array($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $catId = (int) ($item['category_id'] ?? 0);
                $qty = (int) ($item['jumlah'] ?? 0);
                if ($catId > 0 && $qty > 0) {
                    $requestedItems[$catId] = ($requestedItems[$catId] ?? 0) + $qty;
                }
            }
        } elseif (!empty($request->input('category_id')) && !empty($request->input('jumlah'))) {
            $catId = (int) $request->input('category_id');
            $qty = (int) $request->input('jumlah');
            if ($catId > 0 && $qty > 0) {
                $requestedItems[$catId] = $qty;
            }
        }

        if (empty($requestedItems)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Silakan tentukan minimal 1 kategori barang dan jumlah unit yang ingin dipinjam.');
        }

        return DB::transaction(function () use ($validated, $requestedItems) {
            $allAllocatedInventories = [];
            $primaryCategoryId = null;

            foreach ($requestedItems as $catId => $qty) {
                if (!$primaryCategoryId) {
                    $primaryCategoryId = $catId;
                }
                $cat = Category::find($catId);
                $catName = $cat ? $cat->nama_kategori : "Kategori #{$catId}";

                // Row-level lock available inventories in this category
                $availableUnits = Inventory::where('category_id', $catId)
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
                    ->take($qty)
                    ->get();

                if ($availableUnits->count() < $qty) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Stok untuk kategori '{$catName}' tidak mencukupi. Tersedia: {$availableUnits->count()} unit, diminta: {$qty} unit.");
                }

                foreach ($availableUnits as $unit) {
                    $allAllocatedInventories[] = $unit;
                }
            }

            $totalUnitsCount = count($allAllocatedInventories);
            $firstUnit = $allAllocatedInventories[0];

            // Generate unique loan code: e.g. PINJAM-20261106-ABCD
            $kodePinjam = 'PINJAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $loan = Loan::create([
                'kode_peminjaman' => $kodePinjam,
                'nama_peminjam' => trim($validated['nama_peminjam']),
                'user_id' => Auth::id(),
                'category_id' => $primaryCategoryId,
                'inventory_id' => $firstUnit->id,
                'jumlah' => $totalUnitsCount,
                'tanggal_pinjam' => Carbon::parse($validated['tanggal_pinjam']),
                'tanggal_kembali' => Carbon::parse($validated['tanggal_kembali']),
                'status' => 'dipinjam',
                'alasan_tujuan' => $validated['alasan_tujuan'] ?? null,
            ]);

            // Save each allocated unit in LoanDetail
            foreach ($allAllocatedInventories as $unit) {
                LoanDetail::create([
                    'loan_id' => $loan->id,
                    'inventory_id' => $unit->id,
                    'kode_barang' => $unit->kode_barang,
                    'status' => 'dipinjam',
                ]);
            }

            return redirect()->route('peminjam.riwayat.index', ['highlight' => $loan->id])
                ->with('success', "Peminjaman berhasil! {$totalUnitsCount} unit barang telah berhasil dipinjam.");
        });
    }

    /**
     * Display loan history for Peminjam role.
     */
    public function riwayat(Request $request)
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
            'details.inventory.category',
            'details.inventory.room',
            'user'
        ])->latest('id');

        // 1. Search Filter (kode peminjaman, nama peminjam, nama barang, kode barang, kategori, alasan)
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
                  })
                  ->orWhereHas('details', function ($dq) use ($search) {
                      $dq->where('kode_barang', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.inventory.category', function ($dcq) use ($search) {
                      $dcq->where('nama_kategori', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.inventory', function ($diq) use ($search) {
                      $diq->where('nama_barang', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status Filter (dipinjam, selesai, terlambat)
        if ($request->filled('status')) {
            $status = strtolower(trim($request->input('status')));
            if ($status === 'terlambat') {
                $now = Carbon::now('Asia/Jakarta');
                $query->where(function ($q) use ($now) {
                    $q->where(function ($dq) use ($now) {
                        $dq->where('status', 'dipinjam')
                          ->where('tanggal_kembali', '<', $now);
                    })->orWhere(function ($sq) {
                        $sq->where('status', 'selesai')
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
            $catId = $request->input('category_id');
            $query->where(function ($q) use ($catId) {
                $q->where('category_id', $catId)
                  ->orWhereHas('details.inventory', function ($dq) use ($catId) {
                      $dq->where('category_id', $catId);
                  });
            });
        }

        // 4. Date Range Filter (Berdasarkan tanggal_pinjam)
        if ($request->filled('date_range')) {
            $dateRange = $request->input('date_range');
            $dates = preg_split('/\s*(?:—|-|to)\s*/u', $dateRange);
            $today = Carbon::now('Asia/Jakarta')->endOfDay();

            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

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

        // 5. Highlight newly submitted transaction
        $highlightId = $request->input('highlight');

        // 6. Quick Stats
        $now = Carbon::now('Asia/Jakarta');
        $totalLoansCount = Loan::count();
        $activeLoansCount = Loan::where('status', 'dipinjam')->count();
        $completedLoansCount = Loan::where('status', 'selesai')->count();
        $overdueLoansCount = Loan::where(function ($q) use ($now) {
            $q->where(function ($dq) use ($now) {
                $dq->where('status', 'dipinjam')
                  ->where('tanggal_kembali', '<', $now);
            })->orWhere(function ($sq) {
                $sq->where('status', 'selesai')
                  ->whereNotNull('tanggal_kembali_aktual')
                  ->whereColumn('tanggal_kembali_aktual', '>', 'tanggal_kembali');
            });
        })->count();

        // 7. Pagination 6 items per page (flow.md 2.5 & 5)
        $loans = $query->paginate(6)->withQueryString();

        return view('peminjam.riwayat.index', compact(
            'loans',
            'schoolProfile',
            'user',
            'categories',
            'highlightId',
            'totalLoansCount',
            'activeLoansCount',
            'completedLoansCount',
            'overdueLoansCount'
        ));
    }
}

