@extends('layouts.admin')

@section('title', 'Data Peminjaman - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

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
<div class="flex flex-col w-full gap-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-1">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Data Peminjaman</h1>
                <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                    Kelola seluruh pengajuan dan riwayat peminjaman sarana dan prasarana oleh guru dan staf {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}.
                </p>
            </div>
            @if(isset($activeLoansCount) && $activeLoansCount > 0)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#DBEAFE] border border-[#BFDBFE] text-[#1E40AF] text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#2563EB] animate-pulse"></span>
                    <span>{{ $activeLoansCount }} Barang Sedang Dipinjam</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Unified Card: Search, Filter, & Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden flex flex-col">
        <!-- Card Header: Search & Filter Toolbar (Tanpa Icon Kiri pada Field/Filter) -->
        <div class="p-4 sm:p-5 border-b border-[#D9E4E2] bg-white">
            <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex flex-col gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Input (lg: 4 cols) -->
                    <div class="relative lg:col-span-4">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kode, nama peminjam, atau alasan..."
                               class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        @if(request('search'))
                            <a href="{{ route('admin.peminjaman.index', request()->except('search')) }}"
                               class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                               title="Hapus kata kunci">
                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                            </a>
                        @endif
                    </div>

                    <!-- Filter Status: Hanya Dipinjam & Selesai (Tanpa icon kiri) (lg: 2 cols) -->
                    <div class="relative lg:col-span-2">
                        <select name="status"
                                onchange="this.form.submit()"
                                class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 border border-[#D9E4E2] truncate">
                            <option value="">Semua Status</option>
                            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <!-- Filter Kategori (Tanpa icon kiri) (lg: 3 cols) -->
                    <div class="relative lg:col-span-3">
                        <select name="category_id"
                                onchange="this.form.submit()"
                                class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 border border-[#D9E4E2] truncate">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <!-- Filter Tanggal (Tanpa icon kiri) (lg: 3 cols) -->
                    <div class="relative lg:col-span-3 flex items-center gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#6B7280]">
                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            </div>
                            <input type="text"
                                   id="filter_loan_date"
                                   name="date_range"
                                   value="{{ request('date_range') }}"
                                   placeholder="Pilih rentang tanggal..."
                                   readonly
                                   class="w-full h-10 pl-9 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 cursor-pointer placeholder-[#6B7280] truncate">
                            @if(request('date_range'))
                                <a href="{{ route('admin.peminjaman.index', request()->except('date_range')) }}"
                                   class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-[#6B7280] hover:text-[#DC2626] transition-colors"
                                   title="Hapus filter rentang tanggal">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                </a>
                            @else
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                                </div>
                            @endif
                        </div>

                        @if(request()->hasAny(['search', 'status', 'category_id', 'date_range', 'highlight']))
                            <a href="{{ route('admin.peminjaman.index') }}"
                               class="h-10 px-3 rounded-lg bg-white border border-[#D9E4E2] text-xs text-[#4B5563] hover:text-[#DC2626] hover:bg-[#FEE2E2]/30 flex items-center justify-center transition-colors shrink-0"
                               title="Reset semua filter">
                                <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4 w-36">Kode Pinjam</th>
                        <th class="py-3.5 px-4 min-w-[160px]">Peminjam</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Kategori & Unit Barang</th>
                        <th class="py-3.5 px-4 min-w-[180px]">Jadwal Peminjaman</th>
                        <th class="py-3.5 px-4 text-center w-36">Status</th>
                        <th class="py-3.5 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9E4E2] text-xs text-[#1F2937]">
                    @forelse ($loans as $loan)
                        @php
                            $isHighlighted = (isset($highlightId) && $highlightId == $loan->id);
                            $isTerlambat = $loan->is_terlambat;
                        @endphp
                        <tr id="loan-row-{{ $loan->id }}"
                            class="transition-colors {{ $isHighlighted ? 'bg-[#FEF3C7] border-l-4 border-[#F59E0B]' : 'hover:bg-[#F8FBFA]' }}">
                            
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center text-[#6B7280] font-medium">
                                {{ $loop->iteration + ($loans->currentPage() - 1) * $loans->perPage() }}
                            </td>

                            <!-- Kode Pinjam -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded bg-[#F3F7F6] font-mono text-[11px] font-bold text-[#1F2937] border border-[#D9E4E2] inline-block">
                                    {{ $loan->kode_peminjaman }}
                                </span>
                            </td>

                            <!-- Nama Peminjam -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#1F2937] text-xs">
                                    {{ $loan->nama_peminjam }}
                                </div>
                                @if($loan->alasan_tujuan)
                                    <div class="text-[11px] text-[#6B7280] mt-0.5 truncate max-w-xs" title="{{ $loan->alasan_tujuan }}">
                                        {{ $loan->alasan_tujuan }}
                                    </div>
                                @endif
                            </td>

                            <!-- Kategori & Unit Terpilih -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#0F766E]">
                                    {{ $loan->category->nama_kategori ?? '-' }}
                                </div>
                                <div class="text-[11px] text-[#4B5563] mt-0.5">
                                    @if($loan->inventory)
                                        <span class="font-mono font-medium text-[#1F2937]">{{ $loan->inventory->kode_barang }}</span> - {{ $loan->inventory->nama_barang }}
                                    @else
                                        <span class="text-[#9CA3AF] italic">Unit belum teralokasi</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Jadwal Peminjaman & Pengembalian Aktual -->
                            <td class="py-3.5 px-4 text-[#4B5563]">
                                <div class="text-[11px]">
                                    <span class="text-[#6B7280]">Pinjam:</span>
                                    <span class="font-medium text-[#1F2937]">{{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                                <div class="text-[11px] mt-0.5">
                                    <span class="text-[#6B7280]">Jadwal Kembali:</span>
                                    <span class="font-medium {{ $isTerlambat && $loan->status === 'dipinjam' ? 'text-[#DC2626] font-bold' : 'text-[#1F2937]' }}">{{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                                @if($loan->tanggal_kembali_aktual)
                                    <div class="text-[11px] mt-0.5 text-[#166534] font-medium">
                                        <span>Dikembalikan: {{ $loan->tanggal_kembali_aktual->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge: Hanya Dipinjam & Selesai (+ Indikator Terlambat) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex flex-col items-center gap-1 justify-center">
                                    @if ($loan->status === 'dipinjam')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DBEAFE] text-[#1E40AF]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB] mr-1.5"></span>
                                            Dipinjam
                                        </span>
                                    @elseif ($loan->status === 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A] mr-1.5"></span>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DBEAFE] text-[#1E40AF]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB] mr-1.5"></span>
                                            Dipinjam
                                        </span>
                                    @endif

                                    <!-- Indikator Terlambat Tambahan -->
                                    @if ($isTerlambat)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#FEE2E2] text-[#991B1B]">
                                            <span class="w-1 h-1 rounded-full bg-[#DC2626] mr-1"></span>
                                            Terlambat
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi: Selesaikan (Jika Dipinjam) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    @if ($loan->status === 'dipinjam')
                                        <button type="button"
                                                onclick="confirmCompleteLoan({{ $loan->id }}, '{{ addslashes($loan->nama_peminjam) }}', '{{ addslashes($loan->kode_peminjaman) }}')"
                                                class="px-3 py-1.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]"
                                                title="Selesaikan Peminjaman (Barang Dikembalikan)">
                                            <span class="material-symbols-outlined text-[16px]">task_alt</span>
                                            <span>Selesaikan</span>
                                        </button>
                                    @else
                                        <span class="text-[#16A34A] text-xs font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                            <span>Selesai</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#6B7280] mb-3">
                                        <span class="material-symbols-outlined text-[28px]">assignment_late</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-[#1F2937]">Tidak ada data peminjaman ditemukan</h3>
                                    <p class="text-xs text-[#6B7280] mt-1 max-w-sm">
                                        @if(request()->hasAny(['search', 'status', 'category_id', 'date_range']))
                                            Kombinasi pencarian atau filter yang Anda pilih tidak cocok dengan transaksi peminjaman manapun.
                                        @else
                                            Belum ada pengajuan atau riwayat peminjaman barang tercatat di sistem.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination (Strictly 15 per page per flow.md 5 & DESIGN.md 6.8) -->
        <div class="p-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-semibold text-[#1F2937]">{{ $loans->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-[#1F2937]">{{ $loans->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ $loans->total() }}</span> transaksi (15 per halaman)
            </span>
            <div class="text-xs">
                {{ $loans->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Hidden Form for Completing Loan Status -->
<form id="formCompleteLoan" method="POST" class="hidden">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" value="selesai">
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr Date Range Picker
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

        // Smooth scroll to highlighted row if present
        @if(isset($highlightId))
            const targetRow = document.getElementById('loan-row-{{ $highlightId }}');
            if (targetRow) {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        @endif
    });

    // Selesaikan Peminjaman Confirmation Dialog
    function confirmCompleteLoan(id, borrowerName, loanCode) {
        Swal.fire({
            title: 'Selesaikan peminjaman?',
            text: `Apakah Anda yakin ingin menyelesaikan peminjaman '${loanCode}' oleh '${borrowerName}'? Unit barang akan kembali berstatus Tersedia di sistem.`,
            showCancelButton: true,
            confirmButtonColor: '#0F766E',
            cancelButtonColor: '#9CA3AF',
            confirmButtonText: 'Ya, Selesaikan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '24rem',
            customClass: {
                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formCompleteLoan');
                form.action = `/admin/peminjaman/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
