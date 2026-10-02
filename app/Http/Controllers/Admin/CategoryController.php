<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with search, sorting, and pagination (10 items).
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();

        // 1. Notification bell badge count
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        // 2. Query categories directly from categories table with inventories count
        $query = Category::withCount('inventories');

        // Search filter: kode_kategori, nama_kategori, keterangan
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_kategori', 'like', "%{$search}%")
                  ->orWhere('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Sorting filter
        $sort = $request->input('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('nama_kategori', 'desc');
                break;
            case 'items_desc':
                $query->orderBy('inventories_count', 'desc');
                break;
            case 'items_asc':
                $query->orderBy('inventories_count', 'asc');
                break;
            case 'recent':
                $query->latest();
                break;
            case 'code_asc':
                $query->orderBy('kode_kategori', 'asc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('nama_kategori', 'asc');
                break;
        }

        // Pagination 10 items per page (strictly following flow.md line 128)
        $categories = $query->paginate(10)->withQueryString();

        // Suggested next category code
        $nextCode = $this->generateNextCategoryCode();

        return view('admin.kategori.index', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'categories',
            'nextCode'
        ));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:50',
                'unique:categories,kode_kategori',
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'nama_kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan. Silakan gunakan kode unik lain.',
            'kode_kategori.regex' => 'Format kode kategori hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $validated['kode_kategori'] = strtoupper(trim($validated['kode_kategori']));
        $validated['nama_kategori'] = trim($validated['nama_kategori']);
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $category = Category::create($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', "Kategori '{$category->nama_kategori}' berhasil ditambahkan ke dalam sistem.");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:50',
                'unique:categories,kode_kategori,' . $category->id,
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'nama_kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan oleh kategori lain.',
            'kode_kategori.regex' => 'Format kode kategori hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $validated['kode_kategori'] = strtoupper(trim($validated['kode_kategori']));
        $validated['nama_kategori'] = trim($validated['nama_kategori']);
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $category->update($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', "Perubahan data kategori '{$category->nama_kategori}' berhasil disimpan.");
    }

    /**
     * Remove the specified category from storage.
     * Enforces business rule from flow.md:
     * "Hapus: alert keputusan. Kategori yang masih punya unit tidak boleh dihapus."
     */
    public function destroy(Category $category)
    {
        $unitCount = $category->inventories()->count();

        if ($unitCount > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', "Penghapusan ditolak: Kategori '{$category->nama_kategori}' masih memiliki {$unitCount} unit barang inventaris fisik. Sesuai aturan sistem, kategori yang masih memiliki unit tidak boleh dihapus.");
        }

        $categoryName = $category->nama_kategori;
        $category->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', "Kategori '{$categoryName}' berhasil dihapus dari sistem.");
    }

    /**
     * Helper to suggest a logical next category code.
     */
    protected function generateNextCategoryCode(): string
    {
        $count = Category::count() + 1;
        $code = 'KTG-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);
        
        while (Category::where('kode_kategori', $code)->exists()) {
            $count++;
            $code = 'KTG-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
