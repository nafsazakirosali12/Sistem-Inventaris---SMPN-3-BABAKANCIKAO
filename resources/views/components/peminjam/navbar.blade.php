<!-- Peminjam Top Navbar (Dark Green Main Identity) -->
<nav class="bg-[#134E4A] min-h-[64px] px-4 sm:px-6 py-2 flex flex-wrap items-center justify-between text-white shadow-md border-b border-[#115E59] sticky top-0 z-50">
    <!-- Brand & Logo -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-95 transition-opacity">
            <img src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}" alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKAN CIKAO' }}" class="h-10 w-10 object-contain">
            <div>
                <h2 class="font-bold text-sm sm:text-base leading-tight text-white uppercase tracking-wider">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKAN CIKAO' }}</h2>
                <p class="text-[11px] text-[#CCFBF1] opacity-95">{{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}</p>
            </div>
        </a>
    </div>

    <!-- Navigation Links & User Controls -->
    <div class="flex items-center space-x-2 sm:space-x-6 mt-2 sm:mt-0">
        <!-- Links Peminjam -->
        <div class="flex items-center space-x-1 sm:space-x-3 text-xs font-semibold">
            <a href="{{ route('home') }}"
               class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                Beranda
            </a>
            <a href="#"
               class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('peminjaman.*') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                Peminjaman
            </a>
            <a href="#"
               class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('riwayat.*') ? 'bg-[#0F766E] text-white shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white' }}">
                Riwayat Peminjaman
            </a>
        </div>

        <!-- Logout Action -->
        @auth
            <div class="flex items-center space-x-3 pl-3 border-l border-[#115E59]">
                <span class="text-xs text-[#CCFBF1] hidden md:inline-block">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                <button type="button"
                        onclick="confirmPeminjamLogout()"
                        class="px-3 py-1.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-xs font-semibold text-white flex items-center space-x-1.5 transition-colors shadow-sm">
                    <span>Keluar</span>
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                </button>
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
