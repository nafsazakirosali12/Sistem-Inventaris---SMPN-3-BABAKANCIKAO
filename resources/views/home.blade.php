@extends('layouts.peminjam')

@section('title', 'Home - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="space-y-6">
    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-[#134E4A] to-[#0F766E] rounded-xl p-6 sm:p-8 text-white shadow-lg overflow-hidden">
        <div class="relative z-10">
            <span class="px-2.5 py-1 rounded-md bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider inline-block mb-2">
                Selamat Datang
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}</h1>
            <p class="text-sm text-[#CCFBF1] max-w-2xl">
                Selamat datang di {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}. Layanan peminjaman fasilitas dan sarana prasana sekolah terpadu.
            </p>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Profil Sekolah -->
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <h3 class="font-bold text-base text-[#1F2937]">Profil Sekolah</h3>
            </div>
            <dl class="space-y-3 text-xs text-[#4B5563]">
                <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                    <dt class="font-semibold text-[#1F2937]">NPSN:</dt>
                    <dd>{{ $schoolProfile->npsn ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                    <dt class="font-semibold text-[#1F2937]">Alamat:</dt>
                    <dd class="text-right max-w-[200px] sm:max-w-xs">{{ $schoolProfile->alamat ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                    <dt class="font-semibold text-[#1F2937]">Telepon:</dt>
                    <dd>{{ $schoolProfile->telepon ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-semibold text-[#1F2937]">Email:</dt>
                    <dd>{{ $schoolProfile->email ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Card Denah -->
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                        <span class="material-symbols-outlined">map</span>
                    </div>
                    <h3 class="font-bold text-base text-[#1F2937]">Denah Sekolah</h3>
                </div>
                <p class="text-xs text-[#4B5563] mb-4 leading-relaxed">
                    Pratinjau denah tata letak gedung dan fasilitas SMPN 3 Babakancikao. Anda dapat mengunduh peta denah resmi di bawah ini.
                </p>
            </div>
            <button type="button"
                    onclick="showDenahInfo()"
                    class="w-full py-2.5 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white font-semibold text-xs flex items-center justify-center space-x-2 transition-colors">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Unduh Denah Sekolah</span>
            </button>
        </div>
    </div>
</div>

<script>
    function showDenahInfo() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Denah Sekolah',
                text: 'Dokumen peta denah tata letak fasilitas SMPN 3 Babakancikao tersedia untuk diunduh.',
                confirmButtonColor: '#0F766E',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                    confirmButton: 'px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
        } else {
            alert('Fitur unduh denah sekolah');
        }
    }
</script>
@endsection
