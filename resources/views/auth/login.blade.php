@extends('layouts.app')

@section('title', 'Login - ' . ($schoolProfile->nama_sistem ?? 'Sistem Inventaris SMPN 3 Babakancikao'))

@section('content')
<main class="w-full min-h-screen bg-[#134E4A] flex flex-col items-center justify-between p-4 sm:p-6 selection:bg-[#CCFBF1] selection:text-[#0F766E]">

    <div class="flex-1 flex flex-col items-center justify-center w-full my-6">
        <!-- Main Card Container -->
        <div class="w-full max-w-[400px] bg-white rounded-xl shadow-2xl p-6 sm:p-8 relative z-10 transition-all duration-200">

            <!-- School Header Branding -->
            <div class="flex flex-col items-center text-center">
                <div class="w-[72px] h-[72px] mb-3 flex items-center justify-center p-1.5 bg-[#F0FDFA] rounded-xl border border-[#CCFBF1] shadow-sm">
                    <img src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}"
                         alt="Logo Resmi {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                         class="w-full h-full object-contain">
                </div>
                <h1 class="font-bold text-lg uppercase tracking-wider text-[#1F2937]">
                    {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                </h1>
                <span class="mt-2.5 inline-flex items-center px-3 py-1 rounded-[6px] bg-[#CCFBF1] text-[#0F766E] text-xs font-semibold">
                    {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}
                </span>
            </div>

            <div class="h-px bg-[#D9E4E2] w-full my-5"></div>

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-[#FEE2E2] border border-[#FCA5A5] flex items-start space-x-2.5 animate-fadeIn">
                    <span class="material-symbols-outlined text-[#DC2626] text-[20px] shrink-0 mt-0.5">
                        cancel
                    </span>
                    <div class="text-xs text-[#991B1B] font-medium leading-relaxed">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Session Flash Message -->
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-[#DCFCE7] border border-[#86EFAC] flex items-start space-x-2.5">
                    <span class="material-symbols-outlined text-[#16A34A] text-[20px] shrink-0 mt-0.5">
                        check_circle
                    </span>
                    <p class="text-xs text-[#166534] font-medium leading-relaxed">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="flex flex-col space-y-4" onsubmit="handleLoginSubmit(event)">
                @csrf

                <!-- Username / NIP Input -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-[#1F2937] mb-1.5">
                        Nama pengguna <span class="text-[#DC2626]">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">badge</span>
                        </div>
                        <input type="text"
                               id="username"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="Masukkan nama pengguna"
                               required
                               autofocus
                               class="w-full h-10 pl-9 pr-3 rounded-lg border border-[#D9E4E2] bg-[#F9FAFB] text-[#1F2937] text-xs placeholder-[#6B7280] transition-all duration-150 shadow-sm focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/30">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-[#1F2937]">
                            Kata Sandi <span class="text-[#DC2626]">*</span>
                        </label>
                        <a href="javascript:void(0)"
                           onclick="showHelpAlert()"
                           class="text-xs text-[#0F766E] font-medium hover:text-[#115E59] hover:underline transition-colors focus:outline-none">
                            Lupa sandi?
                        </a>
                    </div>
                    <div class="relative flex items-center">
                        <div class="absolute left-3 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">lock</span>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Masukkan kata sandi"
                               required
                               class="w-full h-10 pl-9 pr-10 rounded-lg border border-[#D9E4E2] bg-[#F9FAFB] text-[#1F2937] text-xs placeholder-[#6B7280] transition-all duration-150 shadow-sm focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/30">
                        <button type="button"
                                id="togglePassword"
                                onclick="togglePasswordVisibility()"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                class="absolute right-2.5 inset-y-0 flex items-center justify-center text-[#6B7280] hover:text-[#1F2937] rounded transition-colors focus:outline-none">
                            <span class="material-symbols-outlined text-[19px] leading-none select-none" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>


                <!-- Submit Button -->
                <button type="submit"
                        id="submitBtn"
                        class="w-full h-10 mt-2 bg-[#0F766E] hover:bg-[#115E59] text-white font-semibold text-xs rounded-lg flex items-center justify-center space-x-2 transition-all duration-150 shadow-sm hover:shadow active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    <span>Masuk ke Sistem</span>
                    <span class="material-symbols-outlined text-[18px]">login</span>
                </button>
            </form>

            <!-- Notice Box -->
            <div class="mt-6 p-3 rounded-lg bg-[#F0FDFA] border border-[#CCFBF1] flex items-start space-x-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[18px] shrink-0 mt-0.5">
                    info
                </span>
                <p class="text-xs text-[#0F766E] leading-relaxed font-normal">
                    Belum memiliki akun? Silakan hubungi bagian <strong class="font-semibold">Admin Sarpras</strong> untuk aktivasi akun.
                </p>
            </div>
        </div>
    </div>

    <!-- Toast Notification for Help / Lupa Sandi -->
    <div id="toastMessage"
         class="fixed bottom-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-[#1F2937] text-white px-4 py-3 rounded-lg shadow-xl text-xs font-medium flex items-center space-x-2.5 border border-gray-700">
            <span class="material-symbols-outlined text-[#F59E0B] text-[20px]">contact_support</span>
            <span>Silakan hubungi Ruang Tata Usaha/Sarpras untuk reset kata sandi.</span>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full py-3 text-center border-t border-[#115E59]/40 mt-4">
        <p class="text-xs text-[#CCFBF1]/80 tracking-wide font-normal">
            © 2024 {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }} • Purwakarta, Jawa Barat
        </p>
    </footer>

</main>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput && eyeIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }
    }

    function showHelpAlert() {
        const toast = document.getElementById('toastMessage');
        if (toast) {
            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 4000);
        }
    }

    function handleLoginSubmit(event) {
        const submitBtn = document.getElementById('submitBtn');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        if (!usernameInput.value.trim() || !passwordInput.value.trim()) {
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            submitBtn.innerHTML = `
                <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>Memverifikasi...</span>
            `;
        }
    }
</script>
@endsection
