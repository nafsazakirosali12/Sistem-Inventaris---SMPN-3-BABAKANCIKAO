@extends('layouts.app')

@section('title', 'Kategori Barang - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<!-- Header Navbar -->
<header class="fixed top-0 left-0 right-0 h-16 bg-[#134E4A] z-50 flex items-center justify-between px-6 shadow-[0_1px_8px_rgba(0,0,0,0.12)]">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo-sekolah.svg') }}" alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}" class="h-10 w-auto object-contain">
        <div class="flex flex-col">
            <span class="font-bold text-sm tracking-wider text-white uppercase">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}</span>
            <span class="text-xs text-[#CCFBF1]/80">{{ $schoolProfile->nama_sistem ?? 'Sistem Inventaris & Peminjaman' }}</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <!-- Notification Bell -->
        <button type="button" class="relative p-2 rounded-lg text-[#CCFBF1] hover:text-white transition-colors focus:outline-none" aria-label="Notifikasi">
            <span class="material-symbols-outlined text-[24px]">notifications</span>
            @if ($perluTindakanCount > 0)
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#EAB308] ring-2 ring-[#134E4A]"></span>
            @endif
        </button>

        <!-- Admin Profile & Logout -->
        <div class="flex items-center gap-3 pl-3 border-l border-[#115E59]">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-semibold text-white leading-tight">{{ $user->name ?? 'Admin Sarpras' }}</p>
                <p class="text-[11px] text-[#CCFBF1]">Pengelola Aset</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#0F766E] flex items-center justify-center border border-[#CCFBF1]/30">
                <span class="material-symbols-outlined text-white text-[18px]">person</span>
            </div>
            <button type="button"
                    onclick="openLogoutConfirmModal()"
                    class="p-1.5 rounded-lg text-[#CCFBF1] hover:text-white hover:bg-white/10 transition-colors"
                    title="Keluar dari akun">
                <span class="material-symbols-outlined text-[20px]">logout</span>
            </button>
        </div>
    </div>
</header>

<!-- Sidebar Navigation -->
<aside class="fixed left-0 top-16 bottom-0 w-64 bg-[#134E4A] z-40 flex flex-col justify-between py-4 border-t border-[#115E59]">
    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">grid_view</span>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">account_balance</span>
            <span>Profil Sekolah</span>
        </a>
        <!-- Kategori Menu (Active) -->
        <a href="{{ route('admin.kategori.index') }}" aria-current="page" class="flex items-center h-11 px-4 transition-all bg-[#0F766E] text-white font-semibold text-xs rounded-lg shadow-sm">
            <span class="material-symbols-outlined mr-3 text-[20px]">category</span>
            <span>Kategori</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">meeting_room</span>
            <span>Ruangan</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">inventory_2</span>
            <span>Data Inventaris</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">assignment_return</span>
            <span>Data Peminjaman</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">manage_accounts</span>
            <span>Daftar Akun</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">summarize</span>
            <span>Laporan Inventaris</span>
        </a>
    </nav>
</aside>

