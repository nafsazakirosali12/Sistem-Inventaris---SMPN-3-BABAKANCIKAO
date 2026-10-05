<!-- Admin Header Topbar (Standard Full-Width Attached Layout) -->
<header class="fixed top-0 right-0 left-0 md:left-64 h-16 bg-white z-30 flex items-center justify-between px-4 sm:px-6 border-b border-[#D9E4E2] shadow-xs transition-all duration-200">
    <!-- Left Section: Mobile Hamburger & Text-Only Breadcrumb -->
    <div class="flex items-center gap-3">
        <!-- Hamburger Button (Mobile Only) -->
        <button @click="sidebarOpen = !sidebarOpen"
                type="button"
                class="md:hidden p-1.5 rounded-lg text-[#4B5563] hover:text-[#0F766E] hover:bg-[#F3F7F6] transition-colors focus:outline-none"
                aria-label="Toggle Sidebar Menu">
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>

        <!-- Navbar Breadcrumb Navigation -->
        <nav class="flex items-center gap-1.5 text-xs text-[#6B7280]">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0F766E] transition-colors font-semibold text-[#4B5563]">
                Dashboard
            </a>

            <span class="text-[#B7C9C6] font-semibold">/</span>
            <span class="text-[#0F766E] font-semibold">
                @if(request()->routeIs('admin.dashboard'))
                    Dashboard
                @elseif(request()->routeIs('admin.profil*'))
                    Profil Sekolah
                @elseif(request()->routeIs('admin.kategori*'))
                    Kategori
                @elseif(request()->routeIs('admin.ruangan*'))
                    Ruangan
                @elseif(request()->routeIs('admin.akun*'))
                    Daftar Akun
                @elseif(request()->routeIs('admin.inventaris*'))
                    Data Inventaris
                @elseif(request()->routeIs('admin.peminjaman*'))
                    Data Peminjaman
                @elseif(request()->routeIs('admin.laporan*'))
                    Laporan Inventaris
                @else
                    @yield('breadcrumb_title', 'Halaman')
                @endif
            </span>
        </nav>
    </div>

    <!-- Right Section: Notification & Admin User Profile Dropdown -->
    <div class="flex items-center gap-2 sm:gap-4">
        <!-- Notification Bell -->
        <a href="{{ route('admin.dashboard') }}" class="relative p-2 rounded-lg text-[#4B5563] hover:text-[#0F766E] hover:bg-[#F3F7F6] transition-colors focus:outline-none" title="Notifikasi Permohonan">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
            @if (isset($perluTindakanCount) && $perluTindakanCount > 0)
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#EAB308] ring-2 ring-white animate-pulse"></span>
            @endif
        </a>

        <!-- Admin Profile Menu with Dropdown (Displays Name Only, No Role) -->
        <div class="relative inline-block text-left" x-data="{ open: false }" id="adminUserDropdown">
            <button @click="open = !open"
                    type="button"
                    class="flex items-center gap-2 sm:gap-2.5 pl-3 border-l border-[#D9E4E2] focus:outline-none hover:opacity-90 transition-opacity"
                    id="user-menu-button">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-[#1F2937] leading-tight">{{ auth()->user()->name ?? 'Admin Sarpras' }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-[#0F766E] flex items-center justify-center border border-[#CCFBF1] text-white shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                </div>
                <span class="material-symbols-outlined text-[#6B7280] text-[18px] transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
            </button>

            <!-- Dropdown Menu (Strictly Displays Name Only, then 2 Options: Profil Saya & Logout) -->
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black/5 divide-y divide-gray-100 focus:outline-none z-50 overflow-hidden border border-[#D9E4E2]"
                 style="display: none;">
                <!-- Header: Displays Name Only -->
                <div class="px-4 py-3 bg-[#F0FDFA]">
                    <p class="text-xs font-bold text-[#1F2937] truncate">{{ auth()->user()->name ?? 'Admin Sarpras' }}</p>
                </div>

                <!-- 1. Profil Saya -->
                <div class="py-1">
                    <a href="{{ route('admin.profil.index') }}" class="group flex items-center px-4 py-2.5 text-xs text-[#1F2937] hover:bg-[#F0FDFA] hover:text-[#0F766E] transition-colors font-medium">
                        <span class="material-symbols-outlined text-[18px] mr-2.5 text-[#6B7280] group-hover:text-[#0F766E]">account_circle</span>
                        Profil Saya
                    </a>
                </div>

                <!-- 2. Keluar -->
                <div class="py-1">
                    <button type="button"
                            onclick="confirmAdminLogout()"
                            class="w-full group flex items-center px-4 py-2.5 text-xs text-[#DC2626] hover:bg-[#FEE2E2]/50 transition-colors font-medium">
                        <span class="material-symbols-outlined text-[18px] mr-2.5 text-[#DC2626]">logout</span>
                        Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Hidden Logout Form -->
<form id="adminLogoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    function confirmAdminLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar dari akun?',
                text: 'Sesi aktif Anda sebagai Admin Sarpras akan diakhiri.',
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
                    document.getElementById('adminLogoutForm').submit();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin keluar dari akun Admin?')) {
                document.getElementById('adminLogoutForm').submit();
            }
        }
    }
</script>
