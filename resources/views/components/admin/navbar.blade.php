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
                @elseif(request()->routeIs('admin.profile*'))
                    Profil Admin
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
        <!-- Notification Bell with Interactive Popover -->
        <div class="relative inline-block text-left" x-data="{ notifOpen: false }">
            <button @click="notifOpen = !notifOpen"
                    type="button"
                    class="relative p-2 rounded-lg text-[#4B5563] hover:text-[#0F766E] hover:bg-[#F3F7F6] transition-colors focus:outline-none"
                    title="Notifikasi Pengajuan Peminjaman"
                    aria-label="Notifikasi Peminjaman">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                @if (isset($pendingLoansCount) && $pendingLoansCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#F59E0B] ring-2 ring-white animate-pulse"></span>
                @endif
            </button>

            <!-- Notification Popover Dropdown (Width ~320px, DESIGN.md 6.11) -->
            <div x-show="notifOpen"
                 @click.away="notifOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                 class="origin-top-right absolute right-0 mt-2 w-80 sm:w-[320px] rounded-xl shadow-xl bg-white divide-y divide-[#D9E4E2] focus:outline-none z-50 overflow-hidden border border-[#D9E4E2]"
                 style="display: none;">
                
                <!-- Popover Header -->
                <div class="px-4 py-3 bg-[#F0FDFA] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#0F766E]">notifications</span>
                        <span class="text-xs font-bold text-[#1F2937]">Notifikasi</span>
                    </div>
                    @if (isset($pendingLoansCount) && $pendingLoansCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A]">
                            {{ $pendingLoansCount }} Baru
                        </span>
                    @endif
                </div>

                <!-- Popover Notification List (From actual database) -->
                <div class="max-h-80 overflow-y-auto divide-y divide-[#F3F7F6]">
                    @if (isset($pendingLoansNotifications) && $pendingLoansNotifications->count() > 0)
                        @foreach ($pendingLoansNotifications as $notif)
                            <a href="{{ route('admin.peminjaman.index', ['highlight' => $notif->id]) }}"
                               class="block px-4 py-3 hover:bg-[#F0FDFA] transition-colors group">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-[#1F2937] group-hover:text-[#0F766E] transition-colors">
                                            {{ $notif->nama_peminjam }}
                                        </p>
                                        <p class="text-[11px] text-[#4B5563] mt-0.5 leading-snug">
                                            Mengajukan peminjaman <span class="font-medium text-[#1F2937]">{{ $notif->category->nama_kategori ?? 'Barang' }}</span>
                                        </p>
                                        <div class="flex items-center gap-1 text-[10px] text-[#6B7280] mt-1.5">
                                            <span class="material-symbols-outlined text-[12px] text-[#9CA3AF]">schedule</span>
                                            <span>{{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full bg-[#F3F7F6] group-hover:bg-[#CCFBF1] text-[#6B7280] group-hover:text-[#0F766E] flex items-center justify-center shrink-0 transition-colors mt-0.5">
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <!-- Empty State (DESIGN.md 6.11) -->
                        <div class="py-8 px-4 text-center flex flex-col items-center justify-center">
                            <div class="w-10 h-10 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#9CA3AF] mb-2">
                                <span class="material-symbols-outlined text-[22px]">notifications_paused</span>
                            </div>
                            <p class="text-xs font-semibold text-[#1F2937]">Tidak ada peminjaman baru</p>
                            <p class="text-[11px] text-[#6B7280] mt-0.5">Semua permohonan peminjaman telah ditindaklanjuti.</p>
                        </div>
                    @endif
                </div>

                <!-- Popover Footer -->
                <div class="px-4 py-2.5 bg-[#F8FBFA] text-center">
                    <a href="{{ route('admin.peminjaman.index') }}" class="text-xs font-semibold text-[#0F766E] hover:text-[#115E59] hover:underline inline-flex items-center gap-1">
                        <span>Lihat Semua Peminjaman</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Profile Menu with Dropdown (Displays Name & Avatar with updated link) -->
        <div class="relative inline-block text-left" x-data="{ open: false }" id="adminUserDropdown">
            <button @click="open = !open"
                    type="button"
                    class="flex items-center gap-2 sm:gap-2.5 pl-3 border-l border-[#D9E4E2] focus:outline-none hover:opacity-90 transition-opacity"
                    id="user-menu-button">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-[#1F2937] leading-tight">{{ auth()->user()->name ?? 'Admin Sarpras' }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-[#0F766E] flex items-center justify-center border border-[#CCFBF1] text-white shadow-xs overflow-hidden">
                    @if(auth()->user() && !empty(auth()->user()->avatar) && file_exists(public_path(auth()->user()->avatar)))
                        <img src="{{ asset(auth()->user()->avatar) }}?v={{ filemtime(public_path(auth()->user()->avatar)) }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    @endif
                </div>
                <span class="material-symbols-outlined text-[#6B7280] text-[18px]" :class="{ 'rotate-180': open }">menu</span>
            </button>

            <!-- Dropdown Menu (2 Options Only: Profil & Keluar) -->
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black/5 focus:outline-none z-50 overflow-hidden border border-[#D9E4E2]"
                 style="display: none;">

                <!-- 1. Profil Admin -->
                <a href="{{ route('admin.profile.index') }}" class="group flex items-center px-4 py-2.5 text-xs text-[#1F2937] hover:bg-[#F0FDFA] hover:text-[#0F766E] transition-colors font-medium">
                    <span class="material-symbols-outlined text-[18px] mr-2.5 text-[#6B7280] group-hover:text-[#0F766E]">manage_accounts</span>
                    Profil
                </a>

                <!-- 2. Keluar -->
                <button type="button"
                        onclick="confirmAdminLogout()"
                        class="w-full group flex items-center px-4 py-2.5 text-xs text-[#DC2626] hover:bg-[#FEE2E2]/50 transition-colors font-medium border-t border-[#D9E4E2]">
                    <span class="material-symbols-outlined text-[18px] mr-2.5 text-[#DC2626]">logout</span>
                    Keluar
                </button>
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
