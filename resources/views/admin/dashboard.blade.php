@extends('layouts.app')

@section('title', 'Dashboard Sarpras - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<!-- Header Navbar -->
<header class="fixed top-0 left-0 right-0 h-16 bg-[#134E4A] z-50 flex items-center justify-between px-6 shadow-[0_1px_8px_rgba(0,0,0,0.08)]">
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
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#EAB308]"></span>
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
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin keluar dari akun Admin?')"
                        class="p-1.5 rounded-lg text-[#CCFBF1] hover:text-white hover:bg-white/10 transition-colors"
                        title="Logout">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Sidebar Navigation -->
<aside class="fixed left-0 top-16 bottom-0 w-64 bg-[#134E4A] z-40 flex flex-col justify-between py-4 border-t border-[#115E59]">
    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center h-11 px-4 transition-all bg-[#0F766E] text-white font-semibold text-xs rounded-lg">
            <span class="material-symbols-outlined mr-3 text-[20px]">grid_view</span>
            <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
            <span class="material-symbols-outlined mr-3 text-[20px]">account_balance</span>
            <span>Profil Sekolah</span>
        </a>
        <a href="{{ route('admin.kategori.index') }}" class="flex items-center h-11 px-4 rounded-lg text-[#CCFBF1] hover:bg-white/10 hover:text-white transition-all text-xs font-medium">
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
        <div class="flex flex-col w-full">

            <!-- Top Breadcrumb & Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-1.5 text-[#6B7280] text-xs mb-1">
                        <span>Beranda</span>
                        <span class="text-[#B7C9C6]">/</span>
                        <span class="text-[#0F766E] font-semibold">Dashboard</span>
                    </div>
                    <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Dashboard Sarana & Prasarana</h1>
                    <p class="text-xs text-[#4B5563] mt-0.5">Ringkasan data aset inventaris dan aktivitas peminjaman barang terkini.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="inline-flex items-center justify-center h-10 px-4 bg-[#0F766E] text-white font-semibold text-xs rounded-lg hover:bg-[#115E59] shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                        <span class="material-symbols-outlined mr-2 text-[20px]">add_box</span>
                        <span>+ Catat Barang Baru</span>
                    </button>
                </div>
            </div>

            <!-- Section 1: 3 Kartu Statistik Utama (Dinamis dari Database) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Stat 1: Total Aset -->
                <div class="bg-white rounded-xl p-5 shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md border border-[#D9E4E2]">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">Total Aset Inventaris</span>
                            <div class="mt-2 text-3xl text-[#1F2937] font-bold leading-none">
                                {{ number_format($totalAset, 0, ',', '.') }}
                                <span class="text-sm font-normal text-[#4B5563]">Unit</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#CCFBF1] flex items-center justify-center text-[#0F766E] flex-shrink-0">
                            <span class="material-symbols-outlined text-[24px]">layers</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 bg-[#F0FDFA] -mx-5 -mb-5 px-5 py-2 flex items-center justify-between text-[#4B5563] text-xs border-t border-[#D9E4E2]">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[#0F766E] text-[16px]">domain</span>
                            Tercatat dalam {{ $totalKategori }} Kategori & {{ $totalRuangan }} Ruangan
                        </span>
                        <a href="#" class="font-semibold text-[#0F766E] hover:underline">Lihat Semua →</a>
                    </div>
                </div>

                <!-- Stat 2: Sedang Dipinjam -->
                <div class="bg-white rounded-xl p-5 shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md border border-[#D9E4E2]">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">Sedang Dipinjam</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#DBEAFE] text-[#1E40AF]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB] mr-1.5"></span>
                                    Status: Berjalan
                                </span>
                            </div>
                            <div class="mt-2 text-3xl text-[#1F2937] font-bold leading-none">
                                {{ number_format($sedangDipinjam, 0, ',', '.') }}
                                <span class="text-sm font-normal text-[#4B5563]">Unit</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#DBEAFE] flex items-center justify-center text-[#2563EB] flex-shrink-0">
                            <span class="material-symbols-outlined text-[24px]">front_hand</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 bg-[#F0FDFA] -mx-5 -mb-5 px-5 py-2 flex items-center justify-between text-[#4B5563] text-xs border-t border-[#D9E4E2]">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[#2563EB] text-[16px]">groups</span>
                            Oleh {{ $totalGuruAktif }} Guru & Staf Aktif
                        </span>
                        <a href="#" class="font-semibold text-[#1E40AF] hover:underline">Log Peminjaman →</a>
                    </div>
                </div>

                <!-- Stat 3: Perlu Tindakan / Menunggu Persetujuan -->
                <div class="bg-white rounded-xl p-5 shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md border border-[#D9E4E2]">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">Perlu Tindakan</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF9C3] text-[#854D0E] {{ $perluTindakanCount > 0 ? 'animate-pulse' : '' }}">
                                    <span class="w-2 h-2 rounded-full bg-[#EAB308] mr-1"></span>
                                    Penting
                                </span>
                            </div>
                            <div class="mt-2 text-3xl text-[#1F2937] font-bold leading-none">
                                {{ number_format($perluTindakanCount, 0, ',', '.') }}
                                <span class="text-sm font-normal text-[#4B5563]">Permohonan</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#FEF9C3] flex items-center justify-center text-[#854D0E] flex-shrink-0">
                            <span class="material-symbols-outlined text-[24px]">schedule</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 bg-[#FEF3C7]/50 -mx-5 -mb-5 px-5 py-2 flex items-center justify-between text-[#854D0E] text-xs border-t border-[#FEF3C7]">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-[#EAB308]"></span>
                            {{ $pendingHariIni }} Baru Hari Ini • {{ $perluTindakanCount }} Menunggu
                        </span>
                        <a href="#" class="font-semibold text-[#854D0E] underline">Verifikasi</a>
                    </div>
                </div>
            </div>

            <!-- Section 2: Baris Dua Kolom (Diagram Kondisi & Log Persetujuan Cepat) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6 items-start">
                <!-- Kolom Kiri: Donut Chart Status (5 Kolom Dinamis) -->
                <div class="lg:col-span-5 bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-bold text-base text-[#1F2937]">Kondisi & Status Aset</h2>
                            <p class="text-xs text-[#4B5563]">Peta kelaikan dan ketersediaan barang inventaris real-time</p>
                        </div>
                        <span class="material-symbols-outlined text-[#6B7280] text-[20px]">pie_chart</span>
                    </div>
                    <!-- Diagram SVG Donut Sederhana & Terukur -->
                    <div class="py-3 flex flex-col sm:flex-row items-center justify-center gap-6">
                        <div class="relative w-44 h-44 flex-shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <!-- Background track -->
                                <circle cx="50" cy="50" fill="transparent" r="38" stroke="#E5E9E8" stroke-width="12"></circle>
                                <!-- Baik / Tersedia -->
                                <circle cx="50" cy="50" fill="transparent" r="38" stroke="#16A34A" stroke-dasharray="{{ $baikDash }} {{ $circumference }}" stroke-dashoffset="0" stroke-width="12"></circle>
                                <!-- Sedang Dipinjam -->
                                <circle cx="50" cy="50" fill="transparent" r="38" stroke="#2563EB" stroke-dasharray="{{ $dipinjamDash }} {{ $circumference }}" stroke-dashoffset="{{ $offset1 }}" stroke-width="12"></circle>
                                <!-- Rusak / Servis -->
                                <circle cx="50" cy="50" fill="transparent" r="38" stroke="#DC2626" stroke-dasharray="{{ $rusakDash }} {{ $circumference }}" stroke-dashoffset="{{ $offset2 }}" stroke-width="12"></circle>
                                <!-- Menunggu Verifikasi -->
                                <circle cx="50" cy="50" fill="transparent" r="38" stroke="#EAB308" stroke-dasharray="{{ $menungguDash }} {{ $circumference }}" stroke-dashoffset="{{ $offset3 }}" stroke-width="12"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                                <span class="text-xl text-[#1F2937] leading-tight font-bold">{{ number_format($totalAset, 0, ',', '.') }}</span>
                                <span class="text-[11px] text-[#6B7280]">Total Unit</span>
                            </div>
                        </div>
                        <!-- Detail Angka & Persentase Dinamis -->
                        <div class="w-full flex flex-col gap-2.5">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-[#DCFCE7]/40 border border-[#DCFCE7]">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#16A34A] flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-[#1F2937]">Baik / Tersedia</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-[#166534]">{{ $baikCount }} Unit</span>
                                    <span class="text-[11px] text-[#6B7280] ml-1">({{ $baikPct }}%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-[#DBEAFE]/40 border border-[#DBEAFE]">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#2563EB] flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-[#1F2937]">Sedang Dipinjam</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-[#1E40AF]">{{ $dipinjamCount }} Unit</span>
                                    <span class="text-[11px] text-[#6B7280] ml-1">({{ $dipinjamPct }}%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-[#FEE2E2]/40 border border-[#FEE2E2]">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#DC2626] flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-[#1F2937]">Rusak / Pemeliharaan</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-[#991B1B]">{{ $rusakCount }} Unit</span>
                                    <span class="text-[11px] text-[#6B7280] ml-1">({{ $rusakPct }}%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-[#FEF9C3]/50 border border-[#FEF9C3]">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#EAB308] flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-[#1F2937]">Menunggu Verifikasi</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-[#854D0E]">{{ $menungguCount }} Unit</span>
                                    <span class="text-[11px] text-[#6B7280] ml-1">({{ $menungguPct }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Antrean Peminjaman Cepat (7 Kolom Dinamis) -->
                <div class="lg:col-span-7 bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-base text-[#1F2937]">Peminjaman Memerlukan Persetujuan</h2>
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#EAB308] text-[#1F2937] text-[11px] font-bold">
                                    {{ $pendingLoans->count() }}
                                </span>
                            </div>
                            <p class="text-xs text-[#4B5563]">Daftar reservasi barang terkini yang menunggu verifikasi admin sarpras</p>
                        </div>
                        <a class="text-xs font-semibold text-[#0F766E] hover:underline" href="#">Kelola Semua</a>
                    </div>

                    <!-- Queue List Items -->
                    <div class="space-y-3">
                        @forelse ($pendingLoans as $loan)
                            <div class="p-3 bg-[#F0FDFA]/50 border border-[#D9E4E2] rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F0FDFA] transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-[22px]">assignment</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-xs text-[#1F2937]">
                                                {{ $loan->inventory->nama_barang ?? $loan->category->nama_kategori ?? 'Permohonan Peminjaman' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FEF9C3] text-[#854D0E]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#EAB308] mr-1"></span>
                                                Menunggu
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[#4B5563] text-xs mt-1">
                                            <span class="flex items-center gap-1 font-medium text-[#1F2937]">
                                                <span class="material-symbols-outlined text-[15px] text-[#6B7280]">person</span>
                                                {{ $loan->nama_peminjam }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[15px] text-[#6B7280]">meeting_room</span>
                                                {{ $loan->inventory->room->nama_ruangan ?? '-' }}
                                            </span>
                                            <span class="flex items-center gap-1 text-[#0F766E]">
                                                <span class="material-symbols-outlined text-[15px]">schedule</span>
                                                {{ $loan->tanggal_pinjam->format('H:i') }} - {{ $loan->tanggal_kembali->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 self-end sm:self-center">
                                    <button class="h-8 px-3 rounded-lg text-[#0F766E] hover:bg-white font-semibold text-xs transition-colors" type="button">Tinjau</button>
                                    <button class="h-8 px-3 rounded-lg bg-[#0F766E] text-white font-semibold text-xs hover:bg-[#115E59] shadow-sm transition-colors" type="button">Setujui</button>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-[#6B7280] text-xs bg-[#F9FAFB] rounded-lg border border-dashed border-[#D9E4E2]">
                                Tidak ada permohonan peminjaman yang menunggu persetujuan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Section 3: Data Table Inventaris & Riwayat Terkini (Dinamis dari Database) -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden mb-6">
                <!-- Table Header & Filtration Controls Form -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="p-5 bg-white border-b border-[#D9E4E2]">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h2 class="font-bold text-base text-[#1F2937]">Daftar Inventaris & Riwayat Terkini</h2>
                            <p class="text-xs text-[#4B5563]">Status ketersediaan barang operasional dan peminjam aktif</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <!-- Search Input -->
                            <div class="relative min-w-[240px]">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[18px]">search</span>
                                <input name="search"
                                       value="{{ request('search') }}"
                                       class="w-full h-10 pl-9 pr-3 text-xs bg-[#F3F7F6] rounded-lg text-[#1F2937] placeholder-[#6B7280] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F766E]/30 border border-[#D9E4E2]"
                                       placeholder="Cari kode atau nama barang...">
                            </div>
                            <!-- Filter Ruangan -->
                            <div class="relative">
                                <select name="room_id"
                                        onchange="this.form.submit()"
                                        class="h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F766E]/30 border border-[#D9E4E2]">
                                    <option value="">Semua Ruangan</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none text-[18px]">expand_more</span>
                            </div>
                            <!-- Filter Status -->
                            <div class="relative">
                                <select name="status"
                                        onchange="this.form.submit()"
                                        class="h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F766E]/30 border border-[#D9E4E2]">
                                    <option value="">Semua Status</option>
                                    <option value="baik" {{ request('status') == 'baik' ? 'selected' : '' }}>Baik / Tersedia</option>
                                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                                    <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak / Perlu Servis</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none text-[18px]">expand_more</span>
                            </div>
                            <!-- Submit Filter -->
                            <button type="submit" class="h-10 px-3 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg flex items-center gap-1.5 font-semibold text-xs transition-colors">
                                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                                <span>Filter</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Responsive Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                                <th class="py-3 px-4">KODE BARANG</th>
                                <th class="py-3 px-4">NAMA ASET & KATEGORI</th>
                                <th class="py-3 px-4">RUANGAN</th>
                                <th class="py-3 px-4">PENANGGUNG JAWAB / PEMINJAM</th>
                                <th class="py-3 px-4">STATUS KONDISI</th>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#D9E4E2] text-xs">
                            @forelse ($inventories as $inv)
                                @php
                                    $activeLoan = $inv->loans->first();
                                    $isTerlambat = $activeLoan && $activeLoan->is_terlambat;
                                @endphp
                                <tr class="bg-white hover:bg-[#F3F7F6] transition-colors {{ $activeLoan && $activeLoan->status === 'menunggu' ? 'bg-[#FEF3C7] shadow-[inset_3px_0_0_#F59E0B]' : '' }}">
                                    <td class="py-3.5 px-4 font-mono font-medium text-[#1F2937]">
                                        <div class="flex items-center gap-1.5">
                                            @if ($activeLoan && $activeLoan->status === 'menunggu')
                                                <span class="w-2 h-2 rounded-full bg-[#EAB308] animate-ping"></span>
                                            @endif
                                            {{ $inv->kode_barang }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-[#1F2937]">{{ $inv->nama_barang }}</div>
                                        <div class="text-[11px] text-[#6B7280]">{{ $inv->category->nama_kategori ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-[#4B5563]">
                                        {{ $inv->room->nama_ruangan ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($activeLoan)
                                            <div class="font-medium text-[#1F2937]">{{ $activeLoan->nama_peminjam }}</div>
                                            @if ($isTerlambat)
                                                <div class="text-[11px] text-[#991B1B] font-semibold flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">warning</span>
                                                    Jatuh tempo lewat
                                                </div>
                                            @else
                                                <div class="text-[11px] text-[#6B7280]">Peminjam Aktif</div>
                                            @endif
                                        @else
                                            <span class="text-[#6B7280] italic">{{ $inv->room->penanggung_jawab ?? 'Tersimpan' }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($inv->status === 'baik')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#DCFCE7] text-[#166534]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A] mr-1.5"></span>
                                                Baik / Tersedia
                                            </span>
                                        @elseif ($inv->status === 'dipinjam')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] {{ $isTerlambat ? 'bg-[#FEE2E2] text-[#991B1B]' : 'bg-[#DBEAFE] text-[#1E40AF]' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $isTerlambat ? 'bg-[#DC2626]' : 'bg-[#2563EB]' }} mr-1.5"></span>
                                                {{ $isTerlambat ? 'Terlambat' : 'Dipinjam' }}
                                            </span>
                                        @elseif ($inv->status === 'rusak')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#FEE2E2] text-[#991B1B]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] mr-1.5"></span>
                                                Rusak / Perlu Servis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#FEF9C3] text-[#854D0E]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#EAB308] mr-1.5"></span>
                                                {{ ucfirst($inv->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-[#0F766E] hover:bg-[#E5E9E8] transition-colors" title="Lihat Detail" type="button">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                            </button>
                                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-[#0F766E] hover:bg-[#E5E9E8] transition-colors" title="Ubah Data" type="button">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-[#6B7280] italic">
                                        Belum ada data inventaris yang tersimpan di database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination Dynamic -->
                <div class="px-5 py-3 bg-[#F3F7F6] flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#D9E4E2]">
                    <p class="text-xs text-[#4B5563]">
                        Menampilkan <span class="font-semibold text-[#1F2937]">{{ $inventories->firstItem() ?? 0 }} - {{ $inventories->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ number_format($inventories->total(), 0, ',', '.') }}</span> data inventaris
                    </p>
                    <div class="text-xs">
                        {{ $inventories->withQueryString()->links() }}
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Dashboard Footer -->
    <footer class="w-full bg-[#134E4A] text-[#CCFBF1] py-3 px-6 text-center text-xs border-t border-[#115E59]">
        <p>© 2024 {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }} • Sistem Informasi Sarana & Prasarana Sekolah</p>
    </footer>
</div>
@endsection
