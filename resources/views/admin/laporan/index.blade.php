@extends('layouts.admin')

@section('title', 'Laporan Inventaris Barang')

@push('styles')
<style>
    /* ── Select2 Custom Multi-Select & Single Select Theme ────────── */
    .select2-container--default .select2-selection--multiple {
        background-color: #F3F7F6;
        border: 1px solid #D9E4E2;
        border-radius: 0.5rem;
        min-height: 40px;
        padding: 3px 6px;
        transition: all 0.15s ease-in-out;
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--multiple {
        background-color: #ffffff;
        border-color: #0F766E;
        box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.18);
        outline: none;
    }

    /* Pilihan chip badge yang rapi berbentuk pill modern */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        background-color: #E6FFFA !important;
        border: 1px solid #99F6E4 !important;
        color: #0F766E !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        border-radius: 9999px !important;
        padding: 2px 8px 2px 4px !important;
        margin: 2px 2px 2px 0 !important;
        line-height: 1.3 !important;
        gap: 4px !important;
    }

    /* Tombol silang (remove) chip yang rapi, bulat, presisi dan nyaman diklik */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        position: static !important;
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        border-radius: 9999px !important;
        background-color: #CCFBF1 !important;
        color: #0F766E !important;
        font-size: 13px !important;
        font-weight: bold !important;
        line-height: 1 !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: #FEE2E2 !important;
        color: #EF4444 !important;
        transform: scale(1.15) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding: 0 4px 0 2px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #0F766E !important;
    }

    .select2-container--default .select2-selection--multiple .select2-search__field {
        font-size: 0.75rem;
        font-family: inherit;
        color: #1F2937;
        margin-top: 2px;
    }

    .select2-container--default .select2-selection--single {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    /* ── Tampilan Lembar Kertas Asli (A4 Landscape Paper Sheet) ───── */
    .kib-paper-sheet {
        background-color: #ffffff;
        color: #000000;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #D9E4E2;
        border-radius: 0.375rem;
        margin: 0 auto;
        width: 100%;
        max-width: 1180px; /* A4 Landscape Proportional Width */
        padding: 32px 36px 40px 36px;
        box-sizing: border-box;
    }

    /* ── Official Table KIB B Styling (Landscape) ─────────────────── */
    .kib-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5px;
        line-height: 1.3;
    }
    .kib-table th, .kib-table td {
        border: 1px solid #000000;
        padding: 3.5px 4px;
        vertical-align: middle;
        word-break: break-word;
    }
    .kib-table th {
        background-color: #f3f4f6;
        font-weight: bold;
        text-align: center;
        color: #000000;
    }
    .kib-table tr.col-numbers td {
        background-color: #f9fafb;
        font-weight: bold;
        text-align: center;
        font-size: 8.5px;
        padding: 2px;
    }

    /* ── Print Stylesheet (Landscape, Tanpa URL 127 & Title Header/Footer Browser) ─ */
    @media print {
        @page {
            size: A4 landscape;
            margin: 0; /* Menghilangkan URL 127.0.0.1... dan title bawaan header/footer cetak browser */
        }
        html, body {
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        nav, aside, header, footer, .no-print, .main-navbar {
            display: none !important;
        }
        .md\:pl-64 {
            padding-left: 0 !important;
        }
        main {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .kib-paper-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 10mm 12mm !important; /* Margin fisik dalam kertas A4 Landscape */
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .kib-table {
            font-size: 8pt !important;
            page-break-inside: auto;
        }
        .kib-table thead {
            display: table-header-group;
        }
        .kib-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .kib-signatures {
            page-break-inside: avoid !important;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <!-- PAGE HEADER: Sederhana & Bersih -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] tracking-tight">Laporan Inventaris Barang</h1>
            <p class="text-xs text-[#6B7280] mt-0.5">
                Sesuaikan filter pada bilah satu baris di bawah, lalu cetak atau unduh dokumen KIB B resmi.
            </p>
        </div>
    </div>

    {{-- 1. BILAH FILTER: SATU BARIS SAJA --}}
    <div class="no-print bg-white rounded-xl border border-[#D9E4E2] shadow-xs p-4">
        <form id="reportFilterForm" method="GET" action="{{ route('admin.laporan.index') }}">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-[#0F766E] text-[18px]">tune</span>
                <span class="text-xs font-bold text-[#1F2937] uppercase tracking-wide">Cakupan Data Laporan</span>
            </div>

            <!-- Single Row Grid (6 Kolom pada Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">

                <!-- 1. Format Dokumen Laporan -->
                <div>
                    <label for="filter_format" class="block text-xs font-semibold text-[#374151] mb-1 truncate" title="Format Dokumen Laporan">
                        Format Dokumen Laporan
                    </label>
                    <select name="format" id="filter_format" class="select2-single w-full" onchange="updateDownloadButtonState()">
                        @foreach($formats as $fKey => $fVal)
                            <option value="{{ $fKey }}" {{ request('format', 'pdf') === $fKey ? 'selected' : '' }}>
                                {{ $fVal['label'] }} (.{{ $fVal['ext'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Kategori Barang -->
                <div>
                    <label for="filter_kategori" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Kategori Barang
                    </label>
                    <select name="category_id" id="filter_kategori" class="select2-single w-full">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string)request('category_id') === (string)$category->id ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Lokasi Ruangan -->
                <div>
                    <label for="filter_ruangan" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Lokasi Ruangan
                    </label>
                    <select name="room_id" id="filter_ruangan" class="select2-single w-full">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ (string)request('room_id') === (string)$room->id ? 'selected' : '' }}>
                                {{ $room->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Kondisi / Status Unit (Multi-select) -->
                <div>
                    @php
                        $selectedStatuses = (array) request('status', []);
                    @endphp
                    <label for="filter_status" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Kondisi Barang
                    </label>
                    <select name="status[]" id="filter_status" class="select2-multiple w-full" multiple="multiple" data-placeholder="Semua Kondisi">
                        <option value="baik" {{ in_array('baik', $selectedStatuses) ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ in_array('rusak', $selectedStatuses) ? 'selected' : '' }}>Rusak</option>
                        <option value="perlu perbaikan" {{ in_array('perlu perbaikan', $selectedStatuses) ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="hilang" {{ in_array('hilang', $selectedStatuses) ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>

                <!-- 5. Tahun Perolehan (Year Picker Popover) -->
                <div>
                    <label for="filter_tahun" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Tahun Perolehan
                    </label>
                    <div class="relative w-full"
                         x-data="{
                             open: false,
                             inputValue: '{{ request('tahun', '') }}',
                             todayYear: {{ date('Y') }},
                             decadeStart: Math.floor(({{ request('tahun') ? (int)substr(request('tahun'), 0, 4) : date('Y') }}) / 10) * 10,
                             startYear: {{ request('tahun') && preg_match('/^(\d{4})/', request('tahun'), $m) ? $m[1] : 'null' }},
                             endYear: {{ request('tahun') && preg_match('/(\d{4})$/', request('tahun'), $m2) ? $m2[1] : 'null' }},
                             hoverYear: null,
                             pickingStep: 1,

                             get decadeEnd() { return this.decadeStart + 9; },
                             get activeStart() {
                                 if (this.startYear && this.endYear) return Math.min(this.startYear, this.endYear);
                                 if (this.startYear && !this.endYear && this.hoverYear) return Math.min(this.startYear, this.hoverYear);
                                 return this.startYear;
                             },
                             get activeEnd() {
                                 if (this.startYear && this.endYear) return Math.max(this.startYear, this.endYear);
                                 if (this.startYear && !this.endYear && this.hoverYear) return Math.max(this.startYear, this.hoverYear);
                                 return this.endYear;
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
                             prevDecade() { this.decadeStart -= 10; },
                             nextDecade() { this.decadeStart += 10; },
                             onYearClick(y) {
                                 if (this.pickingStep === 1 || !this.startYear || (this.startYear && this.endYear && this.pickingStep !== 2)) {
                                     this.startYear = y;
                                     this.endYear = null;
                                     this.hoverYear = y;
                                     this.pickingStep = 2;
                                 } else if (this.pickingStep === 2) {
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
                                 }
                             },
                             onYearHover(y) {
                                 if (this.pickingStep === 2) { this.hoverYear = y; }
                             },
                             selectToday() {
                                 this.startYear = this.todayYear;
                                 this.endYear = this.todayYear;
                                 this.pickingStep = 1;
                                 this.hoverYear = null;
                                 this.inputValue = String(this.todayYear);
                                 this.open = false;
                                 this.$refs.yearInput.value = this.todayYear;
                             },
                             resetYear() {
                                 this.startYear = null;
                                 this.endYear = null;
                                 this.hoverYear = null;
                                 this.pickingStep = 1;
                                 this.inputValue = '';
                                 this.$refs.yearInput.value = '';
                             }
                         }"
                         @click.outside="open = false; hoverYear = null"
                         @keydown.escape.window="open = false; hoverYear = null">

                        <div class="relative w-full">
                            <input type="text"
                                   x-ref="yearInput"
                                   name="tahun"
                                   id="filter_tahun"
                                   :value="inputValue"
                                   placeholder="Semua Tahun"
                                   readonly
                                   @click="open = !open"
                                   class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all cursor-pointer font-medium select-none truncate">

                            <!-- Icon Kalender / Clear -->
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                                <button type="button"
                                        x-show="inputValue"
                                        @click.stop="resetYear()"
                                        class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer"
                                        title="Hapus filter tahun">
                                    <span class="material-symbols-outlined text-[15px]">cancel</span>
                                </button>
                                <span x-show="!inputValue" class="material-symbols-outlined text-[17px] text-[#6B7280] pointer-events-none">calendar_today</span>
                            </div>
                        </div>

                        <!-- Year Picker Popover Dropdown -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute z-50 left-0 lg:left-auto lg:right-0 top-full mt-1.5 w-72 bg-white rounded-xl shadow-xl border border-[#D9E4E2] p-3 select-none"
                             style="display: none;">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#D9E4E2]">
                                <button type="button" @click="prevDecade()" class="w-7 h-7 flex items-center justify-center rounded-lg text-[#374151] hover:bg-[#F3F7F6]">
                                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                                </button>
                                <span class="text-xs font-bold text-[#1F2937]" x-text="`${decadeStart} - ${decadeEnd}`"></span>
                                <button type="button" @click="nextDecade()" class="w-7 h-7 flex items-center justify-center rounded-lg text-[#374151] hover:bg-[#F3F7F6]">
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-4 gap-1.5 text-center mb-2">
                                <template x-for="item in years" :key="item.year">
                                    <button type="button"
                                            @click="onYearClick(item.year)"
                                            @mouseenter="onYearHover(item.year)"
                                            :class="{
                                                'bg-[#0F766E] text-white font-bold shadow-xs': item.isStart || item.isEnd || item.isSingle,
                                                'bg-[#CCFBF1] text-[#0F766E] font-semibold': item.isInRange,
                                                'text-[#9CA3AF] opacity-50': !item.isCurrentDecade && !item.isSelected,
                                                'text-[#0F766E] font-bold border border-[#0F766E]/40': item.isToday && !item.isSelected,
                                                'text-[#1F2937] hover:bg-[#F0FDFA] hover:text-[#0F766E]': !item.isSelected && item.isCurrentDecade
                                            }"
                                            class="h-8 rounded-lg text-xs flex items-center justify-center transition-all cursor-pointer">
                                        <span x-text="item.year"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="pt-2 border-t border-[#D9E4E2] flex items-center justify-between">
                                <button type="button" @click="selectToday()" class="text-[11px] font-semibold text-[#0F766E] hover:underline">
                                    Tahun Ini ({{ date('Y') }})
                                </button>
                                <span class="text-[10px] text-gray-400" x-text="pickingStep === 2 ? 'Pilih akhir' : 'Pilih awal'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Tombol Aksi (Filter & Reset) Sejajar dalam 1 Baris -->
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 h-10 px-3 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">filter_list</span>
                        <span>Filter</span>
                    </button>

                    <a href="{{ route('admin.laporan.index') }}"
                       class="h-10 px-3 bg-gray-100 hover:bg-gray-200 text-[#4B5563] rounded-lg text-xs font-semibold transition-colors flex items-center justify-center"
                       title="Reset Semua Filter">
                        <span class="material-symbols-outlined text-[16px]">refresh</span>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- 2. CARD PREVIEW KERTAS ASLI (Ukuran A4 Landscape) DENGAN TOMBOL CETAK / UNDUH DI ATASNYA -->
    <div class="bg-white rounded-xl border border-[#D9E4E2] shadow-xs overflow-hidden">

        <!-- Bilah Aksi di Atas Lembar Kertas: Tombol Cetak / Unduh -->
        <div class="no-print p-4 sm:p-5 border-b border-[#D9E4E2] bg-[#F8FBFA] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0F766E] text-[20px]">description</span>
                    <h2 class="text-sm font-bold text-[#1F2937]">Pratinjau Lembar Kerja KIB B (A4 Landscape)</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#0F766E]">
                        {{ number_format($filteredTotalCount) }} Barang
                    </span>
                </div>
                <p class="text-xs text-[#6B7280] mt-0.5">
                    Dokumen resmi KIB B dalam tata letak A4 Landscape. Silakan pilih format lalu unduh atau cetak dokumen.
                </p>
            </div>

            <!-- Tombol Cetak / Unduh di Atas Tabel -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        id="btnCetakUnduh"
                        onclick="executePrintOrDownload()"
                        class="h-9 px-4 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]" id="btnIcon">print</span>
                    <span id="btnText">Cetak / Unduh Dokumen</span>
                </button>
            </div>
        </div>

        <!-- Container Scrollable Lembar Kertas -->
        <div class="p-4 sm:p-6 bg-[#F3F7F6] overflow-x-auto flex justify-center">

            <!-- LEMBAR KERTAS ASLI (KIB B Sheet: A4 Landscape, Mengikuti kop.jpeg & bawah.jpeg) -->
            <div id="printableDocument" class="kib-paper-sheet">

                <!-- KOP SURAT (Logo di kiri, 3 Baris Judul Ditengah, Identitas vertikal menyatu) -->
                <div class="border-b-2 border-black pb-3 mb-4">
                    <div class="relative flex items-center justify-center min-h-[72px] mb-2">
                        <!-- Logo Instansi Tetap di Sebelah Kiri -->
                        @php
                            $logoSrc = (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo)))
                                ? asset($schoolProfile->logo)
                                : asset('images/logo-sekolah.png');
                        @endphp
                        <div class="absolute left-0 top-1/2 -translate-y-1/2">
                            <img src="{{ $logoSrc }}" alt="Logo" class="w-16 h-16 object-contain">
                        </div>

                        <!-- Tiga Baris Judul Ditengah (Centering Penuh) -->
                        <div class="text-center w-full px-20">
                            <h2 class="text-xs sm:text-sm font-bold tracking-wider uppercase text-black leading-tight">
                                PEMERINTAH {{ strtoupper($schoolProfile->kabupaten_kota ?: 'KABUPATEN PURWAKARTA') }}
                            </h2>
                            <h3 class="text-sm sm:text-base font-bold tracking-wide uppercase text-black mt-1 leading-tight">
                                REKAPITULASI INVENTARIS BARANG (KIB) B
                            </h3>
                            <h4 class="text-xs sm:text-sm font-bold tracking-wider uppercase text-black mt-0.5 leading-tight">
                                PERALATAN DAN MESIN
                            </h4>
                        </div>
                    </div>

                    <!-- Identitas Lengkap Menyatu (Nomor kode lokasi tidak dipisah ke atas) -->
                    <div class="mt-2 pt-2 border-t border-black text-[10px] text-black leading-relaxed">
                        <table class="w-full">
                            <tr>
                                <td class="font-bold w-44 py-0.5">PROVINSI</td>
                                <td class="font-bold w-3">:</td>
                                <td class="uppercase">{{ $schoolProfile->provinsi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">KAB./KOTA</td>
                                <td class="font-bold">:</td>
                                <td class="uppercase">{{ $schoolProfile->kabupaten_kota ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">BIDANG</td>
                                <td class="font-bold">:</td>
                                <td class="uppercase">{{ $schoolProfile->bidang ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">UNIT ORGANISASI</td>
                                <td class="font-bold">:</td>
                                <td class="uppercase">{{ $schoolProfile->unit_organisasi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">SUB UNIT ORGANISASI</td>
                                <td class="font-bold">:</td>
                                <td class="uppercase">{{ $schoolProfile->sub_unit_organisasi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">UPB</td>
                                <td class="font-bold">:</td>
                                <td class="uppercase">{{ $schoolProfile->upb ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold py-0.5">NO. KODE LOKASI</td>
                                <td class="font-bold">:</td>
                                <td class="font-mono font-bold">{{ $schoolProfile->no_kode_lokasi ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TABEL DATA 16 KOLOM KIB B (Format A4 Landscape) -->
                <div class="overflow-x-auto">
                    <table class="kib-table">
                        <thead>
                            <!-- Header Baris 1 -->
                            <tr>
                                <th rowspan="2" class="w-6">No.</th>
                                <th rowspan="2" class="w-20">Kode Barang</th>
                                <th rowspan="2">Jenis Barang / Nama Barang</th>
                                <th rowspan="2" class="w-16">Nomor Registrasi</th>
                                <th rowspan="2" class="w-24">Merk / Type</th>
                                <th rowspan="2" class="w-16">Ukuran / CC</th>
                                <th rowspan="2" class="w-16">Bahan</th>
                                <th rowspan="2" class="w-12">Tahun Pembelian</th>
                                <th colspan="5" class="py-0.5">Nomor</th>
                                <th rowspan="2" class="w-20">Asal Usul Cara Perolehan</th>
                                <th rowspan="2" class="w-24">Harga (Ribuan Rp)</th>
                                <th rowspan="2" class="w-20">Keterangan</th>
                            </tr>
                            <!-- Header Baris 2: Sub-kolom Nomor -->
                            <tr>
                                <th class="w-12">Pabrik</th>
                                <th class="w-12">Rangka</th>
                                <th class="w-12">Mesin</th>
                                <th class="w-12">Polisi</th>
                                <th class="w-12">BPKB</th>
                            </tr>
                            <!-- Header Baris 3: Angka Kolom [1] s/d [16] -->
                            <tr class="col-numbers">
                                @for($col = 1; $col <= 16; $col++)
                                    <td>{{ $col }}</td>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inv)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-mono text-center">{{ $inv->kode_barang ?: '-' }}</td>
                                    <td class="font-semibold">{{ $inv->nama_barang ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->nomor_register ?: '-' }}</td>
                                    <td>{{ $inv->merk_type ?: '-' }}</td>
                                    <td class="text-center">{{ $inv->ukuran_cc ?: '-' }}</td>
                                    <td class="text-center">{{ $inv->bahan ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->tahun_pembelian ?: '-' }}</td>

                                    <!-- 5 Sub-Kolom Nomor -->
                                    <td class="text-center font-mono">{{ $inv->nomor_pabrik ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->nomor_rangka ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->nomor_mesin ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->nomor_polisi ?: '-' }}</td>
                                    <td class="text-center font-mono">{{ $inv->nomor_bpkb ?: '-' }}</td>

                                    <td>{{ $inv->asal_usul ?: '-' }}</td>
                                    <td class="text-right">Rp {{ number_format($inv->harga ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-[8.5px]">{{ $inv->keterangan ?? $inv->deskripsi ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-6 text-gray-500 italic">
                                        Tidak ada data inventaris yang ditemukan sesuai kriteria filter.
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Baris Total Keseluruhan Harga -->
                            <tr class="bg-gray-100 font-bold border-t-2 border-black text-black">
                                <td colspan="14" class="text-right px-3 py-1.5 uppercase font-bold text-[9.5px]">
                                    JUMLAH HARGA KESELURUHAN :
                                </td>
                                <td class="text-right px-3 py-1.5 font-bold text-[9.5px] text-black">
                                    Rp {{ number_format($filteredTotalHarga, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- BAGIAN TANDA TANGAN / PENGESAHAN (Nama & NIP dari Database school_profile) -->
                <div class="kib-signatures mt-8 pt-3 text-[10px] text-black" style="page-break-inside: avoid;">
                    <div class="grid grid-cols-2 gap-12">

                        <!-- Kolom Kiri: Mengetahui / Kepala Sekolah -->
                        <div class="text-center">
                            <p class="font-normal">Mengetahui,</p>
                            <p class="font-bold text-xs">Kepala Sekolah</p>
                            <div class="h-16 flex items-center justify-center">
                                <!-- Spasi Tanda Tangan -->
                            </div>
                            <p class="font-bold underline text-xs tracking-wide">
                                ( {{ $schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd' }} )
                            </p>
                            <p class="mt-0.5">
                                NIP. {{ $schoolProfile->nip_kepala_sekolah ?: '19720315 199802 1 002' }}
                            </p>
                        </div>

                        <!-- Kolom Kanan: Pembuat Laporan -->
                        <div class="text-center">
                            @php
                                $lokasi = $schoolProfile->kabupaten_kota
                                    ? str_replace(['Kabupaten ', 'Kota '], '', $schoolProfile->kabupaten_kota)
                                    : 'Babakancikao';
                            @endphp
                            <p class="font-normal">{{ $lokasi }}, {{ $generatedDate }}</p>
                            <p class="font-bold text-xs">Pembuat Laporan</p>
                            <div class="h-16 flex items-center justify-center">
                                <!-- Spasi Tanda Tangan -->
                            </div>
                            <p class="font-bold underline text-xs tracking-wide">
                                ( {{ $schoolProfile->nama_pembuat ?: 'Admin Sarpras' }} )
                            </p>
                            <p class="mt-0.5">
                                NIP. {{ $schoolProfile->nip_pembuat ?: '19800101 200501 1 001' }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<!-- html2canvas untuk unduh gambar langsung di sisi browser -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 Single
        $('.select2-single').select2({
            width: '100%',
            dropdownParent: $('body')
        });

        // Inisialisasi Select2 Multiple untuk Kondisi Unit
        $('.select2-multiple').select2({
            width: '100%',
            dropdownParent: $('body'),
            closeOnSelect: false
        });

        // Perbarui label tombol saat halaman dimuat
        updateDownloadButtonState();
    });

    // Memperbarui teks dan ikon tombol aksi sesuai format dokumen yang dipilih
    function updateDownloadButtonState() {
        const formatSelect = document.getElementById('filter_format');
        const format = formatSelect ? formatSelect.value : 'pdf';
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');

        if (!btnText || !btnIcon) return;

        if (format === 'pdf') {
            btnText.textContent = 'Cetak / Unduh PDF';
            btnIcon.textContent = 'print';
        } else if (format === 'xlsx') {
            btnText.textContent = 'Unduh Excel (.xlsx)';
            btnIcon.textContent = 'table_view';
        } else if (format === 'docx') {
            btnText.textContent = 'Unduh Word (.docx)';
            btnIcon.textContent = 'description';
        } else if (['png', 'jpg', 'jpeg', 'webp'].includes(format)) {
            btnText.textContent = `Unduh Gambar (.${format.toUpperCase()})`;
            btnIcon.textContent = 'image';
        }
    }

    // Mengunduh langsung dokumen pratinjau sebagai gambar (PNG, JPG, JPEG, WebP) via html2canvas
    function downloadDocumentAsImage(format) {
        const docElem = document.getElementById('printableDocument');
        if (!docElem) return;

        const btn = document.getElementById('btnCetakUnduh');
        const origHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span><span>Mengunduh .${format.toUpperCase()}...</span>`;
        }

        const mimeType = (format === 'png') ? 'image/png' :
                         (format === 'webp') ? 'image/webp' : 'image/jpeg';
        const ext = (format === 'jpeg') ? 'jpg' : format;

        html2canvas(docElem, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            windowWidth: docElem.scrollWidth
        }).then(function(canvas) {
            const dataUrl = canvas.toDataURL(mimeType, 0.95);
            const link = document.createElement('a');
            const now = new Date();
            const timestamp = now.getFullYear() +
                String(now.getMonth() + 1).padStart(2, '0') +
                String(now.getDate()).padStart(2, '0') + '_' +
                String(now.getHours()).padStart(2, '0') +
                String(now.getMinutes()).padStart(2, '0') +
                String(now.getSeconds()).padStart(2, '0');
            link.download = `Laporan_Inventaris_KIB_B_${timestamp}.${ext}`;
            link.href = dataUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }).catch(function(err) {
            console.error('Error generating image:', err);
            alert('Gagal memproses gambar. Silakan coba kembali.');
        }).finally(function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        });
    }

    // Menjalankan Cetak / Unduh Dokumen sesuai format yang dipilih
    function executePrintOrDownload() {
        const formatSelect = document.getElementById('filter_format');
        const format = formatSelect ? formatSelect.value : 'pdf';

        if (format === 'xlsx' || format === 'docx') {
            // Langsung unduh file Excel atau Word dari backend
            const form = document.getElementById('reportFilterForm');
            const url = "{{ route('admin.laporan.download') }}?" + new URLSearchParams(new FormData(form)).toString();
            window.location.href = url;
        } else if (['png', 'jpg', 'jpeg', 'webp'].includes(format)) {
            // Langsung unduh gambar di browser tanpa perlu berpindah halaman
            downloadDocumentAsImage(format);
        } else {
            // Default PDF / Cetak: Langsung buka window.print()
            // Kosongkan title sementara agar header default browser tidak mencetak judul halaman atau URL 127
            const oldTitle = document.title;
            document.title = ' ';
            window.print();
            setTimeout(function() {
                document.title = oldTitle;
            }, 1000);
        }
    }
</script>
@endpush
@endsection
