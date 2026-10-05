@extends('layouts.admin')

@section('title', 'Daftar Akun Peminjam - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-1">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Daftar Akun Peminjam</h1>
                <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                    Kelola data akun peminjam yang digunakan oleh guru untuk mengakses sistem, mulai dari menambahkan akun baru, melihat informasi akun, hingga mengelola data akun yang telah terdaftar.
                </p>
            </div>
        </div>
    </div>

    <!-- Unified Card: Search, Filter, Actions & Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden flex flex-col">
        <!-- Card Header: Search & Filter Toolbar -->
        <div class="p-4 sm:p-5 border-b border-[#D9E4E2] flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-white">
            <form action="{{ route('admin.akun.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search Input with Positioned Icon -->
                <div class="relative flex-1 min-w-[260px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[20px] leading-none select-none">search</span>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari username akun peminjam..."
                           class="w-full h-10 pl-10 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] placeholder-[#6B7280] border border-[#D9E4E2] focus:outline-none focus:bg-white focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    @if(request('search'))
                        <a href="{{ route('admin.akun.index', ['sort' => request('sort')]) }}"
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
                        <option value="username_asc" {{ request('sort') == 'username_asc' ? 'selected' : '' }}>Username (A-Z)</option>
                        <option value="username_desc" {{ request('sort') == 'username_desc' ? 'selected' : '' }}>Username (Z-A)</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Terkini Didaftarkan</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'sort']))
                    <a href="{{ route('admin.akun.index') }}"
                       class="h-10 px-3 rounded-lg bg-white border border-[#D9E4E2] text-xs text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] flex items-center justify-center transition-colors shrink-0"
                       title="Reset filter">
                        <span class="material-symbols-outlined text-[18px] mr-1">restart_alt</span>
                        <span>Reset</span>
                    </a>
                @endif
            </form>

            <!-- Action Button: Tambah Akun Peminjam -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        onclick="openAddAccountModal()"
                        class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Tambah Akun</span>
                </button>
            </div>
        </div>

        <!-- Table Content (Strictly Username & Password with Proportionate Spacing) -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F0FDFA] text-[#1F2937] text-xs font-semibold border-b border-[#D9E4E2]">
                        <th class="py-3.5 px-4 text-center w-16">No</th>
                        <th class="py-3.5 px-4 w-72">Username</th>
                        <th class="py-3.5 px-4 text-center w-64">Password</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9E4E2] text-xs text-[#1F2937]">
                    @forelse ($accounts as $acc)
                        <tr class="hover:bg-[#F8FBFA] transition-colors">
                            <td class="py-3.5 px-4 text-center text-[#6B7280] font-medium">
                                {{ $loop->iteration + ($accounts->currentPage() - 1) * $accounts->perPage() }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded bg-[#F3F7F6] font-mono text-xs font-bold text-[#1F2937] border border-[#D9E4E2]">
                                        {{ $acc->username }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-mono text-xs text-[#9CA3AF] tracking-widest bg-gray-100 px-3 py-1 rounded select-none">
                                    ••••••••
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    <!-- Tombol Edit Pop-up -->
                                    <button type="button"
                                            onclick="openEditAccountModal({{ $acc->id }}, '{{ addslashes($acc->username) }}')"
                                            class="w-8 h-8 rounded-lg hover:bg-[#CCFBF1] text-[#0F766E] flex items-center justify-center transition-colors focus:outline-none"
                                            title="Ubah Username / Password">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button type="button"
                                            onclick="handleDeleteAccount({{ $acc->id }}, '{{ addslashes($acc->username) }}')"
                                            class="w-8 h-8 rounded-lg hover:bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center transition-colors focus:outline-none"
                                            title="Hapus Akun">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-[#F3F7F6] flex items-center justify-center text-[#6B7280] mb-3">
                                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-[#1F2937]">Tidak ada akun peminjam ditemukan</h3>
                                    <p class="text-xs text-[#6B7280] mt-1 max-w-sm">
                                        @if(request('search'))
                                            Kata kunci "{{ request('search') }}" tidak cocok dengan username manapun.
                                        @else
                                            Belum ada data akun peminjam tersimpan di dalam sistem.
                                        @endif
                                    </p>
                                    <button type="button"
                                            onclick="openAddAccountModal()"
                                            class="mt-4 px-4 py-2 rounded-lg bg-[#0F766E] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm hover:bg-[#115E59]">
                                        <span class="material-symbols-outlined text-[16px]">person_add</span>
                                        <span>Tambah Akun Baru</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="p-4 bg-[#F8FBFA] border-t border-[#D9E4E2] flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span class="font-semibold text-[#1F2937]">{{ $accounts->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-[#1F2937]">{{ $accounts->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#1F2937]">{{ $accounts->total() }}</span> akun peminjam
            </span>
            <div class="text-xs">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Hidden Form for Delete Action -->
<form id="formDeleteAccount" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH AKUN PEMINJAM (2 FIELD: USERNAME & PASSWORD)              -->
<!-- ========================================================================= -->
<div id="modalTambahAkun" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">person_add</span>
                <h2 class="text-base font-bold text-[#1F2937]">Tambah Akun Peminjam</h2>
            </div>
            <button type="button"
                    onclick="closeAddAccountModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formStoreAccount" action="{{ route('admin.akun.store') }}" method="POST" onsubmit="return handleStoreAccountSubmit(event)" class="p-6 flex flex-col gap-4">
            @csrf

            <!-- Role Indicator Info -->
            <div class="p-3 rounded-lg bg-[#F0FDFA] border border-[#CCFBF1] flex items-center gap-2 text-xs text-[#0F766E]">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>Role akun otomatis diset sebagai <strong>Peminjam</strong> saat disimpan.</span>
            </div>

            <!-- Username -->
            <div class="flex flex-col gap-1.5">
                <label for="add_username" class="text-xs font-semibold text-[#1F2937]">
                    Username <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="text"
                           id="add_username"
                           name="username"
                           value="{{ old('username') }}"
                           placeholder="Masukkan username akun..."
                           required
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg font-mono text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] leading-none select-none">person</span>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-1.5">
                <label for="add_password" class="text-xs font-semibold text-[#1F2937]">
                    Password <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="password"
                           id="add_password"
                           name="password"
                           placeholder="Minimal 6 karakter"
                           required
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] leading-none select-none">lock</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeAddAccountModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: UBAH AKUN PEMINJAM (2 FIELD: USERNAME & PASSWORD BARU)           -->
<!-- ========================================================================= -->
<div id="modalEditAkun" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">manage_accounts</span>
                <h2 class="text-base font-bold text-[#1F2937]">Ubah Username & Password</h2>
            </div>
            <button type="button"
                    onclick="closeEditAccountModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditAkun" method="POST" class="p-6 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <!-- Username -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_username" class="text-xs font-semibold text-[#1F2937]">
                    Username <span class="text-[#DC2626]">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="text"
                           id="edit_username"
                           name="username"
                           required
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg font-mono text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] leading-none select-none">person</span>
                    </div>
                </div>
            </div>

            <!-- Password Baru (Opsional) -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_password" class="text-xs font-semibold text-[#1F2937]">
                    Password Baru (Opsional)
                </label>
                <div class="relative flex items-center">
                    <input type="password"
                           id="edit_password"
                           name="password"
                           placeholder="Kosongkan jika tidak diubah"
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                        <span class="material-symbols-outlined text-[18px] leading-none select-none">lock</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeEditAccountModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="button"
                        onclick="promptSaveEditAccount()"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let isConfirmedAccountSubmission = false;

    // Modal Tambah Akun
    function openAddAccountModal() {
        document.getElementById('modalTambahAkun').classList.remove('hidden');
        setTimeout(() => {
            const input = document.getElementById('add_username');
            if (input) input.focus();
        }, 100);
    }

    function closeAddAccountModal() {
        document.getElementById('modalTambahAkun').classList.add('hidden');
    }

    // Konfirmasi Tambah Akun
    function handleStoreAccountSubmit(event) {
        if (isConfirmedAccountSubmission) {
            return true;
        }

        event.preventDefault();
        const username = document.getElementById('add_username').value.trim();
        const password = document.getElementById('add_password').value;

        if (!username || !password) {
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Username dan password wajib diisi.',
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

        if (password.length < 6) {
            Swal.fire({
                title: 'Password Terlalu Pendek',
                text: 'Password minimal terdiri dari 6 karakter.',
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
            title: 'Buat Akun Peminjam?',
            text: `Apakah Anda yakin ingin mendaftarkan akun peminjam '@${username}'?`,
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
                isConfirmedAccountSubmission = true;
                document.getElementById('formStoreAccount').submit();
            }
        });

        return false;
    }

    // Modal Edit Akun
    function openEditAccountModal(id, username) {
        const form = document.getElementById('formEditAkun');
        form.action = `/admin/akun/${id}`;

        document.getElementById('edit_username').value = username;
        document.getElementById('edit_password').value = '';

        document.getElementById('modalEditAkun').classList.remove('hidden');
    }

    function closeEditAccountModal() {
        document.getElementById('modalEditAkun').classList.add('hidden');
    }

    // Konfirmasi Edit Akun
    function promptSaveEditAccount() {
        const usernameInput = document.getElementById('edit_username').value.trim();

        if (!usernameInput) {
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Username wajib diisi.',
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
            title: 'Simpan perubahan akun?',
            text: 'Apakah Anda yakin ingin memperbarui username/password untuk akun ini?',
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
                document.getElementById('formEditAkun').submit();
            }
        });
    }

    // Konfirmasi Hapus Akun
    function handleDeleteAccount(id, username) {
        Swal.fire({
            title: 'Hapus akun peminjam?',
            text: `Akun '@${username}' akan dihapus. Pengguna tidak dapat login lagi ke sistem.`,
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
                const form = document.getElementById('formDeleteAccount');
                form.action = `/admin/akun/${id}`;
                form.submit();
            }
        });
    }

    // Keyboard accessibility: ESC key closes open modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddAccountModal();
            closeEditAccountModal();
        }
    });
</script>
@endsection
