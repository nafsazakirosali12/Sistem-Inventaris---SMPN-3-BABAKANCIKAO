@extends('layouts.admin')

@section('title', 'Tambah Inventaris Baru - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="flex flex-col w-full gap-6 pb-20 md:pb-6">

    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">Tambah Inventaris Baru</h1>
            <p class="text-xs text-[#4B5563] mt-0.5 max-w-3xl">
                Masukkan data unit fisik inventaris barang baru ke dalam sistem inventaris SMPN 3 Babakancikao.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inventaris.index') }}"
               class="h-10 px-4 rounded-lg bg-white border border-[#D9E4E2] text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Tambah Inventaris -->
    <form id="formCreateInventory"
          action="{{ route('admin.inventaris.store') }}"
          method="POST"
          enctype="multipart/form-data"
          onsubmit="return handleFormSubmit(event)"
          class="flex flex-col gap-6">
        @csrf

        <!-- =================================================================== -->
        <!-- INFORMASI UTAMA                                                     -->
        <!-- =================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="border-b border-[#D9E4E2] pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#1F2937]">Informasi Utama</h2>
                    <p class="text-xs text-[#6B7280] mt-0.5">Identitas utama barang, klasifikasi kategori, ruangan penempatan, dan status unit.</p>
                </div>
                <span class="px-2.5 py-1 rounded bg-[#F0FDFA] text-[#0F766E] border border-[#CCFBF1] text-[11px] font-semibold">
                    Wajib Diisi *
                </span>
            </div>

            <!-- Upload Foto Barang dengan Live Preview -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-semibold text-[#1F2937]">
                    Foto Barang (Opsional)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <!-- Dropzone Area -->
                    <div class="md:col-span-8">
                        <label for="foto_input"
                               class="border-2 border-dashed border-[#D9E4E2] hover:border-[#0F766E] bg-[#F8FBFA] hover:bg-[#F0FDFA] rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-all text-center">
                            <span class="material-symbols-outlined text-[#0F766E] text-[36px] mb-2">add_photo_alternate</span>
                            <span class="text-xs font-semibold text-[#1F2937]">Pilih foto barang atau seret ke sini</span>
                            <span class="text-[11px] text-[#6B7280] mt-1">Format: JPG, PNG, atau WebP (Maksimal 2 MB)</span>
                            <input type="file"
                                   id="foto_input"
                                   name="foto"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="hidden"
                                   onchange="previewImage(this)">
                        </label>
                        @error('foto')
                            <p class="text-xs text-[#DC2626] mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Live Image Preview Box -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center">
                        <div id="preview_container" class="relative w-full h-36 bg-[#F3F7F6] border border-[#D9E4E2] rounded-xl overflow-hidden flex items-center justify-center">
                            <img id="preview_img" src="" alt="Pratinjau Foto" class="hidden w-full h-full object-cover">
                            <div id="preview_placeholder" class="flex flex-col items-center justify-center text-[#9CA3AF] p-4 text-center">
                                <span class="material-symbols-outlined text-[32px] mb-1">image</span>
                                <span class="text-[11px]">Pratinjau foto barang akan muncul di sini</span>
                            </div>
                            <button type="button"
                                    id="btn_remove_preview"
                                    onclick="removeImagePreview()"
                                    class="hidden absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 hover:bg-white text-[#DC2626] shadow-sm flex items-center justify-center transition-all"
                                    title="Hapus foto">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Form Utama: Kode Barang (Tanpa Icon Barcode), Nama Barang -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode Barang: Tanpa icon barcode di dalam input -->
                <div class="flex flex-col gap-1.5">
                    <label for="kode_barang" class="text-xs font-semibold text-[#1F2937]">
                        Kode Barang <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="text"
                           id="kode_barang"
                           name="kode_barang"
                           value="{{ old('kode_barang') }}"
                           placeholder="mis. INV-2024-LAP-001"
                           required
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg font-mono text-xs uppercase font-bold text-[#1F2937] border {{ $errors->has('kode_barang') ? 'border-[#DC2626] focus:border-[#DC2626] focus:ring-[#DC2626]/20' : 'border-[#D9E4E2] focus:border-[#0F766E] focus:ring-[#0F766E]/20' }} focus:bg-white focus:outline-none focus:ring-2 transition-all">
                    <!-- <span class="text-[11px] text-[#6B7280]">Input manual oleh admin, unik untuk setiap unit fisik barang.</span> -->
                    @error('kode_barang')
                        <p class="text-xs text-[#DC2626] mt-0.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nama Barang -->
                <div class="flex flex-col gap-1.5">
                    <label for="nama_barang" class="text-xs font-semibold text-[#1F2937]">
                        Nama Barang <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="text"
                           id="nama_barang"
                           name="nama_barang"
                           value="{{ old('nama_barang') }}"
                           placeholder="mis. Laptop ASUS Vivobook 14"
                           required
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border {{ $errors->has('nama_barang') ? 'border-[#DC2626] focus:border-[#DC2626] focus:ring-[#DC2626]/20' : 'border-[#D9E4E2] focus:border-[#0F766E] focus:ring-[#0F766E]/20' }} focus:bg-white focus:outline-none focus:ring-2 transition-all">
                    @error('nama_barang')
                        <p class="text-xs text-[#DC2626] mt-0.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Grid Form Utama: Kategori, Ruangan, Status Unit -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Dropdown Kategori (Dari Database) -->
                <div class="flex flex-col gap-1.5">
                    <label for="category_id" class="text-xs font-semibold text-[#1F2937]">
                        Kategori Barang <span class="text-[#DC2626]">*</span>
                    </label>
                    <div class="relative">
                        <select id="category_id"
                                name="category_id"
                                required
                                class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer border {{ $errors->has('category_id') ? 'border-[#DC2626]' : 'border-[#D9E4E2]' }} focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all truncate">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }} ({{ $cat->kode_kategori }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div>
                    </div>
                    @error('category_id')
                        <p class="text-xs text-[#DC2626] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dropdown Ruangan (Dari Database) -->
                <div class="flex flex-col gap-1.5">
                    <label for="room_id" class="text-xs font-semibold text-[#1F2937]">
                        Ruangan Penempatan <span class="text-[#DC2626]">*</span>
                    </label>
                    <div class="relative">
                        <select id="room_id"
                                name="room_id"
                                required
                                class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer border {{ $errors->has('room_id') ? 'border-[#DC2626]' : 'border-[#D9E4E2]' }} focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all truncate">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->nama_ruangan }} ({{ $room->kode_ruangan }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div> 
                    </div>
                    @error('room_id')
                        <p class="text-xs text-[#DC2626] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Unit: Baik, Rusak, Hilang (Kondisi fisik unit, BUKAN status transaksi peminjaman) -->
                <div class="flex flex-col gap-1.5">
                    <label for="status" class="text-xs font-semibold text-[#1F2937]">
                        Status Unit <span class="text-[#DC2626]">*</span>
                    </label>
                    <div class="relative">
                        <select id="status"
                                name="status"
                                required
                                class="w-full h-10 pl-3 pr-8 bg-[#F3F7F6] text-xs text-[#1F2937] rounded-lg appearance-none cursor-pointer border {{ $errors->has('status') ? 'border-[#DC2626]' : 'border-[#D9E4E2]' }} focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                            <option value="baik" {{ old('status', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ old('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="perlu_perbaikan" {{ old('status') == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                            <option value="hilang" {{ old('status') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#6B7280]">
                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div>
                    </div>
                    <!-- <span class="text-[11px] text-[#6B7280]">Kondisi fisik/ketersediaan kondisi unit barang.</span> -->
                    @error('status')
                        <p class="text-xs text-[#DC2626] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- DETAIL ASET & REKAPITULASI (Judul Card Sesuai Revisi)                -->
        <!-- =================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-[#D9E4E2] p-5 sm:p-6 flex flex-col gap-5">
            <div class="border-b border-[#D9E4E2] pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#1F2937]">Detail Aset &amp; Rekapitulasi</h2>
                    <p class="text-xs text-[#6B7280] mt-0.5">Spesifikasi teknis, nomor identitas legalitas/pabrik, asal-usul, dan harga pengadaan aset. Field opsional boleh dikosongkan.</p>
                </div>
                <span class="px-2.5 py-1 rounded bg-[#F3F7F6] text-[#4B5563] border border-[#D9E4E2] text-[11px] font-semibold">
                    Opsional
                </span>
            </div>

            <!-- Baris 1: Nomor Register, Merek/Tipe, Ukuran/CC, Bahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Nomor Register -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_register" class="text-xs font-semibold text-[#1F2937]">
                        Nomor Register
                    </label>
                    <input type="text"
                           id="nomor_register"
                           name="nomor_register"
                           value="{{ old('nomor_register') }}"
                           placeholder="mis. 0001 / REG-01"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Merek/Tipe -->
                <div class="flex flex-col gap-1.5">
                    <label for="merk_type" class="text-xs font-semibold text-[#1F2937]">
                        Merek / Tipe
                    </label>
                    <input type="text"
                           id="merk_type"
                           name="merk_type"
                           value="{{ old('merk_type') }}"
                           placeholder="mis. ASUS Vivobook A416"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Ukuran/CC -->
                <div class="flex flex-col gap-1.5">
                    <label for="ukuran_cc" class="text-xs font-semibold text-[#1F2937]">
                        Ukuran / CC
                    </label>
                    <input type="text"
                           id="ukuran_cc"
                           name="ukuran_cc"
                           value="{{ old('ukuran_cc') }}"
                           placeholder="mis. 14 Inci / 125 cc"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Bahan -->
                <div class="flex flex-col gap-1.5">
                    <label for="bahan" class="text-xs font-semibold text-[#1F2937]">
                        Bahan / Material
                    </label>
                    <input type="text"
                           id="bahan"
                           name="bahan"
                           value="{{ old('bahan') }}"
                           placeholder="mis. Aluminium / Plastik / Kayu"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>
            </div>

            <!-- Baris 2: Nomor Pabrik, Rangka, Mesin, Polisi, BPKB -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Nomor Pabrik -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_pabrik" class="text-xs font-semibold text-[#1F2937]">
                        Nomor Pabrik
                    </label>
                    <input type="text"
                           id="nomor_pabrik"
                           name="nomor_pabrik"
                           value="{{ old('nomor_pabrik') }}"
                           placeholder="mis. PBK-992182"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Nomor Rangka -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_rangka" class="text-xs font-semibold text-[#1F2937]">
                        Nomor Rangka
                    </label>
                    <input type="text"
                           id="nomor_rangka"
                           name="nomor_rangka"
                           value="{{ old('nomor_rangka') }}"
                           placeholder="mis. MH1JF1..."
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Nomor Mesin -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_mesin" class="text-xs font-semibold text-[#1F2937]">
                        Nomor Mesin
                    </label>
                    <input type="text"
                           id="nomor_mesin"
                           name="nomor_mesin"
                           value="{{ old('nomor_mesin') }}"
                           placeholder="mis. JF1E123..."
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Nomor Polisi -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_polisi" class="text-xs font-semibold text-[#1F2937]">
                        Nomor Polisi
                    </label>
                    <input type="text"
                           id="nomor_polisi"
                           name="nomor_polisi"
                           value="{{ old('nomor_polisi') }}"
                           placeholder="mis. T 1234 XX"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Nomor BPKB -->
                <div class="flex flex-col gap-1.5">
                    <label for="nomor_bpkb" class="text-xs font-semibold text-[#1F2937]">
                        Nomor BPKB
                    </label>
                    <input type="text"
                           id="nomor_bpkb"
                           name="nomor_bpkb"
                           value="{{ old('nomor_bpkb') }}"
                           placeholder="mis. BPKB-00123"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>
            </div>

            <!-- Baris 3: Tahun Pembelian, Asal/Perolehan, Harga -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Tahun Pembelian -->
                <div class="flex flex-col gap-1.5">
                    <label for="tahun_pembelian" class="text-xs font-semibold text-[#1F2937]">
                        Tahun Pembelian / Pengadaan
                    </label>
                    <input type="text"
                           id="tahun_pembelian"
                           name="tahun_pembelian"
                           value="{{ old('tahun_pembelian', date('Y')) }}"
                           placeholder="mis. 2024"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border {{ $errors->has('tahun_pembelian') ? 'border-[#DC2626]' : 'border-[#D9E4E2]' }} focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                    @error('tahun_pembelian')
                        <p class="text-xs text-[#DC2626] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asal / Perolehan -->
                <div class="flex flex-col gap-1.5">
                    <label for="asal_usul" class="text-xs font-semibold text-[#1F2937]">
                        Asal / Sumber Perolehan
                    </label>
                    <input type="text"
                           id="asal_usul"
                           name="asal_usul"
                           value="{{ old('asal_usul') }}"
                           placeholder="mis. BOS / APBD / Hibah"
                           class="w-full h-10 px-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                </div>

                <!-- Harga: Format Bersih (Rp prefix di luar input, nilai murni dikirim ke server) -->
                <div class="flex flex-col gap-1.5">
                    <label for="harga_display" class="text-xs font-semibold text-[#1F2937]">
                        Harga Perolehan
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-semibold text-[#4B5563]">
                            Rp
                        </span>
                        <input type="text"
                               id="harga_display"
                               value="{{ old('harga') ? number_format((float)preg_replace('/[^0-9]/', '', old('harga')), 0, ',', '.') : '' }}"
                               placeholder="5.000.000"
                               oninput="handleRupiahInput(this)"
                               class="w-full h-10 pl-9 pr-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] font-mono border {{ $errors->has('harga') ? 'border-[#DC2626]' : 'border-[#D9E4E2]' }} focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all">
                        <input type="hidden" name="harga" id="harga" value="{{ old('harga') }}">
                    </div>
                    <span class="text-[11px] text-[#6B7280]">Nilai disimpan ke database sebagai angka murni (misal 5000000).</span>
                    @error('harga')
                        <p class="text-xs text-[#DC2626] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Baris 4: Deskripsi / Keterangan -->
            <div class="flex flex-col gap-1.5">
                <label for="deskripsi" class="text-xs font-semibold text-[#1F2937]">
                    Deskripsi / Catatan Tambahan
                </label>
                <textarea id="deskripsi"
                          name="deskripsi"
                          rows="3"
                          placeholder="Tambahkan rincian kelengkapan barang, nomor serial tambahan, atau catatan kondisi fisik..."
                          class="w-full p-3 bg-[#F3F7F6] rounded-lg text-xs text-[#1F2937] border border-[#D9E4E2] focus:bg-white focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/20 transition-all resize-none">{{ old('deskripsi') }}</textarea>
            </div>
        </div>

        <!-- Sticky Footer Action Bar -->
        <div class="fixed md:static bottom-0 left-0 right-0 z-20 bg-white/95 backdrop-blur-md md:bg-transparent border-t md:border-t-0 border-[#D9E4E2] p-4 md:p-0 flex items-center justify-end gap-3 shadow-lg md:shadow-none">
            <a href="{{ route('admin.inventaris.index') }}"
               class="h-10 px-5 rounded-lg border border-[#D9E4E2] bg-white text-xs font-semibold text-[#4B5563] hover:text-[#1F2937] hover:bg-[#F3F7F6] transition-colors flex items-center justify-center">
                Batal
            </a>
            <button type="submit"
                    class="h-10 px-6 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Inventaris</span>
            </button>
        </div>
    </form>

</div>

<script>
    let isConfirmedSubmit = false;

    // Helper Format Rupiah Input
    function handleRupiahInput(input) {
        let rawVal = input.value.replace(/[^0-9]/g, '');
        document.getElementById('harga').value = rawVal;
        if (rawVal) {
            input.value = new Intl.NumberFormat('id-ID').format(rawVal);
        } else {
            input.value = '';
        }
    }

    // Live Image Preview Handler
    function previewImage(input) {
        const file = input.files[0];
        const previewImg = document.getElementById('preview_img');
        const placeholder = document.getElementById('preview_placeholder');
        const removeBtn = document.getElementById('btn_remove_preview');

        if (file) {
            if (file.size > 2097152) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran foto maksimal 2 MB. Silakan pilih foto dengan ukuran lebih kecil.',
                    confirmButtonColor: '#0F766E',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                        confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                    }
                });
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
                removeBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function removeImagePreview() {
        const input = document.getElementById('foto_input');
        const previewImg = document.getElementById('preview_img');
        const placeholder = document.getElementById('preview_placeholder');
        const removeBtn = document.getElementById('btn_remove_preview');

        input.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }

    // Confirmation Alert before submitting (Per DESIGN.md 6.10)
    function handleFormSubmit(event) {
        if (isConfirmedSubmit) {
            return true;
        }

        event.preventDefault();

        const kode = document.getElementById('kode_barang').value.trim();
        const nama = document.getElementById('nama_barang').value.trim();
        const kategori = document.getElementById('category_id').value;
        const ruangan = document.getElementById('room_id').value;
        const status = document.getElementById('status').value;

        if (!kode || !nama || !kategori || !ruangan || !status) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Pastikan seluruh field wajib pada Informasi Utama (Kode Barang, Nama, Kategori, Ruangan, Status Unit) telah terisi.',
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Lengkapi Data',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
            return false;
        }

        Swal.fire({
            title: 'Simpan data inventaris?',
            text: `Unit barang '${nama}' dengan kode '${kode}' akan ditambahkan ke sistem inventaris.`,
            showCancelButton: true,
            confirmButtonColor: '#0F766E',
            cancelButtonColor: '#9CA3AF',
            confirmButtonText: 'Ya, Simpan',
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
                isConfirmedSubmit = true;
                document.getElementById('formCreateInventory').submit();
            }
        });

        return false;
    }
</script>
@endsection
