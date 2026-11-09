@extends('layouts.admin')

@section('title', 'Laporan Inventaris Barang')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* ── Select2 Custom Single Select Theme (Konsisten & Rapi) ── */
    #reportFilterForm .select2-container--default .select2-selection--single {
        background-color: #F3F7F6;
        border: 1px solid #D9E4E2;
        border-radius: 0.5rem;
        height: 40px !important;
        transition: all 0.15s ease-in-out;
        display: flex !important;
        align-items: center !important;
        position: relative;
    }
    #reportFilterForm .select2-container--default.select2-container--focus .select2-selection--single,
    #reportFilterForm .select2-container--default.select2-container--open .select2-selection--single {
        background-color: #ffffff;
        border-color: #0F766E;
        box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.2);
        outline: none;
    }
    #reportFilterForm .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        font-size: 0.75rem;
        color: #1F2937;
        padding-left: 0.75rem !important;
        padding-right: 2rem !important;
        font-weight: 500;
    }
    #reportFilterForm .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
        width: 20px;
        position: absolute;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Select2 Custom Multi-Select (Kondisi Barang: Desain Identik dengan Dropdown Lain, Konsisten & Rapi) ── */
    #reportFilterForm .select2-container {
        height: 40px !important;
        max-height: 40px !important;
        width: 100% !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple {
        background-color: #F3F7F6;
        border: 1px solid #D9E4E2;
        border-radius: 0.5rem;
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        padding: 3px 28px 3px 0.75rem !important;
        box-sizing: border-box !important;
        transition: all 0.15s ease-in-out;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        cursor: pointer;
        overflow: hidden !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple:hover {
        border-color: #0F766E !important;
    }
    #reportFilterForm .select2-container--default.select2-container--focus .select2-selection--multiple,
    #reportFilterForm .select2-container--default.select2-container--open .select2-selection--multiple {
        background-color: #ffffff !important;
        border-color: #0F766E !important;
        box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.2) !important;
        outline: none !important;
    }

    /* Panah Dropdown Konsisten di Kanan (Sama Persis dengan Dropdown Single Lainnya) */
    #reportFilterForm .select2-container--default .select2-selection--multiple::after {
        content: '';
        position: absolute;
        right: 14px;
        top: 50%;
        margin-top: -2px;
        border-color: #6B7280 transparent transparent transparent;
        border-style: solid;
        border-width: 5px 4px 0 4px;
        height: 0;
        width: 0;
        pointer-events: none;
        transition: transform 0.2s ease;
    }
    #reportFilterForm .select2-container--default.select2-container--open .select2-selection--multiple::after {
        border-color: transparent transparent #0F766E transparent;
        border-width: 0 4px 5px 4px;
    }

    /* Rendered Container: Scroll ke Bawah (Vertikal) Secara Halus jika Banyak Opsi Terpilih */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 3px !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
        width: 100% !important;
        max-height: 34px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
        scrollbar-width: thin;
        scrollbar-color: #0F766E transparent;
    }

    /* Ketika kosong: ul rendered disembunyikan agar tidak memakan ruang/mendorong placeholder ke kanan */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered:empty {
        display: none !important;
        width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Webkit Scrollbar Halus */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar {
        width: 3px;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar-thumb {
        background-color: #0F766E;
        border-radius: 9999px;
    }

    /* Chips Badge */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__choice {
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        background-color: #E6FFFA !important;
        border: 1px solid #99F6E4 !important;
        color: #0F766E !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        border-radius: 9999px !important;
        padding: 1px 6px 1px 4px !important;
        margin: 1px !important;
        line-height: 1.2 !important;
        gap: 3px !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        position: static !important;
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 14px !important;
        height: 14px !important;
        border: none !important;
        border-radius: 9999px !important;
        background-color: #CCFBF1 !important;
        color: #0F766E !important;
        font-size: 12px !important;
        font-weight: bold !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: #FEE2E2 !important;
        color: #EF4444 !important;
    }

    /* ── Penanganan Field (Tidak Terbagi Dua & Placeholder Rapi di Kiri) ── */
    /* Ketika sudah ada pilihan (choice), sembunyikan input inline sepenuhnya agar field TIDAK TERBAGI DUA */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered:not(:empty) + .select2-search--inline,
    #reportFilterForm .select2-container--default .select2-selection--multiple:has(.select2-selection__choice) .select2-search--inline {
        display: none !important;
    }

    /* Ketika belum ada pilihan: Tampilkan placeholder penuh di sebelah kiri secara presisi */
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-selection__rendered:empty + .select2-search--inline,
    #reportFilterForm .select2-container--default .select2-selection--multiple:not(:has(.select2-selection__choice)) .select2-search--inline {
        display: flex !important;
        flex: 1 1 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        justify-content: flex-start !important;
        text-align: left !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
        height: 34px !important;
        line-height: 34px !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 0.75rem !important;
        font-family: inherit !important;
        color: #1F2937 !important;
        font-weight: 500 !important;
        width: 100% !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        cursor: pointer !important;
        caret-color: transparent !important; /* Hilangkan kursor ketik di dalam field */
        text-align: left !important;
    }
    #reportFilterForm .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field::placeholder {
        color: #1F2937 !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        text-align: left !important;
    }

    /* Tanda centang pada opsi yang sedang aktif/terpilih di dalam dropdown */
    .select2-container--default .select2-results__option[aria-selected=true]::after {
        content: '✓';
        float: right;
        color: #0F766E;
        font-weight: bold;
    }

    /* Flatpickr Calendar Styling Theme */
    .flatpickr-calendar {
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid #D9E4E2;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 12px;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange,
    .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange {
        background: #0F766E !important;
        border-color: #0F766E !important;
        color: #ffffff !important;
    }
    .flatpickr-day.inRange {
        background: #CCFBF1 !important;
        border-color: #CCFBF1 !important;
        color: #0F766E !important;
    }
    .flatpickr-months .flatpickr-month {
        background: #0F766E;
        color: #fff;
        fill: #fff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    span.flatpickr-weekday {
        background: #0F766E;
        color: #CCFBF1;
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
        padding: 2.54cm; /* Margin 2.54 cm pada seluruh sisi */
        box-sizing: border-box;
    }

    /* ── Official Table KIB B Styling (Landscape) ─────────────────── */
    .kib-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9px;
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

    /* ── Print Stylesheet (Landscape, Margin 2.54 cm, Tanpa Header Berulang) ─ */
    @media print {
        @page {
            size: A4 landscape;
            margin: 2.54cm 2.54cm 2.54cm 2.54cm; /* Margin 2.54 cm pada seluruh 4 sisi */
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
            padding: 0 !important; /* Margin fisik diatur sepenuhnya oleh @page */
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .kib-table {
            font-size: 7.5pt !important;
            page-break-inside: auto;
        }
        .kib-table thead {
            display: table-row-group !important; /* Mencegah judul kolom diulang saat ganti halaman */
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
                    <select name="format" id="filter_format" class="select2-laporan w-full" onchange="updateDownloadButtonState()">
                        @foreach($formats as $fKey => $fVal)
                            <option value="{{ $fKey }}" {{ request('format', 'pdf') === $fKey ? 'selected' : '' }}>
                                {{ $fVal['label'] }} (.{{ $fVal['ext'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Kategori Barang (Multi-Select Select2) -->
                <div>
                    @php
                        $selectedCategories = (array) request('category_id', []);
                    @endphp
                    <label for="filter_kategori" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Kategori Barang
                    </label>
                    <select name="category_id[]" id="filter_kategori" class="select2-multiple-filter select2-kategori-multiple w-full" multiple="multiple" data-placeholder="Semua Kategori">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array((string)$category->id, array_map('strval', $selectedCategories)) ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Lokasi Ruangan (Multi-Select Select2) -->
                <div>
                    @php
                        $selectedRooms = (array) request('room_id', []);
                    @endphp
                    <label for="filter_ruangan" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Lokasi Ruangan
                    </label>
                    <select name="room_id[]" id="filter_ruangan" class="select2-multiple-filter select2-ruangan-multiple w-full" multiple="multiple" data-placeholder="Semua Ruangan">
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ in_array((string)$room->id, array_map('strval', $selectedRooms)) ? 'selected' : '' }}>
                                {{ $room->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Kondisi Barang (Multi-Select Select2 Searchable) -->
                <div>
                    @php
                        $selectedStatuses = (array) request('status', []);
                    @endphp
                    <label for="filter_status" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Kondisi Barang
                    </label>
                    <select name="status[]" id="filter_status" class="select2-multiple-filter select2-kondisi-multiple w-full" multiple="multiple" data-placeholder="Semua Kondisi">
                        <option value="baik" {{ in_array('baik', $selectedStatuses) ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ in_array('rusak', $selectedStatuses) ? 'selected' : '' }}>Rusak</option>
                        <option value="perlu perbaikan" {{ in_array('perlu perbaikan', $selectedStatuses) ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="hilang" {{ in_array('hilang', $selectedStatuses) ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>

                <!-- 5. Rentang Periode (Flatpickr Daterangepicker) -->
                <div>
                    <label for="filter_periode" class="block text-xs font-semibold text-[#374151] mb-1 truncate">
                        Rentang Periode
                    </label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        </div>
                        <input type="text"
                               id="filter_periode"
                               name="periode"
                               value="{{ request('periode', request('tahun')) }}"
                               placeholder="Pilih rentang periode..."
                               readonly
                               class="w-full h-10 pl-9 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 cursor-pointer placeholder-[#6B7280] truncate">
                        @if(request('periode') || request('tahun'))
                            <a href="{{ route('admin.laporan.index', request()->except(['periode', 'tahun'])) }}"
                               class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-[#6B7280] hover:text-[#DC2626] transition-colors"
                               title="Hapus filter rentang periode">
                                <span class="material-symbols-outlined text-[16px]">cancel</span>
                            </a>
                        @else
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                                <span class="material-symbols-outlined text-[18px]">expand_more</span>
                            </div>
                        @endif
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
                    <h2 class="text-sm font-bold text-[#1F2937]">Pratinjau Lembar Kerja KIB B</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#0F766E]">
                        {{ number_format($filteredTotalCount) }} Barang
                    </span>
                </div>
                <p class="text-xs text-[#6B7280] mt-0.5">
                    Dokumen resmi KIB B dalam tata letak A4 Landscape. Silakan pilih format lalu unduh atau cetak dokumen.
                </p>
            </div>

            <!-- Tombol Cetak & Unduh Dokumen Asli di Atas Tabel -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Tombol Unduh Dokumen Asli Resmi (PDF / Excel / Word) -->
                <button type="button"
                        id="btnUnduhDokumen"
                        onclick="downloadOfficialDocument()"
                        class="h-9 px-4 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]" id="btnIcon">picture_as_pdf</span>
                    <span id="btnText">Unduh Dokumen (.pdf)</span>
                </button>

                <!-- Tombol Cetak Pratinjau Kertas Langsung -->
                <button type="button"
                        onclick="printDocument()"
                        class="h-9 px-3 bg-white hover:bg-gray-50 border border-[#D9E4E2] text-[#374151] rounded-lg text-xs font-semibold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer"
                        title="Cetak pratinjau lembar kerja langsung ke printer">
                    <span class="material-symbols-outlined text-[16px] text-[#0F766E]">print</span>
                    <span class="hidden sm:inline">Cetak</span>
                </button>
            </div>
        </div>

        <!-- Container Scrollable Lembar Kertas -->
        <div class="p-4 sm:p-6 bg-[#F3F7F6] overflow-x-auto flex justify-center">

            <!-- LEMBAR KERTAS ASLI (KIB B Sheet: A4 Landscape, Mengikuti kop.jpeg & bawah.jpeg) -->
            <div id="printableDocument" class="kib-paper-sheet">

                <!-- KOP SURAT (Logo di kiri, 3 Baris Judul Ditengah, Identitas vertikal menyatu) -->
                <div class="border-b-4 border-double border-black pb-3 mb-4">
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
                            <!-- Header Baris 1: Proposi 100% Sesuai PDF Asli -->
                            <tr>
                                <th rowspan="2" style="width: 3%;">No.</th>
                                <th rowspan="2" style="width: 8%;">Kode Barang</th>
                                <th rowspan="2" style="width: 18%;">Jenis Barang / Nama Barang</th>
                                <th rowspan="2" style="width: 4.5%;">Nomor Registrasi</th>
                                <th rowspan="2" style="width: 8%;">Merk / Type</th>
                                <th rowspan="2" style="width: 4.5%;">Ukuran / CC</th>
                                <th rowspan="2" style="width: 4.5%;">Bahan</th>
                                <th rowspan="2" style="width: 4.5%;">Tahun Pembelian</th>
                                <th colspan="5" class="py-0.5" style="width: 20%;">Nomor</th>
                                <th rowspan="2" style="width: 7%;">Asal Usul Cara Perolehan</th>
                                <th rowspan="2" style="width: 9%;">Harga (Rp)</th>
                                <th rowspan="2" style="width: 9%;">Keterangan</th>
                            </tr>
                            <!-- Header Baris 2: Sub-kolom Nomor -->
                            <tr>
                                <th style="width: 4%;">Pabrik</th>
                                <th style="width: 4%;">Rangka</th>
                                <th style="width: 4%;">Mesin</th>
                                <th style="width: 4%;">Polisi</th>
                                <th style="width: 4%;">BPKB</th>
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
                                    <td>
                                        <div class="font-semibold text-black">{{ $inv->nama_barang ?: '-' }}</div>
                                        @if($inv->category)
                                            <div class="text-[8px] text-[#0F766E] font-medium">Kelompok: {{ $inv->category->nama_kategori }}</div>
                                        @endif
                                    </td>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 Single dengan fitur pencarian (search) aktif agar pengguna bisa mengetik
        $('.select2-laporan').select2({
            width: '100%',
            dropdownAutoWidth: false,
            minimumResultsForSearch: 0 // Pastikan fitur pencarian selalu aktif agar bisa ngetik
        });

        // Inisialisasi Select2 Multiple untuk Kategori, Ruangan, dan Kondisi Barang
        // dengan Kotak Ketik Pencarian di dalam Dropdown (seperti format dropdown profesional)
        if (typeof $.fn.select2 !== 'undefined' && typeof $.fn.select2.amd !== 'undefined') {
            $.fn.select2.amd.require([
                'select2/dropdown',
                'select2/dropdown/search',
                'select2/dropdown/attachBody',
                'select2/utils'
            ], function (Dropdown, DropdownSearch, AttachBody, Utils) {
                var customDropdownAdapter = Utils.Decorate(
                    Utils.Decorate(Dropdown, DropdownSearch),
                    AttachBody
                );

                $('.select2-multiple-filter').each(function() {
                    var ph = $(this).data('placeholder') || 'Pilih...';
                    $(this).select2({
                        width: '100%',
                        dropdownAdapter: customDropdownAdapter,
                        placeholder: ph,
                        closeOnSelect: false
                    });
                });
            });
        } else {
            $('.select2-multiple-filter').each(function() {
                var ph = $(this).data('placeholder') || 'Pilih...';
                $(this).select2({
                    width: '100%',
                    placeholder: ph,
                    closeOnSelect: false
                });
            });
        }

        // Auto-focus input pencarian di dropdown saat dibuka (baik single maupun multiple)
        $(document).on('select2:open', function() {
            setTimeout(function() {
                const searchField = document.querySelector('.select2-dropdown .select2-search__field, .select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                    if (!searchField.getAttribute('placeholder')) {
                        searchField.setAttribute('placeholder', 'Ketik untuk mencari...');
                    }
                }
            }, 50);
        });

        // Inisialisasi Flatpickr Daterange Picker untuk Periode Perolehan
        flatpickr("#filter_periode", {
            mode: "range",
            dateFormat: "d/m/Y",
            conjunction: " — ",
            locale: "id",
            allowInput: false,
            defaultDate: @json(request('periode') ? preg_split('/\s*(?:—|-|to)\s*/u', request('periode')) : (request('tahun') ? preg_split('/\s*(?:—|-|to)\s*/u', request('tahun')) : [])),
        });

        // Perbarui label tombol saat halaman dimuat
        updateDownloadButtonState();
    });

    // Memperbarui teks dan ikon tombol aksi sesuai format dokumen yang dipilih (Hanya PDF, Excel, dan Word)
    function updateDownloadButtonState() {
        const formatSelect = document.getElementById('filter_format');
        const format = formatSelect ? formatSelect.value : 'pdf';
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');

        if (!btnText || !btnIcon) return;

        if (format === 'pdf') {
            btnText.textContent = 'Unduh Dokumen (.pdf)';
            btnIcon.textContent = 'picture_as_pdf';
        } else if (format === 'xlsx') {
            btnText.textContent = 'Unduh Excel (.xlsx)';
            btnIcon.textContent = 'table_view';
        } else if (format === 'docx') {
            btnText.textContent = 'Unduh Word (.docx)';
            btnIcon.textContent = 'description';
        }
    }

    // Mengunduh dokumen asli resmi langsung dari backend sesuai format pilihan (PDF Asli, Excel, Word)
    function downloadOfficialDocument() {
        const form = document.getElementById('reportFilterForm');
        const url = "{{ route('admin.laporan.download') }}?" + new URLSearchParams(new FormData(form)).toString();
        window.location.href = url;
    }

    // Menjalankan cetak pratinjau lembar kerja langsung ke printer
    function printDocument() {
        const oldTitle = document.title;
        document.title = ' ';
        window.print();
        setTimeout(function() {
            document.title = oldTitle;
        }, 1000);
    }

    // Kompatibilitas alias bila terpanggil
    function executePrintOrDownload() {
        downloadOfficialDocument();
    }
</script>
@endpush
@endsection
