<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SchoolProfileController extends Controller
{
    /**
     * Display the school profile page (Read-only by default per flow.md 3.4).
     */
    public function index()
    {
        $schoolProfile = SchoolProfile::first();

        if (!$schoolProfile) {
            $schoolProfile = SchoolProfile::create([
                'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
                'npsn' => '20203040',
                'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
                'tagline' => 'Portal Terpadu Sarana & Prasarana',
                'alamat' => 'Jl. Industri Ubrug, Babakancikao, Kab. Purwakarta, Jawa Barat 41151',
                'telepon' => '(0264) 1234567',
                'email' => 'info@smpn3babakancikao.sch.id',
                'provinsi' => 'Jawa Barat',
                'kabupaten_kota' => 'Kabupaten Purwakarta',
                'bidang' => 'Pendidikan',
                'unit_organisasi' => 'Dinas Pendidikan',
                'sub_unit_organisasi' => 'SMPN 3 Babakancikao',
                'upb' => 'SMPN 3 Babakancikao',
                'no_kode_lokasi' => '12.34.56.78.90',
            ]);
        }

        $user = Auth::user();
        $perluTindakanCount = Loan::where('status', 'menunggu')->count();

        return view('admin.profil.index', compact('schoolProfile', 'user', 'perluTindakanCount'));
    }

    /**
     * Update the school profile in storage.
     */
    public function update(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile();

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => ['required', 'digits:8'],
            'alamat' => 'required|string|max:1000',
            'telepon' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'denah' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'bidang' => 'nullable|string|max:255',
            'unit_organisasi' => 'nullable|string|max:255',
            'sub_unit_organisasi' => 'nullable|string|max:255',
            'upb' => 'nullable|string|max:255',
            'no_kode_lokasi' => 'nullable|string|max:255',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:255',
            'nama_pembuat' => 'nullable|string|max:255',
            'nip_pembuat' => 'nullable|string|max:255',
        ], [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'npsn.required' => 'NPSN wajib diisi.',
            'npsn.digits' => 'NPSN harus berisi tepat 8 digit angka.',
            'alamat.required' => 'Alamat sekolah wajib diisi.',
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda tambah (+), dan tanda hubung (-).',
            'email.email' => 'Format email sekolah tidak valid.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.mimes' => 'Format logo harus JPG, PNG, atau WebP.',
            'logo.max' => 'Ukuran file logo maksimal 2 MB.',
            'foto.image' => 'File foto sekolah harus berupa gambar.',
            'foto.mimes' => 'Format foto sekolah harus JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran file foto sekolah maksimal 2 MB.',
            'denah.image' => 'File denah sekolah harus berupa gambar.',
            'denah.mimes' => 'Format denah sekolah harus JPG, PNG, atau WebP.',
            'denah.max' => 'Ukuran file denah sekolah maksimal 5 MB.',
        ]);

        $uploadDir = public_path('uploads/profile');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // 1. Handle Upload Logo (max 2MB)
        if ($request->hasFile('logo')) {
            if ($schoolProfile->logo && File::exists(public_path($schoolProfile->logo))) {
                File::delete(public_path($schoolProfile->logo));
            }
            $logoFile = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move($uploadDir, $logoName);
            $validated['logo'] = 'uploads/profile/' . $logoName;
        }

        // 2. Handle Upload Foto Sekolah (max 2MB)
        if ($request->hasFile('foto')) {
            if ($schoolProfile->foto && File::exists(public_path($schoolProfile->foto))) {
                File::delete(public_path($schoolProfile->foto));
            }
            $fotoFile = $request->file('foto');
            $fotoName = 'foto_' . time() . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move($uploadDir, $fotoName);
            $validated['foto'] = 'uploads/profile/' . $fotoName;
        }

        // 3. Handle Upload Denah Sekolah (max 5MB)
        if ($request->hasFile('denah')) {
            if ($schoolProfile->denah && File::exists(public_path($schoolProfile->denah))) {
                File::delete(public_path($schoolProfile->denah));
            }
            $denahFile = $request->file('denah');
            $denahName = 'denah_' . time() . '.' . $denahFile->getClientOriginalExtension();
            $denahFile->move($uploadDir, $denahName);
            $validated['denah'] = 'uploads/profile/' . $denahName;
        }

        $schoolProfile->fill($validated)->save();

        return redirect()->route('admin.profil.index')
            ->with('success', 'Data profil sekolah berhasil diperbarui.');
    }
}
