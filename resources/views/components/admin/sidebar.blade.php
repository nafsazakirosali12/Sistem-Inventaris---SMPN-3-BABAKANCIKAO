<!-- Admin Sidebar Navigation (Dark Green Main Identity) -->
<aside id="adminSidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed left-0 top-0 bottom-0 w-64 bg-[#134E4A] z-40 flex flex-col justify-between py-0 border-r border-[#115E59] shadow-md transition-transform duration-200 ease-in-out md:translate-x-0">
    
    <div class="flex flex-col h-full overflow-y-auto">
        <!-- 1. Top Section: Logo & Nama Sekolah (Khusus Sidebar Admin) -->
        <div class="h-16 px-4 bg-[#134E4A] flex items-center gap-3 border-b border-[#115E59] shrink-0">
            <img src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}" alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}" class="h-9 w-auto object-contain shrink-0">
            <div class="flex flex-col overflow-hidden">
                <span class="font-bold text-xs tracking-wider text-white uppercase truncate">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}</span>
                <span class="text-[10px] text-[#CCFBF1]/80 truncate">Sistem Inventaris</span>
            </div>
        </div>

        <!-- 2. Navigation Menu Links -->
        <nav class="flex-1 px-3 py-4 space-y-1.5">
            <!-- 1. Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.dashboard') ? 'text-[#2DD4BF]' : '' }}">grid_view</span>
                <span>Dashboard</span>
            </a>

            <!-- 2. Profil Sekolah -->
            <a href="{{ route('admin.profil.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.profil*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.profil*') ? 'text-[#2DD4BF]' : '' }}">account_balance</span>
                <span>Profil Sekolah</span>
            </a>

            <!-- 3. Kategori -->
            <a href="{{ route('admin.kategori.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.kategori.*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.kategori.*') ? 'text-[#2DD4BF]' : '' }}">category</span>
                <span>Kategori</span>
            </a>

            <!-- 4. Ruangan -->
            <a href="{{ route('admin.ruangan.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.ruangan*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.ruangan*') ? 'text-[#2DD4BF]' : '' }}">meeting_room</span>
                <span>Ruangan</span>
            </a>

            <!-- 5. Data Inventaris -->
            <a href="{{ route('admin.inventaris.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.inventaris*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.inventaris*') ? 'text-[#2DD4BF]' : '' }}">inventory_2</span>
                <span>Data Inventaris</span>
            </a>

            <!-- 6. Data Peminjaman -->
            <a href="{{ route('admin.peminjaman.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.peminjaman*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.peminjaman*') ? 'text-[#2DD4BF]' : '' }}">assignment_return</span>
                <span>Data Peminjaman</span>
            </a>

            <!-- 7. Daftar Akun -->
            <a href="{{ route('admin.akun.index') }}"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.akun*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.akun*') ? 'text-[#2DD4BF]' : '' }}">manage_accounts</span>
                <span>Daftar Akun</span>
            </a>

            <!-- 8. Laporan Inventaris -->
            <a href="#"
               class="flex items-center h-11 px-4 transition-all text-xs rounded-lg {{ request()->routeIs('admin.laporan*') ? 'bg-[#0F766E] text-white font-bold border-l-4 border-[#2DD4BF] shadow-sm' : 'text-[#CCFBF1] hover:bg-white/10 hover:text-white font-medium' }}">
                <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('admin.laporan*') ? 'text-[#2DD4BF]' : '' }}">summarize</span>
                <span>Laporan Inventaris</span>
            </a>
        </nav>
    </div>
</aside>
