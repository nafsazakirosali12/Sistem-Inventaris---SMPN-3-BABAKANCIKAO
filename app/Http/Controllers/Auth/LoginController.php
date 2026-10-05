<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
            'tagline' => 'Portal Terpadu Sarana & Prasarana',
        ]);

        return view('auth.login', compact('schoolProfile'));
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username / NIP / NUPTK wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('username'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Check if user credentials match username OR nip_nuptk OR email safely
        $query = User::where('username', $loginInput);

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'nip_nuptk')) {
            $query->orWhere('nip_nuptk', $loginInput);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'email')) {
            $query->orWhere('email', $loginInput);
        }

        $user = $query->first();

        if ($user && Auth::attempt(['username' => $user->username, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return $this->redirectUser(Auth::user());
        }

        throw ValidationException::withMessages([
            'username' => ['Username atau kata sandi yang Anda masukkan salah.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }

    /**
     * Redirect user based on role.
     */
    protected function redirectUser($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}
