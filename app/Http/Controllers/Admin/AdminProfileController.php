<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AdminProfileController extends Controller
{
    /**
     * Display the Admin Profile page.
     */
    public function index()
    {
        $user = Auth::user();
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
        ]);
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        return view('admin.profile.index', compact('user', 'schoolProfile', 'perluTindakanCount'));
    }

    /**
     * Update the Admin Profile (photo, username, password).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username,' . $user->id,
                'regex:/^[A-Za-z0-9\._]+$/',
            ],
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $updateData = [
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
        ];

        // 1. Handle Password Update with Hashing
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // 2. Handle Profile Photo Upload
        if ($request->hasFile('foto')) {
            $uploadDir = public_path('uploads/avatars');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            // Remove previous avatar file if exists
            if (!empty($user->avatar) && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $fotoFile = $request->file('foto');
            $fotoName = 'avatar_' . $user->id . '_' . time() . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move($uploadDir, $fotoName);
            $updateData['avatar'] = 'uploads/avatars/' . $fotoName;
        }

        $user->update($updateData);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Perubahan profil admin berhasil disimpan.');
    }
}
