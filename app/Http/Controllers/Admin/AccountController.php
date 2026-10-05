<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display a listing of peminjam accounts with search, sorting, and pagination.
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

        // 2. Query users strictly filtered by role 'peminjam' (flow.md 3.11)
        $query = User::where('role', 'peminjam');

        // Search filter: search by username
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Sorting filter
        $sort = $request->input('sort', 'username_asc');
        switch ($sort) {
            case 'username_desc':
                $query->orderBy('username', 'desc');
                break;
            case 'recent':
                $query->latest();
                break;
            case 'username_asc':
            default:
                $query->orderBy('username', 'asc');
                break;
        }

        // Pagination 10 items per page
        $accounts = $query->paginate(10)->withQueryString();

        return view('admin.akun.index', compact(
            'schoolProfile',
            'user',
            'perluTindakanCount',
            'accounts'
        ));
    }

    /**
     * Store a new peminjam account (only username & password input, role automatically peminjam).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username',
                'regex:/^[A-Za-z0-9\._]+$/',
            ],
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $username = strtolower(trim($validated['username']));

        $account = User::create([
            'name' => $username,
            'username' => $username,
            'password' => Hash::make($validated['password']),
            'role' => 'peminjam', // Automatically set role to peminjam
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', "Akun peminjam '@{$account->username}' berhasil dibuat.");
    }

    /**
     * Update specified peminjam account (username & password update).
     */
    public function update(Request $request, User $user)
    {
        // Enforce update only for peminjam role
        if ($user->role !== 'peminjam') {
            return redirect()->route('admin.akun.index')
                ->with('error', 'Hanya akun bertipe Peminjam yang dapat dikelola pada menu ini.');
        }

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username,' . $user->id,
                'regex:/^[A-Za-z0-9\._]+$/',
            ],
            'password' => 'nullable|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $username = strtolower(trim($validated['username']));

        $updateData = [
            'username' => $username,
            'name' => $username,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.akun.index')
            ->with('success', "Akun peminjam '@{$user->username}' berhasil diperbarui.");
    }

    /**
     * Remove specified peminjam account.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'peminjam') {
            return redirect()->route('admin.akun.index')
                ->with('error', 'Akun Administrator tidak dapat dihapus melalui menu ini.');
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.akun.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $username = $user->username;
        $user->delete();

        return redirect()->route('admin.akun.index')
            ->with('success', "Akun peminjam '@{$username}' berhasil dihapus dari sistem.");
    }
}
