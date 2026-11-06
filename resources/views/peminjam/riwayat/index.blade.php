@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- ═══════════════════════════════════════════════════════════
         1. PAGE HEADER & ACTIONS
    ════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-[#D9E4E2]">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[15px]">history</span>
                    <span>Daftar Transaksi</span>
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] tracking-tight">Riwayat Peminjaman</h1>
                <p class="text-xs sm:text-sm text-[#6B7280] mt-1 max-w-2xl">
                    Halaman riwayat transaksi peminjaman sarana dan prasarana sekolah.
                </p>
            </div>

            <!-- Button to Peminjaman Katalog -->
            <div class="shrink-0">
                <a href="{{ route('peminjam.inventaris.index') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    <span>Katalog Inventaris</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════
         2. PLACEHOLDER / KONTEN DIKOSONGKAN SEMENTARA
    ════════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-2xl border border-[#D9E4E2] p-12 text-center shadow-xs max-w-2xl mx-auto my-8">
        <div class="w-20 h-20 rounded-full bg-[#F0FDFA] text-[#0F766E] mx-auto flex items-center justify-center mb-5 border border-[#CCFBF1]">
            <span class="material-symbols-outlined text-4xl">history_toggle_off</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-[#1F2937]">Riwayat Peminjaman</h3>
        <p class="text-xs text-[#6B7280] mt-2 max-w-md mx-auto leading-relaxed">
            Daftar riwayat transaksi peminjaman saat ini dikosongkan sementara. Seluruh pengajuan peminjaman baru dapat dilakukan melalui halaman katalog inventaris.
        </p>
        <div class="mt-6">
            <a href="{{ route('peminjam.inventaris.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                <span>Mulai Ajukan Peminjaman</span>
            </a>
        </div>
    </div>

</div>
@endsection
