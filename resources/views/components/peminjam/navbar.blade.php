<!-- Peminjam Top Navbar (White Clean Navbar per Reference) -->
<nav x-data="{ mobileMenuOpen: false }" class="bg-white min-h-[70px] px-4 sm:px-8 py-3 text-[#1F2937] shadow-xs border-b border-[#D9E4E2] sticky top-0 z-50">
    <div class="flex items-center justify-between">
        <!-- Brand & Logo -->
        <div class="flex items-center space-x-3.5">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-90 transition-opacity">
                <img src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}"
                     alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                     class="h-10 w-10 sm:h-11 sm:w-11 object-contain shrink-0">
                <div>
                    <h2 class="font-bold text-sm sm:text-base leading-tight text-[#1F2937] uppercase tracking-wide">
                        {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                    </h2>
                    <p class="text-[11px] text-[#0F766E] font-medium leading-tight">
                        {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}
                    </p>
                </div>
            </a>
        </div>

        <!-- Navigation Links & User Controls (Desktop) -->
        <div class="flex items-center space-x-2 sm:space-x-6">
            <!-- Links Menu Utama Desktop -->
            <div class="hidden lg:flex items-center space-x-1 sm:space-x-2 text-xs font-semibold text-[#4B5563]">
                <a href="{{ route('home') }}"
                   class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-[#F0FDFA] text-[#0F766E] font-bold' : 'hover:bg-[#F3F7F6] hover:text-[#1F2937]' }}">
                    Beranda
                </a>
                <a href="{{ route('admin.inventaris.index') }}"
                   class="px-3.5 py-2 rounded-lg transition-colors hover:bg-[#F3F7F6] hover:text-[#1F2937]">
                    Katalog Barang
                </a>
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="px-3.5 py-2 rounded-lg transition-colors hover:bg-[#F3F7F6] hover:text-[#1F2937]">
                    Riwayat Pinjaman
                </a>
                <a href="{{ route('home') }}#denah-sekolah"
                   class="px-3.5 py-2 rounded-lg transition-colors hover:bg-[#F3F7F6] hover:text-[#1F2937]">
                    Denah Sekolah
                </a>
                <a href="{{ route('home') }}#kontak-kami"
                   class="px-3.5 py-2 rounded-lg transition-colors hover:bg-[#F3F7F6] hover:text-[#1F2937]">
                    Kontak
                </a>
            </div>

            <!-- Auth Controls Desktop -->
            @auth
                <div class="hidden sm:flex items-center space-x-3 pl-2 sm:pl-4 border-l border-[#D9E4E2]">
                    <div class="flex flex-col text-right">
                        <span class="text-xs font-bold text-[#1F2937] leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-[#0F766E] font-mono leading-tight uppercase font-semibold">{{ auth()->user()->role }}</span>
                    </div>
                    <button type="button"
                            onclick="confirmPeminjamLogout()"
                            class="h-9 px-3.5 rounded-lg bg-[#FEE2E2] hover:bg-[#FECACA] text-xs font-semibold text-[#DC2626] flex items-center space-x-1.5 transition-colors shadow-xs"
                            title="Keluar dari sistem">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        <span>Keluar</span>
                    </button>
                </div>
                <form id="peminjamLogoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <div class="hidden sm:block pl-2 sm:pl-4 border-l border-[#D9E4E2]">
                    <a href="{{ route('login') }}"
                       class="h-9 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center space-x-1.5 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                        <span class="material-symbols-outlined text-[16px]">login</span>
                        <span>Masuk</span>
                    </a>
                </div>
            @endauth

            <!-- Mobile Hamburger Toggle Button -->
            <button type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 rounded-lg text-[#4B5563] hover:text-[#0F766E] hover:bg-[#F0FDFA] transition-colors focus:outline-none"
                    aria-label="Toggle Menu">
                <span class="material-symbols-outlined text-2xl" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Navigation -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-cloak
         class="lg:hidden pt-4 pb-2 border-t border-[#D9E4E2] mt-3 space-y-2 text-sm font-semibold">
        <a href="{{ route('home') }}"
           @click="mobileMenuOpen = false"
           class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->routeIs('home') ? 'bg-[#F0FDFA] text-[#0F766E] font-bold' : 'text-[#4B5563] hover:bg-[#F3F7F6]' }}">
            <span class="material-symbols-outlined text-base">home</span>
            <span>Beranda</span>
        </a>
        <a href="{{ route('admin.inventaris.index') }}"
           @click="mobileMenuOpen = false"
           class="flex items-center space-x-2 px-3 py-2 rounded-lg text-[#4B5563] hover:bg-[#F3F7F6] hover:text-[#1F2937]">
            <span class="material-symbols-outlined text-base">inventory_2</span>
            <span>Katalog Barang</span>
        </a>
        <a href="{{ route('admin.peminjaman.index') }}"
           @click="mobileMenuOpen = false"
           class="flex items-center space-x-2 px-3 py-2 rounded-lg text-[#4B5563] hover:bg-[#F3F7F6] hover:text-[#1F2937]">
            <span class="material-symbols-outlined text-base">history</span>
            <span>Riwayat Pinjaman</span>
        </a>
        <a href="{{ route('home') }}#denah-sekolah"
           @click="mobileMenuOpen = false"
           class="flex items-center space-x-2 px-3 py-2 rounded-lg text-[#4B5563] hover:bg-[#F3F7F6] hover:text-[#1F2937]">
            <span class="material-symbols-outlined text-base">map</span>
            <span>Denah Sekolah</span>
        </a>
        <a href="{{ route('home') }}#kontak-kami"
           @click="mobileMenuOpen = false"
           class="flex items-center space-x-2 px-3 py-2 rounded-lg text-[#4B5563] hover:bg-[#F3F7F6] hover:text-[#1F2937]">
            <span class="material-symbols-outlined text-base">contact_support</span>
            <span>Kontak</span>
        </a>

        <div class="pt-2 border-t border-[#D9E4E2]">
            @auth
                <div class="px-3 py-2 flex items-center justify-between bg-[#F0FDFA] rounded-lg mb-2">
                    <div>
                        <div class="text-xs font-bold text-[#1F2937]">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-[#0F766E] uppercase font-semibold">{{ auth()->user()->role }}</div>
                    </div>
                    <button type="button"
                            onclick="confirmPeminjamLogout()"
                            class="px-3 py-1.5 rounded-md bg-[#FEE2E2] text-xs font-semibold text-[#DC2626] flex items-center space-x-1">
                        <span class="material-symbols-outlined text-[14px]">logout</span>
                        <span>Keluar</span>
                    </button>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="flex items-center justify-center space-x-2 w-full py-2.5 rounded-lg bg-[#0F766E] text-white font-semibold text-xs shadow-sm">
                    <span class="material-symbols-outlined text-base">login</span>
                    <span>Masuk ke Akun</span>
                </a>
            @endauth
        </div>
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
