@extends('layouts.admin')

@section('title', 'Detail Inventaris: ' . $inventory->nama_barang . ' - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">{{ $inventory->nama_barang }}</h1>
                <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold bg-[#F3F7F6] text-[#1F2937] border border-[#D9E4E2]">
                    {{ $inventory->kode_barang }}
                </span>
            </div>
            <p class="text-xs text-[#4B5563] mt-0.5">
                Rincian data fisik, status unit, spesifikasi legalitas, dan riwayat pemanfaatan barang inventaris sekolah.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inventaris.index') }}"
               class="h-10 px-4 rounded-lg bg-white border border-[#D9E4E2] text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali</span>
            </a>
            <a href="{{ route('admin.inventaris.edit', $inventory->id) }}"
               class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                <span>Edit Inventaris</span>
            </a>
        </div>
    </div>

    <!-- Main Detail Layout: Foto Kiri (4 cols), Informasi Kanan (8 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- =================================================================== -->
        <!-- SISI KIRI: FOTO BARANG & STATUS RINGKAS (4 Kolom)                   -->
        <!-- =================================================================== -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Card Foto Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden p-5 flex flex-col items-center">
                <div class="w-full aspect-4/3 bg-[#F3F7F6] rounded-lg border border-[#D9E4E2] overflow-hidden flex items-center justify-center relative shadow-inner">
                    @if($inventory->foto_url)
                        <img src="{{ $inventory->foto_url }}"
                             alt="{{ $inventory->nama_barang }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center text-[#9CA3AF] p-6 text-center">
                            <span class="material-symbols-outlined text-[48px] mb-2 text-[#CBD5E1]">image_not_supported</span>
                            <span class="text-xs font-medium text-[#6B7280]">Belum ada foto barang</span>
                            <span class="text-[11px] text-[#9CA3AF] mt-0.5">Unggah foto melalui menu Edit</span>
                        </div>
                    @endif
                </div>

                <!-- Status Unit Badge Banner (Kondisi Fisik) -->
                @php
                    $statusUnit = strtolower($inventory->status ?? 'baik');
                    if (in_array($statusUnit, ['tersedia', 'dipinjam'])) {
                        $statusUnit = 'baik';
                    }
                @endphp
                <div class="w-full mt-4 p-3 bg-[#F8FBFA] rounded-lg border border-[#D9E4E2] flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#4B5563]">Status Unit:</span>
                    @if($statusUnit === 'rusak')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#FEE2E2] text-[#991B1B]">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626] mr-2"></span>
                            Rusak
                        </span>
                    @elseif($statusUnit === 'hilang')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#FEF3C7] text-[#92400E]">
                            <span class="w-2 h-2 rounded-full bg-[#D97706] mr-2"></span>
                            Hilang
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#166534]">
                            <span class="w-2 h-2 rounded-full bg-[#16A34A] mr-2"></span>
                            Baik
                        </span>
                    @endif
                </div>

                <!-- Status Transaksi Peminjaman (Terpisah Jelas) -->
                @if($inventory->is_currently_borrowed)
                    <div class="w-full mt-2 p-3 bg-[#EFF6FF] rounded-lg border border-[#BFDBFE] flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#1E40AF]">Status Peminjaman:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DBEAFE] text-[#1E40AF]">
                            <span class="w-2 h-2 rounded-full bg-[#2563EB] mr-1.5"></span>
                            Sedang Dipinjam
                        </span>
                    </div>
                @endif

                <!-- Quick Metadata -->
                <div class="w-full mt-4 flex flex-col gap-2.5 text-xs text-[#4B5563] pt-4 border-t border-[#D9E4E2]">
                    <div class="flex items-center justify-between">
                        <span class="text-[#6B7280]">Kategori:</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->category->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#6B7280]">Ruangan:</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->room->nama_ruangan ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#6B7280]">Penanggung Jawab:</span>
                        <span class="font-medium text-[#1F2937]">{{ $inventory->room->penanggung_jawab ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#6B7280]">Tercatat Sejak:</span>
                        <span class="font-medium text-[#1F2937]">{{ $inventory->created_at ? $inventory->created_at->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- SISI KANAN: DETAIL INFORMASI LENGKAP & KELOMPOK DATA (8 Kolom)      -->
        <!-- =================================================================== -->
        <div class="lg:col-span-8 flex flex-col gap-6">

            <!-- KELOMPOK 1: INFORMASI UTAMA & SPESIFIKASI FISIK -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
                <div class="px-5 py-3.5 bg-[#F0FDFA] border-b border-[#CCFBF1] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0F766E] text-[20px]">info</span>
                    <h2 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider">1. Informasi Utama & Spesifikasi Teknis</h2>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Kode Barang</span>
                        <span class="font-mono font-bold text-[#1F2937] text-sm">{{ $inventory->kode_barang }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nama Barang</span>
                        <span class="font-semibold text-[#1F2937] text-sm">{{ $inventory->nama_barang }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor Register</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_register ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Merek / Tipe</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->merk_type ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Ukuran / CC</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->ukuran_cc ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Bahan / Material</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->bahan ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- KELOMPOK 2: ADMINISTRASI & IDENTITAS KENDARAAN / MESIN -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
                <div class="px-5 py-3.5 bg-[#F0FDFA] border-b border-[#CCFBF1] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0F766E] text-[20px]">badge</span>
                    <h2 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider">2. Nomor Pabrik & Legalitas Kendaraan / Mesin</h2>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor Pabrik</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_pabrik ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor Rangka</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_rangka ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor Mesin</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_mesin ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor Polisi</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_polisi ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Nomor BPKB</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->nomor_bpkb ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- KELOMPOK 3: PENGADAAN & NILAI ASET -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
                <div class="px-5 py-3.5 bg-[#F0FDFA] border-b border-[#CCFBF1] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0F766E] text-[20px]">payments</span>
                    <h2 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider">3. Asal Usul & Nilai Pengadaan Aset</h2>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Tahun Pembelian</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->tahun_pembelian ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Asal / Sumber Perolehan</span>
                        <span class="font-semibold text-[#1F2937]">{{ $inventory->asal_usul ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-3 rounded-lg bg-[#F8FBFA] border border-[#E5EBEA]">
                        <span class="text-[#6B7280]">Harga Perolehan</span>
                        <span class="font-semibold text-[#0F766E] text-sm">
                            @if($inventory->harga > 0)
                                Rp {{ number_format($inventory->harga, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- KELOMPOK 4: DESKRIPSI & CATATAN KONDISI -->
            <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
                <div class="px-5 py-3.5 bg-[#F0FDFA] border-b border-[#CCFBF1] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0F766E] text-[20px]">description</span>
                    <h2 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider">4. Deskripsi & Catatan Tambahan</h2>
                </div>
                <div class="p-5 text-xs text-[#1F2937] leading-relaxed">
                    @if($inventory->deskripsi || $inventory->keterangan)
                        <p class="whitespace-pre-line">{{ $inventory->deskripsi ?? $inventory->keterangan }}</p>
                    @else
                        <span class="text-[#9CA3AF] italic">- Tidak ada keterangan tambahan -</span>
                    @endif
                </div>
            </div>

            <!-- KELOMPOK 5: RIWAYAT PEMINJAMAN TERAKHIR (Bila Ada) -->
            @if($inventory->loans && $inventory->loans->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
                    <div class="px-5 py-3.5 bg-[#F0FDFA] border-b border-[#CCFBF1] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#0F766E] text-[20px]">history</span>
                            <h2 class="text-xs font-bold text-[#1F2937] uppercase tracking-wider">5. Riwayat Peminjaman Unit</h2>
                        </div>
                        <span class="text-[11px] text-[#6B7280] font-medium">{{ $inventory->loans->count() }} Transaksi</span>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-[#F8FBFA] border-b border-[#D9E4E2] text-[#6B7280] font-semibold">
                                    <th class="py-2.5 px-4">Nama Peminjam</th>
                                    <th class="py-2.5 px-4">Tgl Pinjam</th>
                                    <th class="py-2.5 px-4">Tgl Kembali</th>
                                    <th class="py-2.5 px-4 text-center">Status Transaksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#D9E4E2]">
                                @foreach($inventory->loans->take(5) as $loan)
                                    <tr class="hover:bg-[#F8FBFA] transition-colors">
                                        <td class="py-2.5 px-4 font-semibold text-[#1F2937]">{{ $loan->nama_peminjam }}</td>
                                        <td class="py-2.5 px-4 text-[#4B5563]">{{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="py-2.5 px-4 text-[#4B5563]">{{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="py-2.5 px-4 text-center">
                                            @if($loan->status === 'dipinjam')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#DBEAFE] text-[#1E40AF]">Dipinjam</span>
                                            @elseif($loan->status === 'selesai')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#DCFCE7] text-[#166534]">Selesai</span>
                                            @elseif($loan->status === 'menunggu')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#FEF9C3] text-[#854D0E]">Menunggu</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#F3F7F6] text-[#4B5563]">{{ ucfirst($loan->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