<!-- Main Workspace -->
<div class="pl-64 flex flex-col min-h-screen">
    <main class="relative pt-20 flex-1 w-full p-6 bg-[#F3F7F6]">
        <div class="flex flex-col w-full gap-6">

            <!-- Breadcrumb & Page Header -->
            <div class="flex flex-col gap-1">
                <nav class="flex items-center gap-1.5 text-[#6B7280] text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0F766E] transition-colors flex items-center gap-1">
                        <span>Beranda</span>
                    </a>
                    <span class="text-[#B7C9C6]">/</span>
                    <span class="text-[#0F766E] font-semibold">Kategori</span>
                </nav>
                <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                    <div>
                        <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Kategori Barang</h1>
                        <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                            Kelola klasifikasi dan pengelompokan sarana prasarana sekolah untuk memudahkan pendataan, pemeliharaan berkala, serta sirkulasi peminjaman inventaris SMPN 3 Babakancikao.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flash Notifications / Alert Toast -->
            @if (session('success'))
                <div id="toastSuccess" class="p-4 rounded-xl bg-[#DCFCE7] border border-[#86EFAC] flex items-center justify-between shadow-sm animate-in fade-in duration-200">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#16A34A] text-[24px]">check_circle</span>
                        <div>
                            <h4 class="text-xs font-bold text-[#166534]">Berhasil</h4>
                            <p class="text-xs text-[#166534]">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('toastSuccess').remove()" class="text-[#166534] hover:text-[#14532D]">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="toastError" class="p-4 rounded-xl bg-[#FEE2E2] border border-[#FCA5A5] flex items-center justify-between shadow-sm animate-in fade-in duration-200">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#DC2626] text-[24px]">error</span>
                        <div>
                            <h4 class="text-xs font-bold text-[#991B1B]">Perhatian</h4>
                            <p class="text-xs text-[#991B1B]">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('toastError').remove()" class="text-[#991B1B] hover:text-[#7F1D1D]">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-[#FEE2E2] border border-[#FCA5A5] flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-[#DC2626] text-[22px] shrink-0 mt-0.5">warning</span>
                    <div class="text-xs text-[#991B1B]">
                        <p class="font-bold mb-1">Terdapat kesalahan validasi data:</p>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif



            <!-- Unified Card: Search, Filter, Actions & Data Table -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden flex flex-col">
                <!-- Card Header: Search & Filter Toolbar -->
                <div class="p-4 sm:p-5 border-b border-[#D9E4E2] flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-white">
                    <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <!-- Search Input with Perfectly Positioned Icon -->
                        <div class="relative flex-1 min-w-[260px]">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                                <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama atau kode kategori..."
                                   class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                            @if(request('search'))
                                <a href="{{ route('admin.kategori.index', ['sort' => request('sort')]) }}"
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                                   title="Hapus pencarian">
                                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                                </a>
                            @endif
                        </div>

                        <!-- Sort Filter -->
                        <div class="flex items-center gap-2 bg-[#F3F7F6] rounded-lg px-3 h-10 border border-[#D9E4E2]">
                            <span class="material-symbols-outlined text-[#6B7280] text-[18px]">sort</span>
                            <select name="sort"
                                    onchange="this.form.submit()"
                                    class="bg-transparent text-xs text-[#1F2937] focus:outline-none cursor-pointer pr-2">
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Kategori (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Kategori (Z-A)</option>
                                <option value="items_desc" {{ request('sort') == 'items_desc' ? 'selected' : '' }}>Jumlah Unit Terbanyak</option>
                                <option value="items_asc" {{ request('sort') == 'items_asc' ? 'selected' : '' }}>Jumlah Unit Tersedikit</option>
                                <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Kode Kategori (A-Z)</option>
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Terkini Ditambahkan</option>
                            </select>
                        </div>

                        @if(request()->hasAny(['search', 'sort']))
                            <a href="{{ route('admin.kategori.index') }}"
                               class="h-10 px-3 rounded-lg bg-white border border-[#D9E4E2] text-xs text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] flex items-center justify-center transition-colors shrink-0"
                               title="Reset filter">
                                <span class="material-symbols-outlined text-[18px] mr-1">restart_alt</span>
                                <span>Reset</span>
                            </a>
                        @endif
                    </form>

                    <!-- Action Button: Tambah Kategori -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                                onclick="openAddCategoryModal()"
                                class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            <span>Tambah Kategori</span>
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                                <th class="py-3.5 px-4 text-center w-14">No</th>
                                <th class="py-3.5 px-4 w-32">Kode Kategori</th>
                                <th class="py-3.5 px-4">Nama Kategori</th>
                                <th class="py-3.5 px-4">Keterangan</th>
                                <th class="py-3.5 px-4 text-center w-32">Jumlah Barang</th>
                                <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#D9E4E2] text-xs text-[#1F2937]">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-[#F8FBFA] transition-colors">
                                    <td class="py-3.5 px-4 text-center text-[#6B7280] font-medium">
                                        {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded bg-[#F3F7F6] font-mono text-[11px] font-bold text-[#1F2937] border border-[#D9E4E2]">
                                            {{ $cat->kode_kategori }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-semibold text-[#1F2937] text-xs">{{ $cat->nama_kategori }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-[#4B5563] text-xs leading-relaxed max-w-md">
                                        {{ $cat->keterangan ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#166534]">
                                            {{ $cat->inventories_count }} Unit
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1 justify-center">
                                            <!-- Tombol Edit Pop-up -->
                                            <button type="button"
                                                    onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->kode_kategori) }}', '{{ addslashes($cat->nama_kategori) }}', '{{ addslashes($cat->keterangan ?? '') }}')"
                                                    class="w-8 h-8 rounded-lg hover:bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center transition-colors focus:outline-none"
                                                    title="Ubah Kategori">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>

                                            <!-- Tombol Hapus dengan Alert Keputusan -->
                                            <button type="button"
                                                    onclick="handleDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->nama_kategori) }}', {{ $cat->inventories_count }})"
                                                    class="w-8 h-8 rounded-lg hover:bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center transition-colors focus:outline-none"
                                                    title="Hapus Kategori">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-14 h-14 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#6B7280] mb-3">
                                                <span class="material-symbols-outlined text-[28px]">search_off</span>
                                            </div>
                                            <h3 class="text-sm font-semibold text-[#1F2937]">Tidak ada kategori ditemukan</h3>
                                            <p class="text-xs text-[#6B7280] mt-1 max-w-sm">
                                                @if(request('search'))
                                                    Kata kunci "{{ request('search') }}" tidak cocok dengan kategori manapun.
                                                @else
                                                    Belum ada data kategori tersimpan di dalam sistem inventaris.
                                                @endif
                                            </p>
                                            <button type="button"
                                                    onclick="openAddCategoryModal()"
                                                    class="mt-4 px-4 py-2 rounded-lg bg-[#0F766E] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm hover:bg-[#115E59]">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                <span>Tambah Kategori Baru</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination (Strictly 10 per page) -->
                <div class="p-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-xs text-[#6B7280]">
                        Menampilkan <span class="font-semibold text-[#1F2937]">{{ $categories->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-[#1F2937]">{{ $categories->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ $categories->total() }}</span> kategori (10 per halaman)
                    </span>
                    <div class="text-xs">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>



        </div>
    </main>

    <!-- Footer Otomatis dari Profil Sekolah (flow.md 17 & DESIGN.md 6.12) -->
    <footer class="w-full bg-[#134E4A] text-[#CCFBF1] py-8 px-6 border-t border-[#115E59]">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Kolom 1: Profil Sekolah & Sistem -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-sekolah.svg') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                            {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                        </h3>
                        <p class="text-[11px] text-[#CCFBF1]/80">
                            NPSN: {{ $schoolProfile->npsn ?? '-' }} • {{ $schoolProfile->kabupaten_kota ?? 'Kabupaten Purwakarta' }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-[#CCFBF1]/80 leading-relaxed mt-1">
                    {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}
                    @if($schoolProfile->tagline)
                        — {{ $schoolProfile->tagline }}
                    @endif
                </p>
            </div>

            <!-- Kolom 2: Kontak Resmi -->
            <div class="flex flex-col gap-2">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-1">Kontak Resmi</h4>
                <div class="flex items-start gap-2.5 text-xs text-[#CCFBF1]/90">
                    <span class="material-symbols-outlined text-[18px] text-[#2DD4BF] shrink-0 mt-0.5">location_on</span>
                    <span class="leading-relaxed">{{ $schoolProfile->alamat ?? 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat' }}</span>
                </div>
                <div class="flex items-center gap-2.5 text-xs text-[#CCFBF1]/90">
                    <span class="material-symbols-outlined text-[18px] text-[#2DD4BF] shrink-0">call</span>
                    <span>{{ $schoolProfile->telepon ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2.5 text-xs text-[#CCFBF1]/90">
                    <span class="material-symbols-outlined text-[18px] text-[#2DD4BF] shrink-0">mail</span>
                    <span>{{ $schoolProfile->email ?? '-' }}</span>
                </div>
            </div>

            <!-- Kolom 3: Identitas Lembaga & Media Sosial -->
            <div class="flex flex-col gap-2">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-1">Unit Organisasi</h4>
                <p class="text-xs text-[#CCFBF1]/90 leading-relaxed">
                    {{ $schoolProfile->unit_organisasi ?? 'Dinas Pendidikan Purwakarta' }}<br>
                    {{ $schoolProfile->sub_unit_organisasi ?? 'SMPN 3 Babakancikao' }}<br>
                    <span class="text-[11px] text-[#CCFBF1]/70">No. Kode Lokasi: {{ $schoolProfile->no_kode_lokasi ?? '-' }}</span>
                </p>

                <!-- Media Sosial (Hanya tampil jika ada data per flow.md 2.2) -->
                @if($schoolProfile->instagram || $schoolProfile->youtube || $schoolProfile->facebook || $schoolProfile->tiktok)
                    <div class="flex items-center gap-2.5 mt-2">
                        @if($schoolProfile->instagram)
                            <a href="{{ Str::startsWith($schoolProfile->instagram, 'http') ? $schoolProfile->instagram : 'https://instagram.com/' . ltrim($schoolProfile->instagram, '@') }}"
                               target="_blank" rel="noopener"
                               class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 text-[#CCFBF1] hover:text-white flex items-center justify-center transition-colors"
                               title="Instagram">
                                <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                            </a>
                        @endif
                        @if($schoolProfile->youtube)
                            <a href="{{ Str::startsWith($schoolProfile->youtube, 'http') ? $schoolProfile->youtube : 'https://youtube.com/' . $schoolProfile->youtube }}"
                               target="_blank" rel="noopener"
                               class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 text-[#CCFBF1] hover:text-white flex items-center justify-center transition-colors"
                               title="YouTube">
                                <span class="material-symbols-outlined text-[16px]">smart_display</span>
                            </a>
                        @endif
                        @if($schoolProfile->facebook)
                            <a href="{{ Str::startsWith($schoolProfile->facebook, 'http') ? $schoolProfile->facebook : 'https://facebook.com/' . $schoolProfile->facebook }}"
                               target="_blank" rel="noopener"
                               class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 text-[#CCFBF1] hover:text-white flex items-center justify-center transition-colors"
                               title="Facebook">
                                <span class="material-symbols-outlined text-[16px]">public</span>
                            </a>
                        @endif
                        @if($schoolProfile->tiktok)
                            <a href="{{ Str::startsWith($schoolProfile->tiktok, 'http') ? $schoolProfile->tiktok : 'https://tiktok.com/@' . ltrim($schoolProfile->tiktok, '@') }}"
                               target="_blank" rel="noopener"
                               class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 text-[#CCFBF1] hover:text-white flex items-center justify-center transition-colors"
                               title="TikTok">
                                <span class="material-symbols-outlined text-[16px]">videocam</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH KATEGORI (Pop-up)                                         -->
<!-- ========================================================================= -->
<div id="modalTambahKategori" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">create_new_folder</span>
                <h2 class="text-base font-bold text-[#1F2937]">Tambah Kategori Baru</h2>
            </div>
            <button type="button"
                    onclick="closeAddCategoryModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('admin.kategori.store') }}" method="POST" class="p-6 flex flex-col gap-4">
            @csrf

            <!-- Kode Kategori -->
            <div class="flex flex-col gap-1.5">
                <label for="add_kode_kategori" class="text-xs font-semibold text-[#1F2937]">
                    Kode Kategori <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           id="add_kode_kategori"
                           name="kode_kategori"
                           value="{{ old('kode_kategori', $nextCode) }}"
                           placeholder="mis. KAT-ELEK"
                           required
                           class="w-full h-10 px-3 pr-9 bg-[#F3F7F6] rounded-lg font-mono text-xs uppercase font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[18px]">tag</span>
                </div>
                <span class="text-[11px] text-[#6B7280]">Kode unik singkat untuk identifikasi kategori aset (huruf kapital).</span>
            </div>

            <!-- Nama Kategori -->
            <div class="flex flex-col gap-1.5">
                <label for="add_nama_kategori" class="text-xs font-semibold text-[#1F2937]">
                    Nama Kategori (Jenis Barang) <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="add_nama_kategori"
                       name="nama_kategori"
                       value="{{ old('nama_kategori') }}"
                       placeholder="mis. Peralatan Musik Tradisional"
                       required
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Keterangan -->
            <div class="flex flex-col gap-1.5">
                <label for="add_keterangan" class="text-xs font-semibold text-[#1F2937]">
                    Keterangan (Opsional)
                </label>
                <textarea id="add_keterangan"
                          name="keterangan"
                          rows="3"
                          placeholder="Jelaskan jenis sarana atau prasarana yang dicakup dalam kategori ini..."
                          class="w-full p-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeAddCategoryModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: UBAH KATEGORI (Pop-up flow.md 126)                                -->
<!-- ========================================================================= -->
<div id="modalEditKategori" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">edit_note</span>
                <h2 class="text-base font-bold text-[#1F2937]">Ubah Data Kategori</h2>
            </div>
            <button type="button"
                    onclick="closeEditCategoryModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditKategori" method="POST" class="p-6 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <!-- Kode Kategori -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_kode_kategori" class="text-xs font-semibold text-[#1F2937]">
                    Kode Kategori <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           id="edit_kode_kategori"
                           name="kode_kategori"
                           required
                           class="w-full h-10 px-3 pr-9 bg-[#F3F7F6] rounded-lg font-mono text-xs uppercase font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[18px]">tag</span>
                </div>
            </div>

            <!-- Nama Kategori -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_nama_kategori" class="text-xs font-semibold text-[#1F2937]">
                    Nama Kategori <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="edit_nama_kategori"
                       name="nama_kategori"
                       required
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Keterangan -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_keterangan" class="text-xs font-semibold text-[#1F2937]">
                    Keterangan
                </label>
                <textarea id="edit_keterangan"
                          name="keterangan"
                          rows="3"
                          class="w-full p-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none"></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeEditCategoryModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="button"
                        onclick="promptSaveEditCategory()"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: ALERT KEPUTUSAN SIMPAN EDIT (flow.md 126 & DESIGN.md 6.10)        -->
<!-- ========================================================================= -->
<div id="modalConfirmSaveEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/60 backdrop-blur-sm">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl text-center flex flex-col items-center animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <div class="w-14 h-14 rounded-full bg-[#FEF3C7] text-[#F59E0B] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[32px]">warning</span>
        </div>
        <h3 class="text-base font-bold text-[#1F2937] mb-1">Simpan perubahan?</h3>
        <p class="text-xs text-[#4B5563] mb-6 leading-relaxed">
            Apakah Anda yakin ingin memperbarui data kategori ini? Perubahan akan langsung tercermin pada data inventaris.
        </p>
        <div class="flex items-center justify-center gap-3 w-full">
            <button type="button"
                    onclick="closeSaveEditConfirmModal()"
                    class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] bg-[#F3F7F6] hover:bg-[#E5E7EB] transition-colors">
                Batal
            </button>
            <button type="button"
                    onclick="submitEditCategoryForm()"
                    class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-white bg-[#0F766E] hover:bg-[#115E59] transition-colors shadow-sm">
                Simpan
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: ALERT KEPUTUSAN HAPUS (flow.md 127 & DESIGN.md 6.10)              -->
<!-- ========================================================================= -->
<div id="modalConfirmDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/60 backdrop-blur-sm">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl text-center flex flex-col items-center animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <div class="w-14 h-14 rounded-full bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[32px]">delete</span>
        </div>
        <h3 class="text-base font-bold text-[#1F2937] mb-1">Hapus kategori ini?</h3>
        <p class="text-xs text-[#4B5563] mb-6 leading-relaxed">
            Kategori <strong id="deleteCategoryName" class="text-[#1F2937]"></strong> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="formDeleteCategory" method="POST" class="w-full">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-center gap-3 w-full">
                <!-- Focus awal berada di Batal (sesuai DESIGN.md 6.10) -->
                <button type="button"
                        id="btnCancelDelete"
                        onclick="closeDeleteConfirmModal()"
                        class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] bg-[#F3F7F6] hover:bg-[#E5E7EB] transition-colors focus:ring-2 focus:ring-[#0F766E]/30">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-white bg-[#DC2626] hover:bg-[#B91C1C] transition-colors shadow-sm">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 5: ALERT HAPUS DITOLAK (flow.md 127 rule: masih punya unit)          -->
<!-- ========================================================================= -->
<div id="modalDeleteRejected" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/60 backdrop-blur-sm">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl text-center flex flex-col items-center animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <div class="w-14 h-14 rounded-full bg-[#DBEAFE] text-[#2563EB] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[32px]">info</span>
        </div>
        <h3 class="text-base font-bold text-[#1F2937] mb-1">Hapus ditolak</h3>
        <p class="text-xs text-[#4B5563] mb-6 leading-relaxed">
            Kategori <strong id="rejectedCategoryName" class="text-[#1F2937]"></strong> tidak boleh dihapus karena masih menampung <strong id="rejectedCategoryUnits" class="text-[#1E40AF]"></strong> unit inventaris fisik. Silakan pindahkan atau hapus unit barang terlebih dahulu.
        </p>
        <button type="button"
                onclick="closeDeleteRejectedModal()"
                class="w-full h-10 px-4 rounded-lg text-xs font-semibold text-[#1F2937] bg-[#F3F7F6] hover:bg-[#E5E7EB] transition-colors">
            Tutup
        </button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 6: ALERT KEPUTUSAN LOGOUT (DESIGN.md 6.10)                          -->
<!-- ========================================================================= -->
<div id="modalConfirmLogout" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/60 backdrop-blur-sm">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl text-center flex flex-col items-center animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <div class="w-14 h-14 rounded-full bg-[#FEF3C7] text-[#F59E0B] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[32px]">logout</span>
        </div>
        <h3 class="text-base font-bold text-[#1F2937] mb-1">Keluar dari akun?</h3>
        <p class="text-xs text-[#4B5563] mb-6 leading-relaxed">
            Sesi aktif Anda sebagai Admin Sarpras akan diakhiri. Anda perlu masuk kembali untuk mengelola inventaris.
        </p>
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <div class="flex items-center justify-center gap-3 w-full">
                <button type="button"
                        onclick="closeLogoutConfirmModal()"
                        class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] bg-[#F3F7F6] hover:bg-[#E5E7EB] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 h-10 px-4 rounded-lg text-xs font-semibold text-white bg-[#0F766E] hover:bg-[#115E59] transition-colors shadow-sm">
                    Keluar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal Tambah Kategori
    function openAddCategoryModal() {
        document.getElementById('modalTambahKategori').classList.remove('hidden');
        setTimeout(() => {
            const input = document.getElementById('add_nama_kategori');
            if (input) input.focus();
        }, 100);
    }

    function closeAddCategoryModal() {
        document.getElementById('modalTambahKategori').classList.add('hidden');
    }

    // Modal Edit Kategori
    function openEditCategoryModal(id, code, name, description) {
        const form = document.getElementById('formEditKategori');
        form.action = `/admin/kategori/${id}`;

        document.getElementById('edit_kode_kategori').value = code;
        document.getElementById('edit_nama_kategori').value = name;
        document.getElementById('edit_keterangan').value = description;

        document.getElementById('modalEditKategori').classList.remove('hidden');
    }

    function closeEditCategoryModal() {
        document.getElementById('modalEditKategori').classList.add('hidden');
    }

    // Modal Alert Simpan Perubahan (Edit)
    function promptSaveEditCategory() {
        const nameInput = document.getElementById('edit_nama_kategori');
        const codeInput = document.getElementById('edit_kode_kategori');

        if (!nameInput.value.trim() || !codeInput.value.trim()) {
            alert('Kode kategori dan nama kategori wajib diisi.');
            return;
        }

        document.getElementById('modalConfirmSaveEdit').classList.remove('hidden');
    }

    function closeSaveEditConfirmModal() {
        document.getElementById('modalConfirmSaveEdit').classList.add('hidden');
    }

    function submitEditCategoryForm() {
        document.getElementById('formEditKategori').submit();
    }

    // Modal Alert Hapus Kategori (flow.md rule)
    function handleDeleteCategory(id, name, unitCount) {
        if (unitCount > 0) {
            // Tolak penghapusan jika unit > 0
            document.getElementById('rejectedCategoryName').textContent = name;
            document.getElementById('rejectedCategoryUnits').textContent = unitCount;
            document.getElementById('modalDeleteRejected').classList.remove('hidden');
        } else {
            // Tampilkan konfirmasi keputusan hapus
            const form = document.getElementById('formDeleteCategory');
            form.action = `/admin/kategori/${id}`;
            document.getElementById('deleteCategoryName').textContent = name;
            document.getElementById('modalConfirmDelete').classList.remove('hidden');

            // Fokus awal di tombol Batal sesuai DESIGN.md 6.10
            setTimeout(() => {
                const btnCancel = document.getElementById('btnCancelDelete');
                if (btnCancel) btnCancel.focus();
            }, 100);
        }
    }

    function closeDeleteConfirmModal() {
        document.getElementById('modalConfirmDelete').classList.add('hidden');
    }

    function closeDeleteRejectedModal() {
        document.getElementById('modalDeleteRejected').classList.add('hidden');
    }

    // Modal Alert Logout
    function openLogoutConfirmModal() {
        document.getElementById('modalConfirmLogout').classList.remove('hidden');
    }

    function closeLogoutConfirmModal() {
        document.getElementById('modalConfirmLogout').classList.add('hidden');
    }

    // Keyboard accessibility: ESC key closes all active modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddCategoryModal();
            closeEditCategoryModal();
            closeSaveEditConfirmModal();
            closeDeleteConfirmModal();
            closeDeleteRejectedModal();
            closeLogoutConfirmModal();
        }
    });

    // Otomatis tutup alert sukses setelah 2 detik sesuai DESIGN.md 6.10
    document.addEventListener('DOMContentLoaded', function() {
        const toastSuccess = document.getElementById('toastSuccess');
        if (toastSuccess) {
            setTimeout(function() {
                toastSuccess.style.transition = 'opacity 0.4s ease-out';
                toastSuccess.style.opacity = '0';
                setTimeout(() => toastSuccess.remove(), 400);
            }, 2000);
        }
    });
</script>
@endsection
