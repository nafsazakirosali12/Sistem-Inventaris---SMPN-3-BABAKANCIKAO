@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
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
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover,
    .flatpickr-day.notAllowed,
    .flatpickr-day.notAllowed:hover {
        color: #9CA3AF !important;
        background: #F3F7F6 !important;
        cursor: not-allowed !important;
        opacity: 0.35 !important;
        pointer-events: none !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8"
     x-data="riwayatApp()">

    <!-- ═══════════════════════════════════════════════════════════
         1. PAGE HEADER (Format & Ukuran Font Sama dengan Peminjaman)
    ════════════════════════════════════════════════════════════ -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-5 sm:pb-6 border-b border-[#D9E4E2]">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[15px]">history</span>
                    <span>Daftar Transaksi</span>
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] tracking-tight">Riwayat Peminjaman</h1>
                <p class="text-xs sm:text-sm text-[#6B7280] mt-1 max-w-2xl leading-relaxed">
                    Daftar riwayat transaksi peminjaman sarana dan prasarana oleh guru dan staf {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}.
                </p>
            </div>

            <!-- Tombol Aksi: Kembali ke Beranda & Ajukan Peminjaman -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center gap-2 h-10 px-3.5 sm:px-4 rounded-xl bg-white border border-[#D9E4E2] text-xs font-bold text-[#374151] hover:bg-[#F3F7F6] hover:text-[#0F766E] transition-all shadow-xs cursor-pointer flex-1 sm:flex-none">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Beranda</span>
                </a>
                <a href="{{ route('peminjam.inventaris.index') }}"
                   class="inline-flex items-center justify-center gap-2 h-10 px-3.5 sm:px-4 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex-1 sm:flex-none">
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>Ajukan Peminjaman</span>
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mt-4 sm:mt-6 p-3.5 sm:p-4 rounded-2xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] flex items-start gap-2.5 sm:gap-3 shadow-xs">
                <span class="material-symbols-outlined text-[22px] sm:text-[24px] text-[#10B981] shrink-0 mt-0.5">check_circle</span>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-bold text-[#065F46]">Berhasil!</h4>
                    <p class="text-xs mt-0.5 text-[#047857] leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 sm:mt-6 p-3.5 sm:p-4 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-start gap-2.5 sm:gap-3 shadow-xs">
                <span class="material-symbols-outlined text-[22px] sm:text-[24px] text-[#EF4444] shrink-0 mt-0.5">error</span>
                <div class="flex-1 text-xs sm:text-sm">
                    <h4 class="font-bold text-[#991B1B]">Perhatian</h4>
                    <p class="text-xs mt-0.5 text-[#B91C1C] leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- ═══════════════════════════════════════════════════════════
             2. SEARCH & FILTER TOOLBAR (Hanya 3 Status: Dipinjam, Selesai, Terlambat)
        ════════════════════════════════════════════════════════════ -->
        <div class="mt-4 sm:mt-6 bg-white rounded-xl border border-[#D9E4E2] p-3 sm:p-4 shadow-sm">
            <form method="GET" action="{{ route('peminjam.riwayat.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 sm:gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px] leading-none select-none">search</span>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari peminjam atau kode pinjam..."
                           class="w-full h-10 pl-9 sm:pl-10 pr-9 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium">
                    @if(request('search'))
                        <a href="{{ route('peminjam.riwayat.index', request()->except('search')) }}"
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                           title="Hapus pencarian">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                        </a>
                    @endif
                </div>

                <!-- Dropdown Status: Hanya Dipinjam, Selesai, Terlambat -->
                <div class="w-full sm:w-auto lg:w-44 shrink-0">
                    <select name="status" class="select2-filter w-full">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

                <!-- Dropdown Kategori Barang -->
                <div class="w-full sm:w-auto lg:w-56 shrink-0">
                    <select name="category_id" class="select2-filter w-full">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal (Flatpickr) -->
                <div class="relative w-full sm:w-auto lg:w-52 shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] leading-none select-none">calendar_month</span>
                    </div>
                    <input type="text"
                           id="filter_loan_date"
                           name="date_range"
                           value="{{ request('date_range') }}"
                           placeholder="Rentang tanggal..."
                           readonly
                           class="w-full h-10 pl-9 pr-8 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all font-medium cursor-pointer">
                    @if(request('date_range'))
                        <a href="{{ route('peminjam.riwayat.index', request()->except('date_range')) }}"
                           class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                           title="Hapus filter tanggal">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                        </a>
                    @endif
                </div>

                <!-- Buttons: Filter & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="h-10 px-4 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-semibold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer flex-1 sm:flex-none"
                            title="Terapkan Pencarian">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        <span>Filter</span>
                    </button>
                    @if(request('search') || request('status') || request('category_id') || request('date_range'))
                        <a href="{{ route('peminjam.riwayat.index') }}"
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
         3. CARDS GRID RIWAYAT (Sederhana, Bersih, Mobile-Friendly)
         - Kode peminjaman diawali tanda '#'
         - Ada icon profil pada nama peminjam
         - Jika terlambat tapi sudah selesai, tampil status Selesai & Terlambat
         - Tanpa tulisan Akun Guru di bawah card
         - Icon detail menggunakan 'visibility' (mata)
    ════════════════════════════════════════════════════════════ -->
    @if($loans->isEmpty())
        <div class="bg-white rounded-2xl border border-[#D9E4E2] p-8 sm:p-12 text-center shadow-xs max-w-lg mx-auto my-6 sm:my-8">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#F0FDFA] text-[#0F766E] mx-auto flex items-center justify-center mb-3 sm:mb-4 border border-[#CCFBF1]">
                <span class="material-symbols-outlined text-2xl sm:text-3xl">history_toggle_off</span>
            </div>
            <h3 class="text-sm sm:text-base font-bold text-[#1F2937]">Tidak Ada Riwayat Peminjaman</h3>
            <p class="text-xs text-[#6B7280] mt-1 max-w-sm mx-auto leading-relaxed">
                @if(request('search') || request('status') || request('category_id') || request('date_range'))
                    Tidak ditemukan data peminjaman yang cocok dengan filter yang Anda tentukan.
                @else
                    Belum ada riwayat transaksi peminjaman sarana dan prasarana.
                @endif
            </p>
            <div class="mt-4 sm:mt-5 flex items-center justify-center gap-2">
                @if(request('search') || request('status') || request('category_id') || request('date_range'))
                    <a href="{{ route('peminjam.riwayat.index') }}"
                       class="px-3.5 py-2 rounded-xl bg-white border border-[#D9E4E2] text-xs font-semibold text-[#374151] hover:bg-[#F3F7F6]">
                        Reset Filter
                    </a>
                @endif
                <a href="{{ route('peminjam.inventaris.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-bold transition-all shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
                    <span>Ajukan Pinjam</span>
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($loans as $loan)
                @php
                    $isHighlighted = (string)$loan->id === (string)$highlightId;
                    $isOverdue = $loan->is_terlambat;
                    $totalUnits = $loan->jumlah_barang;
                    $catTitle = $loan->category->nama_kategori ?? ($loan->category_summary ?: ($loan->inventory->category->nama_kategori ?? 'Inventaris'));
                @endphp

                <div id="loan-card-{{ $loan->id }}"
                     class="bg-white rounded-xl sm:rounded-2xl border transition-all duration-150 flex flex-col justify-between shadow-xs hover:shadow-sm {{ $isHighlighted ? 'ring-2 ring-[#0F766E] border-[#0F766E] bg-gradient-to-b from-[#F0FDFA]/30 to-white' : 'border-[#D9E4E2]' }}">

                    <div class="p-4 sm:p-5 flex-1 flex flex-col">

                        <!-- Highlight Flag jika baru selesai diajukan -->
                        @if($isHighlighted)
                            <div class="mb-2.5 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#0F766E] text-white text-[10px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span>Baru Diajukan</span>
                                </span>
                            </div>
                        @endif

                        <!-- Top Row: Kode Peminjaman (diawali tanda '#') & Status Badges -->
                        <div class="flex items-center justify-between gap-2 pb-3 border-b border-[#F3F7F6]">
                            <span class="font-mono text-xs font-bold text-[#1F2937] tracking-tight truncate" title="#{{ $loan->kode_peminjaman }}">
                                #{{ $loan->kode_peminjaman }}
                            </span>

                            <!-- Status Badges: Jika Terlambat & Sudah Selesai, Tetap Munculkan Status Selesai -->
                            <div class="shrink-0 flex items-center gap-1.5 flex-wrap justify-end">
                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#991B1B] border border-[#FECACA]">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        <span>Terlambat</span>
                                    </span>
                                @endif

                                @if($loan->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0]">
                                        <span class="material-symbols-outlined text-[12px]">check</span>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EFF6FF] text-[#1E40AF] border border-[#BFDBFE]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        <span>Dipinjam</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- 1. Nama Peminjam dengan Icon Profile -->
                        <div class="mt-3 flex items-center gap-2.5">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#F0FDFA] border border-[#CCFBF1] text-[#0F766E] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px] sm:text-[18px]">person</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] uppercase font-semibold text-[#9CA3AF] tracking-wider block leading-none">Peminjam</span>
                                <h3 class="text-xs sm:text-sm font-bold text-[#1F2937] truncate mt-0.5" title="{{ $loan->nama_peminjam }}">
                                    {{ $loan->nama_peminjam }}
                                </h3>
                            </div>
                        </div>

                        <!-- 2. Kategori Barang & Jumlah Unit -->
                        <div class="mt-2.5 py-1.5 px-2.5 rounded-lg bg-[#F8FBFA] flex items-center justify-between text-xs">
                            <span class="font-medium text-[#374151] truncate max-w-[170px]" title="{{ $catTitle }}">
                                {{ $catTitle }}
                            </span>
                            <span class="font-bold text-[#0F766E] shrink-0">
                                {{ $totalUnits }} Unit
                            </span>
                        </div>

                        <!-- 3. Jadwal Waktu: Tanggal Pinjam & Batas Pengembalian -->
                        <div class="mt-3 pt-2.5 border-t border-[#F3F7F6] space-y-1.5 text-xs">
                            <div class="flex items-center justify-between text-[#6B7280]">
                                <span class="flex items-center gap-1 text-[11px]">
                                    <span class="material-symbols-outlined text-[14px] text-[#9CA3AF]">calendar_today</span>
                                    <span>Tgl Pinjam:</span>
                                </span>
                                <span class="font-medium text-[#1F2937]">
                                    {{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d/m/Y H:i') : '-' }} WIB
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[#6B7280]">
                                <span class="flex items-center gap-1 text-[11px]">
                                    <span class="material-symbols-outlined text-[14px] {{ $isOverdue ? 'text-[#EF4444]' : 'text-[#9CA3AF]' }}">event</span>
                                    <span>Batas Kembali:</span>
                                </span>
                                <span class="font-bold {{ $isOverdue ? 'text-[#991B1B]' : 'text-[#1F2937]' }}">
                                    {{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y H:i') : '-' }} WIB
                                </span>
                            </div>

                            @if($loan->status === 'selesai' && $loan->tanggal_kembali_aktual)
                                <div class="flex items-center justify-between text-[#065F46] pt-1 border-t border-[#F3F7F6]">
                                    <span class="flex items-center gap-1 text-[11px]">
                                        <span class="material-symbols-outlined text-[14px] text-[#10B981]">event_available</span>
                                        <span>Dikembalikan:</span>
                                    </span>
                                    <span class="font-semibold text-[11px]">
                                        {{ $loan->tanggal_kembali_aktual->format('d/m/Y H:i') }} WIB
                                    </span>
                                </div>
                            @endif
                        </div>

                    </div>

                    <!-- Card Footer: Tombol Detail dengan Ikon Mata (Tanpa Akun Guru) -->
                    <div class="px-4 sm:px-5 py-3 bg-[#FAFDFD] border-t border-[#D9E4E2] flex items-center justify-end">
                        <button type="button"
                                @click="openModal({{ json_encode([
                                    'id' => $loan->id,
                                    'kode' => $loan->kode_peminjaman,
                                    'peminjam' => $loan->nama_peminjam,
                                    'kategori' => $catTitle,
                                    'jumlah' => $totalUnits,
                                    'status' => $loan->status,
                                    'is_terlambat' => $isOverdue,
                                    'pinjam' => $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d F Y, H:i') . ' WIB' : '-',
                                    'kembali' => $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d F Y, H:i') . ' WIB' : '-',
                                    'kembali_aktual' => $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d F Y, H:i') . ' WIB' : null,
                                    'alasan' => $loan->alasan_tujuan ?: 'Tidak ada keterangan tambahan.',
                                    'created_at' => $loan->created_at ? $loan->created_at->format('d F Y, H:i') . ' WIB' : '-',
                                    'units' => $loan->details->isNotEmpty()
                                        ? $loan->details->map(function($d) {
                                            return [
                                                'kode' => $d->kode_barang,
                                                'nama' => $d->inventory->nama_barang ?? '-',
                                                'kategori' => $d->inventory->category->nama_kategori ?? '-',
                                                'ruangan' => $d->inventory->room->nama_ruangan ?? '-',
                                            ];
                                        })->values()->all()
                                        : ($loan->inventory ? [[
                                            'kode' => $loan->inventory->kode_barang,
                                            'nama' => $loan->inventory->nama_barang ?? '-',
                                            'kategori' => $loan->inventory->category->nama_kategori ?? '-',
                                            'ruangan' => $loan->inventory->room->nama_ruangan ?? '-',
                                        ]] : [])
                                ]) }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#D9E4E2] text-[#0F766E] hover:bg-[#F0FDFA] hover:border-[#0F766E] text-xs font-semibold transition-all shadow-2xs cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Detail</span>
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- ═══════════════════════════════════════════════════════════
             4. PAGINATION (flow.md 5: 6 per halaman)
        ════════════════════════════════════════════════════════════ -->
        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-[#D9E4E2]">
            <p class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-medium text-[#1F2937]">{{ $loans->firstItem() ?? 0 }}-{{ $loans->lastItem() ?? 0 }}</span> dari <span class="font-medium text-[#1F2937]">{{ $loans->total() }}</span> riwayat
            </p>

            <div class="pagination-custom">
                {{ $loans->links() }}
            </div>
        </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════
         5. MODAL DETAIL TRANSAKSI LENGKAP
         - Kode peminjaman diawali tanda '#'
         - Tanpa tulisan Akun Guru
         - Tanpa tombol tutup di bawah (cukup silang 'X' di atas)
    ════════════════════════════════════════════════════════════ -->
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="detailModalOpen"
             x-transition:enter="ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="detailModalOpen = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
            <div x-show="detailModalOpen"
                 x-transition:enter="ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="detailModalOpen = false"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all my-4 sm:my-8 w-full max-w-xl border border-[#D9E4E2]">

                <!-- Modal Header dengan Tombol Silang 'X' -->
                <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-[#F0FDFA] border-b border-[#D9E4E2] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#0F766E] text-white flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">receipt_long</span>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-[#1F2937]">Rincian Transaksi Peminjaman</h3>
                            <p class="text-[11px] text-[#0F766E] font-mono font-semibold" x-text="'#' + (activeLoan?.kode || '')"></p>
                        </div>
                    </div>

                    <!-- Tombol Silang Penutup -->
                    <button type="button"
                            @click="detailModalOpen = false"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-white transition-colors cursor-pointer"
                            title="Tutup">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 sm:p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                    <!-- Info Ringkas Peminjam & Status (Tanpa Akun Guru) -->
                    <div class="grid grid-cols-2 gap-3 p-3 sm:p-3.5 rounded-xl bg-[#F8FBFA] border border-[#E5EBEA] text-xs">
                        <div>
                            <span class="text-[10px] text-[#6B7280] uppercase font-bold tracking-wider block">Peminjam</span>
                            <span class="font-bold text-[#1F2937] text-xs sm:text-sm block mt-0.5" x-text="activeLoan?.peminjam"></span>
                        </div>

                        <div>
                            <span class="text-[10px] text-[#6B7280] uppercase font-bold tracking-wider block">Status</span>
                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                <template x-if="activeLoan?.is_terlambat">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FEF2F2] text-[#991B1B] border border-[#FECACA] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        <span>Terlambat</span>
                                    </span>
                                </template>
                                <template x-if="activeLoan?.status === 'selesai'">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#ECFDF5] text-[#065F46] border border-[#A7F3D0] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">check</span>
                                        <span>Selesai</span>
                                    </span>
                                </template>
                                <template x-if="activeLoan?.status === 'dipinjam'">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EFF6FF] text-[#1E40AF] border border-[#BFDBFE] flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        <span>Dipinjam</span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori & Jadwal Waktu -->
                    <div class="p-3.5 rounded-xl border border-[#D9E4E2] bg-white space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-[#F3F7F6]">
                            <span class="text-[#6B7280]">Kategori Barang</span>
                            <span class="font-bold text-[#1F2937]">
                                <span x-text="activeLoan?.kategori"></span> (<span x-text="activeLoan?.jumlah"></span> Unit)
                            </span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-[#F3F7F6]">
                            <span class="text-[#6B7280]">Tanggal Pinjam</span>
                            <span class="font-semibold text-[#1F2937]" x-text="activeLoan?.pinjam"></span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-[#F3F7F6]">
                            <span class="text-[#6B7280]">Batas Pengembalian</span>
                            <span class="font-semibold text-[#1F2937]" x-text="activeLoan?.kembali"></span>
                        </div>

                        <template x-if="activeLoan?.kembali_aktual">
                            <div class="flex justify-between py-1 border-b border-[#F3F7F6] text-[#065F46]">
                                <span>Realisasi Pengembalian</span>
                                <strong x-text="activeLoan?.kembali_aktual"></strong>
                            </div>
                        </template>
                    </div>

                    <!-- Alasan / Keperluan Peminjaman (Khusus di Dalam Detail) -->
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider block mb-1">
                            Alasan / Keperluan Peminjaman
                        </span>
                        <div class="p-3 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB] text-xs text-[#374151] leading-relaxed italic"
                             x-text="activeLoan?.alasan">
                        </div>
                    </div>

                    <!-- Unit Barang Fisik yang Terambil (Khusus di Dalam Detail) -->
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#6B7280] tracking-wider block mb-1.5">
                            Daftar Unit Barang yang Terambil
                        </span>

                        <div class="border border-[#D9E4E2] rounded-xl overflow-x-auto">
                            <table class="w-full text-left text-xs min-w-[320px]">
                                <thead class="bg-[#F8FBFA] text-[#6B7280] border-b border-[#D9E4E2] text-[11px] font-semibold">
                                    <tr>
                                        <th class="py-2.5 px-3 w-8 text-center">No</th>
                                        <th class="py-2.5 px-3">Kode Barang</th>
                                        <th class="py-2.5 px-3">Nama Barang</th>
                                        <th class="py-2.5 px-3">Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F3F7F6]">
                                    <template x-for="(unit, idx) in (activeLoan?.units || [])" :key="idx">
                                        <tr class="hover:bg-[#FAFDFD]">
                                            <td class="py-2 px-3 text-center text-[#9CA3AF] text-[11px]" x-text="idx + 1"></td>
                                            <td class="py-2 px-3 font-mono font-bold text-[#0F766E]" x-text="unit.kode"></td>
                                            <td class="py-2 px-3 text-[#1F2937]" x-text="unit.nama"></td>
                                            <td class="py-2 px-3 text-[#6B7280]" x-text="unit.ruangan"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!activeLoan?.units || activeLoan?.units.length === 0">
                                        <tr>
                                            <td colspan="4" class="py-3 px-3 text-center text-gray-400 italic text-[11px]">
                                                Tidak ada unit fisik teralokasi.
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    function riwayatApp() {
        return {
            detailModalOpen: false,
            activeLoan: null,

            openModal(loanData) {
                this.activeLoan = loanData;
                this.detailModalOpen = true;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#filter_loan_date", {
                mode: "range",
                dateFormat: "d/m/Y",
                conjunction: " — ",
                locale: "id",
                allowInput: false,
                maxDate: "{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y') }}",
                defaultDate: @json(request('date_range') ? preg_split('/\s*(?:—|-|to)\s*/u', request('date_range')) : []),
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        instance.element.closest('form').submit();
                    } else if (selectedDates.length === 1 && !dateStr.includes('—')) {
                        setTimeout(() => {
                            if (instance.selectedDates.length === 1) {
                                instance.element.closest('form').submit();
                            }
                        }, 600);
                    }
                }
            });
        }

        @if(isset($highlightId))
            const targetCard = document.getElementById('loan-card-{{ $highlightId }}');
            if (targetCard) {
                setTimeout(() => {
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        @endif
    });
</script>
@endpush
