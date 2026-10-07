@extends('layouts.peminjam')

@section('title', 'Beranda - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="space-y-6">
    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-[#134E4A] to-[#0F766E] rounded-xl p-6 sm:p-8 text-white shadow-lg overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="relative z-10 max-w-2xl">
            <span class="px-2.5 py-1 rounded-md bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider inline-block mb-2">
                Selamat Datang
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}</h1>
            <p class="text-sm text-[#CCFBF1] leading-relaxed">
                Selamat datang di {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}. Layanan peminjaman fasilitas dan sarana prasarana sekolah terpadu.
            </p>
        </div>

        @if(!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo)))
            <div class="shrink-0 bg-white/10 backdrop-blur-xs p-3 rounded-2xl border border-white/20 shadow-md">
                <img src="{{ asset($schoolProfile->logo) }}" alt="Logo Sekolah" class="h-20 sm:h-24 w-auto object-contain">
            </div>
        @endif
    </div>

    <!-- Info Cards Grid (3 Cards: Profil Sekolah, Gedung Sekolah, Denah Sekolah) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- 1. Card Profil Sekolah -->
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#D9E4E2]">
                    <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-[#1F2937]">Profil Sekolah</h3>
                        <p class="text-[11px] text-[#6B7280]">Identitas & Informasi Resmi</p>
                    </div>
                </div>
                <dl class="space-y-3 text-xs text-[#4B5563]">
                    <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                        <dt class="font-semibold text-[#1F2937]">NPSN:</dt>
                        <dd class="font-mono font-bold text-[#0F766E]">{{ $schoolProfile->npsn ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                        <dt class="font-semibold text-[#1F2937]">Alamat:</dt>
                        <dd class="text-right max-w-[180px] sm:max-w-xs text-[11px] leading-tight">{{ $schoolProfile->alamat ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-[#D9E4E2] pb-2">
                        <dt class="font-semibold text-[#1F2937]">Telepon:</dt>
                        <dd>{{ $schoolProfile->telepon ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-[#1F2937]">Email:</dt>
                        <dd class="text-[11px]">{{ $schoolProfile->email ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- 2. Card Foto Gedung Sekolah -->
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#D9E4E2]">
                    <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                        <span class="material-symbols-outlined">domain</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-[#1F2937]">Gedung Sekolah</h3>
                        <p class="text-[11px] text-[#6B7280]">Fasilitas & Lingkungan</p>
                    </div>
                </div>

                @if(!empty($schoolProfile->foto) && file_exists(public_path($schoolProfile->foto)))
                    <div class="relative w-full h-40 rounded-xl overflow-hidden border border-[#D9E4E2] bg-[#F8FBFA] shadow-xs group cursor-pointer"
                         onclick="previewGedung('{{ asset($schoolProfile->foto) }}', '{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}')">
                        <img src="{{ asset($schoolProfile->foto) }}"
                             alt="Gedung {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                            <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            <span>Lihat Foto Full</span>
                        </div>
                    </div>
                @else
                    <div class="w-full h-40 rounded-xl border border-dashed border-[#D9E4E2] bg-[#F8FBFA] flex flex-col items-center justify-center text-[#9CA3AF] p-4 text-center">
                        <span class="material-symbols-outlined text-[36px] text-[#CBD5E1]">domain</span>
                        <span class="text-xs font-medium mt-1">Belum ada foto gedung</span>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                @if(!empty($schoolProfile->foto) && file_exists(public_path($schoolProfile->foto)))
                    <button type="button"
                            onclick="previewGedung('{{ asset($schoolProfile->foto) }}', '{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}')"
                            class="w-full py-2 px-3 rounded-lg border border-[#0F766E] text-[#0F766E] hover:bg-[#F0FDFA] font-semibold text-xs flex items-center justify-center space-x-1.5 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                        <span>Lihat Gambar Gedung</span>
                    </button>
                @else
                    <span class="block w-full text-center py-2 text-xs text-[#9CA3AF]">Gambar gedung belum diunggah</span>
                @endif
            </div>
        </div>

        <!-- 3. Card Denah Sekolah -->
        <div class="bg-white rounded-xl border border-[#D9E4E2] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#D9E4E2]">
                    <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E]">
                        <span class="material-symbols-outlined">map</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-[#1F2937]">Denah Sekolah</h3>
                        <p class="text-[11px] text-[#6B7280]">Peta Tata Letak Ruangan</p>
                    </div>
                </div>

                @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                    <div class="relative w-full h-40 rounded-xl overflow-hidden border border-[#D9E4E2] bg-[#F8FBFA] shadow-xs group cursor-pointer"
                         onclick="previewDenah('{{ asset($schoolProfile->denah) }}')">
                        <img src="{{ asset($schoolProfile->denah) }}"
                             alt="Denah {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                            <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            <span>Lihat Denah Full</span>
                        </div>
                    </div>
                @else
                    <div class="w-full h-40 rounded-xl border border-dashed border-[#D9E4E2] bg-[#F8FBFA] flex flex-col items-center justify-center text-[#9CA3AF] p-4 text-center">
                        <span class="material-symbols-outlined text-[36px] text-[#CBD5E1]">map</span>
                        <span class="text-xs font-medium mt-1">Belum ada denah sekolah</span>
                    </div>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                    <button type="button"
                            onclick="previewDenah('{{ asset($schoolProfile->denah) }}')"
                            class="flex-1 py-2.5 px-3 rounded-lg border border-[#0F766E] text-[#0F766E] hover:bg-[#F0FDFA] font-semibold text-xs flex items-center justify-center space-x-1 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                        <span>Pratinjau</span>
                    </button>
                    <a href="{{ asset($schoolProfile->denah) }}"
                       download="Denah-Sekolah-{{ Str::slug($schoolProfile->nama_sekolah ?? 'SMPN-3-Babakancikao') }}"
                       target="_blank"
                       class="flex-1 py-2.5 px-3 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white font-semibold text-xs flex items-center justify-center space-x-1 transition-colors shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh</span>
                    </a>
                @else
                    <button type="button"
                            onclick="showDenahInfo()"
                            class="w-full py-2.5 px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white font-semibold text-xs flex items-center justify-center space-x-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Denah Sekolah</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function previewGedung(url, nama) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Gedung Sekolah',
                text: nama,
                imageUrl: url,
                imageAlt: 'Gedung Sekolah',
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                    confirmButton: 'px-5 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
        }
    }

    function previewDenah(url) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Denah Sekolah',
                imageUrl: url,
                imageAlt: 'Denah Sekolah',
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Tutup',
                showCancelButton: true,
                cancelButtonText: 'Unduh Denah',
                cancelButtonColor: '#115E59',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                    confirmButton: 'px-5 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white',
                    cancelButton: 'px-5 py-2 rounded-lg text-xs font-semibold text-white'
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Denah-Sekolah';
                    a.target = '_blank';
                    a.click();
                }
            });
        }
    }

    function showDenahInfo() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Denah Sekolah Belum Tersedia',
                text: 'Admin sekolah belum mengunggah file denah tata letak gedung sekolah.',
                confirmButtonColor: '#0F766E',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                    confirmButton: 'px-5 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
        } else {
            alert('Belum ada denah yang diunggah oleh admin.');
        }
    }
</script>
@endsection
