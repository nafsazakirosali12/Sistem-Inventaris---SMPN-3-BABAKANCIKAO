<!-- Peminjam Top Navbar (Dark Green Main Identity) -->
<nav class="bg-[#134E4A] h-16 px-3 sm:px-6 flex items-center justify-between text-white shadow-md border-b border-[#115E59] sticky top-0 z-50">
    <!-- Brand & Logo -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
        <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 hover:opacity-95 transition-opacity min-w-0">
            <img src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}"
                 alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKAN CIKAO' }}"
                 class="h-9 w-9 sm:h-10 sm:w-10 object-contain shrink-0">
            <div class="min-w-0 hidden xs:block sm:block">
                <h2 class="font-bold text-xs sm:text-sm leading-tight text-white uppercase tracking-wider truncate max-w-[150px] sm:max-w-none">
                    {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKAN CIKAO' }}
                </h2>
                <p class="text-[10px] sm:text-[11px] text-[#CCFBF1] opacity-90 truncate max-w-[150px] sm:max-w-xs hidden sm:block">
                    {{ $schoolProfile->nama_sistem ?? 'Sistem Inventaris & Peminjaman' }}
                </p>
            </div>
        </a>
    </div>

    <!-- Navigation Links & User Controls -->
    <div class="flex items-center gap-1 sm:gap-4 shrink-0">
        <!-- Links Peminjam – dipersingkat pada mobile -->
        <div class="flex items-center gap-0.5 sm:gap-1 text-xs font-semibold">
            <a href="{{ route('home') }}"
               class="px-2 sm:px-3 py-2 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('home') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                <span class="hidden sm:inline">Beranda</span>
                <span class="sm:hidden material-symbols-outlined text-[20px] leading-none">home</span>
            </a>
            <a href="#"
               class="px-2 sm:px-3 py-2 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('peminjaman.*') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                <span class="hidden sm:inline">Peminjaman</span>
                <span class="sm:hidden material-symbols-outlined text-[20px] leading-none">assignment_return</span>
            </a>
            <a href="#"
               class="px-2 sm:px-3 py-2 rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('riwayat.*') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                <span class="hidden sm:inline">Riwayat</span>
                <span class="sm:hidden material-symbols-outlined text-[20px] leading-none">history</span>
            </a>
        </div>

        <!-- User Menu Dropdown (Burger Icon) -->
        @auth
            <div class="relative inline-block text-left pl-2 sm:pl-3 border-l border-[#115E59]" x-data="{ open: false }">
                <button @click="open = !open"
                        type="button"
                        class="p-2 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white flex items-center justify-center transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-[#CCFBF1]/40"
                        :class="{ 'bg-[#115E59] ring-2 ring-[#CCFBF1]/40': open }"
                        title="Menu Pengguna"
                        aria-label="Menu Pengguna">
                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200">menu</span>
                </button>

                <!-- Dropdown Menu (1: Nama yang sedang login, 2: Keluar) -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black/5 focus:outline-none z-50 overflow-hidden border border-[#D9E4E2]"
                     style="display: none;">

                    <!-- 1. Nama yang Sedang Login -->
                    <div class="px-4 py-3 bg-[#F0FDFA] border-b border-[#D9E4E2] flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-[#0F766E] text-white flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-[#1F2937] truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-[#0F766E] font-medium capitalize">{{ auth()->user()->role ?? 'Peminjam' }}</p>
                        </div>
                    </div>

                    <!-- 2. Keluar -->
                    <button type="button"
                            @click="open = false; confirmPeminjamLogout()"
                            class="w-full group flex items-center px-4 py-2.5 text-xs text-[#DC2626] hover:bg-[#FEE2E2]/60 transition-colors font-medium text-left">
                        <span class="material-symbols-outlined text-[18px] mr-2.5 text-[#DC2626] transition-transform group-hover:translate-x-0.5">logout</span>
                        <span>Keluar</span>
                    </button>
                </div>
            </div>

            <form id="peminjamLogoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        @endauth
    </div>
</nav>

<script>
    function confirmPeminjamLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin ingin keluar?',
                text: 'Sesi peminjaman Anda akan diakhiri.',
                showCancelButton: true,
                confirmButtonColor: '#0F766E',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                    cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('peminjamLogoutForm').submit();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                document.getElementById('peminjamLogoutForm').submit();
            }
        }
    }
</script>
