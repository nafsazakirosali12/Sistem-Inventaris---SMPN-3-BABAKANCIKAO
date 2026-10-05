@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="space-y-6">
    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-[#134E4A] to-[#0F766E] rounded-xl p-6 sm:p-8 text-white shadow-lg overflow-hidden">
        <div class="relative z-10">
            <span class="px-2.5 py-1 rounded-md bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider inline-block mb-2">
                Selamat Datang Peminjam
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}</h1>
            <p class="text-sm text-[#CCFBF1] max-w-2xl">
                Layanan peminjaman fasilitas dan sarana prasarana sekolah terpadu.
            </p>
        </div>
    </div>

    <!-- Quick Stats & Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                <span class="material-symbols-outlined text-[28px]">inventory_2</span>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Inventaris Tersedia</p>
                <h4 class="text-lg font-bold text-[#1F2937]">Siap Dipinjam</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-[#D9E4E2] p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#FEF9C3] flex items-center justify-center text-[#854D0E]">
                <span class="material-symbols-outlined text-[28px]">history</span>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Status Peminjaman</p>
                <h4 class="text-lg font-bold text-[#1F2937]">Riwayat Aktif</h4>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-[#D9E4E2] p-5 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-[#DBEAFE] flex items-center justify-center text-[#1E40AF]">
                <span class="material-symbols-outlined text-[28px]">contact_support</span>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Bantuan & Layanan</p>
                <h4 class="text-lg font-bold text-[#1F2937]">Staf Sarpras</h4>
            </div>
        </div>
    </div>
</div>
@endsection
