@extends('layouts.admin')

@section('title', 'Data Inventaris - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Page Header (Breadcrumb removed as per revision requirement) -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Data Inventaris</h1>
            <p class="text-xs text-[#4B5563] mt-0.5 max-w-3xl">
                Kelola seluruh unit fisik sarana dan prasarana sekolah, nomor register aset, status ketersediaan, serta lokasi penempatan untuk operasional dan peminjaman SMPN 3 Babakancikao.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventaris.create') }}"
               class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Tambah Inventaris</span>
            </a>
        </div>
    </div>

    <!-- Unified Card: Search, Filter, Actions & Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] flex flex-col relative">
        <!-- Card Header: Search & Filter Toolbar -->
        <div class="p-4 sm:p-5 border-b border-[#D9E4E2] bg-white">
            <form action="{{ route('admin.inventaris.index') }}" method="GET" class="flex flex-col gap-3">
                <button type="submit" class="hidden"></button>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Input: Kode Barang, Nama Barang, Nomor Register (lg: 3 cols) -->
                    <div class="relative lg:col-span-3">
                        <button type="submit"
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#6B7280] hover:text-[#0F766E] transition-colors cursor-pointer"
                                title="Klik untuk mencari">
                            <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                        </button>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kode, nama, no regist..."
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); this.form.submit(); }"
                               class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        @if(request('search'))
                            <a href="{{ route('admin.inventaris.index', request()->except('search')) }}"
                               class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                               title="Hapus kata kunci">
                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                            </a>
                        @endif
                    </div>

                    <!-- Filter Kategori -->
                    <div class="lg:col-span-2">
                        <select name="category_id"
                                class="select2-filter w-full">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Ruangan -->
                    <div class="lg:col-span-2">
                        <select name="room_id"
                                class="select2-filter w-full">
                            <option value="">Semua Ruangan</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="lg:col-span-2">
                        <select name="status"
                                class="select2-filter w-full">
                            <option value="">Semua Status Unit</option>
                            <option value="baik" {{ request('status') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="perlu_perbaikan" {{ request('status') == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                            <option value="hilang" {{ request('status') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>

                    <!-- Filter Tahun (Modern Year Range Picker Component) -->
                    <div class="relative lg:col-span-3 flex items-center gap-2"
                         x-data="{
                             open: false,
                             inputValue: '{{ request('tahun') ?? '' }}',
                             todayYear: {{ date('Y') }},
                             startYear: null,
                             endYear: null,
                             hoverYear: null,
                             pickingStep: 1,
                             decadeStart: {{ date('Y') - (date('Y') % 10) }},

                             init() {
                                 if (this.inputValue) {
                                     const parts = this.inputValue.split(/\s*[-—–to]+\s*/);
                                     if (parts.length >= 2 && parts[0] && parts[1]) {
                                         this.startYear = parseInt(parts[0]);
                                         this.endYear = parseInt(parts[1]);
                                         this.decadeStart = Math.floor(this.startYear / 10) * 10;
                                     } else if (parts.length === 1 && parts[0]) {
                                         this.startYear = parseInt(parts[0]);
                                         this.endYear = parseInt(parts[0]);
                                         this.decadeStart = Math.floor(this.startYear / 10) * 10;
                                     }
                                 }
                             },

                             get decadeEnd() {
                                 return this.decadeStart + 9;
                             },

                             get activeStart() {
                                 if (this.pickingStep === 2 && this.startYear !== null && this.hoverYear !== null) {
                                     return Math.min(this.startYear, this.hoverYear);
                                 }
                                 if (this.startYear !== null && this.endYear !== null) {
                                     return Math.min(this.startYear, this.endYear);
                                 }
                                 return this.startYear;
                             },

                             get activeEnd() {
                                 if (this.pickingStep === 2 && this.startYear !== null && this.hoverYear !== null) {
                                     return Math.max(this.startYear, this.hoverYear);
                                 }
                                 if (this.startYear !== null && this.endYear !== null) {
                                     return Math.max(this.startYear, this.endYear);
                                 }
                                 return this.endYear;
                             },

                             get stepGuide() {
                                 if (this.pickingStep === 2) {
                                     return 'Pilih tahun akhir untuk menyelesaikan rentang...';
                                 }
                                 if (this.startYear && this.endYear) {
                                     return this.startYear === this.endYear
                                         ? 'Tahun terpilih: ' + this.startYear
                                         : 'Rentang: ' + this.startYear + ' — ' + this.endYear;
                                 }
                                 return 'Pilih tahun awal rentang';
                             },

                             get years() {
                                 const list = [];
                                 for (let i = -1; i <= 10; i++) {
                                     const y = this.decadeStart + i;
                                     const aStart = this.activeStart;
                                     const aEnd = this.activeEnd;

                                     const isStart = aStart !== null && y === aStart;
                                     const isEnd = aEnd !== null && y === aEnd;
                                     const isInRange = aStart !== null && aEnd !== null && y > aStart && y < aEnd;
                                     const isSingle = aStart !== null && aStart === aEnd && y === aStart;

                                     list.push({
                                         year: y,
                                         isCurrentDecade: i >= 0 && i <= 9,
                                         isToday: y === this.todayYear,
                                         isStart: isStart,
                                         isEnd: isEnd,
                                         isInRange: isInRange,
                                         isSingle: isSingle,
                                         isSelected: isStart || isEnd || isInRange
                                     });
                                 }
                                 return list;
                             },

                             prevDecade() {
                                 this.decadeStart -= 10;
                             },

                             nextDecade() {
                                 this.decadeStart += 10;
                             },

                             onYearClick(y) {
                                 if (this.pickingStep === 1 || !this.startYear || (this.startYear && this.endYear && this.pickingStep !== 2)) {
                                     // Langkah 1: Pilih tahun awal
                                     this.startYear = y;
                                     this.endYear = null;
                                     this.hoverYear = y;
                                     this.pickingStep = 2;
                                 } else if (this.pickingStep === 2) {
                                     // Langkah 2: Pilih tahun akhir & selesaikan rentang
                                     const finalStart = Math.min(this.startYear, y);
                                     const finalEnd = Math.max(this.startYear, y);
                                     this.startYear = finalStart;
                                     this.endYear = finalEnd;
                                     this.pickingStep = 1;
                                     this.hoverYear = null;

                                     const val = (finalStart === finalEnd) ? String(finalStart) : `${finalStart} - ${finalEnd}`;
                                     this.inputValue = val;
                                     this.open = false;
                                     this.$refs.yearInput.value = val;
                                     this.$refs.yearInput.closest('form').submit();
                                 }
                             },

                             onYearHover(y) {
                                 if (this.pickingStep === 2) {
                                     this.hoverYear = y;
                                 }
                             },

                             selectToday() {
                                 this.startYear = this.todayYear;
                                 this.endYear = this.todayYear;
                                 this.pickingStep = 1;
                                 this.hoverYear = null;
                                 this.inputValue = String(this.todayYear);
                                 this.open = false;
                                 this.$refs.yearInput.value = this.todayYear;
                                 this.$refs.yearInput.closest('form').submit();
                             }
                         }"
                         @click.outside="open = false; hoverYear = null"
                         @keydown.escape.window="open = false; hoverYear = null">

                        <!-- Input Container -->
                        <div class="relative flex-1 min-w-0">
                            <input type="text"
                                   x-ref="yearInput"
                                   name="tahun"
                                   id="filter_tahun"
                                   :value="inputValue"
                                   placeholder="Pilih Tahun"
                                   readonly
                                   @click="open = !open"
                                   class="w-full h-10 pl-3 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all cursor-pointer font-medium select-none truncate">

                            <!-- Calendar Icon (Right Side) -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#6B7280]">
                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            </div>

                            <!-- Clear Button (when year is selected) -->
                            @if(request('tahun'))
                                <a href="{{ route('admin.inventaris.index', request()->except('tahun')) }}"
                                   class="absolute inset-y-0 right-8 pr-1 flex items-center text-[#9CA3AF] hover:text-[#DC2626] transition-colors"
                                   title="Hapus filter tahun"
                                   @click.stop>
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                </a>
                            @endif
                        </div>

                        <!-- Reset All Filters Button -->
                        @if(request()->hasAny(['search', 'category_id', 'room_id', 'status', 'tahun', 'sort']))
                            <a href="{{ route('admin.inventaris.index') }}"
                               class="h-10 px-3 rounded-lg bg-white border border-[#D9E4E2] text-xs text-[#4B5563] hover:text-[#DC2626] hover:bg-[#FEE2E2]/30 flex items-center justify-center transition-colors shrink-0"
                               title="Reset semua filter">
                                <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                            </a>
                        @endif

                        <!-- Year Range Picker Dropdown Panel -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 top-full mt-1.5 w-64 sm:w-72 bg-white rounded-xl shadow-xl border border-[#D9E4E2] p-3.5 z-50 font-sans"
                             style="display: none;"
                             @click.stop
                             @mouseleave="hoverYear = null">

                            <!-- Header: 10-Year Range & Navigation Arrows -->
                            <div class="flex items-center justify-between pb-2 mb-1.5 border-b border-[#D9E4E2]">
                                <button type="button"
                                        @click.stop="prevDecade()"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-[#6B7280] hover:text-[#0F766E] hover:bg-[#F0FDFA] transition-colors cursor-pointer"
                                        title="10 Tahun Sebelumnya">
                                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                                </button>

                                <span x-text="decadeStart + ' - ' + decadeEnd"
                                      class="text-xs font-bold text-[#1F2937] tracking-wide select-none">
                                </span>

                                <button type="button"
                                        @click.stop="nextDecade()"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-[#6B7280] hover:text-[#0F766E] hover:bg-[#F0FDFA] transition-colors cursor-pointer"
                                        title="10 Tahun Berikutnya">
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                </button>
                            </div>

                            <!-- Guidance Subtitle (Step Indicator) -->
                            <div class="text-[11px] text-[#0F766E] font-medium text-center py-1 select-none flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span x-text="stepGuide"></span>
                            </div>

                            <!-- Years Grid (4 Columns) -->
                            <div class="grid grid-cols-4 gap-1.5 my-2">
                                <template x-for="item in years" :key="item.year">
                                    <button type="button"
                                            @click.stop="onYearClick(item.year)"
                                            @mouseenter="onYearHover(item.year)"
                                            class="h-9 text-xs font-medium transition-all flex items-center justify-center cursor-pointer select-none"
                                            :class="{
                                                'bg-[#0F766E] text-white font-bold rounded-lg shadow-xs ring-2 ring-[#0F766E]/20': item.isStart || item.isEnd || item.isSingle,
                                                'bg-[#CCFBF1] text-[#0F766E] font-semibold rounded-none': item.isInRange,
                                                'text-[#0F766E] font-bold border border-[#0F766E]/40 bg-[#F0FDFA] rounded-lg': item.isToday && !item.isSelected,
                                                'text-[#1F2937] hover:bg-[#F3F7F6] hover:text-[#0F766E] rounded-lg': !item.isSelected && !item.isToday && item.isCurrentDecade,
                                                'text-[#9CA3AF] hover:bg-[#F3F7F6] rounded-lg': !item.isSelected && !item.isToday && !item.isCurrentDecade
                                            }">
                                        <span x-text="item.year"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Footer: Today Button -->
                            <div class="pt-2 border-t border-[#D9E4E2] flex items-center justify-center">
                                <button type="button"
                                        @click.stop="selectToday()"
                                        class="w-full py-1.5 px-3 rounded-lg text-xs font-semibold text-[#0F766E] bg-[#F0FDFA] hover:bg-[#CCFBF1]/50 border border-[#CCFBF1] transition-colors text-center cursor-pointer"
                                        title="Pilih tahun saat ini">
                                    Tahun Ini ({{ date('Y') }})
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>

        <!-- Table Content (Responsive Horizontal Scroll) -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4 w-36">Kode Barang</th>
                        <th class="py-3.5 px-4 w-16 text-center">Foto</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Nama Barang (Unit)</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Kategori (Nama Barang)</th>
                        <th class="py-3.5 px-4 min-w-[140px]">Ruangan</th>
                        <th class="py-3.5 px-4 text-center w-36">Status Unit</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9E4E2] text-xs text-[#1F2937]">
                    @forelse ($inventories as $inv)
                        @php
                            $statusUnit = strtolower($inv->status ?? 'baik');
                        @endphp
                        <tr class="hover:bg-[#F8FBFA] transition-colors">
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center text-[#6B7280] font-medium">
                                {{ $loop->iteration + ($inventories->currentPage() - 1) * $inventories->perPage() }}
                            </td>

                            <!-- Kode Barang -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded bg-[#F3F7F6] font-mono text-[11px] font-bold text-[#1F2937] border border-[#D9E4E2] inline-block">
                                    {{ $inv->kode_barang }}
                                </span>
                            </td>

                            <!-- Foto (Thumbnail 40x40 per DESIGN.md 6.7) -->
                            <td class="py-3.5 px-4 text-center">
                                @if($inv->foto_url)
                                    <img src="{{ $inv->foto_url }}"
                                         alt="{{ $inv->nama_barang }}"
                                         class="w-10 h-10 object-cover rounded-md border border-[#D9E4E2] mx-auto shadow-2xs">
                                @else
                                    <div class="w-10 h-10 rounded-md bg-[#F3F7F6] border border-[#D9E4E2] flex items-center justify-center text-[#9CA3AF] mx-auto" title="Belum ada foto">
                                        <span class="material-symbols-outlined text-[20px]">image_not_supported</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Nama Barang & Detail Singkat -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1F2937] text-xs hover:text-[#0F766E] transition-colors">
                                    <a href="{{ route('admin.inventaris.show', $inv->id) }}">
                                        {{ $inv->nama_barang }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-[#6B7280] mt-0.5">
                                    @if($inv->merk_type)
                                        <span>{{ $inv->merk_type }}</span>
                                    @endif
                                    @if($inv->nomor_register)
                                        @if($inv->merk_type) <span class="text-[#9CA3AF]">•</span> @endif
                                        <span>Reg: {{ $inv->nomor_register }}</span>
                                    @endif
                                    @if($inv->tahun_pembelian)
                                        <span class="text-[#9CA3AF]">•</span>
                                        <span>Thn: {{ $inv->tahun_pembelian }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kategori (Text bersih tanpa icon dekoratif) -->
                            <td class="py-3.5 px-4 text-[#1F2937] font-medium">
                                {{ $inv->category->nama_kategori ?? '-' }}
                            </td>

                            <!-- Ruangan (Text bersih tanpa icon dekoratif) -->
                            <td class="py-3.5 px-4 text-[#4B5563]">
                                {{ $inv->room->nama_ruangan ?? '-' }}
                            </td>

                            <!-- Status Unit (Kondisi Fisik: Baik, Rusak, Perlu Perbaikan, Hilang) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($statusUnit === 'rusak')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEE2E2] text-[#991B1B]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] mr-1.5"></span>
                                            Rusak
                                        </span>
                                    @elseif($statusUnit === 'perlu_perbaikan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF3C7] text-[#92400E]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] mr-1.5"></span>
                                            Perlu Perbaikan
                                        </span>
                                    @elseif($statusUnit === 'hilang')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#F3F4F6] text-[#374151]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#6B7280] mr-1.5"></span>
                                            Hilang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A] mr-1.5"></span>
                                            Baik
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi: Detail, Edit, Hapus -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    <!-- Tombol Detail -->
                                    <a href="{{ route('admin.inventaris.show', $inv->id) }}"
                                       class="w-8 h-8 rounded-lg hover:bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center transition-colors focus:outline-none"
                                       title="Lihat Detail Barang">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.inventaris.edit', $inv->id) }}"
                                       class="w-8 h-8 rounded-lg hover:bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center transition-colors focus:outline-none"
                                       title="Ubah Data Inventaris">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <button type="button"
                                            onclick="handleDeleteInventory({{ $inv->id }}, '{{ addslashes($inv->nama_barang) }}', '{{ addslashes($inv->kode_barang) }}', {{ $inv->is_currently_borrowed ? 'true' : 'false' }})"
                                            class="w-8 h-8 rounded-lg hover:bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center transition-colors focus:outline-none"
                                            title="Hapus Inventaris">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#6B7280] mb-3">
                                        <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-[#1F2937]">Tidak ada data inventaris ditemukan</h3>
                                    <p class="text-xs text-[#6B7280] mt-1 max-w-sm">
                                        @if(request()->hasAny(['search', 'category_id', 'room_id', 'status', 'date_range', 'tahun']))
                                            Kombinasi pencarian atau filter yang Anda pilih tidak cocok dengan unit inventaris manapun.
                                        @else
                                            Belum ada unit inventaris fisik yang didaftarkan ke dalam sistem.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'category_id', 'room_id', 'status', 'date_range', 'tahun']))
                                        <a href="{{ route('admin.inventaris.index') }}"
                                           class="mt-4 px-4 py-2 rounded-lg bg-[#F3F7F6] border border-[#D9E4E2] text-[#4B5563] text-xs font-semibold hover:bg-white hover:text-[#1F2937] transition-all">
                                            Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.inventaris.create') }}"
                                           class="mt-4 px-4 py-2 rounded-lg bg-[#0F766E] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm hover:bg-[#115E59]">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            <span>Tambah Inventaris Baru</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination (Strictly 15 per page per flow.md & DESIGN.md) -->
        <div class="p-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-semibold text-[#1F2937]">{{ $inventories->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-[#1F2937]">{{ $inventories->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ $inventories->total() }}</span> inventaris (15 per halaman)
            </span>
            <div class="text-xs">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Hidden Form for Delete Action -->
<form id="formDeleteInventory" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    /**
     * Konfirmasi Hapus Inventaris (Mengikuti pola DESIGN.md 2.4, 6.10, flow.md 3.8)
     * - Penolakan tegas bila unit sedang dipinjam
     */
    function handleDeleteInventory(id, name, code, isCurrentlyBorrowed) {
        if (isCurrentlyBorrowed) {
            Swal.fire({
                icon: 'info',
                title: 'Hapus Ditolak',
                text: `Unit barang '${name}' (${code}) sedang dalam status dipinjam. Sesuai aturan sistem, barang yang sedang dipinjam tidak dapat dihapus.`,
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Tutup',
                width: '24rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Hapus inventaris ini?',
            text: `Unit barang '${name}' (${code}) akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`,
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#9CA3AF',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            width: '24rem',
            customClass: {
                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white shadow-sm',
                cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDeleteInventory');
                form.action = `/admin/inventaris/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
