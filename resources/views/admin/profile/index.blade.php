@extends('layouts.admin')

@section('title', 'Profil Admin - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-1">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Profil Admin</h1>
                <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                    Informasi akun dan kredensial Administrator Sarana & Prasarana {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}.
                </p>
            </div>
            <!-- Top Action Buttons: Kembali & Edit -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="h-10 px-4 rounded-lg border border-[#D9E4E2] bg-white text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
                <button type="button"
                        onclick="openEditProfileModal()"
                        class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span>Edit</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Profile Card (Read-Only View) -->
    <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] overflow-hidden">
        <!-- Top Profile Banner & Identity Block -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-[#F0FDFA] to-white border-b border-[#D9E4E2] flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Profile Avatar (Large, Read-Only, No Camera Icon) -->
            <div class="relative shrink-0">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-[#0F766E] border-4 border-white shadow-md flex items-center justify-center text-white overflow-hidden">
                    @if(!empty($user->avatar) && file_exists(public_path($user->avatar)))
                        <img src="{{ asset($user->avatar) }}?v={{ filemtime(public_path($user->avatar)) }}"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[52px] sm:text-[60px]">person</span>
                    @endif
                </div>
            </div>

            <!-- Profile Identity -->
            <div class="flex flex-col items-center sm:items-start text-center sm:text-left gap-1.5 flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-[#1F2937]">{{ $user->name ?? 'Admin Sarpras' }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#CCFBF1] text-[#0F766E] border border-[#99F6E4]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E] mr-1.5"></span>
                        Administrator
                    </span>
                </div>
                <p class="text-xs font-mono font-semibold text-[#0F766E] bg-[#F0FDFA] px-2 py-0.5 rounded border border-[#CCFBF1] inline-block">
                    {{ '@' . ($user->username ?? 'admin') }}
                </p>
                <p class="text-xs text-[#6B7280] mt-1 max-w-xl leading-relaxed">
                    Pengelola operasional inventaris barang, fasilitas sekolah, peminjaman unit sarpras, dan rekapitulasi pelaporan.
                </p>
            </div>
        </div>

        <!-- Detail Credentials Section (Read-Only) -->
        <div class="p-6 sm:p-8">
            <h3 class="text-sm font-bold text-[#1F2937] mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#0F766E]">shield_person</span>
                <span>Rincian Akun & Kredensial</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Username Block -->
                <div class="p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] flex flex-col gap-1">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Username Akun</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-bold text-[#1F2937] font-mono">{{ $user->username ?? 'admin' }}</span>
                        <span class="material-symbols-outlined text-[18px] text-[#0F766E]">alternate_email</span>
                    </div>
                    <span class="text-[11px] text-[#6B7280] mt-1">Digunakan untuk login ke sistem inventaris.</span>
                </div>

                <!-- Password Block (Read-Only, Bcrypt Masked) -->
                <div class="p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] flex flex-col gap-1">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Password</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-bold text-[#1F2937] tracking-widest font-mono select-none">••••••••</span>
                        <span class="material-symbols-outlined text-[18px] text-[#6B7280]">lock</span>
                    </div>
                    <span class="text-[11px] text-[#6B7280] mt-1">Password tersimpan aman dengan enkripsi hashing Bcrypt.</span>
                </div>

                <!-- Nama Lengkap Block -->
                <div class="p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] flex flex-col gap-1">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Nama Lengkap</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-semibold text-[#1F2937]">{{ $user->name ?? 'Admin Sarpras' }}</span>
                        <span class="material-symbols-outlined text-[18px] text-[#6B7280]">badge</span>
                    </div>
                    <span class="text-[11px] text-[#6B7280] mt-1">Nama yang ditampilkan pada antarmuka sistem.</span>
                </div>

                <!-- Hak Akses Block -->
                <div class="p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] flex flex-col gap-1">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Hak Akses Role</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-semibold text-[#1F2937]">Admin Sarana & Prasarana</span>
                        <span class="material-symbols-outlined text-[18px] text-[#16A34A]">verified_user</span>
                    </div>
                    <span class="text-[11px] text-[#6B7280] mt-1">Akses penuh ke seluruh menu kelola admin.</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT PROFIL ADMIN (Pop-up)                                         -->
<!-- ========================================================================= -->
<div id="modalEditProfile" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#134E4A]/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 border border-[#D9E4E2]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F0FDFA] flex items-center justify-between border-b border-[#CCFBF1]">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">manage_accounts</span>
                <h2 class="text-base font-bold text-[#1F2937]">Edit Profil Admin</h2>
            </div>
            <button type="button"
                    onclick="closeEditProfileModal()"
                    class="w-8 h-8 rounded-lg hover:bg-white text-[#6B7280] hover:text-[#1F2937] flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditProfile"
              action="{{ route('admin.profile.update') }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <!-- Foto Profil Upload with Live Preview -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-semibold text-[#1F2937]">Foto Profil</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-[#0F766E] border-2 border-[#D9E4E2] flex items-center justify-center text-white overflow-hidden shrink-0 shadow-xs">
                        <img id="profile_avatar_preview"
                             src="{{ (!empty($user->avatar) && file_exists(public_path($user->avatar))) ? asset($user->avatar) . '?v=' . filemtime(public_path($user->avatar)) : '' }}"
                             alt="Pratinjau Foto"
                             class="{{ (!empty($user->avatar) && file_exists(public_path($user->avatar))) ? 'w-full h-full object-cover' : 'hidden' }}">
                        <span id="profile_avatar_default_icon" class="material-symbols-outlined text-[36px] {{ (!empty($user->avatar) && file_exists(public_path($user->avatar))) ? 'hidden' : '' }}">person</span>
                    </div>
                    <div class="flex-1">
                        <input type="file"
                               id="edit_admin_foto"
                               name="foto"
                               accept="image/jpeg,image/png,image/webp,image/jpg"
                               onchange="previewAvatar(event)"
                               class="w-full text-xs text-[#4B5563] file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#CCFBF1] file:text-[#0F766E] hover:file:bg-[#99F6E4] file:cursor-pointer border border-[#D9E4E2] rounded-lg p-1 bg-[#F3F7F6] focus:outline-none">
                        <span class="text-[11px] text-[#6B7280] block mt-1">Format: JPG, PNG, atau WebP (Maks. 2 MB).</span>
                    </div>
                </div>
            </div>

            <!-- Nama Admin -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_admin_name" class="text-xs font-semibold text-[#1F2937]">
                    Nama Lengkap / Tampilan <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="edit_admin_name"
                       name="name"
                       value="{{ old('name', $user->name ?? 'Admin Sarpras') }}"
                       required
                       placeholder="mis. Admin Sarpras"
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
            </div>

            <!-- Username -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_admin_username" class="text-xs font-semibold text-[#1F2937]">
                    Username <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="edit_admin_username"
                       name="username"
                       value="{{ old('username', $user->username ?? 'admin') }}"
                       required
                       placeholder="mis. admin"
                       class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs font-mono font-semibold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                <span class="text-[11px] text-[#6B7280]">Gunakan huruf, angka, titik, atau garis bawah tanpa spasi.</span>
            </div>

            <!-- Password Baru (Optional, with eye toggle) -->
            <div class="flex flex-col gap-1.5">
                <label for="edit_admin_password" class="text-xs font-semibold text-[#1F2937]">
                    Password Baru <span class="text-[#6B7280] font-normal">(Opsional)</span>
                </label>
                <div class="relative">
                    <input type="password"
                           id="edit_admin_password"
                           name="password"
                           placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <button type="button"
                            onclick="togglePasswordVisibility('edit_admin_password', 'toggle_pass_icon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6B7280] hover:text-[#1F2937] focus:outline-none"
                            title="Tampilkan / Sembunyikan Password">
                        <span id="toggle_pass_icon" class="material-symbols-outlined text-[18px]">visibility</span>
                    </button>
                </div>
                <span class="text-[11px] text-[#6B7280]">Minimal 6 karakter jika ingin mengganti password.</span>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9E4E2]">
                <button type="button"
                        onclick="closeEditProfileModal()"
                        class="h-10 px-4 rounded-lg text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                    Batal
                </button>
                <button type="button"
                        onclick="promptSaveAdminProfile()"
                        class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open & Close Modal
    function openEditProfileModal() {
        document.getElementById('modalEditProfile').classList.remove('hidden');
    }

    function closeEditProfileModal() {
        document.getElementById('modalEditProfile').classList.add('hidden');
    }

    // Toggle Password Visibility (Eye icon) — untuk modal Edit
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    // Live Avatar Preview
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran foto profil maksimal 2 MB.',
                    confirmButtonColor: '#0F766E',
                    customClass: {
                        popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                        confirmButton: 'px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                    }
                });
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('profile_avatar_preview');
                const defaultIcon = document.getElementById('profile_avatar_default_icon');

                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                previewImg.classList.add('w-full', 'h-full', 'object-cover');
                if (defaultIcon) {
                    defaultIcon.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }

    // Confirmation Alert before submit (DESIGN.md 2.4, 6.10)
    function promptSaveAdminProfile() {
        const usernameInput = document.getElementById('edit_admin_username');
        const nameInput = document.getElementById('edit_admin_name');
        const passwordInput = document.getElementById('edit_admin_password');

        if (!usernameInput.value.trim() || !nameInput.value.trim()) {
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Nama lengkap dan username admin wajib diisi.',
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

        if (passwordInput.value && passwordInput.value.length < 6) {
            Swal.fire({
                title: 'Password Terlalu Pendek',
                text: 'Password baru minimal harus terdiri dari 6 karakter.',
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
            text: 'Perubahan profil admin akan disimpan.',
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
                document.getElementById('formEditProfile').submit();
            }
        });
    }

    // Keyboard accessibility: ESC key closes modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeEditProfileModal();
        }
    });
</script>
@endsection
