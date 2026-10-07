@extends('layouts.admin')

@section('title', 'Profil Sekolah - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6" x-data="schoolProfileApp()">

    <!-- Page Header -->
    <div class="flex flex-col gap-1">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Profil Sekolah</h1>
                <p class="text-xs text-[#4B5563] mt-1 max-w-3xl">
                    Kelola berbagai informasi sekolah yang ditampilkan pada halaman beranda peminjam, bagian footer/bawah halaman, termasuk identitas sekolah, informasi kontak, media sosial, serta kop laporan yang digunakan pada dokumen saat diunduh.
                </p>
            </div>

            <!-- Mode Action Toolbar (Read-only / Edit Mode Toggle per flow.md 3.4) -->
            <div class="flex items-center gap-3">
                <template x-if="!isEditing">
                    <button type="button"
                            @click="enableEditMode()"
                            class="h-10 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        <span>Edit Profil Sekolah</span>
                    </button>
                </template>

                <template x-if="isEditing">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="cancelEditMode()"
                                class="h-10 px-4 rounded-lg bg-white border border-[#D9E4E2] text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                            Batal
                        </button>
                        <button type="button"
                                @click="submitProfileForm()"
                                class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Profile Form -->
    <form id="formProfilSekolah"
          action="{{ route('admin.profil.update') }}"
          method="POST"
          enctype="multipart/form-data"
          class="flex flex-col gap-6">
        @csrf
        @method('PUT')

        <!-- Read-only Banner Notification -->
        <div x-show="!isEditing" class="p-4 rounded-xl bg-white border border-[#D9E4E2] shadow-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-xs text-[#4B5563]">
                <div class="w-8 h-8 rounded-lg bg-[#F0FDFA] text-[#0F766E] flex items-center justify-center shrink-0 border border-[#CCFBF1]">
                    <span class="material-symbols-outlined text-[20px]">lock</span>
                </div>
                <div>
                    <span class="font-bold text-[#1F2937]">Mode Baca (Read-Only)</span>
                    <p class="text-[11px] text-[#6B7280]">Formulir terkunci untuk mencegah perubahan yang tidak disengaja. Klik tombol <strong>Edit Profil Sekolah</strong> di atas untuk mengubah data.</p>
                </div>
            </div>
        </div>

        <div x-show="isEditing" class="p-4 rounded-xl bg-[#F0FDFA] border border-[#CCFBF1] shadow-xs flex items-center gap-3 text-xs text-[#0F766E]">
            <span class="material-symbols-outlined text-[20px]">edit_note</span>
            <span><strong>Mode Edit Aktif:</strong> Anda dapat mengubah data identitas, mengunggah logo/foto/denah baru, dan menyesuaikan atribut KIB.</span>
        </div>

        <!-- 1. SECTION: GAMBAR & DOKUMEN (Logo, Foto, Denah Sekolah) -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[#D9E4E2]">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">image</span>
                <h2 class="text-sm font-bold text-[#1F2937] uppercase tracking-wider">Logo, Foto & Denah Sekolah</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <!-- A. Logo Sekolah -->
                <div class="flex flex-col gap-3 p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] items-center text-center">
                    <span class="text-xs font-bold text-[#1F2937]">Logo Resmi Sekolah</span>
                    <div class="w-32 h-32 rounded-xl bg-white border border-[#D9E4E2] p-2 flex items-center justify-center overflow-hidden shadow-xs relative group">
                        <img id="previewLogo"
                             src="{{ $schoolProfile->logo ? asset($schoolProfile->logo) : asset('images/logo-sekolah.svg') }}"
                             alt="Logo Sekolah"
                             class="max-w-full max-h-full object-contain">
                    </div>
                    <span class="text-[11px] text-[#6B7280]">Format: JPG, PNG, WebP (Maks. 2 MB)</span>
                    
                    <div x-show="isEditing" class="w-full mt-1">
                        <label for="input_logo" class="w-full h-9 px-3 rounded-lg bg-white border border-[#D9E4E2] hover:border-[#0F766E] text-[#0F766E] text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            <span>Pilih Logo Baru</span>
                        </label>
                        <input type="file"
                               id="input_logo"
                               name="logo"
                               accept="image/jpeg,image/png,image/webp"
                               @change="previewImage($event, 'previewLogo')"
                               class="hidden">
                    </div>
                </div>

                <!-- B. Foto Sekolah -->
                <div class="flex flex-col gap-3 p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] items-center text-center">
                    <span class="text-xs font-bold text-[#1F2937]">Foto Gedung Sekolah</span>
                    <div class="w-full h-32 rounded-xl bg-white border border-[#D9E4E2] p-1 flex items-center justify-center overflow-hidden shadow-xs relative">
                        @if($schoolProfile->foto)
                            <img id="previewFoto"
                                 src="{{ asset($schoolProfile->foto) }}"
                                 alt="Foto Sekolah"
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <div id="placeholderFoto" class="flex flex-col items-center justify-center text-[#9CA3AF]">
                                <span class="material-symbols-outlined text-[32px]">domain</span>
                                <span class="text-[11px] mt-1">Belum ada foto</span>
                            </div>
                            <img id="previewFoto" src="" class="hidden w-full h-full object-cover rounded-lg">
                        @endif
                    </div>
                    <span class="text-[11px] text-[#6B7280]">Format: JPG, PNG, WebP (Maks. 2 MB)</span>

                    <div x-show="isEditing" class="w-full mt-1">
                        <label for="input_foto" class="w-full h-9 px-3 rounded-lg bg-white border border-[#D9E4E2] hover:border-[#0F766E] text-[#0F766E] text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            <span>Pilih Foto Gedung</span>
                        </label>
                        <input type="file"
                               id="input_foto"
                               name="foto"
                               accept="image/jpeg,image/png,image/webp"
                               @change="previewImage($event, 'previewFoto', 'placeholderFoto')"
                               class="hidden">
                    </div>
                </div>

                <!-- C. Denah Sekolah -->
                <div class="flex flex-col gap-3 p-4 rounded-xl bg-[#F8FBFA] border border-[#D9E4E2] items-center text-center">
                    <span class="text-xs font-bold text-[#1F2937]">Denah Lokasi / Map Sekolah</span>
                    <div class="w-full h-32 rounded-xl bg-white border border-[#D9E4E2] p-1 flex items-center justify-center overflow-hidden shadow-xs relative">
                        @if($schoolProfile->denah)
                            <img id="previewDenah"
                                 src="{{ asset($schoolProfile->denah) }}"
                                 alt="Denah Sekolah"
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <div id="placeholderDenah" class="flex flex-col items-center justify-center text-[#9CA3AF]">
                                <span class="material-symbols-outlined text-[32px]">map</span>
                                <span class="text-[11px] mt-1">Belum ada denah</span>
                            </div>
                            <img id="previewDenah" src="" class="hidden w-full h-full object-cover rounded-lg">
                        @endif
                    </div>
                    <span class="text-[11px] text-[#6B7280]">Gambar saja (JPG, PNG, WebP Maks. 5 MB)</span>

                    <div x-show="isEditing" class="w-full mt-1">
                        <label for="input_denah" class="w-full h-9 px-3 rounded-lg bg-white border border-[#D9E4E2] hover:border-[#0F766E] text-[#0F766E] text-xs font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            <span>Pilih Gambar Denah</span>
                        </label>
                        <input type="file"
                               id="input_denah"
                               name="denah"
                               accept="image/jpeg,image/png,image/webp"
                               @change="previewImage($event, 'previewDenah', 'placeholderDenah')"
                               class="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SECTION: INFORMASI UTAMA & KONTAK SEKOLAH -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[#D9E4E2]">
                <span class="material-symbols-outlined text-[#0F766E] text-[22px]">school</span>
                <h2 class="text-sm font-bold text-[#1F2937] uppercase tracking-wider">Informasi Utama & Kontak Resmi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label for="nama_sekolah" class="text-xs font-semibold text-[#1F2937]">
                        Nama Sekolah <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="text"
                           id="nama_sekolah"
                           name="nama_sekolah"
                           value="{{ old('nama_sekolah', $schoolProfile->nama_sekolah) }}"
                           :disabled="!isEditing"
                           required
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- NPSN (8 digit angka) -->
                <div class="flex flex-col gap-1.5">
                    <label for="npsn" class="text-xs font-semibold text-[#1F2937]">
                        NPSN (Nomor Pokok Sekolah Nasional) <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="text"
                           id="npsn"
                           name="npsn"
                           value="{{ old('npsn', $schoolProfile->npsn) }}"
                           maxlength="8"
                           :disabled="!isEditing"
                           required
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg font-mono text-xs font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    <span class="text-[10px] text-[#6B7280]">Wajib 8 digit angka.</span>
                </div>

                <!-- No Telepon -->
                <div class="flex flex-col gap-1.5">
                    <label for="telepon" class="text-xs font-semibold text-[#1F2937]">
                        No. Telepon / HP Resmi
                    </label>
                    <div class="relative flex items-center">
                        <input type="text"
                               id="telepon"
                               name="telepon"
                               value="{{ old('telepon', $schoolProfile->telepon) }}"
                               :disabled="!isEditing"
                               placeholder="(0264) 1234567"
                               class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">call</span>
                        </div>
                    </div>
                </div>

                <!-- Email Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-semibold text-[#1F2937]">
                        Email Resmi Sekolah
                    </label>
                    <div class="relative flex items-center">
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $schoolProfile->email) }}"
                               :disabled="!isEditing"
                               placeholder="info@smpn3babakancikao.sch.id"
                               class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">mail</span>
                        </div>
                    </div>
                </div>

                <!-- Alamat Lengkap -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="alamat" class="text-xs font-semibold text-[#1F2937]">
                        Alamat Lengkap Sekolah <span class="text-[#DC2626]">*</span>
                    </label>
                    <textarea id="alamat"
                              name="alamat"
                              rows="3"
                              :disabled="!isEditing"
                              required
                              class="w-full p-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none">{{ old('alamat', $schoolProfile->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. SECTION: MEDIA SOSIAL RESMI -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-[#D9E4E2]">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[#0F766E] text-[22px]">share</span>
                    <h2 class="text-sm font-bold text-[#1F2937] uppercase tracking-wider">Media Sosial Resmi (Opsional)</h2>
                </div>
                <span class="text-[11px] text-[#6B7280]">Bisa diisi username (mis. @smpn3_official) atau URL lengkap. Menautkan langsung ke sosial media.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Instagram -->
                <div class="flex flex-col gap-1.5">
                    <label for="instagram" class="text-xs font-semibold text-[#1F2937]">
                        Instagram Sekolah
                    </label>
                    <div class="relative flex items-center">
                        <input type="text"
                               id="instagram"
                               name="instagram"
                               value="{{ old('instagram', $schoolProfile->instagram) }}"
                               :disabled="!isEditing"
                               placeholder="mis. @smpn3babakancikao_official atau https://instagram.com/..."
                               class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">photo_camera</span>
                        </div>
                    </div>
                    @if($schoolProfile->instagram)
                        <div class="flex items-center justify-between pt-0.5">
                            <a href="{{ Str::startsWith($schoolProfile->instagram, 'http') ? $schoolProfile->instagram : 'https://instagram.com/' . ltrim($schoolProfile->instagram, '@') }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-[11px] text-[#0F766E] hover:underline font-semibold"
                               title="Buka profil Instagram di tab baru">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                <span>Kunjungi Instagram Sekolah</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- YouTube -->
                <div class="flex flex-col gap-1.5">
                    <label for="youtube" class="text-xs font-semibold text-[#1F2937]">
                        YouTube Channel Sekolah
                    </label>
                    <div class="relative flex items-center">
                        <input type="text"
                               id="youtube"
                               name="youtube"
                               value="{{ old('youtube', $schoolProfile->youtube) }}"
                               :disabled="!isEditing"
                               placeholder="mis. @SMPN3BabakancikaoChannel atau https://youtube.com/..."
                               class="w-full h-10 px-3 pr-10 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        <div class="absolute right-3.5 inset-y-0 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px] leading-none select-none">smart_display</span>
                        </div>
                    </div>
                    @if($schoolProfile->youtube)
                        <div class="flex items-center justify-between pt-0.5">
                            <a href="{{ Str::startsWith($schoolProfile->youtube, 'http') ? $schoolProfile->youtube : 'https://youtube.com/' . ltrim($schoolProfile->youtube, '@') }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-[11px] text-[#0F766E] hover:underline font-semibold"
                               title="Buka channel YouTube di tab baru">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                <span>Kunjungi Channel YouTube</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. SECTION: DATA TEMPLATE PADA LAPORAN INVENTARIS -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="flex items-center justify-between pb-3 border-b border-[#D9E4E2]">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[#0F766E] text-[22px]">description</span>
                    <h2 class="text-sm font-bold text-[#1F2937] uppercase tracking-wider">Data Template pada Laporan Inventaris</h2>
                </div>
                <span class="text-[11px] text-[#0F766E] bg-[#F0FDFA] px-2.5 py-1 rounded-full font-semibold border border-[#CCFBF1]">
                    Standarisasi Dokumen Cetak Laporan
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Provinsi -->
                <div class="flex flex-col gap-1.5">
                    <label for="provinsi" class="text-xs font-semibold text-[#1F2937]">Provinsi</label>
                    <input type="text"
                           id="provinsi"
                           name="provinsi"
                           value="{{ old('provinsi', $schoolProfile->provinsi ?? 'Jawa Barat') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Kabupaten / Kota -->
                <div class="flex flex-col gap-1.5">
                    <label for="kabupaten_kota" class="text-xs font-semibold text-[#1F2937]">Kabupaten / Kota</label>
                    <input type="text"
                           id="kabupaten_kota"
                           name="kabupaten_kota"
                           value="{{ old('kabupaten_kota', $schoolProfile->kabupaten_kota ?? 'Kabupaten Purwakarta') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Bidang -->
                <div class="flex flex-col gap-1.5">
                    <label for="bidang" class="text-xs font-semibold text-[#1F2937]">Bidang</label>
                    <input type="text"
                           id="bidang"
                           name="bidang"
                           value="{{ old('bidang', $schoolProfile->bidang ?? 'Pendidikan') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Unit Organisasi -->
                <div class="flex flex-col gap-1.5">
                    <label for="unit_organisasi" class="text-xs font-semibold text-[#1F2937]">Unit Organisasi</label>
                    <input type="text"
                           id="unit_organisasi"
                           name="unit_organisasi"
                           value="{{ old('unit_organisasi', $schoolProfile->unit_organisasi ?? 'Dinas Pendidikan') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Sub Unit Organisasi -->
                <div class="flex flex-col gap-1.5">
                    <label for="sub_unit_organisasi" class="text-xs font-semibold text-[#1F2937]">Sub Unit Organisasi</label>
                    <input type="text"
                           id="sub_unit_organisasi"
                           name="sub_unit_organisasi"
                           value="{{ old('sub_unit_organisasi', $schoolProfile->sub_unit_organisasi ?? 'SMPN 3 Babakancikao') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- UPB -->
                <div class="flex flex-col gap-1.5">
                    <label for="upb" class="text-xs font-semibold text-[#1F2937]">UPB (Unit Pengelola Barang)</label>
                    <input type="text"
                           id="upb"
                           name="upb"
                           value="{{ old('upb', $schoolProfile->upb ?? 'SMPN 3 Babakancikao') }}"
                           :disabled="!isEditing"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- No Kode Lokasi -->
                <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-3">
                    <label for="no_kode_lokasi" class="text-xs font-semibold text-[#1F2937]">No. Kode Lokasi Aset</label>
                    <input type="text"
                           id="no_kode_lokasi"
                           name="no_kode_lokasi"
                           value="{{ old('no_kode_lokasi', $schoolProfile->no_kode_lokasi ?? '12.34.56.78.90') }}"
                           :disabled="!isEditing"
                           placeholder="mis. 12.34.56.78.90"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg font-mono text-xs font-bold text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Penandatangan Laporan / Form Approval Header -->
                <div class="md:col-span-2 lg:col-span-3 pt-3 border-t border-[#D9E4E2]">
                    <h3 class="text-xs font-bold text-[#0F766E] uppercase tracking-wider">Pejabat & Penandatangan Laporan</h3>
                </div>

                <!-- Nama Kepala Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label for="nama_kepala_sekolah" class="text-xs font-semibold text-[#1F2937]">Nama Kepala Sekolah</label>
                    <input type="text"
                           id="nama_kepala_sekolah"
                           name="nama_kepala_sekolah"
                           value="{{ old('nama_kepala_sekolah', $schoolProfile->nama_kepala_sekolah) }}"
                           :disabled="!isEditing"
                           placeholder="Nama Lengkap & Gelar"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- NIP Kepala Sekolah -->
                <div class="flex flex-col gap-1.5">
                    <label for="nip_kepala_sekolah" class="text-xs font-semibold text-[#1F2937]">NIP Kepala Sekolah</label>
                    <input type="text"
                           id="nip_kepala_sekolah"
                           name="nip_kepala_sekolah"
                           value="{{ old('nip_kepala_sekolah', $schoolProfile->nip_kepala_sekolah) }}"
                           :disabled="!isEditing"
                           placeholder="mis. 19750101 200003 1 001"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg font-mono text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Nama Pembuat -->
                <div class="flex flex-col gap-1.5">
                    <label for="nama_pembuat" class="text-xs font-semibold text-[#1F2937]">Nama Pembuat / Pengurus Barang</label>
                    <input type="text"
                           id="nama_pembuat"
                           name="nama_pembuat"
                           value="{{ old('nama_pembuat', $schoolProfile->nama_pembuat) }}"
                           :disabled="!isEditing"
                           placeholder="Nama Lengkap Pembuat Laporan"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- NIP Pembuat -->
                <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-1">
                    <label for="nip_pembuat" class="text-xs font-semibold text-[#1F2937]">NIP Pembuat / Pengurus Barang</label>
                    <input type="text"
                           id="nip_pembuat"
                           name="nip_pembuat"
                           value="{{ old('nip_pembuat', $schoolProfile->nip_pembuat) }}"
                           :disabled="!isEditing"
                           placeholder="mis. 19820512 201001 2 005"
                           class="w-full h-10 px-3 bg-[#F3F7F6] disabled:bg-[#F3F7F6]/60 rounded-lg font-mono text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:border-[#0F766E] focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Bar when editing -->
        <div x-show="isEditing" class="p-4 rounded-xl bg-white border border-[#D9E4E2] shadow-md flex items-center justify-end gap-3 sticky bottom-4 z-20">
            <button type="button"
                    @click="cancelEditMode()"
                    class="h-10 px-4 rounded-lg bg-white border border-[#D9E4E2] text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors">
                Batal
            </button>
            <button type="button"
                    @click="submitProfileForm()"
                    class="h-10 px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold flex items-center gap-2 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 active:scale-[0.99]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Perubahan Profil</span>
            </button>
        </div>
    </form>
</div>

<script>
    function schoolProfileApp() {
        return {
            isEditing: false,

            enableEditMode() {
                this.isEditing = true; 
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            cancelEditMode() {
                Swal.fire({
                    title: 'Batalkan perubahan?',
                    text: 'Semua isian formulir yang belum disimpan akan dikembalikan ke nilai semula.',
                    showCancelButton: true,
                    confirmButtonColor: '#0F766E',
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Lanjutkan Edit',
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
                        this.isEditing = false;
                        document.getElementById('formProfilSekolah').reset();
                    }
                });
            },

            submitProfileForm() {
                const nama = document.getElementById('nama_sekolah').value.trim();
                const npsn = document.getElementById('npsn').value.trim();
                const alamat = document.getElementById('alamat').value.trim();

                if (!nama || !npsn || !alamat) {
                    Swal.fire({
                        title: 'Form Belum Lengkap',
                        text: 'Nama sekolah, NPSN (8 digit), dan alamat sekolah wajib diisi.',
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

                if (npsn.length !== 8 || isNaN(npsn)) {
                    Swal.fire({
                        title: 'Format NPSN Salah',
                        text: 'NPSN harus berupa 8 digit angka.',
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
                    title: 'Simpan Profil Sekolah?',
                    text: 'Apakah Anda yakin ingin menyimpan seluruh perubahan profil dan identitas sekolah ini?',
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
                        const form = document.getElementById('formProfilSekolah');
                        if (form) {
                            form.querySelectorAll('input, textarea, select').forEach(el => el.removeAttribute('disabled'));
                            form.submit();
                        } else {
                            document.getElementById('formProfilSekolah').submit();
                        }
                    }
                });
            },

            previewImage(event, imgId, placeholderId = null) {
                const file = event.target.files[0];
                if (!file) return;

                const validFormats = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validFormats.includes(file.type)) {
                    Swal.fire({
                        title: 'Format File Ditolak',
                        text: 'File yang diunggah harus berformat JPG, PNG, atau WebP.',
                        confirmButtonColor: '#0F766E',
                        width: '22rem',
                        customClass: {
                            popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                            title: 'text-sm font-bold text-[#1F2937] m-0 mb-1',
                            htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                        }
                    });
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    const imgElement = document.getElementById(imgId);
                    if (imgElement) {
                        imgElement.src = e.target.result;
                        imgElement.classList.remove('hidden');
                    }
                    if (placeholderId) {
                        const phElement = document.getElementById(placeholderId);
                        if (phElement) phElement.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        };
    }
</script>
@endsection
