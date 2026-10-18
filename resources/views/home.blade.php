@extends('layouts.peminjam')

@section('title', 'Beranda - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="space-y-12 sm:space-y-16 pb-12">

    <!-- ==========================================
         1. HERO SECTION (Dark Teal Hero Banner)
         ========================================== -->
    <section class="relative bg-gradient-to-br from-[#134E4A] via-[#0F766E] to-[#115E59] text-white overflow-hidden shadow-md">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid-pattern" width="8" height="8" patternUnits="userSpaceOnUse">
                        <path d="M 8 0 L 0 0 0 8" fill="none" stroke="currentColor" stroke-width="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-pattern)" />
            </svg>
        </div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#2DD4BF]/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-[#115E59]/30 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Left Column: School Identity, NPSN, Description & Search Bar -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-[#CCFBF1]/20 border border-[#CCFBF1]/30 backdrop-blur-xs text-[#CCFBF1] text-xs font-semibold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        <span>NPSN: {{ $schoolProfile->npsn ?? '20203040' }}</span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white">
                            {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                        </h1>
                        <p class="text-sm sm:text-base text-[#CCFBF1] font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            {{ $schoolProfile->tagline ?? 'Portal Informasi Inventaris & Peminjaman Sarana Prasarana Sekolah Terpadu' }}
                        </p>
                    </div>

                    <p class="text-xs sm:text-sm text-teal-100/80 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }} memudahkan seluruh civitas akademika untuk meminjam fasilitas, peralatan praktikum, serta sarana belajar secara akuntabel, transparan, dan terintegrasi.
                    </p>

                    <!-- Functional Search Bar (Directs to Katalog Barang with search query) -->
                    <div class="pt-2 max-w-xl mx-auto lg:mx-0">
                        <form action="{{ route('admin.inventaris.index') }}" method="GET" class="relative flex items-center">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#0F766E]">
                                    <span class="material-symbols-outlined text-[20px]">search</span>
                                </div>
                                <input type="text"
                                       name="search"
                                       autocomplete="off"
                                       placeholder="Cari barang atau fasilitas (contoh: proyektor, bola, mikroskop)..."
                                       class="w-full pl-10 pr-28 py-3.5 rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] text-xs sm:text-sm font-medium shadow-lg border border-transparent focus:border-[#2DD4BF] focus:ring-3 focus:ring-[#2DD4BF]/30 transition-all outline-none">
                                <button type="submit"
                                        class="absolute right-1.5 top-1.5 bottom-1.5 px-4 sm:px-5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs sm:text-sm font-semibold flex items-center space-x-1.5 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0F766E]">
                                    <span>Cari</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </button>
                            </div>
                        </form>
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 pt-2.5 text-[11px] text-[#CCFBF1]/80">
                            <span class="font-semibold text-white">Rekomendasi:</span>
                            <a href="{{ route('admin.inventaris.index', ['search' => 'Proyektor']) }}" class="underline hover:text-white transition-colors">Proyektor</a>
                            <span>•</span>
                            <a href="{{ route('admin.inventaris.index', ['search' => 'Laptop']) }}" class="underline hover:text-white transition-colors">Laptop</a>
                            <span>•</span>
                            <a href="{{ route('admin.inventaris.index', ['search' => 'Kamera']) }}" class="underline hover:text-white transition-colors">Kamera</a>
                            <span>•</span>
                            <a href="{{ route('admin.inventaris.index', ['search' => 'Sound']) }}" class="underline hover:text-white transition-colors">Sound System</a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: School Building Photo or High-End Visual Showcase -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-md lg:max-w-none">
                        <div class="relative rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl bg-[#0F766E]/40 backdrop-blur-xs aspect-[4/3] group">
                            @if(!empty($schoolProfile->foto) && file_exists(public_path($schoolProfile->foto)))
                                <img src="{{ asset($schoolProfile->foto) }}"
                                     alt="Gedung {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <!-- High-quality visual representation when building photo has not been uploaded yet -->
                                <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-gradient-to-br from-[#115E59] to-[#134E4A]">
                                    <div class="w-20 h-20 rounded-2xl bg-white/10 flex items-center justify-center mb-4 text-[#CCFBF1] shadow-inner">
                                        <span class="material-symbols-outlined text-4xl">domain</span>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-white mb-1">
                                        {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                                    </h3>
                                    <p class="text-xs text-[#CCFBF1] max-w-xs">
                                        Gedung & Kampus Terpadu Purwakarta
                                    </p>
                                    <div class="mt-4 inline-flex items-center space-x-1.5 px-3 py-1 rounded-md bg-white/10 text-[11px] text-teal-200">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span class="truncate max-w-[200px]">{{ $schoolProfile->kabupaten_kota ?? 'Kabupaten Purwakarta' }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Caption Overlay -->
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 sm:p-5 flex items-center justify-between text-white">
                                <div>
                                    <p class="text-xs font-bold leading-tight">{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}</p>
                                    <p class="text-[10px] text-[#CCFBF1]/90">Gedung Utama & Sarana Prasarana</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#0F766E] text-white">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ==========================================
         2. INFORMASI KONTAK ("Hubungi & Temui Lokasi Kami")
         ========================================== -->
    <section id="kontak-kami" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <span class="px-3 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider inline-block mb-2">
                Kontak & Layanan
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1F2937] tracking-tight">
                Hubungi & Temui Lokasi Kami
            </h2>
            <p class="text-xs sm:text-sm text-[#4B5563] mt-2">
                Informasi resmi saluran komunikasi dan alamat sekolah SMPN 3 Babakancikao untuk keperluan administrasi dan konfirmasi peminjaman inventaris.
            </p>
        </div>

        <!-- 3 Contact Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Alamat Sekolah -->
            <div class="bg-white rounded-2xl border border-[#D9E4E2] p-6 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#F0FDFA] border border-[#CCFBF1] flex items-center justify-center text-[#0F766E] mb-4 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-2xl">location_on</span>
                    </div>
                    <h3 class="text-base font-bold text-[#1F2937] mb-2">Alamat Sekolah</h3>
                    <p class="text-xs text-[#4B5563] leading-relaxed mb-4">
                        {{ $schoolProfile->alamat ?? 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat' }}
                        @if(!empty($schoolProfile->kabupaten_kota) || !empty($schoolProfile->provinsi))
                            <br><span class="font-medium text-[#1F2937]">{{ $schoolProfile->kabupaten_kota ?? '' }}{{ (!empty($schoolProfile->kabupaten_kota) && !empty($schoolProfile->provinsi)) ? ', ' : '' }}{{ $schoolProfile->provinsi ?? '' }}</span>
                        @endif
                    </p>
                </div>
                <div class="pt-4 border-t border-[#F3F7F6]">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($schoolProfile->alamat ?? 'SMPN 3 Babakancikao Purwakarta') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#0F766E] hover:text-[#115E59] group-hover:underline">
                        <span>Buka di Peta</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>
            </div>

            <!-- Card 2: Nomor Telepon -->
            <div class="bg-white rounded-2xl border border-[#D9E4E2] p-6 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#F0FDFA] border border-[#CCFBF1] flex items-center justify-center text-[#0F766E] mb-4 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-2xl">call</span>
                    </div>
                    <h3 class="text-base font-bold text-[#1F2937] mb-2">Nomor Telepon</h3>
                    <p class="text-xs text-[#4B5563] leading-relaxed mb-1">
                        Hubungi pihak tata usaha atau bagian inventaris sarpras pada jam kerja sekolah:
                    </p>
                    <p class="text-sm font-semibold text-[#0F766E] font-mono mt-2">
                        {{ $schoolProfile->telepon ?? '(0264) 8301234' }}
                    </p>
                </div>
                <div class="pt-4 border-t border-[#F3F7F6]">
                    @php
                        $rawPhone = $schoolProfile->telepon ?? '02648301234';
                        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                        if (\Illuminate\Support\Str::startsWith($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#0F766E] hover:text-[#115E59] group-hover:underline">
                        <span>Hubungi via WhatsApp</span>
                        <span class="material-symbols-outlined text-[14px]">chat</span>
                    </a>
                </div>
            </div>

            <!-- Card 3: Email Resmi -->
            <div class="bg-white rounded-2xl border border-[#D9E4E2] p-6 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#F0FDFA] border border-[#CCFBF1] flex items-center justify-center text-[#0F766E] mb-4 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-2xl">mail</span>
                    </div>
                    <h3 class="text-base font-bold text-[#1F2937] mb-2">Email Resmi</h3>
                    <p class="text-xs text-[#4B5563] leading-relaxed mb-1">
                        Kirimkan surat permohonan peminjaman resmi atau kerja sama kegiatan ke email sekolah:
                    </p>
                    <p class="text-sm font-semibold text-[#0F766E] font-mono mt-2 truncate">
                        {{ $schoolProfile->email ?? 'info@smpn3babakancikao.sch.id' }}
                    </p>
                </div>
                <div class="pt-4 border-t border-[#F3F7F6]">
                    <a href="mailto:{{ $schoolProfile->email ?? 'info@smpn3babakancikao.sch.id' }}"
                       class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#0F766E] hover:text-[#115E59] group-hover:underline">
                        <span>Kirim Pesan Email</span>
                        <span class="material-symbols-outlined text-[14px]">send</span>
                    </a>
                </div>
            </div>

        </div>
    </section>


    <!-- ==========================================
         3. PETA LOKASI SEKOLAH
         ========================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-[#D9E4E2] p-5 sm:p-8 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="material-symbols-outlined text-[#0F766E] text-2xl">pin_drop</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-[#1F2937]">Peta Lokasi Sekolah</h2>
                    </div>
                    <p class="text-xs text-[#4B5563] mt-1">
                        {{ $schoolProfile->alamat ?? 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat' }}
                    </p>
                </div>

                <!-- Tombol Petunjuk Arah Fungsional -->
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($schoolProfile->alamat ?? 'SMPN 3 Babakancikao Purwakarta') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 shrink-0">
                    <span class="material-symbols-outlined text-[18px]">directions</span>
                    <span>Petunjuk Arah</span>
                </a>
            </div>

            <!-- Responsive Google Maps Embed Frame -->
            <div class="w-full h-[320px] sm:h-[400px] lg:h-[450px] rounded-xl overflow-hidden border border-[#D9E4E2] bg-[#F3F7F6] relative">
                <iframe
                    title="Peta Lokasi {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                    width="100%"
                    height="100%"
                    frameborder="0"
                    scrolling="no"
                    marginheight="0"
                    marginwidth="0"
                    src="https://maps.google.com/maps?q={{ urlencode($schoolProfile->alamat ?? 'SMPN 3 Babakancikao Purwakarta') }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                    class="w-full h-full border-0"
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>


    <!-- ==========================================
         4. DENAH SEKOLAH ("Tata Letak Gedung & Ruang Inventaris")
         ========================================== -->
    <section id="denah-sekolah" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-[#D9E4E2] p-6 sm:p-8 shadow-xs">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <span class="px-2.5 py-1 rounded-md bg-[#F0FDFA] text-[#0F766E] text-xs font-bold uppercase tracking-wider inline-block mb-1 border border-[#CCFBF1]">
                        Peta Fasilitas
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#1F2937]">Tata Letak Gedung & Ruang Inventaris</h2>
                    <p class="text-xs text-[#4B5563] mt-1">
                        Panduan penempatan ruang sarana, laboratorium, perpustakaan, dan tempat pengambilan barang inventaris di {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}.
                    </p>
                </div>

                <!-- Tombol Unduh Denah PNG -->
                @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                    <a href="{{ asset($schoolProfile->denah) }}"
                       download="Denah-{{ \Illuminate\Support\Str::slug($schoolProfile->nama_sekolah ?? 'SMPN-3-Babakancikao') }}.png"
                       class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#0F766E]/40 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Denah (PNG)</span>
                    </a>
                @else
                    <button type="button"
                            onclick="showDenahNotice()"
                            class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl bg-[#E5E7EB] hover:bg-[#D1D5DB] text-[#4B5563] text-xs font-semibold transition-all focus:outline-none shrink-0"
                            title="File denah digital sedang dalam pembaruan">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Denah (PNG)</span>
                    </button>
                @endif
            </div>

            <!-- Denah Display Area -->
            @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                <div class="rounded-xl overflow-hidden border border-[#D9E4E2] bg-[#F8FAFA] p-3 flex justify-center items-center">
                    <img src="{{ asset($schoolProfile->denah) }}"
                         alt="Denah Tata Letak Gedung {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                         class="max-h-[550px] w-auto object-contain rounded-lg shadow-xs hover:shadow-md transition-shadow">
                </div>
            @else
                <!-- Fallback Informatif (Jika denah belum diunggah oleh admin, tidak mengarang gambar denah palsu) -->
                <div class="rounded-xl border border-dashed border-[#D9E4E2] bg-[#F9FAFB] p-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-[#F0FDFA] border border-[#CCFBF1] flex items-center justify-center text-[#0F766E] mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">map</span>
                    </div>
                    <h3 class="text-base font-bold text-[#1F2937] mb-1">
                        Peta Digital Denah Sedang Dalam Proses Pembaruan
                    </h3>
                    <p class="text-xs text-[#4B5563] max-w-lg mx-auto leading-relaxed mb-6">
                        Pihak pengelola sarana dan prasarana {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }} sedang menyiapkan file denah digital resolusi tinggi resmi terbaru.
                    </p>

                    <!-- Info Ringkas Lokasi Ruangan Utama -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto text-left">
                        <div class="p-3.5 rounded-xl bg-white border border-[#D9E4E2] text-xs">
                            <div class="flex items-center space-x-2 font-bold text-[#0F766E] mb-1">
                                <span class="material-symbols-outlined text-[18px]">inventory</span>
                                <span>Ruang Sarpras</span>
                            </div>
                            <p class="text-[11px] text-[#4B5563]">Pengambilan & pengembalian peralatan sarana umum sekolah.</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white border border-[#D9E4E2] text-xs">
                            <div class="flex items-center space-x-2 font-bold text-[#0F766E] mb-1">
                                <span class="material-symbols-outlined text-[18px]">computer</span>
                                <span>Lab Komputer</span>
                            </div>
                            <p class="text-[11px] text-[#4B5563]">Inventaris proyektor, kabel converter, perangkat IT & audio.</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white border border-[#D9E4E2] text-xs">
                            <div class="flex items-center space-x-2 font-bold text-[#0F766E] mb-1">
                                <span class="material-symbols-outlined text-[18px]">science</span>
                                <span>Laboratorium IPA</span>
                            </div>
                            <p class="text-[11px] text-[#4B5563]">Peralatan praktikum fisika, biologi, dan mikroskop.</p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white border border-[#D9E4E2] text-xs">
                            <div class="flex items-center space-x-2 font-bold text-[#0F766E] mb-1">
                                <span class="material-symbols-outlined text-[18px]">sports_soccer</span>
                                <span>Ruang Olahraga</span>
                            </div>
                            <p class="text-[11px] text-[#4B5563]">Inventaris bola, matras, jaring net, dan perlengkapan senam.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

</div>

<script>
    function showDenahNotice() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Denah Dalam Pembaruan',
                text: 'File peta denah digital resmi sedang dalam proses pembaruan oleh unit sarana & prasarana SMPN 3 Babakancikao. Untuk informasi tata letak fisik, silakan kunjungi Ruang Sarpras di lokasi sekolah.',
                confirmButtonColor: '#0F766E',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-2',
                    htmlContainer: 'text-xs text-[#4B5563] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-semibold bg-[#0F766E] hover:bg-[#115E59] text-white shadow-sm'
                }
            });
        } else {
            alert('File denah resmi sedang dalam proses pembaruan oleh unit sarana & prasarana.');
        }
    }
</script>
@endsection
