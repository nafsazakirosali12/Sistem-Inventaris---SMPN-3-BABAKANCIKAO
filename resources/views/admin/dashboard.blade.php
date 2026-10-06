@extends('layouts.admin')

@section('title', 'Dashboard - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Dashboard</h1>
        <p class="text-xs text-[#4B5563] mt-0.5">Ringkasan data aset inventaris dan aktivitas peminjaman barang terkini.</p>
    </div>

    {{-- ── Section 1: 3 Stat Cards Utama ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Card 1: Total Aset Inventaris --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col justify-between hover:shadow-md transition-all">
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
                    Tersebar dalam {{ $totalKategori }} Kategori
                </span>
                <a href="{{ route('admin.inventaris.index') }}" class="font-semibold text-[#0F766E] hover:underline">Lihat Semua →</a>
            </div>
        </div>

        {{-- Card 2: Total Kategori --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col justify-between hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">Total Kategori</span>
                    <div class="mt-2 text-3xl text-[#1F2937] font-bold leading-none">
                        {{ number_format($totalKategori, 0, ',', '.') }}
                        <span class="text-sm font-normal text-[#4B5563]">Kategori</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#F3E8FF] flex items-center justify-center text-[#7C3AED] flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px]">category</span>
                </div>
            </div>
            <div class="mt-4 pt-3 bg-[#F5F3FF]/60 -mx-5 -mb-5 px-5 py-2 flex items-center justify-between text-[#4B5563] text-xs border-t border-[#E9D5FF]">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#7C3AED] text-[16px]">inventory_2</span>
                    Klasifikasi jenis barang inventaris
                </span>
                <a href="{{ route('admin.kategori.index') }}" class="font-semibold text-[#7C3AED] hover:underline">Kelola →</a>
            </div>
        </div>

        {{-- Card 3: Total Peminjaman --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col justify-between hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">Total Peminjaman</span>
                    <div class="mt-2 text-3xl text-[#1F2937] font-bold leading-none">
                        {{ number_format($totalPeminjaman, 0, ',', '.') }}
                        <span class="text-sm font-normal text-[#4B5563]">Transaksi</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#DBEAFE] flex items-center justify-center text-[#2563EB] flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px]">front_hand</span>
                </div>
            </div>
            <div class="mt-4 pt-3 bg-[#EFF6FF]/60 -mx-5 -mb-5 px-5 py-2 flex items-center justify-between text-[#4B5563] text-xs border-t border-[#BFDBFE]">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#2563EB] text-[16px]">assignment</span>
                    {{ $sedangDipinjam }} unit sedang dipinjam
                </span>
                <a href="{{ route('admin.peminjaman.index') }}" class="font-semibold text-[#1E40AF] hover:underline">Lihat Log →</a>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Kondisi & Ketersediaan ───────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">

        {{-- Kolom Kiri: Donut Chart Kondisi Inventaris (5 cols) --}}
        <div class="lg:col-span-5 bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col h-full">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-base text-[#1F2937]">Kondisi Inventaris</h2>
                    <p class="text-xs text-[#4B5563]">Status kondisi fisik barang inventaris sekolah</p>
                </div>
                <span class="material-symbols-outlined text-[#6B7280] text-[20px]">pie_chart</span>
            </div>

            <div class="py-3 flex flex-col sm:flex-row items-center justify-center gap-6">
                {{-- Donut SVG --}}
                <div class="relative w-44 h-44 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" fill="transparent" r="38" stroke="#E5E9E8" stroke-width="12"></circle>
                        {{-- Baik --}}
                        <circle cx="50" cy="50" fill="transparent" r="38"
                                stroke="#16A34A"
                                stroke-dasharray="{{ $baikDash }} {{ $circumference }}"
                                stroke-dashoffset="0"
                                stroke-width="12"></circle>
                        {{-- Dipinjam --}}
                        <circle cx="50" cy="50" fill="transparent" r="38"
                                stroke="#2563EB"
                                stroke-dasharray="{{ $dipinjamChartDash }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset1 }}"
                                stroke-width="12"></circle>
                        {{-- Rusak --}}
                        <circle cx="50" cy="50" fill="transparent" r="38"
                                stroke="#DC2626"
                                stroke-dasharray="{{ $rusakDash }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset2 }}"
                                stroke-width="12"></circle>
                        {{-- Perlu Perbaikan --}}
                        <circle cx="50" cy="50" fill="transparent" r="38"
                                stroke="#F59E0B"
                                stroke-dasharray="{{ $perluPerbaikanDash }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset3 }}"
                                stroke-width="12"></circle>
                        {{-- Hilang --}}
                        <circle cx="50" cy="50" fill="transparent" r="38"
                                stroke="#6B7280"
                                stroke-dasharray="{{ $hilangDash }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset4 }}"
                                stroke-width="12"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                        <span class="text-xl text-[#1F2937] leading-tight font-bold">{{ number_format($totalAset, 0, ',', '.') }}</span>
                        <span class="text-[11px] text-[#6B7280]">Total Unit</span>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="w-full flex flex-col gap-2.5">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-[#DCFCE7]/40 border border-[#DCFCE7]">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#16A34A] flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-[#1F2937]">Baik</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-[#166534]">{{ $baikCount }} Unit</span>
                            <span class="text-[11px] text-[#6B7280] ml-1">({{ $baikPct }}%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-[#DBEAFE]/40 border border-[#DBEAFE]">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#2563EB] flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-[#1F2937]">Dipinjam</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-[#1E40AF]">{{ $dipinjamCount }} Unit</span>
                            <span class="text-[11px] text-[#6B7280] ml-1">({{ $dipinjamChartPct }}%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-[#FEE2E2]/40 border border-[#FEE2E2]">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#DC2626] flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-[#1F2937]">Rusak</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-[#991B1B]">{{ $rusakCount }} Unit</span>
                            <span class="text-[11px] text-[#6B7280] ml-1">({{ $rusakPct }}%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-[#FEF3C7]/50 border border-[#FDE68A]">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#F59E0B] flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-[#1F2937]">Perlu Perbaikan</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-[#92400E]">{{ $perluPerbaikanCount }} Unit</span>
                            <span class="text-[11px] text-[#6B7280] ml-1">({{ $perluPerbaikanPct }}%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-[#F3F4F6]/50 border border-[#E5E7EB]">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#6B7280] flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-[#1F2937]">Hilang</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-[#374151]">{{ $hilangCount }} Unit</span>
                            <span class="text-[11px] text-[#6B7280] ml-1">({{ $hilangPct }}%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Ketersediaan Barang (7 cols) --}}
        <div class="lg:col-span-7 bg-white rounded-xl p-5 shadow-sm border border-[#D9E4E2] flex flex-col h-full">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-bold text-base text-[#1F2937]">Ketersediaan Barang</h2>
                    <p class="text-xs text-[#4B5563]">Status unit inventaris berdasarkan kondisi peminjaman aktif</p>
                </div>
                <a href="{{ route('admin.peminjaman.index') }}" class="text-xs font-semibold text-[#0F766E] hover:underline">Kelola Peminjaman →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                {{-- Sedang Dipinjam --}}
                <div class="flex flex-col gap-3 p-4 rounded-xl bg-[#EFF6FF] border border-[#BFDBFE]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-lg bg-[#DBEAFE] flex items-center justify-center text-[#2563EB]">
                                <span class="material-symbols-outlined text-[20px]">front_hand</span>
                            </div>
                            <span class="text-xs font-semibold text-[#1E40AF]">Sedang Dipinjam</span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB] animate-pulse"></span>
                    </div>
                    <div class="text-3xl font-bold text-[#1E40AF] leading-none">
                        {{ number_format($sedangDipinjam, 0, ',', '.') }}
                        <span class="text-sm font-normal text-[#3B82F6]">Unit</span>
                    </div>
                    <p class="text-[11px] text-[#3B82F6]">Unit inventaris yang sedang berada di tangan peminjam</p>
                </div>

                {{-- Barang Tersedia --}}
                <div class="flex flex-col gap-3 p-4 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-lg bg-[#DCFCE7] flex items-center justify-center text-[#16A34A]">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            </div>
                            <span class="text-xs font-semibold text-[#166534]">Barang Tersedia</span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#16A34A]"></span>
                    </div>
                    <div class="text-3xl font-bold text-[#166534] leading-none">
                        {{ number_format($tersedia, 0, ',', '.') }}
                        <span class="text-sm font-normal text-[#16A34A]">Unit</span>
                    </div>
                    <p class="text-[11px] text-[#15803D]">Unit inventaris dalam kondisi baik dan siap dipinjam</p>
                </div>
            </div>

            @php $totalUnit = $sedangDipinjam + $tersedia; @endphp
            @if($totalUnit > 0)
                <div class="mt-4 pt-4 border-t border-[#D9E4E2]">
                    <div class="flex items-center justify-between text-xs text-[#6B7280] mb-1.5">
                        <span>Tingkat Penggunaan</span>
                        <span class="font-semibold text-[#1F2937]">{{ round(($sedangDipinjam / $totalUnit) * 100) }}%</span>
                    </div>
                    <div class="h-2 bg-[#E5E9E8] rounded-full overflow-hidden">
                        <div class="h-2 bg-[#2563EB] rounded-full transition-all"
                             style="width: {{ round(($sedangDipinjam / $totalUnit) * 100) }}%"></div>
                    </div>
                    <p class="text-[11px] text-[#9CA3AF] mt-1">{{ $sedangDipinjam }} dari {{ $totalUnit }} unit baik/dipinjam</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Section 3: Tabel Barang Terbaru (maks 5) ───────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
        <div class="p-5 bg-white border-b border-[#D9E4E2] flex items-center justify-between">
            <div>
                <h2 class="font-bold text-base text-[#1F2937]">Barang Inventaris Terbaru</h2>
                <p class="text-xs text-[#4B5563]">5 data inventaris yang paling baru ditambahkan ke sistem</p>
            </div>
            <a href="{{ route('admin.inventaris.index') }}"
               class="text-xs font-semibold text-[#0F766E] hover:underline flex items-center gap-1">
                Lihat Semua Data
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                        <th class="py-3 px-4">Kode Barang</th>
                        <th class="py-3 px-4">Nama Aset & Kategori</th>
                        <th class="py-3 px-4">Ruangan</th>
                        <th class="py-3 px-4 text-center">Status Kondisi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9E4E2] text-xs">
                    @forelse ($latestInventories as $inv)
                        <tr class="hover:bg-[#F8FBFA] transition-colors">
                            <td class="py-3.5 px-4 font-mono font-medium text-[#1F2937]">
                                {{ $inv->kode_barang }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1F2937]">{{ $inv->nama_barang }}</div>
                                <div class="text-[11px] text-[#6B7280]">{{ $inv->category->nama_kategori ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#4B5563]">
                                {{ $inv->room->nama_ruangan ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php $s = strtolower($inv->status ?? 'baik'); @endphp
                                @if ($s === 'baik')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#DCFCE7] text-[#166534]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A] mr-1.5"></span>Baik
                                    </span>
                                @elseif ($s === 'dipinjam')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#DBEAFE] text-[#1E40AF]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB] mr-1.5"></span>Dipinjam
                                    </span>
                                @elseif ($s === 'rusak')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#FEE2E2] text-[#991B1B]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] mr-1.5"></span>Rusak
                                    </span>
                                @elseif ($s === 'perlu_perbaikan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#FEF3C7] text-[#92400E]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] mr-1.5"></span>Perlu Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold text-[11px] bg-[#F3F4F6] text-[#374151]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#6B7280] mr-1.5"></span>Hilang
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.inventaris.show', $inv->id) }}"
                                   class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-[#0F766E] hover:bg-[#E5E9E8] transition-colors"
                                   title="Lihat Detail">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-[#6B7280] text-xs italic">
                                Belum ada data inventaris yang tersimpan di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 bg-[#F8FBFA] border-t border-[#D9E4E2] flex items-center justify-between">
            <p class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-semibold text-[#1F2937]">{{ $latestInventories->count() }}</span> dari
                <span class="font-semibold text-[#1F2937]">{{ number_format($totalAset, 0, ',', '.') }}</span> total data inventaris
            </p>
            <!-- <a href="{{ route('admin.inventaris.index') }}"
               class="text-xs font-semibold text-[#0F766E] hover:underline flex items-center gap-1">
                Lihat Semua Data
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a> -->
        </div>
    </div>

</div>
@endsection
