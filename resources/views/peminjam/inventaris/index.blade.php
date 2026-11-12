@extends('layouts.peminjam')

@section('title', 'Katalog Inventaris & Peminjaman - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="peminjamKatalogApp({
         categoriesData: {{ json_encode($categoriesDataMap) }},
         sarprasSchedule: {
             hariLayanan: {{ json_encode($schoolProfile->hari_layanan ?? 'Senin s.d. Jumat') }},
             jamBuka: {{ json_encode($schoolProfile->jam_buka ? substr($schoolProfile->jam_buka, 0, 5) : '07:00') }},
             jamTutup: {{ json_encode($schoolProfile->jam_tutup ? substr($schoolProfile->jam_tutup, 0, 5) : '13:00') }},
             hariOperasional: {{ json_encode($schoolProfile->hari_operasional ? (is_array($schoolProfile->hari_operasional) ? $schoolProfile->hari_operasional : json_decode($schoolProfile->hari_operasional, true)) : [1, 2, 3, 4, 5]) }}
         }
     })">

    <!-- ═══════════════════════════════════════════════════════════
         1. PAGE HEADER & SEARCH / FILTER TOOLBAR
    ════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-[#D9E4E2]">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[15px]">inventory_2</span>
                    <span>Katalog Sarana & Prasarana</span>
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] tracking-tight">Peminjaman Inventaris</h1>
                <p class="text-xs sm:text-sm text-[#6B7280] mt-1 max-w-2xl leading-relaxed">
                    Pilih kategori barang yang ingin dipinjam. Sistem secara otomatis mengalokasikan unit barang dan kode barang fisik dari unit terkecil yang siap pakai.
                </p>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        @click="openLoanModal()"
                        class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>Pinjam Barang</span>
                    <template x-if="totalBorrowedCount > 0">
                        <span class="px-1.5 py-0.2 rounded-full bg-white text-[#0F766E] text-[11px] font-bold" x-text="totalBorrowedCount"></span>
                    </template>
                </button>
                <a href="{{ route('peminjam.riwayat.index') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-white border border-[#D9E4E2] text-xs font-bold text-[#374151] hover:bg-[#F3F7F6] hover:text-[#0F766E] transition-all shadow-xs">
                    <span class="material-symbols-outlined text-[18px] text-[#0F766E]">history</span>
                    <span>Lihat Riwayat</span>
                </a>
            </div>
        </div>

        <!-- Alert Flash Message jika Baru Selesai Peminjaman -->
        @if(session('success'))
            <div class="mt-6 p-4 rounded-2xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] flex items-start gap-3 shadow-sm">
                <span class="material-symbols-outlined text-[24px] text-[#10B981] shrink-0 mt-0.5">check_circle</span>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-[#065F46]">Peminjaman Berhasil!</h4>
                    <p class="text-xs mt-0.5 text-[#047857] leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mt-6 p-4 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-start gap-3 shadow-sm">
                <span class="material-symbols-outlined text-[24px] text-[#EF4444] shrink-0 mt-0.5">error</span>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-[#991B1B]">Perhatian</h4>
                    <p class="text-xs mt-0.5 text-[#B91C1C] leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="mt-6 p-4 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-start gap-3 shadow-sm">
                <span class="material-symbols-outlined text-[24px] text-[#EF4444] shrink-0 mt-0.5">error</span>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-[#991B1B]">Pengajuan Peminjaman Belum Berhasil</h4>
                    <ul class="text-xs mt-1 text-[#B91C1C] list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Filter & Search Toolbar (Dropdown Select2 seperti di Admin) -->
        <div class="mt-6 bg-white rounded-xl border border-[#D9E4E2] p-4 shadow-sm">
            <form method="GET" action="{{ route('peminjam.inventaris.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari kategori barang, merek, atau ruangan..."
                           class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium">
                    @if(request('search'))
                        <a href="{{ route('peminjam.inventaris.index', array_filter(['category_id' => request('category_id')])) }}"
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                           title="Hapus pencarian">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                        </a>
                    @endif
                </div>

                <!-- Dropdown Filter Kategori Menggunakan Select2 (Ukuran Rapi & Sejajar) -->
                <div class="w-full sm:w-64 md:w-72 shrink-0">
                    <select name="category_id"
                            class="select2-filter w-full">
                        <option value="">Semua Kategori</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }} (Stok: {{ $stockPerCategory[$cat->id] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Submit & Reset -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="submit"
                            class="h-10 px-4 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-semibold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer"
                            title="Terapkan Pencarian">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        <span>Filter</span>
                    </button>
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('peminjam.inventaris.index') }}"
                           class="h-10 px-3 bg-white hover:bg-[#F3F7F6] text-[#4B5563] hover:text-[#1F2937] border border-[#D9E4E2] rounded-lg text-xs font-semibold transition-colors flex items-center justify-center gap-1 shrink-0"
                           title="Reset Filter">
                            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         2. CARD INVENTARIS (BERDASARKAN KATEGORI)
         Setiap card mewakili satu KATEGORI barang
    ════════════════════════════════════════════════════════════ -->
    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl border border-[#D9E4E2] p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-[#F0FDFA] text-[#0F766E] mx-auto flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[32px]">inventory_2</span>
            </div>
            <h3 class="text-base font-bold text-[#1F2937]">Kategori Tidak Ditemukan</h3>
            <p class="text-xs text-[#6B7280] mt-1 max-w-sm mx-auto">
                Silakan coba ubah kata kunci pencarian atau reset filter untuk menemukan kategori inventaris yang tersedia.
            </p>
            @if(request('search') || request('category_id'))
                <a href="{{ route('peminjam.inventaris.index') }}"
                   class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-[#0F766E] text-white text-xs font-bold hover:bg-[#115E59] transition-all">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Tampilkan Semua Kategori</span>
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($categories as $category)
                @php
                    $catStock = $category->stok_tersedia;
                    $isAvailable = $catStock > 0;
                    $catNameLower = strtolower($category->nama_kategori);
                    $catIcon = match (true) {
                        str_contains($catNameLower, 'laptop') => 'laptop_mac',
                        str_contains($catNameLower, 'proyektor') => 'co_present',
                        str_contains($catNameLower, 'matras') => 'fitness_center',
                        str_contains($catNameLower, 'speaker') => 'volume_up',
                        str_contains($catNameLower, 'monitor') => 'desktop_windows',
                        str_contains($catNameLower, 'mikroskop') => 'biotech',
                        str_contains($catNameLower, 'kamera') => 'photo_camera',
                        str_contains($catNameLower, 'printer') => 'print',
                        str_contains($catNameLower, 'mikrofon') => 'mic',
                        str_contains($catNameLower, 'tablet') => 'tablet_mac',
                        str_contains($catNameLower, 'meja') => 'table_restaurant',
                        str_contains($catNameLower, 'kursi') => 'chair',
                        str_contains($catNameLower, 'lemari') => 'shelves',
                        str_contains($catNameLower, 'kabel') || str_contains($catNameLower, 'aksesoris') => 'cable',
                        str_contains($catNameLower, 'keyboard') => 'keyboard',
                        str_contains($catNameLower, 'mouse') => 'mouse',
                        default => 'inventory_2',
                    };
                @endphp
                <div class="bg-white rounded-2xl border border-[#D9E4E2] hover:border-[#0F766E]/50 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group">

                    <div>
                        <!-- 1. Gambar Inventaris / Kategori (Diambil dari barang pertama terdaftar) -->
                        <div class="relative w-full h-48 bg-[#F3F7F6] overflow-hidden flex items-center justify-center">
                            @if(!empty($category->foto_url))
                                <img src="{{ $category->foto_url }}"
                                     alt="{{ $category->nama_kategori }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-[#9CA3AF] bg-gradient-to-br from-[#F3F7F6] to-[#E2E8F0]">
                                    <span class="material-symbols-outlined text-6xl text-[#0F766E]/40 mb-1 group-hover:scale-110 transition-transform">
                                        {{ $catIcon }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-[#6B7280] tracking-wide">{{ $category->nama_kategori }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- 2. Informasi Utama Card Kategori -->
                        <div class="p-4 sm:p-5 space-y-3">
                            <!-- Nama Kategori -->
                            <div>
                                <h3 class="font-bold text-[#1F2937] text-base group-hover:text-[#0F766E] transition-colors line-clamp-1"
                                    title="{{ $category->nama_kategori }}">
                                    {{ $category->nama_kategori }}
                                </h3>
                                @if(!empty($category->keterangan))
                                    <p class="text-[11px] text-[#6B7280] mt-0.5 line-clamp-2 leading-relaxed">
                                        {{ $category->keterangan }}
                                    </p>
                                @endif
                            </div>

                            <!-- Ruangan -->
                            <div class="flex items-start gap-1.5 text-xs text-[#4B5563]">
                                <span class="material-symbols-outlined text-[17px] text-[#0F766E] shrink-0 mt-0.5">meeting_room</span>
                                <span class="line-clamp-1 font-medium" title="{{ $category->ruangan_summary }}">
                                    {{ $category->ruangan_summary }}
                                </span>
                            </div>

                            <!-- Stok -->
                            <div class="pt-2 border-t border-[#F3F4F6]">
                                @if($isAvailable)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F0FDFA] border border-[#CCFBF1] text-[#0F766E] text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                                        <span>Stok: {{ $catStock }}</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FEF2F2] border border-[#FEE2E2] text-[#DC2626] text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                                        <span>Stok: 0 (Habis)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 3. Button Actions: Detail & Pinjam -->
                    <div class="p-4 sm:p-5 pt-0 grid grid-cols-2 gap-2 mt-auto">
                        <!-- Button Detail -->
                        <button type="button"
                                @click="openCategoryDetail({{ $category->id }})"
                                class="h-9 px-3 rounded-xl bg-white border border-[#D9E4E2] hover:bg-[#F3F7F6] hover:text-[#0F766E] text-[#374151] font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Detail</span>
                        </button>

                        <!-- Button Pinjam -->
                        @if($isAvailable)
                            <button type="button"
                                    @click="openLoanModalWithCategory({{ $category->id }})"
                                    class="h-9 px-3 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">assignment_add</span>
                                <span>Pinjam</span>
                            </button>
                        @else
                            <button type="button"
                                    disabled
                                    class="h-9 px-3 rounded-xl bg-gray-100 text-gray-400 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed border border-gray-200"
                                    title="Stok unit kategori ini sedang tidak tersedia">
                                <span class="material-symbols-outlined text-[16px]">block</span>
                                <span>Habis</span>
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination (Konsisten dengan Riwayat) -->
        <div class="mt-8 pt-6 border-t border-[#D9E4E2]">
            {{ $categories->links() }}
        </div>
    @endif


    <!-- ═══════════════════════════════════════════════════════════
         3. POP-UP MODAL DETAIL KATEGORI
         Menampilkan rincian kategori, total inventaris, stok tersedia,
         dan daftar seluruh barang berdasarkan merek/tipe, kode barang,
         serta status masing-masing barang
    ════════════════════════════════════════════════════════════ -->
    <div x-show="detailModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         @click.self="detailModalOpen = false"
         @keydown.escape.window="detailModalOpen = false">

        <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl border border-[#D9E4E2] transform transition-all">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-[#F8FBFA] border-b border-[#D9E4E2] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">category</span>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#1F2937]" x-text="`Detail Kategori: ${selectedDetailCategory?.nama_kategori || ''}`"></h3>
                        <p class="text-[11px] text-[#6B7280]"></p>
                    </div>
                </div>
                <button type="button"
                        @click="detailModalOpen = false"
                        class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <!-- Gambar Kategori & Ringkasan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div class="w-full h-36 rounded-xl bg-[#F3F7F6] overflow-hidden flex items-center justify-center border border-[#D9E4E2]">
                        <template x-if="selectedDetailCategory?.foto_url">
                            <img :src="selectedDetailCategory.foto_url" :alt="selectedDetailCategory.nama_kategori" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!selectedDetailCategory?.foto_url">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-4xl text-[#0F766E]/40 mb-1">inventory_2</span>
                                <span class="text-[10px] font-semibold text-[#6B7280]">Gambar Kategori</span>
                            </div>
                        </template>
                    </div>

                    <!-- Metric Badges -->
                    <div class="sm:col-span-2 grid grid-cols-2 gap-2.5 text-xs">
                        <div class="p-2.5 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB]">
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Nama Kategori</p>
                            <p class="font-bold text-[#1F2937] mt-0.5" x-text="selectedDetailCategory?.nama_kategori || '-'"></p>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB]">
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Ruangan</p>
                            <p class="font-semibold text-[#1F2937] mt-0.5 text-xs line-clamp-2" x-text="selectedDetailCategory?.ruangan_summary || '-'"></p>
                        </div>

                        <!-- Stok & Nama Penanggung Jawab Ruangan Bersisian Sesuai Permintaan -->
                        <div class="p-2.5 rounded-xl bg-[#F0FDFA] border border-[#CCFBF1]">
                            <p class="text-[10px] text-[#0F766E] font-medium uppercase tracking-wide">Stok</p>
                            <p class="font-bold text-[#0F766E] mt-0.5 text-sm" x-text="`${selectedDetailCategory?.stok_tersedia || 0} Unit`"></p>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB]">
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Penanggung Jawab Ruangan</p>
                            <p class="font-bold text-[#1F2937] mt-0.5 text-xs line-clamp-2" x-text="selectedDetailCategory?.pj_ruangan_summary || '-'"></p>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi / Keterangan -->
                <template x-if="selectedDetailCategory?.keterangan">
                    <div class="p-3 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB] text-xs">
                        <span class="font-bold text-[#374151] block mb-0.5">Keterangan:</span>
                        <p class="text-[#6B7280] leading-relaxed" x-text="selectedDetailCategory.keterangan"></p>
                    </div>
                </template>

                <!-- DAFTAR BARANG DALAM KATEGORI BERDASARKAN MEREK/TIPE, KODE BARANG, DAN STATUS (HANYA STATUS BAIK) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#0F766E]">format_list_bulleted</span>
                            <span>Daftar Barang dalam Kategori Ini</span>
                        </h4>
                        <span class="text-[11px] text-[#6B7280]" x-text="`Stok: ${selectedDetailCategory?.stok_tersedia || 0}`"></span>
                    </div>

                    <div class="rounded-xl border border-[#D9E4E2] overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#F8FBFA] text-[#374151] font-semibold border-b border-[#D9E4E2]">
                                    <th class="py-2.5 px-3 text-center w-10">No</th>
                                    <th class="py-2.5 px-3 text-center w-16">Foto</th>
                                    <th class="py-2.5 px-3 w-32">Kode Barang</th>
                                    <th class="py-2.5 px-3">Nama Barang (Merek / Tipe)</th>
                                    <th class="py-2.5 px-3">Ruangan</th>
                                    <th class="py-2.5 px-3 text-center w-28">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E7EB] text-[#1F2937]">
                                <template x-for="(unit, idx) in (selectedDetailCategory?.all_units || [])" :key="unit.id">
                                    <tr class="hover:bg-[#F9FAFB] transition-colors">
                                        <td class="py-2.5 px-3 text-center text-gray-500 font-medium" x-text="idx + 1"></td>
                                        <td class="py-2.5 px-3 text-center">
                                            <template x-if="unit.foto_url">
                                                <img :src="unit.foto_url"
                                                     :alt="unit.nama_barang"
                                                     class="w-10 h-10 object-cover rounded-lg border border-[#D9E4E2] mx-auto shadow-2xs">
                                            </template>
                                            <template x-if="!unit.foto_url">
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center mx-auto text-gray-400">
                                                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold text-[#0F766E] bg-[#F0FDFA] border border-[#CCFBF1]" x-text="unit.kode_barang"></span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-bold text-[#1F2937]" x-text="unit.nama_barang"></div>
                                            <div class="text-[10px] text-gray-500" x-text="unit.merk_type"></div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="text-gray-700 font-medium" x-text="unit.ruangan"></div>
                                            <div class="text-[10px] text-gray-500 font-normal" x-show="unit.penanggung_jawab_ruangan && unit.penanggung_jawab_ruangan !== '-'" x-text="`PJ: ${unit.penanggung_jawab_ruangan}`"></div>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-block"
                                                  :class="unit.is_available ? 'bg-[#DCFCE7] text-[#166534]' : 'bg-[#DBEAFE] text-[#1E40AF]'"
                                                  x-text="unit.is_available ? 'Tersedia' : 'Dipinjam'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!selectedDetailCategory?.all_units || selectedDetailCategory.all_units.length === 0">
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-400 italic">
                                            Tidak ada barang berstatus baik pada kategori ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-[#F8FBFA] border-t border-[#D9E4E2] flex items-center justify-between">
                <button type="button"
                        @click="detailModalOpen = false"
                        class="h-9 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition-colors cursor-pointer">
                    Tutup
                </button>

                <template x-if="selectedDetailCategory?.stok_tersedia > 0">
                    <button type="button"
                            @click="detailModalOpen = false; openLoanModalWithCategory(selectedDetailCategory.id)"
                            class="h-9 px-4 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white font-bold text-xs flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">assignment_add</span>
                        <span>Pinjam</span>
                    </button>
                </template>
            </div>

        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
         4. FORM PEMINJAMAN MULTI-KATEGORI (MODAL POP-UP)
         Memungkinkan peminjam mengajukan beberapa kategori barang
         sekaligus dalam SATU transaksi peminjaman.
         Sistem mengalokasikan unit barang secara otomatis dari kode terkecil.
    ════════════════════════════════════════════════════════════ -->
    <div x-show="loanModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         @click.self="if (typeof Swal === 'undefined' || !Swal.isVisible()) handleCancelLoan()"
         @keydown.escape.window="if (typeof Swal === 'undefined' || !Swal.isVisible()) handleCancelLoan()">

        <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl border border-[#D9E4E2] transform transition-all">

            <!-- Form Multi-Kategori -->
            <form x-ref="loanForm" method="POST" action="{{ route('peminjam.peminjaman.store') }}" @submit.prevent="submitLoanForm()" novalidate>
                @csrf

                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#F8FBFA] border-b border-[#D9E4E2] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">assignment_add</span>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-[#1F2937]">Form Peminjaman Barang</h3>
                            <p class="text-[11px] text-[#6B7280]"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] font-bold text-xs"
                              x-text="`Total: ${totalBorrowedCount}`"></span>
                        <button type="button"
                                @click="handleCancelLoan()"
                                class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                    <!-- BAGIAN 1: DAFTAR KATEGORI YANG DIPINJAM (DENGAN COUNTER & ALOKASI OTOMATIS) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#1F2937] uppercase tracking-wider flex items-center gap-1.5">
                                <span>Daftar Barang yang Dipinjam</span>
                            </label>
                            <span class="text-[11px] text-[#6B7280]" x-text="`${selectedCategories.length} Kategori Dipilih`"></span>
                        </div>

                        <!-- Card List Setiap Kategori yang Dipilih -->
                        <div class="space-y-3">
                            <template x-for="(item, index) in selectedCategories" :key="item.category_id">
                                <div class="p-4 rounded-xl border border-[#D9E4E2] bg-white shadow-2xs hover:border-[#0F766E]/40 transition-all">
                                    <div class="flex items-start justify-between gap-3">
                                        <!-- Info Kategori & Gambar Barang Pertama -->
                                        <div class="flex items-start gap-3 min-w-0">
                                            <template x-if="categoriesData[item.category_id]?.foto_url">
                                                <img :src="categoriesData[item.category_id]?.foto_url"
                                                     :alt="categoriesData[item.category_id]?.nama_kategori"
                                                     class="w-12 h-12 object-cover rounded-xl border border-[#D9E4E2] shrink-0 shadow-2xs">
                                            </template>
                                            <template x-if="!categoriesData[item.category_id]?.foto_url">
                                                <div class="w-12 h-12 rounded-xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center shrink-0 text-gray-400">
                                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                                </div>
                                            </template>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-[#1F2937] text-sm truncate" x-text="categoriesData[item.category_id]?.nama_kategori"></h4>
                                                </div>
                                                <p class="text-[11px] text-[#6B7280] mt-0.5">
                                                    <span>Ruangan: </span>
                                                    <span class="font-medium text-[#374151]" x-text="categoriesData[item.category_id]?.ruangan_summary"></span>
                                                    <span class="mx-1">•</span>
                                                    <span class="text-[#0F766E] font-semibold" x-text="`Stok: ${categoriesData[item.category_id]?.stok_tersedia || 0}`"></span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Counter (+ dan -) & Tombol Hapus -->
                                        <div class="flex items-center gap-2 shrink-0">
                                            <!-- Counter Controls -->
                                            <div class="flex items-center border border-[#D9E4E2] rounded-xl overflow-hidden bg-[#F8FBFA]">
                                                <button type="button"
                                                        @click="decrementQty(item.category_id)"
                                                        :disabled="item.qty <= 1"
                                                        :class="item.qty <= 1 ? 'opacity-40 cursor-not-allowed text-gray-400' : 'text-[#1F2937] hover:bg-[#E5EBEA] cursor-pointer'"
                                                        class="w-8 h-8 flex items-center justify-center transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">remove</span>
                                                </button>

                                                <span class="w-9 text-center font-bold text-xs text-[#0F766E]" x-text="item.qty"></span>

                                                <button type="button"
                                                        @click="incrementQty(item.category_id)"
                                                        :disabled="item.qty >= (categoriesData[item.category_id]?.stok_tersedia || 0)"
                                                        :class="item.qty >= (categoriesData[item.category_id]?.stok_tersedia || 0) ? 'opacity-40 cursor-not-allowed text-gray-400' : 'text-[#1F2937] hover:bg-[#E5EBEA] cursor-pointer'"
                                                        class="w-8 h-8 flex items-center justify-center transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                                </button>
                                            </div>

                                            <!-- Tombol Hapus Kategori -->
                                            <button type="button"
                                                    @click="removeCategory(item.category_id)"
                                                    class="w-8 h-8 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors cursor-pointer"
                                                    title="Hapus Kategori">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- ALOKASI OTOMATIS: DAFTAR KODE & NAMA BARANG YANG DIALOKASIKAN SISTEM -->
                                    <div class="mt-3 p-2.5 rounded-lg bg-[#F0FDFA] border border-[#CCFBF1]">
                                        <div class="flex items-center justify-between text-[11px] font-bold text-[#0F766E] mb-1.5">
                                            <div class="flex items-center gap-1.5">
                                                <span>Alokasi Barang Otomatis:</span>
                                            </div>
                                            <span x-text="`${getAllocatedUnits(item.category_id, item.qty).length} Unit Terpilih`"></span>
                                        </div>
                                        <div class="space-y-1">
                                            <template x-for="unit in getAllocatedUnits(item.category_id, item.qty)" :key="unit.kode_barang">
                                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded bg-white border border-[#D9E4E2]">
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2 py-0.5 rounded bg-[#F3F7F6] font-mono font-bold text-[#0F766E] text-[11px] border border-[#D9E4E2]" x-text="unit.kode_barang"></span>
                                                        <span class="font-semibold text-[#1F2937]" x-text="unit.nama_barang"></span>
                                                    </div>
                                                    <span class="text-[11px] text-gray-500 font-medium" x-text="unit.merk_type"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Hidden Inputs untuk Pengiriman Data ke Backend -->
                                    <input type="hidden" :name="`items[${index}][category_id]`" :value="item.category_id">
                                    <input type="hidden" :name="`items[${index}][jumlah]`" :value="item.qty">
                                </div>
                            </template>

                            <!-- State Kosong jika Belum Ada Kategori Dipilih -->
                            <template x-if="selectedCategories.length === 0">
                                <div class="p-6 rounded-xl border border-dashed border-[#D9E4E2] text-center bg-[#F8FBFA]">
                                    <span class="material-symbols-outlined text-3xl text-gray-400 mb-1">add_shopping_cart</span>
                                    <p class="text-xs font-semibold text-[#374151]">Belum ada kategori barang yang ditambahkan</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Pilih salah satu kategori barang di bawah ini untuk memulai peminjaman.</p>
                                </div>
                            </template>
                        </div>

                        <!-- DROPDOWN TAMBAH KATEGORI LAIN KE DALAM FORM -->
                        <template x-if="availableCategoriesToAdd.length > 0">
                            <div class="pt-1">
                                <div class="flex items-center gap-2">
                                    <select x-model="selectedAddCategoryId"
                                            @change="if (selectedAddCategoryId) { addCategory(selectedAddCategoryId); }"
                                            class="w-full h-10 px-3 bg-[#F3F7F6] hover:bg-white rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium cursor-pointer">
                                        <option value="">+ Tambah Kategori Lain ke Formulir Peminjaman...</option>
                                        <template x-for="cat in availableCategoriesToAdd" :key="cat.id">
                                            <option :value="cat.id" x-text="`${cat.nama_kategori} (Stok: ${cat.stok_tersedia})`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>


                    <!-- BAGIAN 2: IDENTITAS PEMINJAM & JADWAL PEMINJAMAN -->
                    <div class="space-y-4 pt-3 border-t border-[#E5E7EB]">
                        <h4 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#0F766E]">schedule</span>
                            <span>Informasi Peminjam & Jadwal Penggunaan</span>
                        </h4>

                        <!-- Nama Peminjam (Kosong Default, Wajib Diisi) -->
                        <div>
                            <label class="block text-xs font-semibold text-[#1F2937] mb-1">
                                Nama Lengkap Peminjam <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   x-ref="borrowerNameInput"
                                   name="nama_peminjam"
                                   x-model="borrowerName"
                                   required
                                   placeholder="Wajib diisi: masukkan nama lengkap peminjam..."
                                   class="w-full h-10 px-3 bg-[#F3F7F6] rounded-xl text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium">
                        </div>

                        <!-- Jadwal Peminjaman & Pengembalian (Real-time Validation) -->
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Input Tanggal & Jam Pinjam (Real-time: tidak bisa waktu lampau) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-semibold text-[#1F2937]">
                                            Tanggal & Jam Pinjam <span class="text-red-500">*</span>
                                        </label>
                                        <button type="button"
                                                @click="resetPinjamToNow()"
                                                class="text-[10px] text-[#0F766E] hover:underline font-semibold flex items-center gap-0.5 cursor-pointer"
                                                title="Set ke waktu real-time saat ini">
                                            <span class="material-symbols-outlined text-[12px]">update</span>
                                            <span>Waktu Sekarang</span>
                                        </button>
                                    </div>
                                    <input type="datetime-local"
                                           name="tanggal_pinjam"
                                           x-model="tanggalPinjam"
                                           :min="minPinjamDatetime"
                                           @input="onPinjamChange()"
                                           @change="onPinjamChange()"
                                           @blur="onPinjamChange()"
                                           required
                                           :class="isPinjamInPast ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-[#D9E4E2] focus:border-[#0F766E] focus:ring-[#0F766E]/20'"
                                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-xl text-xs text-[#1F2937] border focus:bg-white focus:outline-none focus:ring-2 transition-all font-medium">
                                    
                                    <!-- Warning Jika Waktu Pinjam di Masa Lalu -->
                                    <template x-if="isPinjamInPast">
                                        <p class="text-[11px] text-red-600 font-medium flex items-center gap-1 mt-1">
                                            <span class="material-symbols-outlined text-[14px]">error</span>
                                            <span>Tanggal atau jam pinjam tidak boleh di waktu yang sudah lewat.</span>
                                        </p>
                                    </template>
                                    <template x-if="!isPinjamInPast">
                                        <p class="text-[10px] text-gray-500 mt-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px] text-[#0F766E]">schedule</span>
                                            <span>Layanan: <span class="font-medium text-gray-700" x-text="`${sarprasSchedule.hariLayanan}, ${sarprasSchedule.jamBuka}–${sarprasSchedule.jamTutup} WIB`"></span></span>
                                        </p>
                                    </template>
                                </div>

                                <!-- Input Batas Tanggal & Jam Kembali (Jam bebas jika di hari berikutnya) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-semibold text-[#1F2937]">
                                            Batas Tanggal & Jam Kembali <span class="text-red-500">*</span>
                                        </label>
                                        <span class="text-[10px] text-gray-500 font-medium"></span>
                                    </div>
                                    <input type="datetime-local"
                                           name="tanggal_kembali"
                                           x-model="tanggalKembali"
                                           :min="minKembaliDatetime"
                                           @input="onKembaliChange()"
                                           @change="onKembaliChange()"
                                           @blur="onKembaliChange()"
                                           required
                                           :class="isKembaliInvalid ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-[#D9E4E2] focus:border-[#0F766E] focus:ring-[#0F766E]/20'"
                                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-xl text-xs text-[#1F2937] border focus:bg-white focus:outline-none focus:ring-2 transition-all font-medium">
                                    
                                    <!-- Warning Jika Waktu Kembali Tidak Valid -->
                                    <template x-if="isKembaliInvalid">
                                        <p class="text-[11px] text-red-600 font-medium flex items-center gap-1 mt-1">
                                            <span class="material-symbols-outlined text-[14px]">error</span>
                                            <span x-text="kembaliErrorMessage"></span>
                                        </p>
                                    </template>
                                    <template x-if="!isKembaliInvalid">
                                        <!-- Quick Presets -->
                                        <div class="flex items-center gap-1 mt-1.5 flex-wrap">
                                            <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                                            <button type="button"
                                                    @click="setQuickKembali(2)"
                                                    class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-[#CCFBF1] hover:text-[#0F766E] text-[10px] text-gray-600 font-semibold transition-colors cursor-pointer">
                                                +2 Jam
                                            </button>
                                            <button type="button"
                                                    @click="setQuickKembali(4)"
                                                    class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-[#CCFBF1] hover:text-[#0F766E] text-[10px] text-gray-600 font-semibold transition-colors cursor-pointer">
                                                +4 Jam
                                            </button>
                                            <button type="button"
                                                    @click="setQuickKembali(0, getJamBukaHour())"
                                                    class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-[#CCFBF1] hover:text-[#0F766E] text-[10px] text-gray-600 font-semibold transition-colors cursor-pointer"
                                                    :title="`Kembalikan besok jam ${sarprasSchedule.jamBuka} WIB`">
                                                <span x-text="`Besok ${sarprasSchedule.jamBuka}`"></span>
                                            </button>
                                            <button type="button"
                                                    @click="setQuickKembali(0, getJamTutupHour())"
                                                    class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-[#CCFBF1] hover:text-[#0F766E] text-[10px] text-gray-600 font-semibold transition-colors cursor-pointer"
                                                    :title="`Kembalikan besok jam ${sarprasSchedule.jamTutup} WIB`">
                                                <span x-text="`Besok ${sarprasSchedule.jamTutup}`"></span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Keperluan / Alasan Peminjaman (Wajib Diisi) -->
                        <div>
                            <label class="block text-xs font-semibold text-[#1F2937] mb-1">
                                Keperluan / Alasan Peminjaman <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alasan_tujuan"
                                      x-ref="alasanTujuanInput"
                                      x-model="alasanTujuan"
                                      rows="2"
                                      required
                                      placeholder="Wajib diisi: contoh Pembelajaran Presentasi di Kelas 8A, Ujian Praktik IPA..."
                                      class="w-full p-3 bg-[#F3F7F6] rounded-xl text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium"></textarea>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex items-center justify-between">
                    <button type="button"
                            @click="handleCancelLoan()"
                            class="h-10 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition-colors cursor-pointer">
                        Batal
                    </button>

                    <button type="button"
                            @click="submitLoanForm()"
                            :disabled="isSubmitting"
                            class="h-10 px-5 rounded-xl text-white font-bold text-xs flex items-center gap-2 transition-all bg-[#0F766E] hover:bg-[#115E59] active:scale-95 cursor-pointer shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="isSubmitting">
                            <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        </template>
                        <span x-text="isSubmitting ? 'Memproses...' : 'Konfirmasi Peminjaman'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- Alpine.js Application Logic -->
<script>
    function peminjamKatalogApp(config) {
        return {
            categoriesData: config.categoriesData || {},
            sarprasSchedule: config.sarprasSchedule || {
                hariLayanan: 'Senin s.d. Jumat',
                jamBuka: '07:00',
                jamTutup: '13:00',
                hariOperasional: [1, 2, 3, 4, 5]
            },
            detailModalOpen: false,
            loanModalOpen: false,
            selectedDetailCategory: null,
            borrowerName: '', // Dikosongkan default agar peminjam menginput sendiri nama peminjam dan wajib diisi
            tanggalPinjam: '',
            tanggalKembali: '',
            minPinjamDatetime: '',
            currentClockText: '',
            alasanTujuan: '',
            selectedCategories: [], // Array of { category_id: int, qty: int }
            selectedAddCategoryId: '',
            isSubmitting: false,

            getJamBukaHour() {
                const parts = (this.sarprasSchedule?.jamBuka || '07:00').split(':');
                return parseInt(parts[0], 10) || 7;
            },

            getJamBukaMinute() {
                const parts = (this.sarprasSchedule?.jamBuka || '07:00').split(':');
                return parseInt(parts[1], 10) || 0;
            },

            getJamTutupHour() {
                const parts = (this.sarprasSchedule?.jamTutup || '13:00').split(':');
                return parseInt(parts[0], 10) || 13;
            },

            getJamTutupMinute() {
                const parts = (this.sarprasSchedule?.jamTutup || '13:00').split(':');
                return parseInt(parts[1], 10) || 0;
            },

            isOperationalDay(d) {
                if (!d || isNaN(d.getTime())) return true;
                const jsDay = d.getDay(); // 0: Sun, 1: Mon, ..., 6: Sat
                const isoDay = jsDay === 0 ? 7 : jsDay;
                const allowed = Array.isArray(this.sarprasSchedule?.hariOperasional)
                    ? this.sarprasSchedule.hariOperasional.map(Number)
                    : [1, 2, 3, 4, 5];
                return allowed.includes(isoDay);
            },

            getNextValidSarprasDate(baseDate = new Date()) {
                let d = new Date(baseDate);
                let count = 0;
                while (!this.isOperationalDay(d) && count < 14) {
                    d.setDate(d.getDate() + 1);
                    count++;
                }
                return d;
            },

            getNextValidSarprasTime(baseDate = new Date()) {
                let d = this.getNextValidSarprasDate(baseDate);
                const curVal = d.getHours() * 60 + d.getMinutes();
                const bukaVal = this.getJamBukaHour() * 60 + this.getJamBukaMinute();
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();

                if (curVal < bukaVal) {
                    d.setHours(this.getJamBukaHour(), this.getJamBukaMinute(), 0, 0);
                } else if (curVal >= tutupVal) {
                    d.setDate(d.getDate() + 1);
                    d = this.getNextValidSarprasDate(d);
                    d.setHours(this.getJamBukaHour(), this.getJamBukaMinute(), 0, 0);
                } else {
                    d.setSeconds(0, 0);
                }
                return d;
            },

            _checkSarprasHours(datetimeStr, label = 'peminjaman') {
                if (!datetimeStr) return null;
                const d = new Date(datetimeStr);
                if (isNaN(d.getTime())) return null;

                if (!this.isOperationalDay(d)) {
                    return `Kantor Sarpras tutup pada hari tersebut. Jadwal ${label} harus pada hari layanan (${this.sarprasSchedule.hariLayanan}).`;
                }

                const curVal = d.getHours() * 60 + d.getMinutes();
                const bukaVal = this.getJamBukaHour() * 60 + this.getJamBukaMinute();
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();

                if (curVal < bukaVal || curVal > tutupVal) {
                    const pad = (n) => String(n).padStart(2, '0');
                    const timeStr = `${pad(d.getHours())}:${pad(d.getMinutes())}`;
                    return `Jam ${label} (${timeStr} WIB) berada di luar jam operasional Sarpras (${this.sarprasSchedule.jamBuka} – ${this.sarprasSchedule.jamTutup} WIB).`;
                }
                return null;
            },

            openLoanModal() {
                this.updateRealtimeClock(false);
                const validNow = this.getNextValidSarprasTime(new Date());
                this.tanggalPinjam = this.formatDatetime(validNow);
                let validKembali = new Date(validNow.getTime() + 2 * 60 * 60 * 1000);
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                const kembaliVal = validKembali.getHours() * 60 + validKembali.getMinutes();
                if (validKembali.toDateString() === validNow.toDateString() && kembaliVal > tutupVal) {
                    validKembali.setHours(this.getJamTutupHour(), this.getJamTutupMinute(), 0, 0);
                }
                this.tanggalKembali = this.formatDatetime(validKembali);
                this.loanModalOpen = true;
            },

            submitLoanForm() {
                if (this.totalBorrowedCount === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Pilih Barang',
                            text: 'Silakan tentukan minimal 1 kategori barang yang ingin dipinjam.',
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '22rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert('Silakan tentukan minimal 1 kategori barang yang ingin dipinjam.');
                        this.loanModalOpen = true;
                    }
                    return;
                }

                if (!this.borrowerName || !this.borrowerName.trim()) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Nama Peminjam Wajib Diisi',
                            text: 'Silakan masukkan nama lengkap peminjam terlebih dahulu.',
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '22rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                            setTimeout(() => {
                                if (this.$refs.borrowerNameInput) {
                                    this.$refs.borrowerNameInput.focus();
                                }
                            }, 50);
                        });
                    } else {
                        alert('Silakan masukkan nama lengkap peminjam terlebih dahulu.');
                        this.loanModalOpen = true;
                        if (this.$refs.borrowerNameInput) {
                            this.$refs.borrowerNameInput.focus();
                        }
                    }
                    return;
                }

                if (!this.alasanTujuan || !this.alasanTujuan.trim()) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Alasan Peminjaman Wajib Diisi',
                            text: 'Silakan masukkan keperluan atau alasan peminjaman terlebih dahulu.',
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '22rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                            setTimeout(() => {
                                if (this.$refs.alasanTujuanInput) {
                                    this.$refs.alasanTujuanInput.focus();
                                }
                            }, 50);
                        });
                    } else {
                        alert('Silakan masukkan keperluan atau alasan peminjaman terlebih dahulu.');
                        this.loanModalOpen = true;
                        if (this.$refs.alasanTujuanInput) {
                            this.$refs.alasanTujuanInput.focus();
                        }
                    }
                    return;
                }

                if (this.isPinjamInPast) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Waktu Tidak Valid',
                            text: 'Tanggal atau jam peminjaman tidak boleh di waktu yang sudah lewat.',
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '22rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert('Tanggal atau jam peminjaman tidak boleh di waktu yang sudah lewat.');
                        this.loanModalOpen = true;
                    }
                    return;
                }

                // Validasi jam operasional Sarpras (07:00 - 13:00 WIB, Senin - Jumat) pada submit
                const sarprasError = this._checkSarprasHours(this.tanggalPinjam, 'peminjaman') || this._checkSarprasHours(this.tanggalKembali, 'pengembalian');
                if (sarprasError) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Di Luar Jam Operasional',
                            text: sarprasError,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Sesuaikan Jam',
                            width: '24rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(sarprasError);
                        this.loanModalOpen = true;
                    }
                    return;
                }

                if (this.isKembaliInvalid) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Waktu Pengembalian Tidak Valid',
                            text: this.kembaliErrorMessage || 'Batas waktu pengembalian tidak valid.',
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '22rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(this.kembaliErrorMessage || 'Batas waktu pengembalian tidak valid.');
                        this.loanModalOpen = true;
                    }
                    return;
                }

                // Alert Konfirmasi Yakin/Tidak Sebelum Meminjam
                const borrowerName = this.borrowerName.trim();
                const totalUnits = this.totalBorrowedCount;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Konfirmasi Peminjaman',
                        text: `Apakah Anda yakin ingin meminjam ${totalUnits} unit barang atas nama '${borrowerName}'?`,
                        showCancelButton: true,
                        confirmButtonColor: '#0F766E',
                        cancelButtonColor: '#9CA3AF',
                        confirmButtonText: 'Ya, Konfirmasi',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        width: '24rem',
                        customClass: {
                            popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                            title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                            htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isSubmitting = true;
                            this.$refs.loanForm.submit();
                        } else {
                            // Tetap berada di pop-up form peminjaman jika Batal
                            this.loanModalOpen = true;
                        }
                    });
                } else {
                    if (confirm(`Apakah Anda yakin ingin meminjam ${totalUnits} unit barang atas nama '${borrowerName}'?`)) {
                        this.isSubmitting = true;
                        this.$refs.loanForm.submit();
                    } else {
                        this.loanModalOpen = true;
                    }
                }
            },

            handleCancelLoan() {
                const hasData = (this.selectedCategories && this.selectedCategories.length > 0) ||
                                (this.borrowerName && this.borrowerName.trim().length > 0) ||
                                (this.alasanTujuan && this.alasanTujuan.trim().length > 0);

                if (!hasData) {
                    this.loanModalOpen = false;
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Batalkan Peminjaman?',
                        text: 'Apakah Anda ingin menyimpan data barang yang sudah dipilih dan diisi, atau menghapus seluruh isian?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonColor: '#0F766E',
                        denyButtonColor: '#DC2626',
                        cancelButtonColor: '#9CA3AF',
                        confirmButtonText: 'Simpan Data',
                        denyButtonText: 'Hapus Data',
                        cancelButtonText: 'Kembali',
                        reverseButtons: true,
                        width: '26rem',
                        customClass: {
                            popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                            title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                            htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                            denyButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white shadow-sm mr-2',
                            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Simpan: formulir ditutup, isian dan pilihan barang tetap tersimpan di form
                            this.loanModalOpen = false;
                        } else if (result.isDenied) {
                            // Hapus: kosongkan pilihan barang dan seluruh isian form, lalu tutup
                            this.resetLoanFormData();
                            this.loanModalOpen = false;
                        } else {
                            // Kembali: tetap di modal form peminjaman
                            this.loanModalOpen = true;
                        }
                    });
                } else {
                    if (confirm('Simpan pilihan dan isian barang? Klik OK untuk simpan, atau Batal untuk menghapus isian.')) {
                        this.loanModalOpen = false;
                    } else {
                        this.resetLoanFormData();
                        this.loanModalOpen = false;
                    }
                }
            },

            resetLoanFormData() {
                this.selectedCategories = [];
                this.borrowerName = '';
                this.alasanTujuan = '';
                this.selectedAddCategoryId = '';
                this.updateRealtimeClock(true);
            },

            init() {
                this.updateRealtimeClock(true);
                // Real-time ticking setiap 10 detik agar min datetime selalu akurat
                setInterval(() => {
                    this.updateRealtimeClock(false);
                }, 10000);
            },

            formatDatetime(d) {
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            },

            updateRealtimeClock(isInitial = false) {
                const now = new Date();
                this.minPinjamDatetime = this.formatDatetime(now);

                const pad = (n) => String(n).padStart(2, '0');
                this.currentClockText = `${pad(now.getHours())}:${pad(now.getMinutes())} WIB`;

                if (isInitial || !this.tanggalPinjam) {
                    this.tanggalPinjam = this.minPinjamDatetime;
                    const later = new Date(now.getTime() + 3 * 60 * 60 * 1000);
                    this.tanggalKembali = this.formatDatetime(later);
                }
            },

            openCategoryDetail(categoryId) {
                const catId = parseInt(categoryId);
                this.selectedDetailCategory = this.categoriesData[catId] || null;
                this.detailModalOpen = true;
            },

            openLoanModalWithCategory(categoryId) {
                this.updateRealtimeClock(false);
                const validNow = this.getNextValidSarprasTime(new Date());
                this.tanggalPinjam = this.formatDatetime(validNow);
                let validKembali = new Date(validNow.getTime() + 2 * 60 * 60 * 1000);
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                const kembaliVal = validKembali.getHours() * 60 + validKembali.getMinutes();
                if (validKembali.toDateString() === validNow.toDateString() && kembaliVal > tutupVal) {
                    validKembali.setHours(this.getJamTutupHour(), this.getJamTutupMinute(), 0, 0);
                }
                this.tanggalKembali = this.formatDatetime(validKembali);
                this.addCategory(categoryId);
                this.loanModalOpen = true;
            },

            onPinjamChange() {
                if (!this.tanggalPinjam) return;

                const pinjamDate = new Date(this.tanggalPinjam);
                const curVal = pinjamDate.getHours() * 60 + pinjamDate.getMinutes();
                const bukaVal = this.getJamBukaHour() * 60 + this.getJamBukaMinute();
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                const datePart = this.tanggalPinjam.split('T')[0];

                if (curVal < bukaVal || curVal > tutupVal) {
                    if (curVal < bukaVal) {
                        this.tanggalPinjam = `${datePart}T${this.sarprasSchedule.jamBuka}`;
                    } else {
                        this.tanggalPinjam = `${datePart}T${this.sarprasSchedule.jamTutup}`;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Di Luar Jam Operasional',
                            text: `Peminjaman hanya dapat dilakukan pada jam operasional Sarpras pukul ${this.sarprasSchedule.jamBuka} – ${this.sarprasSchedule.jamTutup} WIB (${this.sarprasSchedule.hariLayanan}). Jam telah disesuaikan otomatis.`,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '24rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(`Peminjaman hanya dapat dilakukan pada jam operasional Sarpras pukul ${this.sarprasSchedule.jamBuka} – ${this.sarprasSchedule.jamTutup} WIB.`);
                    }
                }

                if (!this.isOperationalDay(pinjamDate)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hari Tutup / Libur',
                            text: `Kantor Sarpras tutup pada hari tersebut. Jadwal peminjaman harus pada hari layanan (${this.sarprasSchedule.hariLayanan}).`,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '24rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(`Kantor Sarpras tutup pada hari tersebut. Silakan pilih hari layanan (${this.sarprasSchedule.hariLayanan}).`);
                    }
                }

                const pinjamTime = new Date(this.tanggalPinjam).getTime();
                const kembaliTime = this.tanggalKembali ? new Date(this.tanggalKembali).getTime() : 0;

                // Jika batas kembali belum ada atau berada di bawah/sama dengan waktu pinjam, mundurkan otomatis +2 jam
                if (!this.tanggalKembali || kembaliTime <= pinjamTime) {
                    let newKembali = new Date(pinjamTime + 2 * 60 * 60 * 1000);
                    const newTutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                    const newKembaliVal = newKembali.getHours() * 60 + newKembali.getMinutes();
                    if (newKembali.toDateString() === pinjamDate.toDateString() && newKembaliVal > newTutupVal) {
                        newKembali.setHours(this.getJamTutupHour(), this.getJamTutupMinute(), 0, 0);
                    }
                    this.tanggalKembali = this.formatDatetime(newKembali);
                }
            },

            onKembaliChange() {
                if (!this.tanggalKembali) return;

                const kembaliDate = new Date(this.tanggalKembali);
                const curVal = kembaliDate.getHours() * 60 + kembaliDate.getMinutes();
                const bukaVal = this.getJamBukaHour() * 60 + this.getJamBukaMinute();
                const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                const datePart = this.tanggalKembali.split('T')[0];

                if (curVal < bukaVal || curVal > tutupVal) {
                    if (curVal < bukaVal) {
                        this.tanggalKembali = `${datePart}T${this.sarprasSchedule.jamBuka}`;
                    } else {
                        this.tanggalKembali = `${datePart}T${this.sarprasSchedule.jamTutup}`;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Di Luar Jam Operasional',
                            text: `Pengembalian hanya dapat dilakukan pada jam operasional Sarpras pukul ${this.sarprasSchedule.jamBuka} – ${this.sarprasSchedule.jamTutup} WIB (${this.sarprasSchedule.hariLayanan}). Jam telah disesuaikan otomatis.`,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '24rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(`Pengembalian hanya dapat dilakukan pada jam operasional Sarpras pukul ${this.sarprasSchedule.jamBuka} – ${this.sarprasSchedule.jamTutup} WIB.`);
                    }
                }

                if (!this.isOperationalDay(kembaliDate)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hari Tutup / Libur',
                            text: `Kantor Sarpras tutup pada hari tersebut. Jadwal pengembalian harus pada hari layanan (${this.sarprasSchedule.hariLayanan}).`,
                            confirmButtonColor: '#0F766E',
                            confirmButtonText: 'Mengerti',
                            width: '24rem',
                            customClass: {
                                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                            }
                        }).then(() => {
                            this.loanModalOpen = true;
                        });
                    } else {
                        alert(`Kantor Sarpras tutup pada hari tersebut. Silakan pilih hari layanan (${this.sarprasSchedule.hariLayanan}) untuk pengembalian.`);
                    }
                }
            },

            resetPinjamToNow() {
                const now = new Date();
                this.updateRealtimeClock(false);
                const validNow = this.getNextValidSarprasTime(now);
                this.tanggalPinjam = this.formatDatetime(validNow);
                this.onPinjamChange();
            },

            setQuickKembali(hoursToAdd, targetNextDayHour = null) {
                if (!this.tanggalPinjam) {
                    this.resetPinjamToNow();
                }
                const pinjamDate = new Date(this.tanggalPinjam);

                if (targetNextDayHour !== null) {
                    // Pilihan cepat besok hari pada jam tertentu
                    const targetDate = new Date(pinjamDate);
                    targetDate.setDate(targetDate.getDate() + 1);
                    const nextValidDate = this.getNextValidSarprasDate(targetDate);
                    const minute = (targetNextDayHour === this.getJamTutupHour()) ? this.getJamTutupMinute() : this.getJamBukaMinute();
                    nextValidDate.setHours(targetNextDayHour, minute, 0, 0);
                    this.tanggalKembali = this.formatDatetime(nextValidDate);
                } else {
                    let targetDate = new Date(pinjamDate.getTime() + hoursToAdd * 60 * 60 * 1000);
                    const tutupVal = this.getJamTutupHour() * 60 + this.getJamTutupMinute();
                    const targetVal = targetDate.getHours() * 60 + targetDate.getMinutes();

                    // Jika masih hari yang sama tapi melebihi jam tutup, clamp ke jam tutup
                    if (targetDate.toDateString() === pinjamDate.toDateString() && targetVal > tutupVal) {
                        targetDate.setHours(this.getJamTutupHour(), this.getJamTutupMinute(), 0, 0);
                    }
                    this.tanggalKembali = this.formatDatetime(targetDate);
                }
            },

            get isPinjamInPast() {
                if (!this.tanggalPinjam) return false;
                const pinjamTime = new Date(this.tanggalPinjam).getTime();
                // Toleransi 15 menit agar waktu pengisian form & menit berjalan tidak dianggap masa lampau
                const nowTime = new Date().getTime() - (15 * 60 * 1000);
                return pinjamTime < nowTime;
            },

            get minKembaliDatetime() {
                return this.tanggalPinjam || this.minPinjamDatetime;
            },

            get kembaliErrorMessage() {
                if (!this.tanggalKembali || !this.tanggalPinjam) return null;
                const pinjamTime = new Date(this.tanggalPinjam).getTime();
                const kembaliTime = new Date(this.tanggalKembali).getTime();

                if (kembaliTime <= pinjamTime) {
                    const pinjamDateStr = this.tanggalPinjam.split('T')[0];
                    const kembaliDateStr = this.tanggalKembali.split('T')[0];

                    if (kembaliDateStr < pinjamDateStr) {
                        return 'Batas tanggal pengembalian tidak boleh sebelum tanggal peminjaman.';
                    } else {
                        return 'Batas jam pengembalian harus setelah jam peminjaman pada hari yang sama.';
                    }
                }
                return null;
            },

            get isKembaliInvalid() {
                return this.kembaliErrorMessage !== null;
            },

            addCategory(categoryId) {
                const catId = parseInt(categoryId);
                if (!catId || !this.categoriesData[catId]) return;
                const existing = this.selectedCategories.find(c => c.category_id === catId);
                if (!existing) {
                    const availableStock = this.categoriesData[catId].stok_tersedia;
                    if (availableStock > 0) {
                        this.selectedCategories.push({
                            category_id: catId,
                            qty: 1
                        });
                    }
                }
                this.selectedAddCategoryId = '';
            },

            removeCategory(categoryId) {
                const catId = parseInt(categoryId);
                this.selectedCategories = this.selectedCategories.filter(c => c.category_id !== catId);
            },

            incrementQty(categoryId) {
                const catId = parseInt(categoryId);
                const item = this.selectedCategories.find(c => c.category_id === catId);
                if (!item) return;
                const maxStock = this.categoriesData[catId]?.stok_tersedia || 0;
                if (item.qty < maxStock) {
                    item.qty++;
                }
            },

            decrementQty(categoryId) {
                const catId = parseInt(categoryId);
                const item = this.selectedCategories.find(c => c.category_id === catId);
                if (!item) return;
                if (item.qty > 1) {
                    item.qty--;
                }
            },

            getAllocatedUnits(categoryId, qty) {
                const catId = parseInt(categoryId);
                const cat = this.categoriesData[catId];
                if (!cat || !cat.available_units) return [];
                return cat.available_units.slice(0, Math.min(qty, cat.available_units.length));
            },

            get totalBorrowedCount() {
                return this.selectedCategories.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
            },

            get availableCategoriesToAdd() {
                const chosenIds = this.selectedCategories.map(c => c.category_id);
                return Object.values(this.categoriesData).filter(c => c.stok_tersedia > 0 && !chosenIds.includes(c.id));
            }
        };
    }
</script>
@endsection
