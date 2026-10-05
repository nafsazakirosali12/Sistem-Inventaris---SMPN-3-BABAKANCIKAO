<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms with search, sorting, and pagination (5 items strictly per flow.md 3.7).
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $user = Auth::user();

        // 1. Notification count
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        // 2. Query rooms with inventories count
        $query = Room::withCount('inventories');

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_ruangan', 'like', "%{$search}%")
                  ->orWhere('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Sort filter
        $sort = $request->input('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('nama_ruangan', 'desc');
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
                $query->orderBy('kode_ruangan', 'asc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('nama_ruangan', 'asc');
                break;
        }

        // Pagination 5 items per page (strictly per flow.md line 135)
        $rooms = $query->paginate(5)->withQueryString();

        // Auto code generator
        $nextCode = $this->generateNextRoomCode();

        return view('admin.ruangan.index', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'rooms',
            'nextCode'
        ));
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:50',
                'unique:rooms,kode_ruangan',
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'nama_ruangan' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi.',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan oleh ruangan lain.',
            'kode_ruangan.regex' => 'Format kode ruangan hanya boleh huruf, angka, (-), dan (_).',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'nama_ruangan.max' => 'Nama ruangan maksimal 255 karakter.',
        ]);

        $validated['kode_ruangan'] = strtoupper(trim($validated['kode_ruangan']));
        $validated['nama_ruangan'] = trim($validated['nama_ruangan']);
        $validated['penanggung_jawab'] = $validated['penanggung_jawab'] ? trim($validated['penanggung_jawab']) : null;
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $room = Room::create($validated);

        return redirect()->route('admin.ruangan.index')
            ->with('success', "Ruangan '{$room->nama_ruangan}' berhasil ditambahkan ke sistem.");
    }

    /**
     * Update specified room.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:50',
                'unique:rooms,kode_ruangan,' . $room->id,
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'nama_ruangan' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi.',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan oleh ruangan lain.',
            'kode_ruangan.regex' => 'Format kode ruangan hanya boleh huruf, angka, (-), dan (_).',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'nama_ruangan.max' => 'Nama ruangan maksimal 255 karakter.',
        ]);

        $validated['kode_ruangan'] = strtoupper(trim($validated['kode_ruangan']));
        $validated['nama_ruangan'] = trim($validated['nama_ruangan']);
        $validated['penanggung_jawab'] = $validated['penanggung_jawab'] ? trim($validated['penanggung_jawab']) : null;
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $room->update($validated);

        return redirect()->route('admin.ruangan.index')
            ->with('success', "Perubahan data ruangan '{$room->nama_ruangan}' berhasil disimpan.");
    }

    /**
     * Remove specified room.
     * Enforces flow.md 3.7 business rule: "Ruangan yang masih berisi unit tidak boleh dihapus."
     */
    public function destroy(Room $room)
    {
        $unitCount = $room->inventories()->count();

        if ($unitCount > 0) {
            return redirect()->route('admin.ruangan.index')
                ->with('error', "Penghapusan ditolak: Ruangan '{$room->nama_ruangan}' masih menampung {$unitCount} unit barang inventaris. Ruangan tidak boleh dihapus jika masih berisi unit barang.");
        }

        $roomName = $room->nama_ruangan;
        $room->delete();

        return redirect()->route('admin.ruangan.index')
            ->with('success', "Ruangan '{$roomName}' berhasil dihapus dari sistem.");
    }

    /**
     * Generate next logical room code.
     */
    protected function generateNextRoomCode(): string
    {
        $count = Room::count() + 1;
        $code = 'RUG-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);

        while (Room::where('kode_ruangan', $code)->exists()) {
            $count++;
            $code = 'RUG-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
