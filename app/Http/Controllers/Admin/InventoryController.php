<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\Room;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventories with search, filter, and pagination (15 items strictly per flow.md & DESIGN.md).
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();

        // 1. Notification bell count
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        // 2. Query inventories with category, room, and active loans
        $query = Inventory::with(['category', 'room', 'loans']);

        // Search: Kode Barang, Nama Barang, Nomor Register, Merek
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('nomor_register', 'like', "%{$search}%")
                  ->orWhere('merk_type', 'like', "%{$search}%");
            });
        }

        // Filter: Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter: Ruangan
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        // Filter: Status Unit (Baik, Rusak, Hilang)
        $statusInput = $request->input('status_unit', $request->input('status'));
        if (!empty($statusInput)) {
            $status = strtolower($statusInput);
            if ($status === 'baik') {
                $query->whereIn('status', ['baik', 'tersedia']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter: Rentang Tanggal / Tahun Pembelian (Date Range Picker)
        if ($request->filled('date_range')) {
            $dateRange = trim($request->input('date_range'));
            // Support delimiters: " — " (em-dash), " - " (hyphen), " to "
            $parts = preg_split('/\s*(?:—|-|to)\s*/u', $dateRange);

            $startDate = null;
            $endDate = null;

            if (count($parts) >= 2) {
                $startStr = trim($parts[0]);
                $endStr = trim($parts[1]);

                try {
                    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $startStr)->startOfDay();
                } catch (\Exception $e) {
                    try { $startDate = \Carbon\Carbon::parse($startStr)->startOfDay(); } catch (\Exception $ex) {}
                }

                try {
                    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $endStr)->endOfDay();
                } catch (\Exception $e) {
                    try { $endDate = \Carbon\Carbon::parse($endStr)->endOfDay(); } catch (\Exception $ex) {}
                }
            } elseif (count($parts) === 1 && !empty(trim($parts[0]))) {
                $singleStr = trim($parts[0]);
                try {
                    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $singleStr)->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $singleStr)->endOfDay();
                } catch (\Exception $e) {
                    try {
                        $startDate = \Carbon\Carbon::parse($singleStr)->startOfDay();
                        $endDate = \Carbon\Carbon::parse($singleStr)->endOfDay();
                    } catch (\Exception $ex) {}
                }
            }

            if ($startDate && $endDate) {
                $today = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();

                // Backend rule: Cannot query dates beyond today
                if ($startDate->isAfter($today)) {
                    $query->whereRaw('1 = 0');
                } else {
                    if ($endDate->isAfter($today)) {
                        $endDate = $today;
                    }

                    $startYear = (int)$startDate->format('Y');
                    $endYear = (int)$endDate->format('Y');

                    $query->where(function ($q) use ($startDate, $endDate, $startYear, $endYear) {
                        // Match 4-digit year format (e.g. 2024)
                        $q->where(function ($sub) use ($startYear, $endYear) {
                            $sub->whereRaw('LENGTH(TRIM(tahun_pembelian)) = 4')
                                ->whereRaw('CAST(tahun_pembelian AS UNSIGNED) >= ?', [$startYear])
                                ->whereRaw('CAST(tahun_pembelian AS UNSIGNED) <= ?', [$endYear]);
                        })
                        // Match full date format (e.g. 2024-05-12)
                        ->orWhere(function ($sub) use ($startDate, $endDate) {
                            $sub->whereRaw('LENGTH(TRIM(tahun_pembelian)) > 4')
                                ->whereBetween('tahun_pembelian', [
                                    $startDate->format('Y-m-d'),
                                    $endDate->format('Y-m-d')
                                ]);
                        });
                    });
                }
            }
        } elseif ($request->filled('tahun')) {
            $query->where('tahun_pembelian', $request->input('tahun'));
        }

        // Sorting
        $sort = $request->input('sort', 'recent');
        switch ($sort) {
            case 'code_asc':
                $query->orderBy('kode_barang', 'asc');
                break;
            case 'code_desc':
                $query->orderBy('kode_barang', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('nama_barang', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'year_desc':
                $query->orderBy('tahun_pembelian', 'desc');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }

        // Pagination 15 items per page (strictly per flow.md line 249 & DESIGN.md 6.8)
        $inventories = $query->paginate(15)->withQueryString();

        // Data for dropdown filters
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        $rooms = Room::orderBy('nama_ruangan', 'asc')->get();

        return view('admin.inventaris.index', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'inventories',
            'categories',
            'rooms'
        ));
    }

    /**
     * Show the form for creating a new inventory (New page, not modal, per flow.md 3.8).
     */
    public function create()
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        $rooms = Room::orderBy('nama_ruangan', 'asc')->get();

        return view('admin.inventaris.create', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'categories',
            'rooms'
        ));
    }

    /**
     * Store a newly created inventory in storage.
     */
    public function store(Request $request)
    {
        // Pre-clean inputs: strip 'Rp' / dots from harga and map status_unit
        if ($request->has('status_unit') && !$request->has('status')) {
            $request->merge(['status' => $request->input('status_unit')]);
        }

        $validated = $request->validate([
            // Informasi Utama
            'kode_barang' => [
                'required',
                'string',
                'max:100',
                'unique:inventories,kode_barang',
            ],
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:baik,rusak,hilang,Baik,Rusak,Hilang',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Detail Aset & Rekapitulasi (Opsional)
            'nomor_register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:255',
            'ukuran_cc' => 'nullable|string|max:100',
            'bahan' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|string|max:50',
            'nomor_pabrik' => 'nullable|string|max:100',
            'nomor_rangka' => 'nullable|string|max:100',
            'nomor_mesin' => 'nullable|string|max:100',
            'nomor_polisi' => 'nullable|string|max:100',
            'nomor_bpkb' => 'nullable|string|max:100',
            'asal_usul' => 'nullable|string|max:255',
            'harga' => 'nullable',
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan. Silakan gunakan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'room_id.required' => 'Ruangan penempatan wajib dipilih.',
            'room_id.exists' => 'Ruangan yang dipilih tidak valid.',
            'status.required' => 'Status unit wajib dipilih.',
            'status.in' => 'Status unit harus berupa Baik, Rusak, atau Hilang.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran file foto maksimal 2 MB.',
        ]);

        // Clean & normalize string inputs
        $validated['kode_barang'] = trim($validated['kode_barang']);
        $validated['nama_barang'] = trim($validated['nama_barang']);
        $validated['status'] = strtolower($validated['status']);
        
        // Clean numeric harga (guaranteed no 'Rp' or dots stored)
        $cleanHarga = preg_replace('/[^0-9]/', '', (string)$request->input('harga'));
        $validated['harga'] = is_numeric($cleanHarga) && $cleanHarga !== '' ? (float)$cleanHarga : 0;

        // Keep 'keterangan' in sync with 'deskripsi'
        $validated['keterangan'] = $validated['deskripsi'] ?? null;

        // Handle Image Upload
        if ($request->hasFile('foto')) {
            $uploadDir = public_path('uploads/inventories');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            $fotoFile = $request->file('foto');
            $fotoName = 'inv_' . time() . '_' . Str::random(8) . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move($uploadDir, $fotoName);
            $validated['foto'] = 'uploads/inventories/' . $fotoName;
        }

        $inventory = Inventory::create($validated);

        return redirect()->route('admin.inventaris.index')
            ->with('success', "Inventaris '{$inventory->nama_barang}' ({$inventory->kode_barang}) berhasil disimpan ke dalam sistem.");
    }

    /**
     * Display the specified inventory (Read-only page per flow.md 3.8 & DESIGN.md 264).
     */
    public function show(Inventory $inventory)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        $inventory->load(['category', 'room', 'loans' => function ($q) {
            $q->latest();
        }]);

        return view('admin.inventaris.show', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'inventory'
        ));
    }

    /**
     * Show the form for editing the specified inventory (New page, not modal).
     */
    public function edit(Inventory $inventory)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        $rooms = Room::orderBy('nama_ruangan', 'asc')->get();

        return view('admin.inventaris.edit', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'inventory',
            'categories',
            'rooms'
        ));
    }

    /**
     * Update the specified inventory in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        // Pre-clean inputs: strip 'Rp' / dots from harga and map status_unit
        if ($request->has('status_unit') && !$request->has('status')) {
            $request->merge(['status' => $request->input('status_unit')]);
        }

        $validated = $request->validate([
            // Informasi Utama
            'kode_barang' => [
                'required',
                'string',
                'max:100',
                'unique:inventories,kode_barang,' . $inventory->id,
            ],
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:baik,rusak,hilang,Baik,Rusak,Hilang',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Detail Aset & Rekapitulasi (Opsional)
            'nomor_register' => 'nullable|string|max:100',
            'merk_type' => 'nullable|string|max:255',
            'ukuran_cc' => 'nullable|string|max:100',
            'bahan' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|string|max:50',
            'nomor_pabrik' => 'nullable|string|max:100',
            'nomor_rangka' => 'nullable|string|max:100',
            'nomor_mesin' => 'nullable|string|max:100',
            'nomor_polisi' => 'nullable|string|max:100',
            'nomor_bpkb' => 'nullable|string|max:100',
            'asal_usul' => 'nullable|string|max:255',
            'harga' => 'nullable',
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan. Silakan gunakan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'room_id.required' => 'Ruangan penempatan wajib dipilih.',
            'room_id.exists' => 'Ruangan yang dipilih tidak valid.',
            'status.required' => 'Status unit wajib dipilih.',
            'status.in' => 'Status unit harus berupa Baik, Rusak, atau Hilang.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran file foto maksimal 2 MB.',
        ]);

        $validated['kode_barang'] = trim($validated['kode_barang']);
        $validated['nama_barang'] = trim($validated['nama_barang']);
        $validated['status'] = strtolower($validated['status']);
        
        // Clean numeric harga (guaranteed no 'Rp' or dots stored)
        $cleanHarga = preg_replace('/[^0-9]/', '', (string)$request->input('harga'));
        $validated['harga'] = is_numeric($cleanHarga) && $cleanHarga !== '' ? (float)$cleanHarga : 0;

        $validated['keterangan'] = $validated['deskripsi'] ?? null;

        // Handle Image Replacement
        if ($request->hasFile('foto')) {
            $uploadDir = public_path('uploads/inventories');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            // Remove old photo if exists
            if ($inventory->foto && File::exists(public_path($inventory->foto))) {
                File::delete(public_path($inventory->foto));
            }

            $fotoFile = $request->file('foto');
            $fotoName = 'inv_' . time() . '_' . Str::random(8) . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move($uploadDir, $fotoName);
            $validated['foto'] = 'uploads/inventories/' . $fotoName;
        }

        $inventory->update($validated);

        return redirect()->route('admin.inventaris.index')
            ->with('success', "Perubahan data inventaris '{$inventory->nama_barang}' ({$inventory->kode_barang}) berhasil disimpan.");
    }

    /**
     * Remove the specified inventory from storage.
     * Enforces business rule from flow.md 3.8 & prompt:
     * "Jika inventaris sedang dipinjam, jangan izinkan penghapusan. Tampilkan pesan yang jelas bahwa barang yang sedang dipinjam tidak dapat dihapus."
     */
    public function destroy(Inventory $inventory)
    {
        if ($inventory->is_currently_borrowed) {
            return redirect()->route('admin.inventaris.index')
                ->with('error', "Penghapusan ditolak: Unit inventaris '{$inventory->nama_barang}' ({$inventory->kode_barang}) sedang dalam status dipinjam. Barang yang sedang dipinjam tidak dapat dihapus dari sistem.");
        }

        // Delete photo file if exists
        if ($inventory->foto && File::exists(public_path($inventory->foto))) {
            File::delete(public_path($inventory->foto));
        }

        $namaBarang = $inventory->nama_barang;
        $kodeBarang = $inventory->kode_barang;

        $inventory->delete();

        return redirect()->route('admin.inventaris.index')
            ->with('success', "Inventaris '{$namaBarang}' ({$kodeBarang}) berhasil dihapus dari sistem.");
    }
}
