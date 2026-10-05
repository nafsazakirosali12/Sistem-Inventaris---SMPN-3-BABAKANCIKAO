@extends('layouts.admin')

@section('title', 'Ruangan - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-1">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Data Ruangan</h1>
                <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                    Kelola data dan informasi setiap ruangan yang digunakan sebagai lokasi penyimpanan dan penempatan barang inventaris sekolah, sehingga lokasi setiap barang dapat dicatat dan dikelola dengan lebih teratur.
                </p>
            </div>
        </div>
    </div>

    <!-- Unified Card: Search, Filter, Actions & Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden flex flex-col">
        <!-- Card Header: Search & Filter Toolbar -->
        <div class="p-4 sm:p-5 border-b border-[#D9E4E2] flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-white">
            <form action="{{ route('admin.ruangan.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search Input with Positioned Icon -->
                <div class="relative flex-1 min-w-[260px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama ruangan, kode, atau penanggung jawab..."
                           class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    @if(request('search'))
                        <a href="{{ route('admin.ruangan.index', ['sort' => request('sort')]) }}"
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] transition-colors"
                           title="Hapus pencarian">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                        </a>
                    @endif
                </div>

                <!-- Sort Filter -->
                <div class="flex items-center gap-2 bg-[#F3F7F6] rounded-lg px-3 h-10 border border-[#D9E4E2]">
                    <span class="material-symbols-outlined text-[#6B7280] text-[18px]">sort</span>
                    <select name="sort"
                            onchange="this.form.submit()"
                            class="bg-transparent text-xs text-[#1F2937] focus:outline-none cursor-pointer pr-2">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Ruangan (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Ruangan (Z-A)</option>
                        <option value="items_desc" {{ request('sort') == 'items_desc' ? 'selected' : '' }}>Jumlah Unit Terbanyak</option>
                        <option value="items_asc" {{ request('sort') == 'items_asc' ? 'selected' : '' }}>Jumlah Unit Tersedikit</option>
                        <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Kode Ruangan (A-Z)</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Terkini Ditambahkan</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'sort']))
                    <a href="{{ route('admin.ruangan.index') }}"
                       class="h-10 px-3 rounded-lg bg-white border border-[#D9E4E2] text-xs text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] flex items-center justify-center transition-colors shrink-0"
                       title="Reset filter">
                        <span class="material-symbols-outlined text-[18px] mr-1">restart_alt</span>
                        <span>Reset</span>
                    </a>
                @endif
            </form>

            <!-- Action Button: Tambah Ruangan -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        onclick="openAddRoomModal()"
                        class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Ruangan</span>
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                        <th class="py-3.5 px-4 text-center w-14">No</th>
                        <th class="py-3.5 px-4 w-32">Kode Ruangan</th>
                        <th class="py-3.5 px-4">Nama Ruangan</th>
                        <th class="py-3.5 px-4">Penanggung Jawab</th>
                        <th class="py-3.5 px-4 text-center w-32">Jumlah Barang</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9E4E2] text-xs text-[#1F2937]">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-[#F8FBFA] transition-colors">
                            <td class="py-3.5 px-4 text-center text-[#6B7280] font-medium">
                                {{ $loop->iteration + ($rooms->currentPage() - 1) * $rooms->perPage() }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded bg-[#F3F7F6] font-mono text-[11px] font-bold text-[#1F2937] border border-[#D9E4E2]">
                                    {{ $room->kode_ruangan }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-[#1F2937] text-xs">{{ $room->nama_ruangan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-[#4B5563]">
                                @if($room->penanggung_jawab)
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-[#0F766E]">person</span>
                                        <span>{{ $room->penanggung_jawab }}</span>
                                    </div>
                                @else
                                    <span class="text-[#9CA3AF] italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#166534]">
                                    {{ $room->inventories_count }} Unit
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#4B5563] text-xs leading-relaxed max-w-xs">
                                {{ $room->keterangan ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    <!-- Tombol Edit Pop-up -->
                                    <button type="button"
                                            onclick="openEditRoomModal({{ $room->id }}, '{{ addslashes($room->kode_ruangan) }}', '{{ addslashes($room->nama_ruangan) }}', '{{ addslashes($room->penanggung_jawab ?? '') }}', '{{ addslashes($room->keterangan ?? '') }}')"
                                            class="w-8 h-8 rounded-lg hover:bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center transition-colors focus:outline-none"
                                            title="Ubah Ruangan">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button type="button"
                                            onclick="handleDeleteRoom({{ $room->id }}, '{{ addslashes($room->nama_ruangan) }}', {{ $room->inventories_count }})"
                                            class="w-8 h-8 rounded-lg hover:bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center transition-colors focus:outline-none"
                                            title="Hapus Ruangan">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#6B7280] mb-3">
                                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-[#1F2937]">Tidak ada ruangan ditemukan</h3>
                                    <p class="text-xs text-[#6B7280] mt-1 max-w-sm">
                                        @if(request('search'))
                                            Kata kunci "{{ request('search') }}" tidak cocok dengan ruangan manapun.
                                        @else
                                            Belum ada data ruangan tersimpan di dalam sistem inventaris.
                                        @endif
                                    </p>
                                    <button type="button"
                                            onclick="openAddRoomModal()"
                                            class="mt-4 px-4 py-2 rounded-lg bg-[#0F766E] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm hover:bg-[#115E59]">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        <span>Tambah Ruangan Baru</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination (Strictly 5 per page as per flow.md 3.7) -->
        <div class="p-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-semibold text-[#1F2937]">{{ $rooms->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-[#1F2937]">{{ $rooms->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ $rooms->total() }}</span> ruangan (5 per halaman)
            </span>
            <div class="text-xs">
                {{ $rooms->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Hidden Form for Delete Action -->
<form id="formDeleteRoom" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH RUANGAN (Pop-up)                                          -->
<!-- ========================================================================= -->
<div id="modalTambahRuangan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">add_location_alt</span>
                <h2 class="text-base font-bold text-[#1F2937]">Tambah Ruangan Baru</h2>
            </div>
            <button type="button"
                    onclick="closeAddRoomModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formStoreRoom" action="{{ route('admin.ruangan.store') }}" method="POST" onsubmit="return handleStoreRoomSubmit(event)" class="p-6 flex flex-col gap-4">
            @csrf

            <!-- Kode Ruangan -->
            <div class="flex flex-col gap-1.5">
                <label for="add_kode_ruangan" class="text-xs font-semibold text-[#1F2937]">
                    Kode Ruangan <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           id="add_kode_ruangan"
                           name="kode_ruangan"
                           value="{{ old('kode_ruangan', $nextCode) }}"
                           placeholder="mis. RUG-LAB-IPA"
                           required
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg font-mono text-xs uppercase font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px]">tag</span>
                    </div>
                </div>
                <span class="text-[11px] text-[#6B7280]">Kode unik singkat untuk pengkodean ruangan/lokasi inventaris.</span>
            </div>

            <!-- Nama Ruangan -->
            <div class="flex flex-col gap-1.5">
                <label for="add_nama_ruangan" class="text-xs font-semibold text-[#1F2937]">
                    Nama Ruangan / Lokasi <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="add_nama_ruangan"
                       name="nama_ruangan"
                       value="{{ old('nama_ruangan') }}"
                       placeholder="mis. Laboratorium Komputer 1"
                       required
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Penanggung Jawab -->
            <div class="flex flex-col gap-1.5">
                <label for="add_penanggung_jawab" class="text-xs font-semibold text-[#1F2937]">
                    Penanggung Jawab (PJ Ruangan)
                </label>
                <input type="text"
                       id="add_penanggung_jawab"
                       name="penanggung_jawab"
                       value="{{ old('penanggung_jawab') }}"
                       placeholder="mis. Dra. Hj. Siti Rahmah, M.Pd"
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Keterangan -->
            <div class="flex flex-col gap-1.5">
                <label for="add_keterangan" class="text-xs font-semibold text-[#1F2937]">
                    Keterangan (Opsional)
                </label>
                <textarea id="add_keterangan"
                          name="keterangan"
                          rows="3"
                          placeholder="Informasi tambahan lokasi gedung, lantai, atau catatan khusus..."
                          class="w-full p-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeAddRoomModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Ruangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: UBAH RUANGAN (Pop-up)                                           -->
<!-- ========================================================================= -->
<div id="modalEditRuangan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">edit_location_alt</span>
                <h2 class="text-base font-bold text-[#1F2937]">Ubah Data Ruangan</h2>
            </div>
            <button type="button"
                    onclick="closeEditRoomModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditRuangan" method="POST" class="p-6 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <!-- Kode Ruangan -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_kode_ruangan" class="text-xs font-semibold text-[#1F2937]">
                    Kode Ruangan <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           id="edit_kode_ruangan"
                           name="kode_ruangan"
                           required
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg font-mono text-xs uppercase font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px]">tag</span>
                    </div>
                </div>
            </div>

            <!-- Nama Ruangan -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_nama_ruangan" class="text-xs font-semibold text-[#1F2937]">
                    Nama Ruangan <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="edit_nama_ruangan"
                       name="nama_ruangan"
                       required
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Penanggung Jawab -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_penanggung_jawab" class="text-xs font-semibold text-[#1F2937]">
                    Penanggung Jawab (PJ Ruangan)
                </label>
                <input type="text"
                       id="edit_penanggung_jawab"
                       name="penanggung_jawab"
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Keterangan -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_keterangan" class="text-xs font-semibold text-[#1F2937]">
                    Keterangan
                </label>
                <textarea id="edit_keterangan"
                          name="keterangan"
                          rows="3"
                          class="w-full p-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none"></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeEditRoomModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="button"
                        onclick="promptSaveEditRoom()"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let isConfirmedRoomSubmission = false;

    // Modal Tambah Ruangan
    function openAddRoomModal() {
        document.getElementById('modalTambahRuangan').classList.remove('hidden');
        setTimeout(() => {
            const input = document.getElementById('add_nama_ruangan');
            if (input) input.focus();
        }, 100);
    }

    function closeAddRoomModal() {
        document.getElementById('modalTambahRuangan').classList.add('hidden');
    }

    // Konfirmasi Tambah Ruangan
    function handleStoreRoomSubmit(event) {
        if (isConfirmedRoomSubmission) {
            return true;
        }

        event.preventDefault();
        const code = document.getElementById('add_kode_ruangan').value.trim();
        const name = document.getElementById('add_nama_ruangan').value.trim();

        if (!code || !name) {
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Kode ruangan dan nama ruangan wajib diisi.',
                confirmButtonColor: '#0F766E',
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
            return false;
        }

        Swal.fire({
            title: 'Simpan Ruangan Baru?',
            text: `Apakah Anda yakin ingin menambahkan ruangan '${name}' ke dalam sistem?`,
            showCancelButton: true,
            confirmButtonColor: '#0F766E',
            cancelButtonColor: '#9CA3AF',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '22rem',
            customClass: {
                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                isConfirmedRoomSubmission = true;
                document.getElementById('formStoreRoom').submit();
            }
        });

        return false;
    }

    // Modal Edit Ruangan
    function openEditRoomModal(id, code, name, pj, description) {
        const form = document.getElementById('formEditRuangan');
        form.action = `/admin/ruangan/${id}`;

        document.getElementById('edit_kode_ruangan').value = code;
        document.getElementById('edit_nama_ruangan').value = name;
        document.getElementById('edit_penanggung_jawab').value = pj;
        document.getElementById('edit_keterangan').value = description;

        document.getElementById('modalEditRuangan').classList.remove('hidden');
    }

    function closeEditRoomModal() {
        document.getElementById('modalEditRuangan').classList.add('hidden');
    }

    // Konfirmasi Edit Ruangan
    function promptSaveEditRoom() {
        const nameInput = document.getElementById('edit_nama_ruangan');
        const codeInput = document.getElementById('edit_kode_ruangan');

        if (!nameInput.value.trim() || !codeInput.value.trim()) {
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Kode ruangan dan nama ruangan wajib diisi.',
                confirmButtonColor: '#0F766E',
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Simpan perubahan?',
            text: 'Apakah Anda yakin ingin memperbarui data ruangan ini?',
            showCancelButton: true,
            confirmButtonColor: '#0F766E',
            cancelButtonColor: '#9CA3AF',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '22rem',
            customClass: {
                popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm',
                cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formEditRuangan').submit();
            }
        });
    }

    // Konfirmasi Hapus Ruangan (Strict Rule: Tidak Boleh Dihapus jika ada barang)
    function handleDeleteRoom(id, name, unitCount) {
        if (unitCount > 0) {
            Swal.fire({
                title: 'Hapus Ditolak',
                text: `Ruangan '${name}' tidak boleh dihapus karena masih berisi ${unitCount} unit barang inventaris. Silakan pindahkan barang ke ruangan lain terlebih dahulu.`,
                confirmButtonColor: '#0F766E',
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
        } else {
            Swal.fire({
                title: 'Hapus ruangan ini?',
                text: `Ruangan '${name}' akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`,
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#DC2626] hover:bg-[#B91C1C] text-white shadow-sm',
                    cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formDeleteRoom');
                    form.action = `/admin/ruangan/${id}`;
                    form.submit();
                }
            });
        }
    }

    // Keyboard accessibility: ESC key closes open modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddRoomModal();
            closeEditRoomModal();
        }
    });
</script>
@endsection
