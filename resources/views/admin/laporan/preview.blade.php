@extends('layouts.admin')

@section('title', 'Pratinjau Laporan Inventaris KIB B')

@push('styles')
<style>
    /* Document Landscape Preview Layout */
    .report-paper {
        background-color: #ffffff;
        color: #000000;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        margin: 0 auto;
        width: 100%;
        max-width: 1180px;
        padding: 32px 36px 40px 36px;
    }

    /* Official Table Styling */
    .kib-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9px;
        line-height: 1.25;
    }
    .kib-table th, .kib-table td {
        border: 1px solid #000000;
        padding: 3px 3px;
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
        font-size: 8px;
        padding: 1.5px 2px;
    }

    /* Print Specific Rules */
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
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
        .report-paper {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .kib-table {
            font-size: 8pt !important;
            page-break-inside: auto;
        }
        .kib-table thead {
            display: table-row-group !important; /* Mencegah pengulangan judul kolom saat ganti halaman */
        }
        .kib-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        @page {
            size: A4 landscape;
            margin: 2.54cm 2.54cm 2.54cm 2.54cm; /* Margin 2.54 cm pada seluruh sisi */
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar (No Print) -->
    <div class="no-print bg-white rounded-xl border border-[#D9E4E2] p-4 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-4 sticky top-18 z-20 backdrop-blur-md bg-white/95">
        <div class="flex items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('admin.laporan.index', $filters) }}"
               class="h-10 px-3.5 border border-[#D9E4E2] rounded-lg text-xs font-semibold text-[#374151] hover:bg-[#F3F7F6] transition-colors flex items-center gap-1.5 shrink-0">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali</span>
            </a>

            <div class="h-6 w-[1px] bg-gray-200 hidden sm:block"></div>

            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-bold text-[#1F2937]">Pratinjau Dokumen KIB B</h1>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase
                        {{ $activeFormat === 'pdf' ? 'bg-red-100 text-red-700 border border-red-200' :
                          ($activeFormat === 'xlsx' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                          ($activeFormat === 'docx' ? 'bg-blue-100 text-blue-800 border border-blue-200' :
                          'bg-purple-100 text-purple-800 border border-purple-200')) }}">
                        .{{ $activeFormat }} ({{ $formatInfo['badge'] }})
                    </span>
                </div>
                <p class="text-[11px] text-[#6B7280]">
                    {{ number_format($totalItems) }} Unit Barang
                </p>
            </div>
        </div>

        <!-- Controls: Format Switcher, Print & Download Buttons -->
        <div class="flex items-center gap-2.5 w-full lg:w-auto justify-end flex-wrap">

            <!-- Switch Format Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button"
                        @click="open = !open"
                        class="h-10 px-3 border border-[#D9E4E2] rounded-lg text-xs font-medium text-[#374151] hover:bg-[#F3F7F6] transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px] text-[#6B7280]">swap_horiz</span>
                    <span>Ganti Format: <strong class="uppercase text-[#0F766E]">.{{ $activeFormat }}</strong></span>
                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                </button>

                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-[#D9E4E2] p-1.5 z-30 select-none"
                     style="display: none;">
                    <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pilih Format</div>
                    @foreach($supportedFormats as $fmtKey => $fmtItem)
                        <a href="{{ route('admin.laporan.preview', array_merge($filters, ['format' => $fmtKey])) }}"
                           class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-colors {{ $activeFormat === $fmtKey ? 'bg-[#0F766E]/10 text-[#0F766E] font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">{{ $fmtItem['icon'] }}</span>
                                <span>{{ $fmtItem['label'] }}</span>
                            </div>
                            <span class="text-[10px] font-mono font-bold uppercase">.{{ $fmtKey }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Print Button -->
            <button type="button"
                    onclick="window.print()"
                    class="h-10 px-3.5 border border-[#D9E4E2] rounded-lg text-xs font-semibold text-[#374151] hover:bg-[#F3F7F6] transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-[#6B7280]">print</span>
                <span class="hidden sm:inline">Cetak</span>
            </button>

            <!-- Download Button -->
            @if(in_array($activeFormat, ['png', 'jpg', 'jpeg', 'webp']))
                <button type="button"
                        id="btnDownloadImage"
                        onclick="triggerImageDownload('{{ $activeFormat }}')"
                        class="h-10 px-5 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Download .{{ strtoupper($activeFormat) }}</span>
                </button>
            @else
                <a href="{{ route('admin.laporan.download', array_merge($filters, ['format' => $activeFormat])) }}"
                   id="btnDownloadBackend"
                   class="h-10 px-5 bg-[#0F766E] hover:bg-[#115E59] text-white rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Download .{{ strtoupper($activeFormat) }}</span>
                </a>
            @endif

        </div>
    </div>

    <!-- Active Filters Information Bar (if filters are set) -->
    @if(array_filter($filters))
        <div class="no-print bg-[#F0FDFA] border border-[#CCFBF1] rounded-xl px-4 py-2.5 text-xs text-[#0F766E] flex items-center justify-between">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold">Filter Aktif:</span>
                @if(!empty($filters['search']))
                    <span class="bg-white px-2 py-0.5 rounded border border-[#CCFBF1]">Cari: "{{ $filters['search'] }}"</span>
                @endif
                @if(!empty($filters['category_id']))
                    @php
                        $catNames = \App\Models\Category::findMany((array)$filters['category_id'])->pluck('nama_kategori')->implode(', ');
                    @endphp
                    @if($catNames)
                        <span class="bg-white px-2 py-0.5 rounded border border-[#CCFBF1]">Kategori: {{ $catNames }}</span>
                    @endif
                @endif
                @if(!empty($filters['room_id']))
                    @php
                        $roomNames = \App\Models\Room::findMany((array)$filters['room_id'])->pluck('nama_ruangan')->implode(', ');
                    @endphp
                    @if($roomNames)
                        <span class="bg-white px-2 py-0.5 rounded border border-[#CCFBF1]">Ruangan: {{ $roomNames }}</span>
                    @endif
                @endif
                @if(!empty($filters['status']))
                    @php
                        $statusText = is_array($filters['status']) ? implode(', ', array_map('ucfirst', $filters['status'])) : ucfirst($filters['status']);
                    @endphp
                    <span class="bg-white px-2 py-0.5 rounded border border-[#CCFBF1]">Status: {{ $statusText }}</span>
                @endif
                @if(!empty($filters['tahun']))
                    <span class="bg-white px-2 py-0.5 rounded border border-[#CCFBF1]">Tahun: {{ $filters['tahun'] }}</span>
                @endif
            </div>
            <a href="{{ route('admin.laporan.preview', ['format' => $activeFormat]) }}" class="text-xs font-semibold text-red-600 hover:underline">
                Hapus Semua Filter
            </a>
        </div>
    @endif

    <!-- Scrollable container for landscape table preview -->
    <div class="overflow-x-auto pb-6">

        <!-- DOCUMENT PREVIEW CONTAINER (Matching kop.jpeg and bawah.jpeg official format) -->
        <div id="reportDocument" class="report-paper">

            <!-- Bagian Header / Identitas Instansi (sesuai kop.jpeg: Logo di kiri, Judul di tengah, lalu identitas menyatu) -->
            <div class="border-b-2 border-black pb-3 mb-4">
                <div class="relative flex items-center justify-center min-h-[72px] mb-2">
                    <!-- Logo Instansi di Sebelah Kiri -->
                    @php
                        $logoSrc = (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo)))
                            ? asset($schoolProfile->logo)
                            : asset('images/logo-sekolah.png');
                    @endphp
                    <div class="absolute left-0 top-1/2 -translate-y-1/2">
                        <img src="{{ $logoSrc }}" alt="Logo" class="w-16 h-16 object-contain shrink-0" crossorigin="anonymous">
                    </div>

                    <!-- Judul Surat Resmi Di Tengah (Justified Center) -->
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

            <!-- Bagian Tabel Data Inventaris (16 Kolom persis kop.jpeg) -->
            <div class="mt-4">
                <table class="kib-table">
                    <thead>
                        <!-- Row 1: Header Titles (Proposi 100% Sesuai PDF Asli) -->
                        <tr>
                            <th rowspan="2" style="width: 3%;">No.</th>
                            <th rowspan="2" style="width: 8%;">Kode Barang</th>
                            <th rowspan="2" style="width: 18%;">Jenis Barang / Nama Barang</th>
                            <th rowspan="2" style="width: 4.5%;">Nomor Registrasi</th>
                            <th rowspan="2" style="width: 8%;">Merk / Type</th>
                            <th rowspan="2" style="width: 4.5%;">Ukuran / CC</th>
                            <th rowspan="2" style="width: 4.5%;">Bahan</th>
                            <th rowspan="2" style="width: 4.5%;">Tahun Pembelian</th>
                            <th colspan="5" class="py-1" style="width: 20%;">Nomor</th>
                            <th rowspan="2" style="width: 7%;">Asal Usul Cara Perolehan</th>
                            <th rowspan="2" style="width: 9%;">Harga (Ribuan Rp)</th>
                            <th rowspan="2" style="width: 9%;">Keterangan</th>
                        </tr>
                        <!-- Row 2: Sub-columns for Nomor -->
                        <tr>
                            <th style="width: 4%;">Pabrik</th>
                            <th style="width: 4%;">Rangka</th>
                            <th style="width: 4%;">Mesin</th>
                            <th style="width: 4%;">Polisi</th>
                            <th style="width: 4%;">BPKB</th>
                        </tr>
                        <!-- Row 3: Column Numbers [1] to [16] -->
                        <tr class="col-numbers">
                            @for($col = 1; $col <= 16; $col++)
                                <td>{{ $col }}</td>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inv)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="text-center font-medium">{{ $loop->iteration }}</td>
                                <td class="font-mono text-[10px] text-center">{{ $inv->kode_barang ?: '-' }}</td>
                                <td>
                                    <div class="font-semibold text-black">{{ $inv->nama_barang ?: '-' }}</div>
                                    @if($inv->category)
                                        <div class="text-[8px] text-[#0F766E] font-medium">Kelompok: {{ $inv->category->nama_kategori }}</div>
                                    @endif
                                </td>
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_register ?: '-' }}</td>
                                <td>{{ $inv->merk_type ?: '-' }}</td>
                                <td class="text-center">{{ $inv->ukuran_cc ?: '-' }}</td>
                                <td class="text-center">{{ $inv->bahan ?: '-' }}</td>
                                <td class="text-center font-mono">{{ $inv->tahun_pembelian ?: '-' }}</td>

                                <!-- 5 Sub-columns for Nomor -->
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_pabrik ?: '-' }}</td>
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_rangka ?: '-' }}</td>
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_mesin ?: '-' }}</td>
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_polisi ?: '-' }}</td>
                                <td class="text-center font-mono text-[10px]">{{ $inv->nomor_bpkb ?: '-' }}</td>

                                <td>{{ $inv->asal_usul ?: '-' }}</td>
                                <td class="text-right font-medium">Rp {{ number_format($inv->harga ?? 0, 0, ',', '.') }}</td>
                                <td class="text-gray-700 text-[10px]">{{ $inv->keterangan ?? $inv->deskripsi ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center py-8 text-gray-500 italic">
                                    Tidak ada data inventaris yang ditemukan sesuai dengan kriteria yang dipilih.
                                </td>
                            </tr>
                        @endforelse

                        <!-- Baris Total Keseluruhan Harga -->
                        <tr class="bg-gray-100 font-bold text-black border-t-2 border-black">
                            <td colspan="14" class="text-right px-4 py-2 font-bold tracking-wide uppercase text-xs">
                                JUMLAH HARGA KESELURUHAN :
                            </td>
                            <td class="text-right px-2 py-2 font-bold text-xs text-black">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian Pengesahan / Mengetahui (sesuai bawah.jpeg) -->
            <div class="mt-10 pt-4 text-xs text-black" style="page-break-inside: avoid;">
                <div class="grid grid-cols-2 gap-8">

                    <!-- Kolom Kiri: Mengetahui / Kepala Sekolah -->
                    <div class="text-center">
                        <p class="font-normal text-xs text-black">Mengetahui,</p>
                        <p class="font-bold text-sm text-black">Kepala Sekolah</p>
                        <div class="h-20 flex items-center justify-center">
                            <!-- Signature stamp space -->
                        </div>
                        <p class="font-bold underline text-sm text-black tracking-wide">
                            ( {{ $schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd' }} )
                        </p>
                        <p class="text-xs text-black font-medium mt-0.5">
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
                        <p class="font-normal text-xs text-black">{{ $lokasi }}, {{ $generatedDate }}</p>
                        <p class="font-bold text-sm text-black">Pembuat Laporan</p>
                        <div class="h-20 flex items-center justify-center">
                            <!-- Signature stamp space -->
                        </div>
                        <p class="font-bold underline text-sm text-black tracking-wide">
                            ( {{ $schoolProfile->nama_pembuat ?: 'Admin Sarpras' }} )
                        </p>
                        <p class="text-xs text-black font-medium mt-0.5">
                            NIP. {{ $schoolProfile->nip_pembuat ?: '19800101 200501 1 001' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- html2canvas for client-side rendering of PNG, JPG, JPEG, and WebP -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    function triggerImageDownload(format) {
        const docElem = document.getElementById('reportDocument');
        if (!docElem) return;

        Swal.fire({
            title: 'Menyiapkan Gambar...',
            text: 'Sedang me-render tampilan dokumen resolusi tinggi ke format .' + format.toUpperCase(),
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Set higher scale for sharp rendering (2x retina quality)
        html2canvas(docElem, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            windowWidth: 1400
        }).then(canvas => {
            let mimeType = 'image/png';
            let quality = 0.95;

            if (format === 'jpg' || format === 'jpeg') {
                mimeType = 'image/jpeg';
            } else if (format === 'webp') {
                mimeType = 'image/webp';
            }

            const dataUrl = canvas.toDataURL(mimeType, quality);
            const now = new Date();
            const dateStr = now.getFullYear() +
                String(now.getMonth() + 1).padStart(2, '0') +
                String(now.getDate()).padStart(2, '0') + '_' +
                String(now.getHours()).padStart(2, '0') +
                String(now.getMinutes()).padStart(2, '0');

            const fileName = `Laporan_Inventaris_KIB_B_${dateStr}.${format}`;

            // Create download anchor
            const link = document.createElement('a');
            link.download = fileName;
            link.href = dataUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil Diunduh!',
                text: `Dokumen laporan berhasil diunduh dalam format .${format.toUpperCase()}`,
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(err => {
            console.error('Render error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat Gambar',
                text: 'Terjadi kendala saat memproses gambar pratinjau: ' + err.message
            });
        });
    }

    // Auto-download if triggered from redirect (e.g. download endpoint for image formats)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_download') === '1') {
            const format = urlParams.get('format') || 'png';
            if (['png', 'jpg', 'jpeg', 'webp'].includes(format)) {
                setTimeout(() => {
                    triggerImageDownload(format);
                }, 600);
            }
        }
    });
</script>
@endsection

